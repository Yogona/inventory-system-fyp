<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserReq;
use App\Http\Requests\UpdateUserReq;
use App\Http\Requests\ChangeEmailReq;
use App\Http\Requests\ChangePhoneReq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Http\Packages\SimpleXLSX;
use App\Http\Requests\ChangeUsernameReq;
use App\Jobs\UploadUsers;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private $response;

    public function __construct()
    {
        $this->response = new ResponseController();
    }

    public function createUser(CreateUserReq $request){
        $phone = $request->phone;
        $createdUser = User::create([
            "first_name" => $request->first_name,
            "last_name"     => $request->last_name,
            "username"      => $request->username,
            "email"         => $request->email,
            "phone"         => $phone,
            "gender"        => $request->gender,
            "role_id"       => $request->role_id,
            "department_id" => $request->department_id,
            "password"      => Hash::make($phone)
        ]);

        return $this->response->__invoke(
            true, "User was created successfully.", $createdUser, 201
        );
    }

    private function validateUsers($rows)
    {
        $extractedRecords = array();

        $heads = $rows[0];
        //index 0 is heads
        $index = 0;
        foreach ($rows as $row) {
            //We skip because it is heads
            if ($index == 0) {
                ++$index;
                continue;
            }

            $firstName  = $row[0];
            $lastName   = $row[1];
            $username   = $row[2];
            $gender     = $row[3];
            $email      = $row[4];
            $phone      = $row[5];
            $roleId     = $row[6];
            $departId   = $row[7];

            $validation = Validator::make(
                array(
                    $heads[0] => $firstName, 
                    $heads[1] => $lastName, 
                    $heads[2] => $username,
                    $heads[3] => $gender,
                    $heads[4] => $email,
                    $heads[5] => $phone,
                    $heads[6] => $roleId
                ), 
                [
                    $heads[0] => "required",//First name
                    $heads[1] => "required",//Last name
                    $heads[2] => "required",//User id
                    $heads[3] => "required",//Gender
                    $heads[4] => "required|unique:users|email",//Email
                    $heads[5] => "required|min:10|max:13",//Phone
                    $heads[6] => "required|integer|gte:1|lte:5"
                ]
            );

            if ($validation->fails()) {
                //We increment to get actual row number
                ++$index;
                return array("hasErrors"=>true, "index" => $index, "errors" => $validation->errors());
            }

            array_push($extractedRecords, [
                "firstName" => $firstName, 
                "lastName"  => $lastName,
                "username"  => $username,
                "gender"    => $gender,
                "email"     => $email,
                "phone"     => $phone,
                "roleId"    => $roleId,
                "departId"  => $departId
            ]);
            ++$index;
        }

        return array("hasErrors"=>false, "data"=>$extractedRecords);
    }

    public function uploadUsers(Request $request)
    {
        if($request->user()->cannot("create-user")){
            return $this->response->__invoke(
                false, "Unauthorized to upload users.", null, 403
            );
        }

        $contactsFile = $request->file('users');
        $ext = $contactsFile->getClientOriginalExtension();

        if ($ext != "xls" && $ext != "xlsx") {
            return $this->response->__invoke(false, "File format is not supported, only xlsx or xls is accepted.", null, 422);
        }

        $xlsx = new SimpleXLSX($contactsFile);
        $records = $xlsx->rows();

        $results = $this->validateUsers($records);

        if ($results["hasErrors"]) {
            return $this->response->__invoke(false, "Check input(s) at row {$results['index']}.", $results['errors'], 422);
        }

        UploadUsers::dispatch($results["data"], $request->user());

        return $this->response->__invoke(true, "Contacts were validated successfully, they will continue uploading in the background. We will let you know once the process is complete!.", null, 200);
    }

    public function updateUser(UpdateUserReq $request, $userId){
        $updatingUser = User::find($userId);

        if(!$updatingUser){
            return $this->response->__invoke(
                false, "User is not found.", null, 404
            );
        }

        $updatingUser->update($request->all());

        return $this->response->__invoke(
            true, "User was updated successfully.", $updatingUser, 200
        );
    }

    public function deleteUser(Request $request, $userId){
        if($request->user()->cannot("create-user")){
            return $this->response->__invoke(
                false, "Not authorized to delete users.", null, 403
            );
        }

        $deletingUser = User::find($userId);

        if(!$deletingUser){
            return $this->response->__invoke(
                false, "User is not found!", null, 404
            );
        }

        $deletingUser->delete();

        return $this->response->__invoke(
            true, "User was deleted successfully.", null, 200
        );
    }

    public function index(Request $request, $type, $records){
        $user = $request->user();

        if($user->role_id == 1){
            if($type == "all"){
                $users = User::paginate($records);
            }else{
                $users = User::where("role_id", $type)->paginate($records);
            } 

            foreach($users as $user){
                $role = Role::find($user->role_id);
                $user->role_id = $role;
                $depart = Department::find($user->department_id);
                $user->department_id = $depart;
            }
        }else{
            return $this->response->__invoke(
                false, "Not authorized to view users.", null, 403
            );
        }

        $usersNum = $users->count();
        if($usersNum == 0){
            return $this->response->__invoke(
                false, "No users were found.", null, 404
            );
        }

        return $this->response->__invoke(
            true, "User".(($usersNum >1)?"s were":" was")." retrieved successfully.", $users, 200
        );
    }

    public function searchIndex(Request $request, $type, $query, $records)
    {
        $user = $request->user();

        if ($user->role_id == 1) {
            if ($type == "all") {
                $users = User::where("first_name", "LIKE", "%$query%")->orWhere("last_name", "LIKE", "%$query%")
                ->orWhere("email", "LIKE", "%$query%")->orWhere("phone", "LIKE", "%$query%")
                ->orWhere("gender", "LIKE", "%$query%")->orWhere("username", "LIKE", "%$query%")
                ->paginate($records);
                
                
            } else {
                $users = User::where("role_id", $type)->where(function($clause)use($query){
                    $clause->where("first_name", "LIKE", "%$query%")->orWhere("last_name", "LIKE", "%$query%")
                    ->orWhere("email", "LIKE", "%$query%")->orWhere("phone", "LIKE", "%$query%")
                    ->orWhere("gender", "LIKE", "%$query%")->orWhere("username", "LIKE", "%$query%");
                })->paginate($records);
            }

            foreach ($users as $user) {
                $role = Role::find($user->role_id);
                $user->role_id = $role;
                $depart = Department::find($user->department_id);
                $user->department_id = $depart;
            }
        } else {
            return $this->response->__invoke(
                false,
                "Not authorized to view users.",
                null,
                403
            );
        }

        $usersNum = $users->count();
        if ($usersNum == 0) {
            return $this->response->__invoke(
                false,
                "No users were found.",
                null,
                404
            );
        }

        return $this->response->__invoke(
            true,
            "User" . (($usersNum > 1) ? "s were" : " was") . " retrieved successfully.",
            $users,
            200
        );
    }

    public function list(Request $request, $type){
        $user = $request->user();

        if($user->role_id == 1){
            if($type == "all"){
                $users = User::all();
            }else{
                $users = User::where("role_id", $type)->get();
            }
        } else if($user->role_id == 4 && $type == 5){
            $users = User::where("role_id", $type)->get();
        } else {
            return $this->response->__invoke(
                false, "Not authorized to list users.", null, 403 
            );
        }

        foreach ($users as $user) {
            $role = Role::find($user->role_id);
            $user->role_id = $role;
            $depart = Department::find($user->department_id);
            $user->department_id = $depart;
        }

        $usersNum = $users->count();
        if ($usersNum == 0) {
            return $this->response->__invoke(
                false,
                "No users were found.",
                null,
                404
            );
        }

        return $this->response->__invoke(
            true,
            "User" . (($usersNum > 1) ? "s were" : " was") . " retrieved successfully.",
            $users,
            200
        );
    }

    public function changeUsername(ChangeUsernameReq $request, $userId){
        $user = User::find($userId);

        if(!$user){
            return $this->response->__invoke(
                false, "User was not found.", null, 404
            );
        }

        $user->update($request->all());
        
        return $this->response->__invoke(
            true, "User username was updated successfully.", $user, 200 
        );
    }

    public function changeEmail(ChangeEmailReq $request, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return $this->response->__invoke(
                false,
                "User was not found.",
                null,
                404
            );
        }

        $user->update($request->all());

        return $this->response->__invoke(
            true,
            "User email was updated successfully.",
            $user,
            200
        );
    }

    public function changePhone(ChangePhoneReq $request, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return $this->response->__invoke(
                false,
                "User was not found.",
                null,
                404
            );
        }

        $user->update($request->all());

        return $this->response->__invoke(
            true,
            "User phone was updated successfully.",
            $user,
            200
        );
    }
}
