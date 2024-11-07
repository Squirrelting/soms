<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Certificate of Good Moral</title>
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

        p {
            font-size: 18px;
            text-indent: 30px;
            margin-bottom: 20px;
        }

        .signatory {
            text-align: right;
            margin-top: 50px;
        }

        .signatory p {
            margin: 0;
        }

        .position {
            margin-top: 5px;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="logo">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($imagePath)) }}" alt="SCNH Logo" width="100" height="100">
    </div>

    <h1>Certificate of Good Moral</h1>

    <p>This certifies that <strong>{{ $firstname }} {{ $middlename }} {{ $lastname }}</strong>, student of <strong>Santiago City National High
            School</strong>, has maintained good moral standing in our institution
        with exemplary conduct and dedication to upholding the values of <strong>integrity, respect, and responsibility</strong> within
        the school community. This achievement reflects not only their personal growth but also their positive influence
        on their peers.</p>

    <p>This Certificate is given this day of <strong>{{ $date }}</strong> at <strong>Santiago City National High
            School</strong>.</p>

    <div class="signatory">
        <u>
            <p><strong>{{ $signatory->name }}</strong></p>
        </u>
        <p class="position">{{ $signatory->position }}</p>
    </div>
</body>

</html>
