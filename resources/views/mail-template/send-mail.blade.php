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
    <p>{{ $mailMessage }}</p>

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

</body>
</html>
