<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    //
    public function registeration(){
        return view('front.account.registeration');
    }

    public function processRegisteration(Request $request){
        
        $validator = Validator::make($request->all(),[
            "name" => "required",
            "email" => "required|email|unique:users,email",
            "password" => "required|min:5|same:confirm_password",
            "confirm_password" => "required|min:5"
        ]);

        if($validator->passes()){

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->name = $request->name;
            $user->save();
            session()->flash('success', 'You have register succesfully.');
            return response()->json([
                "status" => true,
                "errors" => []
            ]);

        }else {
            return response()->json([
                "status" => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function login(){
        return view("front.account.login");
    }

    public function processLogin(Request $request){
        $validator = Validator::make($request->all(),[
    
            "email" => "required|email",
            "password" => "required"
        ]);

        if($validator->passes()){

            $authenticated = Auth::attempt([
                "email" => $request->email,
                "password" => $request->password
            ]);

            if($authenticated){
                return response()->json([
                    "status" => true,
                    "errors" => []
                ]);

            }else {
                return response()->json([
                    "status" => false,
                    "errors" => [
                        "password" => ["Invalid email or password"]
                    ]
                ]);
            }

        }else {
            return response()->json([
                "status" => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function profile(){
        return  view("front.account.profile");
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('account.login');
    }
}
