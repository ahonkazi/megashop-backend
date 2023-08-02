<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthControllerLoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function register(Request $request){
        $validation = $request->validate([
        'name'=>'required|string|max:191',
        'email'=>'required|email|unique:users|email',
        'password'=>'required|min:8'
    ]);
        if (!$validation){
            return response()->json([
        'validationError'=>$validation->message(),
    ]);
        }else{
            $user = User::create([
       'name'=>$request->name,
       'email'=>$request->email,
        'password'=>Hash::make($request->password),
    ]);
          $token = $user->createToken($user->email.'_Token')->plainTextToken;
          
          return response()->json([
        'status'=>200,
        'name'=>$user->name,
        'email'=>$user->email,
        'token'=>$token,
        'message'=>"Registration Successfull"
    ]);
        }


    }
    
    public function login(Request $request){
        $validation = $request->validate([
        'email'=>'required|email',
        'password'=>'required|min:8'
           ]);
        if (!$validation){
            return response()->json([
                'validationError'=>$validation->message(),
            ]);
        }else{
            $user = User::where('email',$request->email)->first();
             if(!$user){
                return response(['status'=>401,'message'=>'No user found with this email']);
             }else if(!Hash::check($request->password,$user->password)){
                 return response(['status'=>401,'message'=>'Check your password again']);
             }else{
                 $token = $user->createToken($user->email.'_Token')->plainTextToken;

        return response()->json([
            'status'=>200,
        'name'=>$user->name,
        'email'=>$user->email,
        'token'=>$token,
        'message'=>"Logged in Successfull"
    ]);
             }

        
        }
        
        
            
    }
    
    public  function logout(){
        auth()->user()->tokens()->delete();
        return response([
        'status'=>200,
        'message'=>"Logout Successfull"
    ]);
    }
    
    public function checkAuth(){
    return response([
        'status'=>200,
        'message'=>'loggedIn'
    ]);
    }
}
