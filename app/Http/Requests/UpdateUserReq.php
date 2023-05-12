<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Controllers\ResponseController;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateUserReq extends FormRequest
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
        if ($this->user()->can("create-user")) {
            return true;
        }

        $this->success = false;
        $this->message = "Not authorized to update users.";
        $this->data = null;
        $this->code = 403;

        return false;
    }

    public function failedAuthorization()
    {
        throw new HttpResponseException($this->response->__invoke(
            $this->success,
            $this->message,
            $this->data,
            $this->code
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
            "first_name"    => "required",
            "last_name"     => "required",
            "gender"        => "regex:/^[M,F]$/",
            "role_id"       => "required|integer|gt:0|lt:6",
            "department_id" => "nullable|integer|gt:0",
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $this->success = false;
        $this->message = "Check inputs.";
        $this->data = $validator->errors();
        $this->code = 422;

        throw new HttpResponseException($this->response->__invoke(
            $this->success,
            $this->message,
            $this->data,
            $this->code
        ));
    }
}
