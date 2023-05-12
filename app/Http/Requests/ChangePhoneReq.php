<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Controllers\ResponseController;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ChangePhoneReq extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "phone" => "required|min:10|max:13"
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
