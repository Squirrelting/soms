<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Record</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            font-size: 24px;
            margin: 10px 0 20px;
        }
        .logo {
            text-align: center;
            margin-top: -30px;
            margin-bottom: 20px;
        }
        .student, .footer, .date {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin: 10px 0;
            width: 100%;
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

    <div class="logo">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($imagePath)) }}" alt="Logo" width="100" height="100">
    </div>

    <h1>Student Record</h1>
    
    <div class="student">  
        <span><strong>LRN:</strong> {{ $student->lrn }}</span> | 
        <span><strong>Name:</strong> {{ $student->firstname }} {{ $student->middlename }} {{ $student->lastname }}</span> |
        <span><strong>Sex:</strong> {{ $student->sex }}</span> |
        <span><strong>Grade:</strong> {{ $student->grade->grade ?? 'N/A' }}</span> |
        <span><strong>Section:</strong> {{ $student->section->section ?? 'N/A' }}</span>
    </div>
    

    <!-- Minor Offenses Table -->
    @if($submittedminorOffenses->isNotEmpty())
    <div class="mt-2">
        <h4>Minor Offenses</h4>
        <table>
            <thead>
                <tr>
                    <th>Offense Committed</th>
                    <th>Penalty</th>
                    <th>Status</th>
                    <th>S.Y. Quarter</th>
                    <th>Grade and Section</th>
                    <th>Committed Date</th>
                    <th>Recorded By</th>
                    <th>Recorded Date</th>
                    <th>Resolved By</th>
                    <th>Resolved Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($submittedminorOffenses as $offense)
                    <tr>
                        <td>{{ $offense->minor_offense }}</td>
                        <td>{{ $offense->minor_penalty }}</td>
                        <td>{{ $offense->student_schoolyear }}, {{ $offense->student_quarter }}</td>
                        <td>{{ $offense->student_grade }}, {{ $offense->student_section }}</td>
                        <td>{{ $offense->committed_date }}</td>
                        <td>{{ $offense->recorded_by }}</td>
                        <td>{{ $offense->recorded_date }}</td>
                        <td>{{ $offense->cleansed_by }}</td>
                        <td>{{ $offense->cleansed_date }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Major Offenses Table -->
    @if($submittedmajorOffenses->isNotEmpty())
    <div class="mt-2">
        <h4>Major Offenses</h4>
        <table>
            <thead>
                <tr>
                    <th>Offense Committed</th>
                    <th>Penalty</th>
                    <th>Status</th>
                    <th>S.Y. Quarter</th>
                    <th>Grade and Section</th>
                    <th>Committed Date</th>
                    <th>Recorded By</th>
                    <th>Recorded Date</th>
                    <th>Resolved By</th>
                    <th>Resolved Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($submittedmajorOffenses as $offense)
                    <tr>
                        <td>{{ $offense->major_offense }}</td>
                        <td>{{ $offense->major_penalty }}</td>
                        <td>{{ $offense->sanction == 1 ? 'Resolved' : 'Unresolved' }}</td>
                        <td>{{ $offense->student_schoolyear }}, {{ $offense->student_quarter }}</td>
                        <td>{{ $offense->student_grade }}, {{ $offense->student_section }}</td>
                        <td>{{ $offense->committed_date }}</td>
                        <td>{{ $offense->recorded_by }}</td>
                        <td>{{ $offense->recorded_date }}</td>
                        <td>{{ $offense->cleansed_by }}</td>
                        <td>{{ $offense->cleansed_date }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        Printed by: {{ $userName }}, {{ $userRole }}
    </div>
    <div class="date">
        Date: {{ $date }}
    </div>

</body>
</html>
