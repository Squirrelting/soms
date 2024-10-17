<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Record</title>
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
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="logo">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($imagePath)) }}" alt="Logo" width="100" height="100">
    </div>

    <h1>Student Record</h1>
    <p><strong>Date: {{ $date }}</strong></p>

    <!-- Minor Offenses Table -->
    @if($submittedminorOffenses->isNotEmpty())
    <div class="mt-4 mx-4">
        <h2>Minor Offenses</h2>
        <table>
            <thead>
                <tr>
                    <th>Offense Committed</th>
                    <th>Penalty</th>
                    <th>Committed Date</th>
                    <th>Recorded Date</th>
                    <th>Cleansed Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($submittedminorOffenses as $offense)
                    <tr>
                        <td>{{ $offense->minor_offense }}</td>
                        <td>{{ $offense->minor_penalty }}</td>
                        <td>{{ $offense->committed_date }}</td>
                        <td>{{ $offense->recorded_date }}</td>
                        <td>{{ $offense->cleansed_date ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Major Offenses Table -->
    @if($submittedmajorOffenses->isNotEmpty())
    <div class="mt-4 mx-4">
        <h2>Major Offenses</h2>
        <table>
            <thead>
                <tr>
                    <th>Offense Committed</th>
                    <th>Penalty</th>
                    <th>Committed Date</th>
                    <th>Recorded Date</th>
                    <th>Cleansed Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($submittedmajorOffenses as $offense)
                    <tr>
                        <td>{{ $offense->major_offense }}</td>
                        <td>{{ $offense->major_penalty }}</td>
                        <td>{{ $offense->committed_date }}</td>
                        <td>{{ $offense->recorded_date }}</td>
                        <td>{{ $offense->cleansed_date ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</body>
</html>
