<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MajorOffenseDetailRequest extends FormRequest
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
            'lrn' => 'required',
            'student_name' => 'required',
            'student_grade' => 'required',
            'student_sex' => 'required',
            'major_offense_id' => 'required|exists:major_offenses,id',
        ];
    }
}
