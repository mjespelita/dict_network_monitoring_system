<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientdetailsRequest extends FormRequest
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
'mac' => 'required','name' => 'required','deviceType' => 'required','switchName' => 'required','switchMac' => 'required','port' => 'required','standardPort' => 'required','trafficDown' => 'required','trafficUp' => 'required','uptime' => 'required','guest' => 'required','blocked' => 'required',
        ];
    }
}
