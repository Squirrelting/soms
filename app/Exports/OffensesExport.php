<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OffensesExport implements FromCollection, WithHeadings
{
    protected $offensesData;

    public function __construct($offensesData)
    {
        $this->offensesData = $offensesData;
    }

    /**
     * Return a collection of offenses to be exported
     */
    public function collection()
    {
        // Format the data for Excel export as a collection
        return collect($this->offensesData->map(function ($offense) {
            return [
                'LRN' => $offense->lrn,
                'Last Name' => $offense->student_lastname,
                'First Name' => $offense->student_firstname,
                'Middle Name' => $offense->student_middlename,
                'Sex' => $offense->student_sex,
                'Grade' => 'Grade ' . $offense->student_grade,
                'Section' => $offense->student_section,
                'Offense' => $offense->minor_offense ?? $offense->major_offense ?? 'N/A',
                'Penalty' => $offense->minor_penalty ?? $offense->major_penalty ?? 'N/A',
                'Date Committed' => $offense->committed_date,
                'Date Recorded' => $offense->recorded_date,
            ];
        }));
    }

    public function headings(): array
    {
        return [
            'LRN',
            'Last Name',
            'First Name',
            'Middle Name',
            'Sex',
            'Grade',
            'Section',
            'Offense',
            'Penalty',
            'Date Committed',
            'Date Recorded',
        ];
    }
}
