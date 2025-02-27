<?php

namespace App\Http\Middleware;

use App\Helper\JWTToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenVerificationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token=$request->header('token');
         $result = JWTToken::VerifyToken($token);

         if($result=='Unauthorized User'){
             return response()->json([
                 'status'=>'failed',
                 'message'=>'unauthorized'
             ],401);
         }
         else{
             $request->headers->set('email',$result);  //User email is kept within token.
             return $next($request);  //when token is verified the user email is transfered to next request stage using request header. So when we send email to next request stage (controller/method),
                                     // the email is used from header, as it's the identity of the user.
         }

    }
}
