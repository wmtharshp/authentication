<?php

namespace Wmtharshp\Authentication\Console;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use RuntimeException;
use Exception;
use Illuminate\Support\Str;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:auth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deploy all file and package to in your project';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->callSilent('vendor:publish', ['--tag' => 'customeauth-view', '--force' => true]);
        // Directories...
        (new Filesystem)->ensureDirectoryExists(app_path('Actions/Contracts'));
        (new Filesystem)->ensureDirectoryExists(app_path('DataTables'));
        (new Filesystem)->ensureDirectoryExists(app_path('Rules'));

        copy(__DIR__.'/../Contracts/CreateNewUser.php', app_path('Actions/Contracts/CreateNewUser.php'));
        copy(__DIR__.'/../Contracts/PasswordValidationRules.php', app_path('Actions/Contracts/PasswordValidationRules.php'));
        copy(__DIR__.'/../DataTables/UsersDataTable.php', app_path('DataTables/UsersDataTable.php'));
        copy(__DIR__.'/../DataTables/RolesDataTable.php', app_path('DataTables/RolesDataTable.php'));
        copy(__DIR__.'/../DataTables/PermissionsDataTable.php', app_path('DataTables/PermissionsDataTable.php'));
        copy(__DIR__.'/../Rules/Password.php', app_path('/Rules/Password.php'));


        app()->make(\App\Composer::class)->run(['require', 'yajra/laravel-datatables-oracle']);
        app()->make(\App\Composer::class)->run(['require', 'yajra/laravel-datatables']);
        app()->make(\App\Composer::class)->run(['require', 'laravel/socialite']);
        app()->make(\App\Composer::class)->run(['require', 'mckenziearts/laravel-notify']);
        app()->make(\App\Composer::class)->run(['require', 'spatie/laravel-permission']);

        // Storage...
        
        $this->updateConfigFile();
        
        $this->updateRouteServicesProvider();
        
        $this->updateUserModel();
        
        $this->updateKernelFile();
    }

    public function updateConfigFile(){
        if (! Str::contains(file_get_contents(base_path('config/app.php')), "PermissionServiceProvider")) {
            $search = "App\Providers\RouteServiceProvider::class,";
            $str = file_get_contents(base_path('config/app.php'));
            $slice = Str::after($str, $search);
    
            $aliases = "// 'ExampleClass' => App\Example\ExampleClass::class,";
            $new_slice = Str::after($slice, $aliases);
            $old_slice = Str::before($slice, $aliases).'"DataTables" => Yajra\DataTables\Facades\DataTables::class,'.$new_slice;
    
            $first_slice = Str::before($str, $search);
            $new_content = $first_slice . "
            App\Providers\RouteServiceProvider::class, 
            Spatie\Permission\PermissionServiceProvider::class,
            Mckenziearts\Notify\LaravelNotifyServiceProvider::class,
            Yajra\DataTables\DataTablesServiceProvider::class,
            Yajra\DataTables\HtmlServiceProvider::class,".$old_slice;
            file_put_contents(base_path('config/app.php'),$new_content );
        }
    }

    public function updateRouteServicesProvider(){
        if (! Str::contains(file_get_contents(app_path('Providers/RouteServiceProvider.php')), "routes/routes.php")) {
            $search = '$this->routes(function () {';
            $str = file_get_contents(app_path('Providers/RouteServiceProvider.php'));
            $slice = Str::after($str, $search);
            $first_slice = Str::before($str, $search);
            $new_content = $first_slice . '
            $this->routes(function () { 
                Route::middleware("web")
                ->group(base_path("routes/routes.php"));'.$slice;
            file_put_contents(app_path('Providers/RouteServiceProvider.php'),$new_content );
        }
    }

    public function updateUserModel(){
        if (! Str::contains(file_get_contents(app_path('Models/User.php')), "HasRoles;")) {
        $search = 'use Laravel\Sanctum\HasApiTokens;';
        $str = file_get_contents(app_path('Models/User.php'));
        $slice = Str::after($str, $search);

        $another_search = "use HasApiTokens";
        $new_slice = Str::after($slice, $another_search);
        $old_slice = Str::before($slice, $another_search).'use HasApiTokens , HasRoles '.$new_slice;

        $first_slice = Str::before($str, $search);
        $new_content = $first_slice . 'use Laravel\Sanctum\HasApiTokens;
        use Spatie\Permission\Traits\HasRoles;'.$old_slice;
        file_put_contents(app_path('Models/User.php'),$new_content );
        }
    }

    public function updateKernelFile(){
        if (! Str::contains(file_get_contents(app_path('Http/Kernel.php')), "\Spatie\Permission\Middlewares\RoleMiddleware::class,")) {
        $search = "'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,";
        $str = file_get_contents(app_path('Http/Kernel.php'));
            $slice = Str::after($str, $search);
            $first_slice = Str::before($str, $search);
            $new_content = $first_slice . "'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
        'permission' => \App\Http\Middleware\PermissionMiddleware::class,
        'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,".$slice;
            file_put_contents(app_path('Http/Kernel.php'),$new_content );
        }
    }

}
