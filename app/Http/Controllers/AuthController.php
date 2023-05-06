<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function login(Request $request){
        $userIdentity = $request->user_id;
        $password = $request->password;

        if (Auth::attempt(["user_id" => $userIdentity, "password" => $password,])) {
            return $this->handleSession($request);
        } else if (Auth::attempt(["email" => $userIdentity, "password" => $password,])) {
            return $this->handleSession($request);
        }

        return $this->response->__invoke(false, "Incorrect user id or password", null, 401);
    }
}
