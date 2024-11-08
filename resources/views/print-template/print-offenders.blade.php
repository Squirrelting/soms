<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Offenders</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-top: -30px;
        }
        h1 {
            text-align: center;
            margin: 10px 0 20px;
            font-size: 24px;
        }
        .filter, .footer, .date {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin: 10px 0;
            width: 100%;
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

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 14px;
        }
        .date {
            text-align: right;
            font-size: 14px;
        }

    </style>
</head>
<body>
    <div class="header">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($imagePath)) }}" alt="Logo" width="100" height="100">
    </div>

    <h1>List of Offenders</h1>

    <div class="filter">  
        <span><strong>Status:</strong> 
            @if ($sanction === "0")
                Unresolved
            @elseif ($sanction === "1")
                Resolved
            @else
                All
            @endif
        </span> | 
        <span><strong>Offense Filter:</strong> {{ $offenseFilter ?? 'All Offenses' }}</span> | 
        <span><strong>Sex:</strong> {{ $sex ?? 'All' }}</span> | 
        <span><strong>School Year:</strong> {{ $selectedYear ?? 'All' }}, <strong>Quarter:</strong> {{ $selectedQuarter ?? 'All' }}</span> | 
        <span><strong>Grade:</strong> {{ $grade ?? 'All' }}, <strong>Section:</strong> {{ $section ?? 'All' }}</span> | 
        <span><strong>Offense:</strong> {{ $selectedOffense ?? 'All Offenses' }}</span>
    </div>
    

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>LRN</th>
                <th>Name</th>
                <th>Sex</th>
                <th>Grade</th>
                <th>Section</th>
                <th>Offense</th>
                <th>Penalty</th>
                <th>Date Committed</th>
                <th>Date Recorded</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($offendersData as $index => $offense)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $offense->lrn }}</td>
                    <td>{{ $offense->student_lastname }}, {{ $offense->student_firstname }}, {{ $offense->student_middlename }}</td>
                    <td>{{ $offense->student_sex }}</td>
                    <td>Grade {{ $offense->student_grade }}</td>
                    <td>{{ $offense->student_section }}</td>
                    <td>{{ $offense->minor_offense ?? $offense->major_offense }}</td>
                    <td>{{ $offense->minor_penalty ?? $offense->major_penalty }}</td>
                    <td>{{ $offense->committed_date }}</td>
                    <td>{{ $offense->recorded_date }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Printed by: {{ $userName }}, {{ $userRole }}
    </div>
    <div class="date">
        Date: {{ $date }}
    </div>
</body>
</html>
