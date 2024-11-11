<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'products' => 'required|array',
            // 'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1'

        ];
    }
    public function attributes()
    {
        return [
            'products.*.quantity' => 'cantidad del producto',

        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        // Transformar las claves de error a las nuevas etiquetas que deseas
        $errors = collect($validator->errors()->toArray())->mapWithKeys(function ($messages, $key) {
            $newKey = preg_replace('/products\.\d+\.quantity/', 'quantity', $key);
            return [$newKey => $messages];
        });
    

        throw new HttpResponseException(response()->json([
            'status' => false,
            'errors' => $errors
        ], 200));
    }
}
