<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Firebase\JWT\JWT;

class Application extends Model
{
    /**
     * Users who authorised this application.
     *
     * @return $this
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('authorisation_code');
    }

    /**
     * Generate a JSON Web Token for the application.
     *
     * @return string
     */
    public function generateAuthToken()
    {
        $jwt = JWT::encode([
            'iss' => 'valhalla',
            'sub' => $this->key,
            'iat' => time(),
            'exp' => time() + (5 * 60 * 60),
        ], 'w5yuCV2mQDVTGmn3');

        return $jwt;
    }
}
