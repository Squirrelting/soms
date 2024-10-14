<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offenders per Sex</title>
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

    <h1>Offenders per Sex Report</h1>
    <p><strong>Date: {{ $date }}</strong></p>
    <p><strong>Selected Date: {{ $selectedDate['startDate'] }} to {{ $selectedDate['endDate'] }}</strong></p>

    <p><strong>Selected Offense Type: {{ $offenseFilter ? ucfirst($offenseFilter) : 'All Offenses' }}</strong></p>
    <table>
        <thead>
            <tr>
                <th>Offense name</th>
                <th>Male Count</th>
                <th>Female Count</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($offensesData as $offense)
                <tr>
                    @if ($offense->type === 'Minor')
                    <td>{{ $offense->minor_offense }}</td>
                    @else
                    <td>{{ $offense->major_offense }}</td>
                    @endif
                    <td>{{ $offense->male_count }}</td>
                    <td>{{ $offense->female_count }}</td>
                    <td>{{ $offense->male_count + $offense->female_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
