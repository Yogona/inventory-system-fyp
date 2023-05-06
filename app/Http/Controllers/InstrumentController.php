<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInstrumentReq;
use App\Models\Instrument;
use Illuminate\Http\Request;

class InstrumentController extends Controller
{
    private $response;

    public function __construct()
    {
        $this->response = new ResponseController();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $instruments = Instrument::all();

        return $this->response->__invoke(
            true, "Instruments were retrieved.", $instruments, 200
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateInstrumentReq $request)
    {
        $instrument = Instrument::create($request->all());

        return $this->response->__invoke(
            true, "Instrument was added to the store successfully.", $instrument, 201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Instrument $instrument)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateInstrumentReq $request, $instrumentId)
    {
        $instrument = Instrument::find($instrumentId);

        if(!$instrument){
            return $this->response->__invoke(
                false, "Instrument is not found.", null, 404
            );
        }

        $instrument->update($request->all());

        return $this->response->__invoke(
            true, "Instrument was updated successfully.", $instrument, 200
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $instrumentId)
    {
        if($request->user()->cannot("create-instrument")){
            return $this->response->__invoke(
                false, "Not authorized to delete instruments.", null, 403
            );
        }

        $instrument = Instrument::find($instrumentId);

        if(!$instrument){
            return $this->response->__invoke(
                false, "Intrument is not found.", null, 404
            );
        }

        $instrument->delete();

        return $this->response->__invoke(
            true, "Instrument was deleted successfully.", null, 200
        );
    }
}
