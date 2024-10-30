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
            'lrn' => 'required|integer|min:11|exists:students,lrn', 
            'student_firstname' => 'required|string|max:255', 
            'student_middlename' => '', 
            'student_lastname' => 'required|string|max:255', 
            'student_grade' => 'required|integer|between:7,12', 
            'student_section' => 'required|string|max:255', 
            'student_sex' => '',
            'student_schoolyear' => 'required|string|max:255', 
            'student_quarter' => 'required|in:1st Quarter,2nd Quarter,3rd Quarter,4th Quarter',
            'committed_date' => 'required|date|before_or_equal:today', 
            'minor_offense' => 'required|string|max:255',  
        ];
    }
}
