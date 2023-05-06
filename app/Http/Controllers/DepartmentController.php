<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDepartmentReq;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    private $response;

    public function __construct()
    {
        $this->response = new ResponseController();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::all();

        return $this->response->__invoke(
            true, "Departments were retrieved", $departments, 200
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createDepartment(CreateDepartmentReq $request)
    {
        $department = Department::create($request->all());

        return $this->response->__invoke(
            true, "Department was created successfully.", $department, 201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateDepartmentReq $request, $departId)
    {
        $department = Department::find($departId);

        if(!$department){
            return $this->response->__invoke(
                false, "Department is not found.", null, 404
            );
        }

        $department->update($request->all());

        return $this->response->__invoke(
            true, "Department was updated successfully.", $department, 200
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $departId)
    {
        if($request->user()->cannot("create-department")){
            return $this->response->__invoke(
                false, "Not authorized to delete departments.", null, 403
            );
        }

        $department = Department::find($departId);

        if(!$department){
            return $this->response->__invoke(
                false, "Department is not found.", null, 404
            );
        }

        $department->delete();

        return $this->response->__invoke(
            true, "Department was deleted successfully.", null, 200
        );
    }
}
