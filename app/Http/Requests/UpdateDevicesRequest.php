<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDevicesRequest extends FormRequest
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
'device_name' => 'required','ip_address' => 'required','status' => 'required','model' => 'required','version' => 'required','uptime' => 'required','cpu' => 'required','memory' => 'required','public_ip' => 'required','link_speed' => 'required','duplex' => 'required','siteId' => 'required',
        ];
    }
}
