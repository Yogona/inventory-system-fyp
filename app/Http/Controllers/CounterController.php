<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Instrument;
use App\Models\User;
use App\Models\Store;
use Illuminate\Http\Request;

class CounterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $departments = Department::all();
        $users = User::all();
        $stores = Store::all();
        $instruments = Instrument::all();

        return response()->json([
            "success"   => true,
            "message"   => "Statistics was retrieved.",
            "data"      => [
                "users"         => $users->count(),
                "stores"        => $stores->count(),
                "departs"       => $departments->count(),
                "instruments"   => $instruments->count()
            ]
        ]);
    }
}
