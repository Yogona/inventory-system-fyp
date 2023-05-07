<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Controllers\ResponseController;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateDepartmentReq extends FormRequest
{
    private $response;
    private $success;
    private $message;
    private $data;
    private $code;

    public function __construct()
    {
        $this->response = new ResponseController();
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if($this->user()->can("create-department")){
            return true;
        }

        $this->success = false;
        $this->message = "Not authorized to create departments.";
        $this->data = null;
        $this->code = 403;

        return false;
    }

    public function failedAuthorization(){
        throw new HttpResponseException($this->response->__invoke(
            $this->success, $this->message, $this->data, $this->code 
        ));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "name"          => "required|max:50",
            "description"   => "nullable",
            "abbr"          => "required|min:3|max:5|unique:departments"
        ];
    }

    public function failedValidation(Validator $validator){
        throw new HttpResponseException($this->response->__invoke(
            $this->success, $this->message, $this->data, $this->code
        ));
    }
}
