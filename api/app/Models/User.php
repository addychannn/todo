<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens as SanctumApiTokens;
use Laravel\Passport\HasApiTokens as PassportApiTokens;
use Veelasky\LaravelHashId\Eloquent\HashableId;

use App\Notifications\sendEmailVerification;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\PasswordChangedNotification; 
use App\Notifications\AdminPasswordChangedNotification; 
use App\Notifications\AccountChangedNotification; 
use App\Notifications\AdminAccountChangedNotification;

// Switch between Sanctum or Passport Api Token
if((bool)env('APP_AUTH_TOKEN', false)){
    class UserExtension extends Authenticatable
    {
        use PassportApiTokens;
        protected $guard_name = "api";
    }
}else{
    class UserExtension extends Authenticatable
    {
        use SanctumApiTokens;
        protected $guard_name = "sanctum";
    }
}

class User extends UserExtension
{
    use HasFactory, Notifiable, HasRoles, HashableId;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'fails',
        'disabled_at',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'disabled_at' => 'datetime',
    ];

    public function profile(){
        return $this->hasOne(Profile::class);
    }

    public function isSuperAdmin(){
        return $this->hasRole('Admin');
    }

    public function statusHistory(){
        return $this->hasMany(AccountStatusHistory::class);
    }

    public function logs() {
        return $this->hasMany(Logs::class, 'user_id');
    }

    public function sessions() {
        return $this->hasMany(SessionModel::class);
    }

    public function clearSessions() {
        $this->sessions()->update(["user_id" => null, "payload" => ""]);
    }
    
    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerification(){
        $this->notify(new sendEmailVerification);
    }

    public function sendPasswordResetNotification($token){
        $this->notify(new ResetPasswordNotification($token));
    }

    public function sendPasswordChangedNotification(){
        $this->notify(new PasswordChangedNotification);
    }

    public function sendAdminPasswordChangedNotification($newPassword){
        $this->notify(new AdminPasswordChangedNotification($newPassword));
    }

    public function sendAdminAccountChangedNotification(){
        $this->notify(new AdminAccountChangedNotification);
    }

    public function sendAccountChangedNotification(){
        $this->notify(new AccountChangedNotification);
    }
}
