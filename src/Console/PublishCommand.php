<?php

namespace Wmtharshp\Authentication\Console;
use Illuminate\Console\Command;

class PublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'publish:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->callSilent('vendor:publish', ['--tag' => 'datatables', '--force' => true]);
        $this->callSilent('vendor:publish', ['--provider' => 'Mckenziearts\Notify\LaravelNotifyServiceProvider', '--force' => true]);
        $this->callSilent('vendor:publish', [ '--provider' => "Spatie\Permission\PermissionServiceProvider", '--force' => true]);
        $this->callSilent('optimize:clear');
        $this->callSilent('config:clear');
        copy(__DIR__.'/../database/2022_12_12_131652_add_google_id_column.php', base_path('database/migrations/2022_12_12_131652_add_google_id_column.php'));
        copy(__DIR__.'/../database/2023_01_04_073048_add_title_to_users_table.php', base_path('database/migrations/2024_01_04_073048_add_title_to_users_table.php'));


        $this->callSilent('migrate');
        $this->callSilent('db:seed', ['--class' => 'PermissionsSeeder', '--force' => true]);
        $this->callSilent('db:seed', ['--class' => 'RoleSeeder', '--force' => true]);
        $this->callSilent('db:seed', ['--class' => 'AdminSeeder', '--force' => true]);

    }

}
