<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\User;

class PassportController extends Controller
{
    public $successStatus = 200;

    public function register(Request $request)
    {
        $validatator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password'
        ]);

        if ($validatator->fails()) {
            return response()->json(['error' => $validatator->errors()], 401);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyApp')->accessToken;
        $success['name'] =  $user->name;

        return response()->json(['success'=>$success], $this->successStatus);
    }


    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        $this->guard()->logout();

        $request->session()->flush();

        $request->session()->regenerate();

        return response()->json(['success' => true], $this->successStatus);
    }


    protected function guard()
    {
        return Auth::guard('api');
    }
}
