<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientstatsRequest extends FormRequest
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
            //
'total' => 'required','wireless' => 'required','wired' => 'required','num2g' => 'required','num5g' => 'required','num6g' => 'required','numUser' => 'required','numGuest' => 'required','numWirelessUser' => 'required','numWirelessGuest' => 'required','num2gUser' => 'required','num5gUser' => 'required','num6gUser' => 'required','num2gGuest' => 'required','num5gGuest' => 'required','num6gGuest' => 'required','poor' => 'required','fair' => 'required','noData' => 'required','good' => 'required','siteId' => 'required',
        ];
    }
}
