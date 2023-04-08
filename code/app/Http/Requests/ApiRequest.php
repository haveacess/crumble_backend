<?php

namespace App\Http\Requests;

use App\Exceptions\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

abstract class ApiRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator);
    }
}
