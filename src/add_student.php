<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = mysqli_real_escape_string($connection, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($connection, $_POST['last_name']);
    $student_epita_email = mysqli_real_escape_string($connection, $_POST['student_epita_email']);
    $contact_email = mysqli_real_escape_string($connection, $_POST['contact_email']);
    $contact_address = mysqli_real_escape_string($connection, $_POST['contact_address']);
    $contact_city = mysqli_real_escape_string($connection, $_POST['contact_city']);
    $contact_country = mysqli_real_escape_string($connection, $_POST['contact_country']);
    $contact_birthdate = mysqli_real_escape_string($connection, $_POST['contact_birthdate']);
    $student_enrollment_status = mysqli_real_escape_string($connection, $_POST['student_enrollment_status']);

    $insert_contact_query = "INSERT INTO contacts (contact_email, contact_first_name,
    contact_last_name, contact_address, contact_city, contact_country, contact_birthdate)
    VALUES ('$contact_email', '$first_name', '$last_name', '$contact_address', '$contact_city',
    '$contact_country', '$contact_birthdate')";

    $contact_result = mysqli_query($connection, $insert_contact_query);

    if ($contact_result) {
        $insert_student_query = "INSERT INTO students (student_epita_email, student_contact_ref,
        student_enrollment_status)
        VALUES ('$student_epita_email', '$contact_email', '$student_enrollment_status')";

        $student_result = mysqli_query($connection, $insert_student_query);

        if ($student_result) {
            header("Location: population.php");
            exit();
        } else {
            $error_message = "Error" . mysqli_error($connection);
        }
    } else {
        $error_message = "Error" . mysqli_error($connection);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student Details</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #000;
            color: #fff;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        h1 {
            text-align: center;
            color: #ff5733;
        }

        form {
            max-width: 400px;
            margin: 0 auto;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #fff;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            background-color: rgba(0, 0, 0, 0.8);
            color: #fff;
        }

        select {
            appearance: none;
        }

        input[type="submit"] {
            background-color: #ff5733;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            animation: rgbEffect 5s infinite alternate;
        }

        input[type="submit"]:hover {
            background-color: #ff4500;
        }

        .error {
            color: #ff5733;
            text-align: center;
            margin-top: 10px;
        }

        @keyframes rgbEffect {
            0% {
                background-color: rgb(255, 0, 0);
            }
            25% {
                background-color: rgb(0, 255, 0);
            }
            50% {
                background-color: rgb(0, 0, 255);
            }
            100% {
                background-color: rgb(255, 0, 0);
            }
        }
    </style>
</head>
<body>
    <h1>Add Student Detail</h1>
    <form action="" method="POST">
        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" required>

        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" required>

        <label for="student_epita_email">Student Epita Email:</label>
        <input type="text" name="student_epita_email" required>

        <label for="contact_email">Contact Email:</label>
        <input type="text" name="contact_email" required>

        <label for="contact_address">Address:</label>
        <input type="text" name="contact_address" required>

        <label for="contact_city">City:</label>
        <input type="text" name="contact_city" required>

        <label for="contact_country">Country:</label>
        <input type="text" name="contact_country" required>

        <label for="contact_birthdate">Date of Birth (yyyy-mm-dd):</label>
        <input type="text" name="contact_birthdate" required>

        <label for="student_enrollment_status">Enrollment Status</label>
        <select name="student_enrollment_status" required>
            <option value="completed">completed</option>
            <option value="confirmed">confirmed</option>
            <option value="selected">selected</option>
        </select><br>
        <input type="submit" value="Add Student">
    </form>
    <?php
    if (isset($error_message)) {
        echo '<p class="error">' . $error_message . '</p>';
    }
    ?>
</body>
</html>
