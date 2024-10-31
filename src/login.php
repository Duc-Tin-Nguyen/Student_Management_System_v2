<?php
include('connection.php');
session_start();

$loginError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($mysqli, $_POST['username']);
    $password = mysqli_real_escape_string($mysqli, $_POST['password']);
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $mysqli->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $_SESSION['firstname'] = $row['firstname'];
        echo("<script>alert('WELCOME, ".$row['username']."!'); window.location.href='welcome.php';</script>");
        exit();
    } else {
        $loginError = 'Incorrect username or password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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

        .login-container {
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

        .login-form {
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

        .login-btn {
            background-color: #ff5733;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 3px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }

        .login-btn:hover {
            background-color: #ff4500;
            box-shadow: 0 0 20px rgba(255, 87, 51, 0.5);
        }

        .error-msg {
            color: #ff0000;
            margin-top: 10px;
        }

        .registration-link {
            margin-top: 10px;
        }

        a {
            text-decoration: none;
            color: #ff5733;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h1>Login</h1>
        <?php if (!empty($loginError)) { ?>
            <p class="error-msg"><?php echo $loginError; ?></p>
        <?php } ?>
        <form class="login-form" method="POST" onsubmit="return validateForm()">
            <input class="form-input" type="text" name="username" id="username" placeholder="Username" required>
            <span id="usernameError" class="error-msg"></span><br>
            <input class="form-input" type="password" name="password" id="password" placeholder="Password" required>
            <span id="passwordError" class="error-msg"></span><br>
            <button class="login-btn" type="submit">Login</button>
        </form>
        <div class="registration-link">
            <p>Don't have an account? <a href="register.php">Register</a></p>
        </div>
    </div>

    <script>
        function validateForm() {
            var username = document.getElementById("username").value;
            var password = document.getElementById("password").value;
            var usernameError = document.getElementById("usernameError");
            var passwordError = document.getElementById("passwordError");

            usernameError.textContent = "";
            passwordError.textContent = "";

            if (username === "") {
                usernameError.textContent = "Please fill out this field.";
                return false;
            }

            if (password === "") {
                passwordError.textContent = "Please fill out this field.";
                return false;
            }

            return true;
        }
    </script>
</body>

</html>
