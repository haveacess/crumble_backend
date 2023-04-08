<?php

namespace App\Http\Requests\Steam;

use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class AddSteamAccountRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'cookie_file' => [
                'required_if' => Rule::requiredIf($this->isNotFilled('cookie')),
                File::types(['json'])
                    ->between(1, 30)
            ],
            'cookie' => [
                'required_if' => Rule::requiredIf(!$this->exists('cookie_file'))
            ],
            'allow_rewrite' => ['boolean']
        ];
    }

    public function messages()
    {
        return [
            'cookie_file.required' => 'Please fill one of two fields: cookie or cookie_file',
            'cookie.required' => 'Please fill one of two fields: cookie or cookie_file',

            'cookie_file.mimes.json' => 'The :attribute field must be a file of type: :values. And has a valid json content'
        ];
    }
}
