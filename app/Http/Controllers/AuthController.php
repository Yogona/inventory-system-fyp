<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\CssSelector\Node\FunctionNode;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private $response;

    public function __construct()
    {
        $this->response = new ResponseController();
    }

    private function handleSession(Request $request)
    {
        $request->session()->regenerate();
        $user = $request->user();

        return $this->response->__invoke(true, "Signed in!", $user, 202);
    }

    public function login(Request $request)
    {
        $username = $request->username;
        $password = $request->password;

        if (Auth::attempt(["username" => $username, "password" => $password,])) {
            return $this->handleSession($request);
        } else if (Auth::attempt(["email" => $username, "password" => $password,])) {
            return $this->handleSession($request);
        }

        return $this->response->__invoke(false, "Incorrect user id or password", null, 401);
    }

    public function logout(Request $request){
        $request->session()->flush();

        return $this->response->__invoke(
            true, "You logged out successfully.", null, 200
        );
    }

    public function changePassword(Request $request){
        $validated = Validator::make($request->all(), [
            "oldPassword" => "required",
            "newPassword" => "required",
            "confirmPassword" => "required|same:newPassword"
        ]);

        $user = $request->user();

        if(!Hash::check($validated["oldPassword"], $user->password)){
            return $this->response->__invoke(
                true,
                "Incorrect password.",
                [], 403
            );
        }

        // $user->password = 

        return $this->response->__invoke(
            true, "Password was changed.", []
        );
    }
}
