<?php

namespace App\Http\Requests;

use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateMonitorUrlRequest extends FormRequest
{

    private string $request_uuid;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    public function prepareForValidation(): void
    {
        $this->request_uuid = Str::uuid()->toString();
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'url' => ['bail', 'required', 'url', 'unique:monitors,url'],
            'check_interval' => ['bail', 'required', 'integer:1,60'],
            'threshold' => ['bail', 'required', 'integer', 'min:1'],
        ];
    }

   /**
     * @param  Validator  $validator
     *
     * @return void
     */
    public function failedValidation(Validator $validator): void
    {
        $errors = $validator->errors()->toArray();
        
        $firstError = collect($errors)->flatten()->first();

       throw new HttpResponseException(
            response()->json([
                'message' => $firstError,
                'errors' => $errors,
            ], 422)
        );
    }
}
