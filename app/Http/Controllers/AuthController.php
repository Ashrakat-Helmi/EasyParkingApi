<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Hamcrest\Type\IsNumeric;

class AuthController extends Controller
{
    public function register(Request $request){
        $fields = $request->validate([
            "name"=>"required|string",
            "email"=>"required|email|string|unique:users,email",
            "password"=>"required|string|confirmed"
        ]);
        $role ='';
        if($request->role){
            $role=$request->role;
        }else{
            $role = 'U';
        }

        $user =User::create([
            'name'=>$fields['name'],
            'email'=>$fields['email'],
            'role'=> $role,
            'password'=>bcrypt($fields['password'])
        ]);
        $token = $user->createToken("myToken")->plainTextToken;

        $response = [
            'user'=>$user,
            'myToken'=>$token
        ];

        return Response()->json(['status'=>'success','data'=>$response], 200 );
    }

    public function login(Request $request){

        $fields = $request->validate([
            "email"=>"required|email|string",
            "password"=>"required|string"
        ]);

        $user= User::where('email',$fields['email'])->first();
        if(!$user || ! Hash::check($fields['password'],$user->password)) {
        abort (403,'Invalid Credentials');
        }else {
            $token=$user->createToken("myToken")->plainTextToken;
        }
        $response=[
            'user' =>$user ,
            'myToken'=>$token
            ] ;
        return Response()->json(['status'=>'success','data'=>$response], 200 );
                
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        
        return [
            'status'=>'success',
            "message"=>"logged out"
        ];
    }

    public function sendVerifyMail(Request $request){
        $email = $request->query('email');
        $user = User::where('email',$email)->first();
        if($user){
            $user->update(['email_verified_at'=>null]);
            $user->sendEmailVerificationNotification();
            return [    
                'status'=>'success', 
                'message'=>'Email verification link sent to your email'
            ];
        }else{
            return [
                'status'=>'error',
                'message'=>'User not found'
            ];
        }
    }

}
