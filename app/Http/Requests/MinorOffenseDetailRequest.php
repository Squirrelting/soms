<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MinorOffenseDetailRequest extends FormRequest
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
            'lrn' => 'required|integer|digits_between:1,8|exists:students,lrn', // Ensure lrn exists in students table
            'student_firstname' => 'required|string|max:255', // Required first name
            'student_lastname' => 'required|string|max:255', // Required last name
            'student_grade' => 'required|integer|between:1,12', // Validate student grade between 1 and 12
            'student_section' => 'required|string|max:255', // Required section, expecting a string
            'student_sex' => '', // Adjusted to validate sex
            'minor_offense_id' => 'required|exists:minor_offenses,id', // Required minor offense ID
        ];
    }
}
