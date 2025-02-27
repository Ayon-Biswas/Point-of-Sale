<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Mail\OTPMail;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    //
    function UserRegistration(Request $request){

        try {

          User::create([
        'firstName' => $request->input('firstName'),
        'lastName'  => $request->input('lastName'),
        'email'  => $request->input('email'),
        'mobile'  => $request->input('mobile'),
        'password'  => $request->input('password'),
    ]);

          return response()->json([
            'status' => 'success',
            'message' => 'User Registration Successful'],200);
        }
        catch(Exception $e){
            return response()->json([
                'status' => 'failed',
                'message' => 'User Registration Failed'],200);
        }
    }

    function UserLogin(Request $request){
       $count = User::where('email','=',$request->input('email'))
             ->where('password','=',$request->input('password'))
             ->count();

       if($count==1){
           //user login-> JWT Token Issued
           $token =JWTToken::CreateToken($request->input('email'));
           return response()->json([
               'status' => 'success',
               'message' => 'User Login Successful',
               'token'=> $token],200);
       }
       else{
           return response()->json([
               'status' => 'failed',
               'message' => 'Unauthorized User'],200);
       }
    }

    function SendOTPCode(Request $request){
        $email=$request->input('email');
        $otp=rand(1000, 9999); //4 digit otp code.

        $count=User::where('email','=',$email)->count();

        if($count==1){
            //Send OTP to user email
            Mail::to($email)->send(new OTPMail($otp)); //Laravel has Mail property/class. From the class call methods "to" & "send"
            //Insert OTP code and update users migration table.
            User::where('email','=',$email)->update(['otp'=>$otp]); //otp column is updated.

            return response()->json([
                'status' => 'success',
                'message' => '4 digit OTP code has been sent to your email!'],200);

        }
        else{
            return response()->json([
                'status' => 'failed',
                'message' => 'Unauthorized User'],200);
        }
    }

    function VerifyOTP(Request $request){
        $email=$request->input('email');
        $otp=$request->input('otp');
        $count=User::where('email','=',$email)
                    ->where('otp','=',$otp)->count();
        if($count==1){
            //database OTP update
            User::where('email','=',$email)->update(['otp'=>'0']);
            //Password Reset Token Issue
            $token =JWTToken::CreateTokenForSetPassword($request->input('email'));
            return response()->json([
                'status'=>'success',
                'message'=>'OTP Verification Successful',
                'token'=>$token
            ],200);
        }
        else{
            return response()->json([
                'status'=>'failed',
                'message'=>'unauthorized'
            ],200);
        }
    }
}
