<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome to Our Platform</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f1f1f1;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            max-width: 200px;
        }

        .message {
            padding: 20px;
            background-color: #ffffff;
        }

        .message p {
            margin-bottom: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
        }

        button {
            background-color: #008cba;
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            text-transform: uppercase;
            display: inline-block;
            font-size: 16px;
            font-weight: bold;
            margin: 4px 2px;
            cursor: pointer;
        }

        button:hover {
            background-color: #005f73;
        }
    </style>
</head>

<body>
    <div class="container">
        
        <div class="message">
            <p>Hello, dear<i> {{ $user->name }}</i>.</p>
            <p>Welcome to the <b>Simplified Payment application</b>.</p>
            <p>Thank you for choose us! 😉</p>

            <button>Visit us here</button>

            <br /><br />

            <p>Best regards.</p>
            <p><b>Simplified Payment Team.</b></p>
        </div>
        
    </div>
</body>
</html>