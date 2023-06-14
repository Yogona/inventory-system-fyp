<?php

namespace App\Http\Controllers;

use App\Models\ImparedInstrument;
use App\Models\Instrument;
use App\Models\User;
use App\Models\Store;
use Illuminate\Http\Request;

class ImparedInstrumentController extends Controller
{
    private $res;

    public function __construct()
    {
        $this->res = new ResponseController();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $storeId, $records)
    {
        $user = $request->user();

        if($user->role_id == 1 || $user->role_id == 2 || $user->role_id == 3){
            $imparedInstruments = ImparedInstrument::where("store", $storeId)->paginate($records);
        } else if($user->role_id == 4){
            $imparedInstruments = ImparedInstrument::where("requester", $user->id)->paginate($records);
        } else {
            $imparedInstruments = ImparedInstrument::where("allocatee", $user->id)->paginate($records);
        }

        $imparedInstrumentsNum = $imparedInstruments->count();
        if($imparedInstrumentsNum == 0) {
            return $this->res->__invoke(
                false,
                "No impared instruments were found, please mark one.",
                null,
                404
            );
        }

        foreach($imparedInstruments as $imparedInstrument) {
            $imparedInstrument->instrument_id       = Instrument::find($imparedInstrument->instrument_id);
            $imparedInstrument->responsible_user    = User::find($imparedInstrument->responsible_user);
            $imparedInstrument->store               = Store::find($imparedInstrument->store_id);
        }

        return $this->res->__invoke(
            true,
            "Impared instrument" . ($imparedInstrumentsNum == 1 ? " was" : "s were") . " retrieved successfully.",
            $imparedInstruments,
            200
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ImparedInstrument $imparedInstrument)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ImparedInstrument $imparedInstrument)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ImparedInstrument $imparedInstrument)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ImparedInstrument $imparedInstrument)
    {
        //
    }
}
