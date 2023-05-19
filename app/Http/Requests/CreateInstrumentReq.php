<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Controllers\ResponseController;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CreateInstrumentReq extends FormRequest
{
    private $response;
    private $success;
    private $message;
    private $data;
    private $code;

    public function __construct()
    {
        $this->response = new ResponseController();
        $this->success = false;
        $this->data = null;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can("create-instrument", $this->store_id);
    }

    public function failedAuthorization(){
        $this->message = "Not authorized to create instrument.";
        $this->code = 403;

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
            "name"          => "required",
            "description"   => "required",
            "quantity"      => "required|integer|gte:0",
            "code"          => "required|unique:instruments",
            "store_id"      => "required|integer|gt:0"
        ];
    }

    public function failedValidation(Validator $validator){
        $this->message = "Please check input(s).";
        $this->data = $validator->errors();
        $this->code = 422;

        throw new HttpResponseException($this->response->__invoke(
            false, $this->message, $this->data, $this->code
        ));
    }
}
