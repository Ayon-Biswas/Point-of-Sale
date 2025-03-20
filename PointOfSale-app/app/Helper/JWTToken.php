<?php

namespace App\Helper;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTToken
{

    public static function CreateToken($userEmail):string{

        $key = env('JWT_KEY');
        $payload = [
            'iss' =>'laravel_token',
            'iat'=>time(), //token creation time
            'exp'=>time()+60*60, //3600s = 1 hour. after an hour token expire
            'userEmail'=>$userEmail //to understand for whom the token is issued.when token is decoded we'll get the complete payload
        ];

        return  JWT::encode($payload,$key,'HS256'); //payload, key and encryption algorithm
    }

    public static function CreateTokenForSetPassword($userEmail):string{

        $key = env('JWT_KEY');
        $payload = [
            'iss' =>'laravel_token',
            'iat'=>time(), //token creation time
            'exp'=>time()+60*20, //1200s = 20 Minutes. after 20 minutes token expire
            'userEmail'=>$userEmail //to understand for whom the token is issued.when token is decoded we'll get the complete payload
        ];

        return  JWT::encode($payload,$key,'HS256'); //payload, key and encryption algorithm
    }

    public static function VerifyToken($token):string{
        try{ //if token is tempered or expired then measures are needed. try block will handle token decode and catch will handle errors
            $key = env('JWT_KEY');
            $decode = JWT::decode($token,new Key($key,'HS256')); //$token is the token we want to decode. Key is the new JWT object, first parameter is key and second is the encryption algorithm.
            return $decode->userEmail;
        }
        catch(Exception $e){
            return "Unauthorized User";
        }

    }

}
