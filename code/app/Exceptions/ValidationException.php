<?php

namespace App\Exceptions;

use App\Http\Responses\ApiResponse;
use Exception;
use Illuminate\Contracts\Validation\Validator;

class ValidationException extends Exception
{
    private Validator $validator;
    private int $httpCode = 422;

    public function __construct(Validator $validator)
    {
        parent::__construct();
        $this->validator = $validator;
    }

    public function render(): ApiResponse
    {
        $response = new ApiResponse($this->httpCode, false);
        return $response
            ->withMessage('Validation Error')
            ->withErrors($this->validator->errors());
    }
}
