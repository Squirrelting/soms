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
            "student_id" => "required|integer|digits_between:1,8",
            "name" => "required|string|max:255",
            "sex" => "required|string|in:Male,Female", 
            "grade" => "required|integer|between:1,12", 
            "email" => "required|string|max:255|email", 
        ];
    }
}
