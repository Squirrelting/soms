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
    <p>Student Name: {{ $student->name }}</p>
    <p>Grade: {{ $student->grade }}</p>
    <h5>Major Offenses:</h5>
    
    <ul>
        @foreach ($submittedmajorOffenses as $majorOffense)
            <li>
                Offense: {{ $majorOffense->majorOffense->major_offenses }}<br>
                Penalty: {{ $majorOffense->majorPenalty->major_penalties }}
            </li>
        @endforeach
    </ul>
    
    <h5>Minor Offenses:</h5>
    <ul>
        @foreach ($submittedminorOffenses as $minorOffense)
            <li>
                Offense: {{ $minorOffense->minorOffense->minor_offenses }}<br>
                Penalty: {{ $minorOffense->minorPenalty->minor_penalties }}
            </li>
        @endforeach
    </ul>    

    <h4>Dear Parent/Guardian</h4>
    <p style="color: gray;"> CONFIDENTIALITY NOTICE: We hope this message finds you well. It is with a sense of responsibility that we bring to your attention certain incidents involving your child within the premises of SANTIAGO CITY NATIONAL HIGH SCHOOL.
        As part of our commitment to maintaining a safe and conducive learning environment, we feel it is essential to inform you about the circumstances. Please be assured that this communication is intended solely for your awareness and cooperation.
        We have documented the details of the incidents, which include both minor and major offenses. Our purpose in sharing this information is to foster open communication and collaboration between the university and parents to ensure the overall well-being and academic success of your child.
        We kindly request your understanding and cooperation in addressing these matters. If you have any concerns or would like to discuss these incidents further, please do not hesitate to contact us. Together, we can work towards creating a positive and supportive learning environment for all students.
        <p>

</body>
</html>
