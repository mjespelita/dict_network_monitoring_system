<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAuditlogsRequest extends FormRequest
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
'time' => 'required','operator' => 'required','resource' => 'required','ip' => 'required','auditType' => 'required','level' => 'required','result' => 'required','content' => 'required','label' => 'required','oldValue' => 'required','newValue' => 'required',
        ];
    }
}
