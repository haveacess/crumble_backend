<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\MessageBag;

class ApiResponse implements Responsable
{
    private int $httpCode;
    private bool $status;
    private string $message = '';
    private MessageBag $errors;
    private array $data = [];

    /**
     * Create new api response
     *
     * @param int $httpCode Http status code for response
     * @param bool $status True is ok, false - not ok
     */
    public function __construct(int $httpCode = 200, bool $status = true)
    {
        $this->status = $status;
        $this->httpCode = $httpCode;
        $this->errors = new MessageBag();
    }

    /**
     * Adding inform message for response
     *
     * @param string $message Some inform message
     * @return $this
     */
    public function withMessage(string $message):self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Adding some errors for response object
     *
     * @param MessageBag $errors Errors from validator or other places
     * @return $this
     */
    public function withErrors(MessageBag $errors):self
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Adding any data to response
     *
     * @param mixed $data New resources or something else
     * @return $this
     */
    public function withData(mixed $data):self
    {
        $this->data = $data;
        return $this;
    }

    public function toResponse($request): JsonResponse
    {
        $response = [
            "status" => $this->status
        ];

        if ($this->message) {
            $response['message'] = $this->message;
        }

        if ($this->errors->isNotEmpty()) {
            $response["errors"] = $this->errors->toArray();
        }

        if ($this->data) {
            $response['data'] = $this->data;
        }

        return response()->json($response, $this->httpCode);
    }
}
