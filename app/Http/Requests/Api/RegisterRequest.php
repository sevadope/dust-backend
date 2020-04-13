<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\HasDifferentChars;

class RegisterRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'username' => ['required', 'string', 'unique:users,username'],
            'email' =>['required', 'email', 'unique:users,email'],
            'password' => [
                'required',
                'min:10',
                'max:255',
                'different:username',
                'different:email',
                new HasDifferentChars(5)
            ]
        ];
    }
}