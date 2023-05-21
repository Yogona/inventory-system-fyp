<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRequestsReq;
use App\Models\InstrumentsRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Assignment;
use App\Models\Instrument;
use App\Models\User;
use App\Models\Status;
use App\Models\Store;

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
    public function index(Request $request, $records)
    {
        $user = $request->user();

        if($user->role_id == 1 || $user->role_id == 2){
            $requests = InstrumentsRequest::orderBy("created_at", "DESC")->paginate($records);
        }
        else if($user->role_id == 3){
            $requests = $user->storeRequests()->orderBy("created_at", "DESC")
            ->paginate($records);
        }
        else if($user->role_id == 4){
            $requests = InstrumentsRequest::where("requester", $user->id)
            ->orderBy("created_at", "DESC")->paginate($records);
        }
        else if($user->role_id == 5){
            $requests = InstrumentsRequest::where("allocatee", $user->id)
            ->orderBy("created_at", "DESC")->paginate($records);
        }

        $requestsNum = $requests->count();
        if($requestsNum == 0){
            return $this->response->__invoke(
                false,
                "No instruments requests.",
                null,
                404
            );
        }

        foreach($requests as $req){
            $lecturer = User::find($req->requester);
            $req->requester = $lecturer;

            $allocatee = User::find($req->allocatee);
            $req->allocatee = $allocatee;

            $instrument = Instrument::find($req->instrument_id);
            $req->instrument_id = $instrument;

            $status = Status::find($req->status_id);
            $req->status_id = $status;

            $assignment = Assignment::find($req->assignment_id);
        }

        return $this->response->__invoke(
            true, 
            "Instruments request".($requestsNum > 1)?"s were":" was"."retrieved successfully.", 
            $requests, 200
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRequestsReq $request)
    {
        $deadline = now()->addDays($request->days);
        $assignmentFile = $request->file("attachment");
        $path = $assignmentFile->store("assignments");
        $ext = $assignmentFile->getClientOriginalExtension();

        if($path == false){
            return $this->response->__invoke(
                false, "Failed to upload file, try again.", null, 500
            );
        }

        $assignment = Assignment::create([
            "title"     => $request->title,
            "file_path" => $path,
            "creator"   => $request->user()->id,
            "assignee"  => $request->allocatee,
            "store_id"  => $request->store_id
        ]);

        $index = 0;
        foreach (json_decode($request->instruments) as $instrument) {
            $instrumentId = $instrument->instrument_id;
            $quantity = $instrument->quantity;

            $data = [
                "instrument_id" => $instrumentId,
                "quantity"      => $quantity
            ];

            ++$index;
            $validation = Validator::make($data, [
                "instrument_id" => "required|gt:0",
                "quantity"      => "required|gt:0|integer"
            ]);

            if ($validation->fails()) {
            } else {
                $request->user()->myRequests()->create([
                    "instrument_id" => $instrumentId,
                    "quantity"      => $quantity,
                    "allocatee"     => $request->allocatee,
                    "store_id"      => $request->store_id,
                    "days"          => $request->days,
                    "deadline"      => $deadline,
                    "assignment_id" => $assignment->id
                ]);
            }
        }

        return $this->response->__invoke(
            true, "Instruments request has been placed.", null, 201
        );
    }

    /**
     * Display the specified resource.
     */
    public function allocate($requestId)
    {
        $req = InstrumentsRequest::find($requestId);
        $instrument = $req->instrument()->first();

        if($req->quantity <= $instrument->quantity){
            try{
                DB::beginTransaction();
                $req->status_id = 2;
                $req->save();
    
                $instrument->quantity -= $req->quantity;
                $instrument->save();
                
                DB::commit();
            }catch(QueryException $exc){
                DB::rollBack();
            }

        }else{
            return $this->response->__invoke(
                false, "There are only $instrument->quantity instruments in the store.", null, 400
            );
        }
        

        return $this->response->__invoke(
            true, "Allocated successfully.", null, 200
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function deallocate($requestId)
    {
        $req = InstrumentsRequest::find($requestId);
        $instrument = $req->instrument()->first();

        try {
            DB::beginTransaction();
            $req->status_id = 3;
            $req->save();

            $instrument->quantity += $req->quantity;
            $instrument->save();

            DB::commit();
        } catch (QueryException $exc) {
            DB::rollBack();
        }

        return $this->response->__invoke(
            true,
            "Deallocated successfully.",
            null,
            200
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $requestId)
    {
        $req = InstrumentsRequest::find($requestId);
        $days = $request->days;

        $deadline = $req->created_at->addDays($days);

        $req->update([
            "allocatee"     => $request->allocatee,
            "instrument_id" => $request->instrument_id,
            "quantity"      => $request->quantity,
            "days"          => $days,
            "deadline"      => $deadline
        ]);

        return $this->response->__invoke(
            true, "Request was updated successfully.", $req, 200
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($requestId)
    {
        $request = InstrumentsRequest::find($requestId);
 
        $request->delete();
    }

    public function getAssignments(Request $request, $storeId, $records){
        $user = $request->user();
        $reqs = Assignment::where("store_id", $storeId);

        if($user->role_id == 1 || $user->role_id == 2 || $user->role_id == 3){
            $reqs = $reqs->paginate($records);
        } else if($user->role_id == 4){
            $reqs = $reqs->where("creator", $user->id)->paginate($records);
        } else if($user->role_id == 5){
            $reqs = $reqs->where("assignee", $user->id)->paginate($records);
        }

        $reqsNum = $reqs->count();
        if($reqsNum == 0){
            return $this->response->__invoke(
                false, "No assignments present.", null, 404
            );
        }

        foreach($reqs as $req){
            $creator = User::find($req->creator);
            $req->creator = $creator;

            $assignee = User::find($req->assignee);
            $req->assignee = $assignee;
        }

        return $this->response->__invoke(
            true, "Assignment".($reqsNum > 1?"s were":" was")." retrieved.", $reqs, 200
        );
    }

    public function downloadAttachment(Request $request, $fileName){
        $filePath = "assignments/$fileName";
        $assignment = Assignment::where("file_path", $filePath)->first();
        $filePath = storage_path("app/$filePath");

        $ext = explode(".", $filePath)[1];

        return response()->download(
            $filePath, 
            "$assignment->title.$ext", 
            ["Content-Type"=>"application/$ext"], 
        );
    }
}
