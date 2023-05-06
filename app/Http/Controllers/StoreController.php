<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStoreReq;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    private $response;

    public function __construct()
    {
        $this->response = new ResponseController();
    }

    /**
     * Display a listing of the resource.
     */
    public function index($records)
    {
        $stores = Store::paginate($records);

        $storesNum = $stores->count();
        if($storesNum == 0){
            return $this->response->__invoke(
                false, "No stores were found.", null, 404
            );
        }

        return $this->response->__invoke(
            true, "Store".(($storesNum > 1)?"s were":" was")." retrieved.", $stores, 200
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateStoreReq $request)
    {
        $store = Store::create($request->all());

        return $this->response->__invoke(
            true, "Store was created successfully.", $store, 201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateStoreReq $request, $storeId)
    {
        $store = Store::find($storeId);

        if(!$store){
            return $this->response->__invoke(
                false, "Store is not found.", null, 404
            );
        }

        $store->update($request->all());

        return $this->response->__invoke(
            true, "Store was updated successfully.", $store, 200
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store)
    {
        //
    }
}
