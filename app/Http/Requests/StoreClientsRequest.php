<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientsRequest extends FormRequest
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
'mac_address' => 'required','device_name' => 'required','device_type' => 'required','connected_device_type' => 'required','switch_name' => 'required','port' => 'required','standard_port' => 'required','network_theme' => 'required','uptime' => 'required','traffic_down' => 'required','traffic_up' => 'required','status' => 'required','siteId' => 'required',
        ];
    }
}
