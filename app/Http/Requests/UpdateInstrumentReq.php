<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Controllers\ResponseController;
use Illuminate\Contracts\Validation\Validator;

class UpdateInstrumentReq extends FormRequest
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
        return $this->user()->can("create-instrument", $this->instrument_id);
    }

    public function failedAuthorization()
    {
        $this->message = "Not authorized to create instrument.";
        $this->code = 403;

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
            "name"          => "required",
            "description"   => "required",
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $this->message = "Please check input(s).";
        $this->data = $validator->errors();
        $this->code = 422;

        throw new HttpResponseException($this->response->__invoke(
            false,
            $this->message,
            $this->data,
            $this->code
        ));
    }
}
