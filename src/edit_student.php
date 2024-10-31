<?php
include('./Connection.php');

// Check if the student email is provided in the URL parameter
if (isset($_GET['email'])) {
    $studentEmail = $_GET['email'];

    // Fetch the student information based on the provided email
    $query = $mysqli->prepare("SELECT s.STUDENT_EPITA_EMAIL, 
                                    c.CONTACT_FIRST_NAME, 
                                    c.CONTACT_LAST_NAME,
                                    s.STUDENT_POPULATION_CODE_REF
                                FROM STUDENTS s
                                INNER JOIN CONTACTS c ON s.STUDENT_CONTACT_REF = c.CONTACT_EMAIL
                                WHERE s.STUDENT_EPITA_EMAIL = ?");

    if (!$query) {
        die("Query preparation failed: " . $mysqli->error);
    }

    $query->bind_param("s", $studentEmail);
    $query->execute();
    $result = $query->get_result();

    // Check if the student exists
    if ($result->num_rows > 0) {
        $studentData = $result->fetch_assoc();
        $major = $studentData['STUDENT_POPULATION_CODE_REF']; // Get the major name
    } else {
        // Handle the case where the student doesn't exist
        die("Student not found.");
    }

    // Check if the form for updating student details is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['newFirstName']) && isset($_POST['newLastName'])) {
        $newFirstName = $_POST['newFirstName'];
        $newLastName = $_POST['newLastName'];

        // Update the student's contact information
        $updateQuery = $mysqli->prepare("UPDATE CONTACTS
                                         SET contact_first_name = ?, contact_last_name = ?
                                         WHERE contact_email = (SELECT student_contact_ref FROM STUDENTS WHERE student_epita_email = ?)");

        if (!$updateQuery) {
            die("Update query preparation failed: " . $mysqli->error);
        }

        $updateQuery->bind_param("sss", $newFirstName, $newLastName, $studentEmail);

        if ($updateQuery->execute()) {
            // Student details updated successfully
            // Redirect back to the page that contains the table of students for the specific major
            header("Location: population.php?major=$major");
            exit;
        } else {
            // Error updating student details
            // You can handle the error here
        }
    }
} else {
    // Handle the case where the student email is not provided in the URL parameter
    die("Student email not provided.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student Details</title>
    <style>
        /* Add the CSS styles from your previous code here */
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
            color: #ff5733;
            font-family: 'Cursive', sans-serif; 
        }

        label {
            color: #fff;
            font-family: 'Verdana', sans-serif; 
        }

        input[type="email"],
        input[type="text"] {
            padding: 10px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            background-color: rgba(0, 0, 0, 0.8);
            color: #fff;
            font-family: 'Verdana', sans-serif;
            margin: 5px 0;
            width: 100%;
        }

        button[type="submit"] {
            background-color: #ff5733;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 3px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            animation: rgbEffect 5s infinite alternate;
            font-family: 'Cursive', sans-serif; 
        }

        button[type="submit"]:hover {
            background-color: #ff4500;
            box-shadow: 0 0 20px rgba(255, 87, 51, 0.5);
            color: #fff;
            background-color: rgba(Math.floor(Math.random() * 256), Math.floor(Math.random() * 256), Math.floor(Math.random() * 256));
        }
    </style>
</head>
<body>
    <h1>Edit Student Details</h1>

    <h2>Student Information</h2>
    <form method="post" action="">
        <label for="studentEmail">Student Email:</label>
        <input type="email" id="studentEmail" name="studentEmail" value="<?= $studentData['STUDENT_EPITA_EMAIL'] ?>" readonly>

        <label for="newFirstName">New First Name:</label>
        <input type="text" id="newFirstName" name="newFirstName" value="<?= $studentData['CONTACT_FIRST_NAME'] ?>" required>

        <label for="newLastName">New Last Name:</label>
        <input type="text" id="newLastName" name="newLastName" value="<?= $studentData['CONTACT_LAST_NAME'] ?>" required>

        <button type="submit">Save Changes</button>
    </form>
</body>
</html>
