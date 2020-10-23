<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use PhpParser\Node\Expr\FuncCall;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    const USUARIO_VERIFICADO = '1';
    const USUARIO_NO_VERIFICADO = '0';

    const USUARIO_REGULAR = 'false';
    const USUARIO_ADMINSTRADOR = 'true';

    protected $table = 'users';

    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'email', 
        'password',
        'verified',
        'verification_token',
        'is_admin'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 
        'remember_token',
        'verification_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setNameAttribute($name)
    {
        $this->attributes['name'] = strtolower($name); 
    }

    public function getNameAttribute($value)
    {
        return ucwords($value);
    }

    public function setEmailAttribute($email)
    {
        $this->attributes['email'] = strtolower($email); 
    }

    public function esVerificado()
    {
        return $this->verified == $this::USUARIO_VERIFICADO;
    }

    public function esAdministrador()
    {
        return $this->is_admin == $this::USUARIO_ADMINSTRADOR;
    }

    public static function generateVerificationToken()
    {
        return Str::random(40);
    }
}
