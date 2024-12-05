<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Send email</title>
</head>
<body>

    <h4>{{ $subject }}</h4>
    <center><img src="{{ $message->embed(public_path('images/SCNHS-Logo.png')) }}" alt="Logo" width="100"> </center>

    <p>LRN: {{ $student->lrn }}</p>
    <p>Student Name: {{ $student->firstname }} {{ $student->middlename }} {{ $student->lastname }}</p>
    <p>Grade: {{ $student->grade->grade??'N/A' }}</p>
    <p>Section: {{ $student->section->section??'N/A' }}</p>

    @if ($submittedminorOffenses->isNotEmpty())
    <h5>Minor Offenses:</h5>
    <ul>
        @foreach ($submittedminorOffenses as $minorOffense)
            <li>
                Offense: {{ $minorOffense->minor_offense }}<br>
                Penalty: {{ $minorOffense->minor_penalty }}<br>
                Date Committed: {{ \Carbon\Carbon::parse($minorOffense->committed_date)->format('Y-m-d') }}
            </li>
        @endforeach
    </ul>
    @endif   

    @if ($submittedmajorOffenses->isNotEmpty())
        <h5>Major Offenses:</h5>
        <ul>
            @foreach ($submittedmajorOffenses as $majorOffense)
                <li>
                    Offense: {{ $majorOffense->major_offense }}<br>
                    Penalty: {{ $majorOffense->major_penalty }}<br>
                    Date Committed: {{ \Carbon\Carbon::parse($majorOffense->committed_date)->format('Y-m-d') }}
                </li>
            @endforeach
        </ul>
    @endif 

    <h4>To Adviser/Teacher</h4>
    <p style="color: gray;">
        CONFIDENTIALITY NOTICE: We hope this message finds you well. We wish to bring to your attention certain incidents involving one of your advisory students at Santiago City National High School. 
    
        As part of our commitment to upholding a safe and conducive environment for learning, we feel it is crucial to keep you informed about these occurrences. This communication is intended solely for your awareness and collaboration in addressing these matters.
    
        The incidents have been documented and include both minor and major offenses. By sharing this information, we aim to foster open communication and cooperation between the school and parents/guardians to support the student's overall well-being and academic success.
    
        We kindly ask for your assistance and understanding in addressing these matters. If you would like to discuss the details further or have any concerns, please feel free to reach out. Together, we can work towards maintaining a positive and supportive learning environment for all our students.
    </p>
</body>
</html>
