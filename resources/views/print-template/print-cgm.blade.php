<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Certificate of Good Moral</title>
    <style>
        /* Ensure that the HTML and body elements take up the full height of the page */
        html, body {
            height: 100%;
        }

        body {
            position: relative;
            margin-left: 40px;
            margin-right: 40px;
            padding-bottom: auto;  /* Space for the footer */
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }

        .body p {
            font-size: 18px;
            text-indent: 30px;
            margin-bottom: 20px;
            text-align: justify;
        }

        .signatory {
            margin-left: 325px;
            margin-top: 80px;
            text-align: center;
        }

        /* Footer Styling */
        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
        }

    </style>
</head>

<body>
    <?php include 'header-footer/header2.php'; ?>  <!-- Header Included at the top -->

        <h1>Certificate of Good Moral</h1>
        <div class="body">
            <p>This certifies that <strong><?php echo $firstname; ?> <?php echo $middlename; ?> <?php echo $lastname; ?></strong>, student of <strong>Santiago City National High
                    School</strong>, has maintained good moral standing in our institution
                with exemplary conduct and dedication to upholding the values of <strong>integrity, respect, and responsibility</strong> within
                the school community. This achievement reflects not only their personal growth but also their positive influence
                on their peers.</p>

            <p>This Certificate is given this day of <strong><?php echo $date; ?></strong> at <strong>Santiago City National High
                    School</strong>.</p>
        </div>

        <div class="signatory">
            <strong><?php echo $signatory->name; ?></strong><br>
            <strong><?php echo $signatory->position; ?></strong>
        </div>

    <div class="footer">
        <?php include 'header-footer/footer.php'; ?>  <!-- Footer Included at the bottom -->
    </div>
</body>

</html>
