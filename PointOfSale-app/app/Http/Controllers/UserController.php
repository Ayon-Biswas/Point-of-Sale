<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Mail\OTPMail;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
class UserController extends Controller
{
    function LoginPage():View{
        return view('pages.auth.login-page');
    }

    function RegistrationPage():View{
        return view('pages.auth.registration-page');
    }
    function SendOtpPage():View{
        return view('pages.auth.send-otp-page');
    }
    function VerifyOTPPage():View{
        return view('pages.auth.verify-otp-page');
    }

    function ResetPasswordPage():View{
        return view('pages.auth.reset-pass-page');
    }

    function ProfilePage():View{
        return view('pages.dashboard.profile-page');
    }


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
                'message' => 'User Registration Failed'],401);
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
                'message' => 'Unauthorized User'],401);
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
                'message' => 'Unauthorized User'],401);
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
                'message'=>'Unauthorized User'
            ],401);
        }
    }

    function ResetPassword(Request $request) { // After OTP Verification a token was sent, by verifying & decoding that token we get the user email and password will be reset.
     try { //without try-catch block error 500 will be thrown.
         $email = $request->header('email'); // Only token is received from user and verified by middleware during password reset.User Email is used internally.
         $password = $request->input('password');
         User::where('email', '=', $email)->update(['password' => $password]);
         return response()->json([
             'status' => 'success',
             'message' => 'Request Successful'],200);
     }
     catch(Exception $e){
         return response()->json([
             'status' => 'failed',
             'message' => 'Something Went Wrong'],401);
     }

    }
}
