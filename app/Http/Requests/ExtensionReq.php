<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Controllers\ResponseController;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Models\Assignment;

class ExtensionReq extends FormRequest
{
    private $response;
    private $success;
    private $message;
    private $data;
    private $code;
    public $assignment;

    public function __construct()
    {
        $this->response = new ResponseController();
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $this->assignment = Assignment::find($this->assignment_id);
        // if ($this->user()->can("create-user")) {
        //     return true;
        // }

        $this->success = false;
        $this->message = "Not authorized to create users.";
        $this->data = null;
        $this->code = 403;

        return true;
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
            "assignment_id" => "required|gt:0",
            "days"          => "required|gt:0|integer"
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
