<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResponseController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke($success = true, $message = "Success", $data = null, $statusCode = 200)
    {
        return response()->json([
            "success" => $success, "message" => $message, "data" => $data,
        ], $statusCode);
    }
}
