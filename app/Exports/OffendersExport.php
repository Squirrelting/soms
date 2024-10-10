<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OffendersExport implements FromCollection, WithHeadings
{
    protected $offensesData;
    protected $date;
    protected $selectedDate;
    protected $offenseFilter;

    public function __construct($offensesData, $date, $selectedDate, $offenseFilter)
    {
        $this->offensesData = $offensesData;
        $this->date = $date;
        $this->selectedDate = $selectedDate;
        $this->offenseFilter = $offenseFilter;
    }

    /**
     * Return a collection of offenses to be exported
     */
    public function collection()
    {
        // Format the data for Excel export as a collection
        return collect($this->offensesData->map(function ($offense) {
            // Adjust the offense name according to its type
            $offenseName = $offense->type === 'Minor' ? $offense->minor_offenses : $offense->major_offenses;

            // Debugging: Check if offense names are set correctly
            if (!isset($offense->minor_offenses) && !isset($offense->major_offenses)) {
                dd('No offense name found for:', $offense);
            }

            return [
                'offense_name' => $offenseName,
                'male_count' => $offense->male_count ?? 0,
                'female_count' => $offense->female_count ?? 0,
                'total' => ($offense->male_count ?? 0) + ($offense->female_count ?? 0),
            ];
        }));
    }

    /**
     * Define the headings for the Excel sheet
     */
    public function headings(): array
    {
        return [
            'Offense Name',
            'Male Count',
            'Female Count',
            'Total',
        ];
    }
}
