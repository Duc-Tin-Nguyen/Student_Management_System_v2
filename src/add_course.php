<?php
include 'connection.php';

$errors = [];
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $requiredFields = [
        'courseCode', 'rev', 'epitaemail', 'startdate', 'starttime', 'endtime', 'type', 'year',
        'period', 'room', 'duration', 'courseName', 'description', 'program', 'contactemail',
        'firstname', 'lastname', 'level'
    ];
    
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $errors[] = "Required field $field is missing.";
        }
    }

    if (empty($errors)) {
        $sql1 = "INSERT INTO sessions (courseCode, rev, epitaemail, startdate, starttime, endtime, type, year, period, room) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $sql2 = "INSERT INTO courses (courseCode, rev, duration, year, courseName, description) VALUES (?, ?, ?, ?, ?, ?)";
        $sql3 = "INSERT INTO programs (courseCode, rev, program) VALUES (?, ?, ?)";
        $sql4 = "INSERT INTO teachers (contactemail, epitaemail, level) VALUES (?, ?, ?)";
        $sql5 = "INSERT INTO contacts (contactemail, firstname, lastname) VALUES (?, ?, ?)";

        $stmt1 = $conn->prepare($sql1);
        $stmt2 = $conn->prepare($sql2);
        $stmt3 = $conn->prepare($sql3);
        $stmt4 = $conn->prepare($sql4);
        $stmt5 = $conn->prepare($sql5);

        $stmt1->bind_param("sssssssssss", $_POST['courseCode'], $_POST['rev'], $_POST['epitaemail'], $_POST['startdate'], $_POST['starttime'], $_POST['endtime'], $_POST['type'], $_POST['year'], $_POST['period'], $_POST['room']);
        $stmt2->bind_param("ssssss", $_POST['courseCode'], $_POST['rev'], $_POST['duration'], $_POST['year'], $_POST['courseName'], $_POST['description']);
        $stmt3->bind_param("sss", $_POST['courseCode'], $_POST['rev'], $_POST['program']);
        $stmt4->bind_param("sss", $_POST['contactemail'], $_POST['epitaemail'], $_POST['level']);
        $stmt5->bind_param("sss", $_POST['contactemail'], $_POST['firstname'], $_POST['lastname']);

        $success = $stmt1->execute() && $stmt2->execute() && $stmt3->execute() && $stmt4->execute() && $stmt5->execute();

        $stmt1->close();
        $stmt2->close();
        $stmt3->close();
        $stmt4->close();
        $stmt5->close();

        if ($success) {
            $message = "New records successfully created!";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Entry Form</title>
</head>
<body>
    <?php if (!empty($errors)) { ?>
    <ul>
        <?php foreach ($errors as $error) { ?>
        <li><?php echo $error; ?></li>
        <?php } ?>
    </ul>
    <?php } ?>

    <?php if (!empty($message)) { ?>
    <p><?php echo $message; ?></p>
    <?php } ?>

    <form method="post" action="">
        <label for="courseCode">Course Code:</label>
        <input type="text" name="courseCode" required><br>
        
        <label for="rev">Revision:</label>
        <input type="text" name="rev" required><br>

        <label for="epitaemail">Epita Email:</label>
        <input type="email" name="epitaemail" required><br>

        <input type="submit" value="Submit">
    </form>
</body>
</html>
