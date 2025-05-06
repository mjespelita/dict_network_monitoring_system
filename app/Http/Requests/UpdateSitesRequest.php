<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSitesRequest extends FormRequest
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
'name' => 'required','siteId' => 'required','customerId' => 'required','customerName' => 'required','region' => 'required','timezone' => 'required','scenario' => 'required','wan' => 'required','connectedApNum' => 'required','disconnectedApNum' => 'required','isolatedApNum' => 'required','connectedSwitchNum' => 'required','disconnectedSwitchNum' => 'required','type' => 'required',
        ];
    }
}
