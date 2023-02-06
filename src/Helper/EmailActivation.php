<?php

namespace App\Helper;

use App\Models\Activation;
use App\Models\User;
use App\Notifications\VerifyEmail;
use App\Notifications\GeneratePassword;
use Carbon\Carbon;
use Illuminate\Support\Str;

class EmailActivation
{
    /**
     * Creates a token and send email.
     *
     * @param  \App\Models\User  $user
     * @return bool or void
     */
    public function createTokenAndSendEmail(User $user)
    {
        // Create new Activation record for this user
        $activation = new Activation();
        $activation->user_id = $user->id;
        $activation->token = Str::random(64);
        $activation->save();

        // Send activation email notification
        $user->notify(new VerifyEmail($activation->token));
    }

    /**
     * Creates a token and send email.
     *
     * @param  \App\Models\User  $user
     * @return bool or void
     */
    public function createNewPassword(User $user)
    {
        // Create new Activation record for this user
        $activation = new Activation();
        $activation->user_id = $user->id;
        $activation->token = Str::random(64);
        $activation->save();

        // Send activation email notification
        $user->notify(new GeneratePassword($activation->token));
    }

    /**
     * Method to removed expired activations.
     *
     * @return void
     */
    public function deleteExpiredActivations()
    {
        Activation::where('created_at', '<=', Carbon::now()->subHours(72))->delete();
    }
}
