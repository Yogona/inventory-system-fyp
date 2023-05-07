<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\CssSelector\Node\FunctionNode;
use App\Models\Role;

class RoleController extends Controller
{
    private $response;

    public function __construct()
    {
        $this->response = new ResponseController();
    }

    public function index(Request $request){
        $user = $request->user();

        if($user->cannot("view-roles")){
            return $this->response->__invoke(
                false, "Not authorized to list roles.", null, 403
            );
        }

        $roles = Role::all();

        return $this->response->__invoke(
            true, "Role(s) was/were retrieved successfully.", $roles, 200
        );
    }
}
