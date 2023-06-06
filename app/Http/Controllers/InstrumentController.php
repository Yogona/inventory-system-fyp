<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInstrumentReq;
use App\Http\Requests\UpdateInstrumentReq;
use App\Models\Instrument;
use App\Models\User;
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
    public function index(Request $request, $storeId, $records)
    {
        $instruments = Instrument::where("store_id", $storeId)->orderBy("name", "ASC")
        ->paginate($records);

        $instrumentsNum = $instruments->count();

        if($instrumentsNum == 0){
            return $this->response->__invoke(
                false,
                "No instruments were found, please add one.",
                null,
                404
            );
        }

        foreach($instruments as $instrument){
            $instrument->added_by = User::find($instrument->added_by);
        }

        return $this->response->__invoke(
            true,
            "Instrument" . ($instrumentsNum == 1 ? " was" : "s were") . " retrieved successfully.", $instruments, 200
        );
    }

    /**
     * Display a listing of the resource.
     */
    public function searchIndex($storeId, $query, $records)
    {
        $instruments = Instrument::where("store_id", $storeId)
        ->where(function($clause) use ($query){
            $clause->where("name", "LIKE", "%$query%")->orWhere("code", "LIKE", "%$query%");
        })->orderBy("name", "ASC")->paginate($records);

        $instrumentsNum = $instruments->count();

        if (!$instrumentsNum) {
            return $this->response->__invoke(
                false,
                "No instruments were found by that search query, improve your search.",
                null,
                404
            );
        }

        return $this->response->__invoke(
            true,
            "Instrument" . ($instrumentsNum == 1 ? " was" : "s were") . " retrieved successfully.",
            $instruments,
            200
        );
    }

    /**
     * Display a listing of the resource.
     */
    public function list(Request $request, $storeId)
    {
        $instruments = Instrument::where("store_id", $storeId)->orderBy("name", "ASC")->get();

        $instrumentsNum = $instruments->count();

        if($instrumentsNum == 0){
            return $this->response->__invoke(
                false, "No instruments were found, please add one.", null, 404
            );
        }

        return $this->response->__invoke(
            true,
            "Instrument".($instrumentsNum == 1?" was":"s were")." retrieved successfully.",
            $instruments,
            200
        );
    }

    /**
     * Display a listing of the resource.
     */
    public function searchList($storeId, $query)
    {
        $instruments = Instrument::where("store_id", $storeId)
        ->where(function($clause)use($query){
            $clause->where("name", "LIKE", "%$query%")->orWhere("code", "LIKE", "%$query%");
        })->orderBy("name", "ASC")->get();

        $instrumentsNum = $instruments->count();

        if ($instrumentsNum == 0) {
            return $this->response->__invoke(
                false,
                "No instruments were found, please add one.",
                null,
                404
            );
        }

        return $this->response->__invoke(
            true,
            "Instrument" . ($instrumentsNum == 1 ? " was" : "s were") . " retrieved successfully.",
            $instruments,
            200
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateInstrumentReq $request)
    {
        $instrument = Instrument::create([
            'name' => $request->name,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'code' => $request->code,
            'added_by' => $request->user()->id,
            'store_id' => $request->store_id
        ]);

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
    public function update(UpdateInstrumentReq $request, $instrumentId)
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
        if($request->user()->cannot("create-instrument", $instrumentId)){
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
