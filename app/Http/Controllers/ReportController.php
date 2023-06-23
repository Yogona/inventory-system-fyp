<?php

namespace App\Http\Controllers;

use App\Models\ImparedInstrument;
use Illuminate\Http\Request;
use App\Models\Instrument;
use App\Models\Store;

class ReportController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $instruments = Instrument::all();
        $imparedInstruments = ImparedInstrument::all();
        foreach($imparedInstruments as $imparedInstrument){
            $instrument = Instrument::find($imparedInstrument->id);
            $imparedInstrument->instrument_id = $instrument;

            $store = Store::find($imparedInstrument->store);
            $imparedInstrument->store = $store;
        }

        return response()->json([
            "success"   => true,
            "message"   => "Report retrieved.",
            "data"      => [
                "instrumentsNum"        => $instruments->count(),
                "instruments"           => $instruments,
                "imparedInstruments"    => $imparedInstruments
            ]
        ]);
    }
}
