<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDepartmentReq;
use App\Http\Requests\UpdateDepartmentReq;
use App\Models\Department;
use Illuminate\Http\Request;
use Symfony\Component\CssSelector\Node\FunctionNode;

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

        $departsNum = $departments->count();

        if ($departsNum == 0) {
            return $this->response->__invoke(
                false,
                "No departments were found, please add one.",
                null,
                404
            );
        };

        return $this->response->__invoke(
            true, "Departments were retrieved", $departments, 200
        );
    }

    public function searchDepartments(Request $request, $query){
        if($query == null){
            $departments = Department::all();
        }else{
            $departments = Department::where("name", "LIKE", "%$query%")
            ->orWhere("abbr", "LIKE", "%$query%")->get();
        }
        

        $departsNum = $departments->count();

        if($departsNum == 0){
            return $this->response->__invoke(
                false, "No departments were found, improve your search.", null, 404
            );
        };

        return $this->response->__invoke(
            true, "Department".(($departsNum > 1)?"s were":" was")." found.", $departments, 200
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
    public function update(UpdateDepartmentReq $request, $departId)
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
