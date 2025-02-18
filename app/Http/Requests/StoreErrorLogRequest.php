<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class StoreErrorLogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $method = $this->method();
        if ($method == 'PATCH') {
            return [
                'microservice' => ['sometimes', 'required'],
                'method' => ['sometimes', 'required'],
                'endpoint' => ['sometimes', 'required'],
            ];
        } else {
            return [
                'microservice' => 'required|string',
                'method' => 'required',
                'endpoint' => 'required',
                // 'request' => 'required',
                // 'status' => 'required',
                // 'error' => 'required',
                // 'line' => 'required',
                // 'file' => 'required',
                // 'message' => 'required',
            ];
        }

    }

    /**
     * Method failedValidation
     *
     * @param Validator $validator [explicite description]
     *
     * @return void
     */
    protected function failedValidation(Validator $validator): void
    {
        $errors = $validator->errors();
        throw new HttpResponseException(response()->json([
            'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'message' => 'Validation errors',
            'errors' => $errors,
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }

    /**
     * Method messages
     *
     * @return void
     */
    public function messages()
    {
        return [
            'microservice.required' => trans('messages.MicroserviceRequire'),
            'method.required' => trans('messages.MethodRequire'),
            'endpoint.required' => trans('messages.EndpointRequire'),
        ];
    }
}
