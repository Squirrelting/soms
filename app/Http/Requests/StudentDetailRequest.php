<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentDetailRequest extends FormRequest
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
            "lrn" => "required|integer|digits_between:1,8",
            "firstname" => "required|string|max:255",
            "middlename" => "nullable|string|max:255",
            "lastname" => "required|string|max:255",
            "sex" => "required|string|in:Male,Female",
            "grade_id" => "required|integer|exists:grade,id", // Use grade_id and reference the 'grades' table
            "section_id" => "required|integer|exists:section,id", // Use section_id and reference the 'sections' table
            "email" => "nullable|string|max:255|email", // Optional email validation
        ];
    }
}
