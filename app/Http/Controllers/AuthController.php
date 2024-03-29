<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\LoginResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ApiResponse;
use App\Models\User;

class AuthController extends Controller
{
    use ApiResponse ;

    public function __construct()
    {
        $this->user  = new User();
    }

    public function login(LoginRequest $request)
    {
        $input = $request->all();
        $user = $this->user->where('email', $input['email'])->first();
        if($user){
            if (Auth::attempt($input))
            {
                $token = $user->createToken('token')->plainTextToken;
                return self::ResponseSuccess(data: ["user" => new LoginResource($user) , "token" => $token] ,
                                             message: "Login Successfully" ,
                                             statusCode: 202);
            }else {
                return self::ResponseFail(message: "incorrect email or password" , statusCode: 401);
            }
        }else{
            return self::ResponseFail(message: "No Account found with this data" , statusCode: 401);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->tokens()->delete();
        return self::ResponseSuccess(message: "You have been successfully logged out!", statusCode: 200);
    }

    public function changePassword(Request $request)
    {
        $input = $request->all();
        $userId = Auth::guard('api')->user()->id;
        $rules = array(
            'password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        );

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return self::ResponseFail(message: $validator->errors()->first());
        } else {
            try {
                if ((Hash::check(request('password'), Auth::user()->password)) == false) {
                    return self::ResponseFail(message: "Check your old password.");
                } else if ((Hash::check(request('new_password'), Auth::user()->password)) == true) {
                    return self::ResponseFail(message: "new password is similar to old one.");
                } else {
                    $this->user->find($userId)->update(['password' => Hash::make($input['new_password'])]);
                    return self::ResponseSuccess(message: "Password updated successfully.");
                }
            } catch (\Exception $ex) {
                if (isset($ex->errorInfo[2])) {
                    $msg = $ex->errorInfo[2];
                } else {
                    $msg = $ex->getMessage();
                }
                return self::ResponseFail(message: $msg);
            }
        }
    }

    public function listEmployees()
    {
        $output = $this->user->role('Employee')->get();
        return self::ResponseSuccess(data : ["employees" => $output ]);
    }
}
