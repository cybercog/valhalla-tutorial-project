<?php

namespace App;

use Firebase\JWT\JWT;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Generate a JSON Web Token for the user.
     *
     * @return string
     */
    public function generateAuthToken(Application $app)
    {
        $jwt = JWT::encode([
            'iss' => $app->key,
            'sub' => $this->email,
            'iat' => time(),
            'jti' => sha1($this->email.time()),
        ], 'w5yuCV2mQDVTGmn3');

        return $jwt;
    }
}
