<?php

namespace App\Http\Controllers;

use App\Models\ExtensionRequest;
use App\Models\User;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Requests\ExtensionReq;
use App\Http\Controllers\ResponseController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\Extend;

class ExtensionRequestController extends Controller
{
    private $response;

    public function __construct()
    {
        $this->response = new ResponseController();
    }

    public function index(Request $request, $storeId, $records)
    {
        $extensions = ExtensionRequest::where("store_id", $storeId);
        $user = $request->user();

        if ($user->role_id == 1 || $user->role_id == 2 || $user->role_id == 3) {
            $extensions = $extensions->orderBy("created_at", "DESC")->paginate($records);
        } else if ($user->role_id == 4) {
            $extensions = $extensions->where("requester", $user->id)->orderBy("created_at", "DESC")
           ->paginate($records);
        } else {
            $extensions = array();
        }

        $extNum = $extensions->count();
        if ($extNum == 0) {
            return $this->response->__invoke(
                false,
                "No extensions were found in this store.",
                null,
                404
            );
        }

        foreach ($extensions as $extension) {
            $assignment = $extension->assignment()->first();
            $extension->assignment = $assignment;

            $extension->requester = $extension->requester()->first();
        }

        return $this->response->__invoke(
            true,
            "Extension request" . ($extNum > 1) ? "s were" : " was" . " retrieved successfully.",
            $extensions,
            200
        );
    }

    public function requestExtension(ExtensionReq $request)
    {
        // $requests = $request->assignment->instrumentsRequest()->first();

        $extension = ExtensionRequest::create([
            "assignment"    => $request->assignment->id,
            "store_id"      => $request->store_id,
            "requester"     => $request->user()->id,
            "extra_days"    => $request->days
        ]);

        return $this->response->__invoke(
            true,
            "Extension request was placed.",
            $extension,
            201
        );
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
    public function show(ExtensionRequest $extensionRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExtensionRequest $extensionRequest)
    {
        //
    }

    public function approve(Request $request, $extId)
    {
        $user = $request->user();

        if ($user->role_id == 4 || $user->role_id == 5) {
            return $this->response->__invoke(
                false,
                "Not authorized to approve extra days extensions for instruments.",
                null,
                403
            );
        }

        $ext = ExtensionRequest::find($extId);

        if(!$ext){
            return $this->response->__invoke(
                false,
                "Extension request not found.",
                null,
                404
            );
        }
        else if($ext->approved){
            return $this->response->__invoke(
                false,
                "Can't re approve this request.",
                null,
                403
            );
        }

        $extraDays = $ext->extra_days;

        $assignment = $ext->assignment()->first();

        foreach ($assignment->instrumentsRequests()->get() as $req) {
            $req->days += $extraDays;
            $deadLine = Carbon::parse($req->deadline);
            $deadLine->addDays($extraDays);
            $req->deadline = $deadLine;
            $req->save();
        }

        // $ext->delete();
        $ext->update([
            "approved" => true
        ]);

        $allocatee  = User::find($assignment->assignee);
        $store      = Store::find($assignment->store_id);

        if($allocatee){
            Mail::to($allocatee->email)->queue(new Extend($allocatee, $store));
        }

        return $this->response->__invoke(
            true,
            "Extra days were granted.",
            null,
            200
        );
    }

    public function destroy(Request $request, $extId)
    {
        $user = $request->user();

        if ($user->role_id == 5) {
            return $this->response->__invoke(
                false,
                "Not authorized to delete requests.",
                null,
                403
            );
        }

        $ext = ExtensionRequest::find($extId);
        $ext->delete();

        return $this->response->__invoke(
            true,
            "Instruments days extension request was deleted successfully.",
            null,
            200
        );
    }
}
