<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Record</title>
    <style>
        body {
            position: relative;
            margin-left: 40px;
            margin-right: 40px;
            padding-bottom: auto;  /* Space for the footer */
        }
        h1 {
            text-align: center;
            font-size: 24px;
            margin: 10px 0 20px;
        }

        .student {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin: 10px 0;
            width: 100%;
        }
        .footer1 {
            margin-top: 20px;
            margin-left: 900px;
        }
        .footer2 {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 100;
        }
        .printby {
            font-size: 14px;
        }
        .date {
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
    <?php include 'header-footer/header2.php'; ?>  <!-- Header Included at the top -->

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

    <div class="footer1">
        <div class="printby">
            Printed by: <strong>{{ $userName }}</strong> <br>
            Designation/Position: <strong>{{ $userRole }}</strong>
        </div>
        <div class="date">
            Date: {{ $date }}
        </div>
    </div>

    <div class="footer2">
        <?php include 'header-footer/footer.php'; ?>  <!-- Footer Included at the bottom -->
    </div>
</body>
</html>
