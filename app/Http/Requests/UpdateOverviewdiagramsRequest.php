<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOverviewdiagramsRequest extends FormRequest
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
'totalGatewayNum' => 'required','connectedGatewayNum' => 'required','disconnectedGatewayNum' => 'required','totalSwitchNum' => 'required','connectedSwitchNum' => 'required','disconnectedSwitchNum' => 'required','totalPorts' => 'required','availablePorts' => 'required','powerConsumption' => 'required','totalApNum' => 'required','connectedApNum' => 'required','isolatedApNum' => 'required','disconnectedApNum' => 'required','totalClientNum' => 'required','wiredClientNum' => 'required','wirelessClientNum' => 'required','guestNum' => 'required',
        ];
    }
}
