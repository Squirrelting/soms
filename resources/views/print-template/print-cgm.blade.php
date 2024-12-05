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

        .header {
            width: 100%;
            text-align: center;
        }

        .header img {
            width: 80px;
            height: 80px;
            vertical-align: middle;
        }

        .header .left, .header .right {
            display: inline-block;
            width: 50px;
            height: 50px;
            vertical-align: middle;
            padding: 3rem;
        }

        .header .center-text {
            display: inline-block;
            vertical-align: middle;
            text-align: center;
        }

        .header p {
            margin: 0;
            font-weight: bold;
        }

        .footer{
            margin-top: 20px;
            margin-left: 500px;
            margin-top: 80px;

        }
        .printby {
            font-size: 14px;
        }

        .body p {
            font-size: 18px;
            text-indent: 30px;
            margin-bottom: 20px;
            text-align: justify;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>

<body>

    <!-- Header Section with inline-block for compatibility with Dompdf -->
    <div class="header">
        <!-- Left Image -->
        <div class="left">
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents($imagePath1)) }}" alt="Left Logo">
        </div>

        <!-- Center Text -->
        <div class="center-text">
            <p>Santiago City National High School</p>
            <p>Department Of Education</p>
            <p>Narra St., Calaocan, Santiago City</p>
            <p>Philippines, 3311</p>
        </div>

        <!-- Right Image -->
        <div class="right">
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents($imagePath2)) }}" alt="Right Logo">
        </div>
    </div>

    <h1>Certificate of Good Moral</h1>

    <div class="body">
    <p>This certifies that <strong>{{ $firstname }} {{ $middlename }} {{ $lastname }}</strong>, student of <strong>Santiago City National High
            School</strong>, has maintained good moral standing in our institution
        with exemplary conduct and dedication to upholding the values of <strong>integrity, respect, and responsibility</strong> within
        the school community. This achievement reflects not only their personal growth but also their positive influence
        on their peers.</p>

    <p>This Certificate is given this day of <strong>{{ $date }}</strong> at <strong>Santiago City National High
            School</strong>.</p>


    <div class="footer">
        <div class="signatory">
            <strong>{{ $signatory->name }}</strong> <br>
            <strong>{{ $signatory->position }}</strong>
        </div>
    </div>


</body>

</html>
