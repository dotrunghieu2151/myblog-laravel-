<?php

use Illuminate\Foundation\Inspiring;
use App\User;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('app:changeUserPassword {userEmail}', function ($userEmail){
   $user = User::where('email',$userEmail)->first();
    if ($user === null) {
        return $this->error("we cannot find this user. Please try again");
    }
    $newPassword = $this->ask("Please enter the new password: ");
    $confirmPassword = $this->ask("Repeat the password");
    $validator = Validator::make(["password"=>$newPassword,
                                  "password_confirmation"=>$confirmPassword],
        ["password" => "required|confirmed|min:6"]);
    if ($validator->fails()) {
        return $this->error($validator->errors()->first('password'));
    }
    $user->password = bcrypt($newPassword);
    $user->save();
    $this->info("password updated");
})->describe('Change the user password');