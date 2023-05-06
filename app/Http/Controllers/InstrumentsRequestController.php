<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstrumentsReq;
use App\Models\InstrumentsRequest;
use Illuminate\Http\Request;

class InstrumentsRequestController extends Controller
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

        if($user->role_id == 1 || $user->role_id == 2){
            $requests = InstrumentsRequest::all();
        }
        else if($user->role_id == 3){
            $requests = $user->storeRequests()->get();
        }
        else if($user->role_id == 4){
            $requests = InstrumentsRequest::where("requester", $user->id)->get();
        }
        else if($user->role_id == 5){
            $requests = InstrumentsRequest::where("allocatee", $user->id)->get();
        }

        return $this->response->__invoke(
            true, "Instruments requests were retrieved.", $requests, 200
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InstrumentsReq $request)
    {
        $deadline = now()->addDays($request->days);

        $instrumentReq = InstrumentsRequest::create([
            "requester"     => $request->user()->id,
            "instrument_id" => $request->instrument_id,
            "quantity"      => $request->quantity,
            "allocatee"     => $request->allocatee,
            "status_id"     => $request->status_id,
            "days"          => $request->days,
            "deadline"      => $deadline
        ]);

        return $this->response->__invoke(
            true, "Instruments request has been placed.", $instrumentReq, 201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(InstrumentsRequest $instrumentsRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InstrumentsRequest $instrumentsRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InstrumentsRequest $instrumentsRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InstrumentsRequest $instrumentsRequest)
    {
        //
    }
}
