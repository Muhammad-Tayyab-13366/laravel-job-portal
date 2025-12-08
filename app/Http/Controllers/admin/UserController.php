<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //
    public function index(){
        $data['users'] = User::orderBy("created_at", "DESC")->paginate(10);
        return view('admin.users.list', $data);
    }

    public function editUser(Request $request, $id){
        $user = User::find($id);
        if(!$user){
            return redirect()->route('admin.users')->with('error', 'User not found.');
        }

        $data['user'] = $user;
        return view('admin.users.edit', $data);
    }
    

    public function updateUser(Request $request){  
        $user_id = $request->user_id;
        $validator = Validator::make($request->all(),[
            "name" => "required|max:20",
            "email" => 'required|email|unique:users,email,'.$user_id.',id',
            "designation" => "required|max:20",
            "mobile" => "required|max:13"
        ]);

        if($validator->passes()){

            $user = User::find($user_id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->designation = $request->designation;
            $user->mobile = $request->mobile;
            $user->save();
            session()->flash('success', 'User information updated successfully.');
            return response()->json([
                "status" => true,
                "errors" => []
            ]);

        }else {
            return response()->json([
                "status" => false,
                "errors" => $validator->errors()
            ]);
        }
    }

    public function deleteUser(Request $request){
        //
        $user_id = $request->user_id;
        $user = User::find($user_id);
        if(!$user){
            return redirect()->route('admin.users')->with('error', 'User not found.');
        }
        $user->delete();
        return session()->flash('success', 'User deleted successfully.');
    }

}
