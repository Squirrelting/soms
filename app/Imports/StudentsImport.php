<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    protected $grade_id;
    protected $section_id;

    public function __construct($grade_id, $section_id)
    {
        $this->grade_id = $grade_id;
        $this->section_id = $section_id;
    }

    public function model(array $row)
{
    
    if (empty($row['lrn']) || empty($row['firstname']) || empty($row['lastname'])) {
        return null; // Skip this row if LRN or names are empty
    }

    $student = Student::where('lrn', $row['lrn'])->first();

    if ($student) {
        $student->update([
            'grade_id' => $this->grade_id,
            'section_id' => $this->section_id,
        ]);
    } else {
        return new Student([
            'lrn' => $row['lrn'],
            'firstname' => $row['firstname'],
            'lastname' => $row['lastname'],
            'grade_id' => $this->grade_id,
            'section_id' => $this->section_id,
        ]);
    }
}

}
