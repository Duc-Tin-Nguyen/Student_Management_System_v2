<?php
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($mysqli, $_POST['username']);
    $email = mysqli_real_escape_string($mysqli, $_POST['email']);
    $password = mysqli_real_escape_string($mysqli, $_POST['password']);

    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";

    if ($mysqli->query($sql) === TRUE) {
        header("Location: login.php");
        exit();
    } else {
        echo 'Error: ' . $mysqli->error;
    }

    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', serif;
            background-color: #000;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #fff;
        }

        .registration-container {
            background-color: rgba(0, 0, 0, 0.8);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            max-width: 400px;
            width: 100%;
            /* RGB color effect */
            animation: rgbEffect 5s infinite alternate;
        }

        @keyframes rgbEffect {
            0% {
                filter: hue-rotate(0deg);
            }
            100% {
                filter: hue-rotate(360deg);
            }
        }

        .registration-form {
            margin-top: 20px;
        }

        .form-input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
            transition: background-color 0.3s ease-in-out;
        }

        .form-input::placeholder {
            color: #ccc;
        }

        .form-input:focus {
            background-color: rgba(255, 255, 255, 0.3);
        }

        .register-btn {
            background-color: #ff5733;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 3px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }

        .register-btn:hover {
            background-color: #ff4500;
            box-shadow: 0 0 20px rgba(255, 87, 51, 0.5);
        }
    </style>
</head>

<body>
    <div class="registration-container">
        <h1>Registration</h1>
        <form class="registration-form" method="POST">
            <input class="form-input" type="text" name="username" placeholder="Username" required><br>
            <input class="form-input" type="text" name="email" placeholder="Email" required><br>
            <input class="form-input" type="password" name="password" placeholder="Password" required><br>
            <button class="register-btn" type="submit">Register</button>
        </form>
    </div>
</body>

</html>
