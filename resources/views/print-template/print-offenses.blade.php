<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Offenses</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 40px;
            line-height: 1.5;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="logo" style="text-align: center;">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($imagePath)) }}" alt="Logo" width="100" height="100">
    </div>

    <h1>List of Offenses</h1>
    <p><strong>Date: {{ $date }}</strong></p>
    {{-- <p><strong>Selected Offense Type: {{ $offenseFilter ? ucfirst($offenseFilter) : 'All Offenses' }}</strong></p> --}}
    <table>
        <thead>
            <tr>
                <th class="py-1 px-2 border text-sm">No.</th>
                <th class="py-1 px-2 border text-sm">LRN</th>
                <th class="py-1 px-2 border text-sm">Name</th>
                <th class="py-1 px-2 border text-sm">Sex</th>
                <th class="py-1 px-2 border text-sm">Grade</th>
                <th class="py-1 px-2 border text-sm">Section</th>
                <th class="py-1 px-2 border text-sm">Offense</th>
                <th class="py-1 px-2 border text-sm">Penalty</th>
                <th class="py-1 px-2 border text-sm">Date Committed</th>
                <th class="py-1 px-2 border text-sm">Date Recorded</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($offensesData as $index => $offense)
                <tr>
                    <td class="py-1 px-2 border text-sm">{{ $index + 1 }}</td>
                    <td class="py-1 px-2 border text-sm">{{ $offense->lrn }}</td>
                    <td class="py-1 px-2 border text-sm">{{ $offense->student_lastname }}, {{ $offense->student_firstname }}, {{ $offense->student_middlename }}</td>
                    <td class="py-1 px-2 border text-sm">{{ $offense->student_sex }}</td>
                    <td class="py-1 px-2 border text-sm">Grade {{ $offense->student_grade }}</td>
                    <td class="py-1 px-2 border text-sm">{{ $offense->student_section }}</td>
                    <td class="py-1 px-2 border text-sm">{{ $offense->minor_offense ?? $offense->major_offense }}</td>
                    <td class="py-1 px-2 border text-sm">{{ $offense->minor_penalty ?? $offense->major_penalty }}</td>
                    <td class="py-1 px-2 border text-sm">{{ $offense->committed_date }}</td>
                    <td class="py-1 px-2 border text-sm">{{ $offense->recorded_date }}</td>
                </tr>
            @endforeach
        </tbody>
        
    </table>
</body>
</html>
