<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserReq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Http\Packages\SimpleXLSX;
use App\Jobs\UploadUsers;

class UserController extends Controller
{
    private $response;

    public function __construct()
    {
        $this->response = new ResponseController();
    }

    public function createUser(CreateUserReq $request){
        $createdUser = User::create($request->all());
        return $this->response->__invoke(
            true, "User was created successfully.", $createdUser, 201
        );
    }

    private function validateUsers($rows)
    {
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
            $userId     = $row[2];
            $gender     = $row[3];
            $email      = $row[4];
            $phone      = $row[5];
            $roleId     = $row[6];
            $departId   = $row[7];

            $validation = Validator::make(
                array(
                    $heads[0] => $firstName, 
                    $heads[1] => $lastName, 
                    $heads[2] => $userId,
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
                    $heads[5] => "required|digits",//Phone
                    $heads[6] => "required|integer|gte:1|lte:5"
                ]
            );

            if ($validation->fails()) {
                //We increment to get actual row number
                ++$index;
                return array("index" => $index, "errors" => $validation->errors());
            }
            ++$index;
        }

        return;
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

        if ($ext != "xlsx" && $ext != "xlsx") {
            return $this->response->__invoke(false, "File format is not supported, only xlsx or xls is accepted.", null, 422);
        }

        $xlsx = new SimpleXLSX($contactsFile);
        $records = $xlsx->rows();

        $errors = $this->validateUsers($records);

        if ($errors) {
            return $this->response->__invoke(false, "Check input(s) at row {$errors['index']}.", $errors['errors'], 422);
        }

        UploadUsers::dispatch($records, $request->user());

        return $this->response->__invoke(true, "Contacts were validated successfully, they will continue uploading in the background. We will let you know once the process is complete!.", null, 200);
    }

    public function updateUser(CreateUserReq $request, $userId){
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

    public function listClassRepresentatives(Request $request, $records){
        $users = User::where("role_id", 5)->paginate($records);

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
}
