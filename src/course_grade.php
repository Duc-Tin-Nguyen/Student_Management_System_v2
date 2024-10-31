<?php
include('./Connection.php');

$courseCode = $_GET['course_code'];

function getCourseGradeData($courseCode) {
    global $mysqli;

    $query = $mysqli->prepare("SELECT s.student_epita_email, c.contact_first_name, c.contact_last_name, g.grade_score
        FROM students s
        JOIN contacts c ON s.student_contact_ref = c.contact_email 
        LEFT JOIN grades g ON s.student_epita_email = g.grade_student_epita_email_ref
        WHERE g.grade_course_code_ref = ?
        ORDER BY s.student_epita_email, g.grade_course_code_ref");
    
    if (!$query) {
        die("Query preparation failed: " . $mysqli->error);
    }

    $query->bind_param("s", $courseCode);
    $query->execute();
    $result = $query->get_result();

    $courseGradeData = array();
    while ($row = $result->fetch_assoc()) {
        $courseGradeData[] = $row;
    }

    return $courseGradeData;
}

function deleteStudentGrade($studentEmail, $courseCode) {
    global $mysqli;

    $deleteQuery = $mysqli->prepare("DELETE FROM grades WHERE grade_student_epita_email_ref = ? AND grade_course_code_ref = ?");
    
    if (!$deleteQuery) {
        die("Delete query preparation failed: " . $mysqli->error);
    }

    $deleteQuery->bind_param("ss", $studentEmail, $courseCode);

    if ($deleteQuery->execute()) {
        return true;
    } else {
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission to update grade
    $studentEmail = $_POST['student_email'];
    $newGrade = $_POST['new_grade'];

    $updateQuery = $mysqli->prepare("UPDATE grades
        SET grade_score = ?
        WHERE grade_student_epita_email_ref = ? AND grade_course_code_ref = ?");
    
    if (!$updateQuery) {
        die("Update query preparation failed: " . $mysqli->error);
    }

    $updateQuery->bind_param("iss", $newGrade, $studentEmail, $courseCode);

    if ($updateQuery->execute()) {
        header("Location: update_course_grade.php?course_code=$courseCode");
        exit();
    } else {
        echo "Grade update failed: " . $mysqli->error;
    }
}

$courseGradeData = getCourseGradeData($courseCode);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Grade Data for <?= $courseCode ?></title>
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
            margin: 20px;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #ff5733;
        }

        table {
            border-collapse: collapse;
            width: 80%;
            margin: 20px auto;
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
            border: 1px solid black;
        }

        th, td {
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: rgba(255, 87, 51, 0.8);
        }

        .rgb-effect {
            animation: text-color-change 5s infinite alternate;
        }

        .update-button {
            background-color: #ff5733;
            color: #fff;
            border: none;
            padding: 6px 12px;
            margin-left: 10px;
            cursor: pointer;
        }

        .update-button:hover {
            animation: button-color-change 5s infinite alternate;
        }

        @keyframes text-color-change {
            0% {
                color: rgb(255, 87, 51);
            }
            50% {
                color: rgb(0, 255, 0);
            }
            100% {
                color: rgb(0, 0, 255);
            }
        }

        @keyframes button-color-change {
            0% {
                background-color: #ff5733;
            }
            50% {
                background-color: #00ff00;
            }
            100% {
                background-color: #0000ff;
            }
        }
    </style>
</head>
<body>
    <h1>Course Grade Data for <?= $courseCode ?></h1>
    <table>
        <tr>
            <th class="rgb-effect">Student Email</th>
            <th class="rgb-effect">Contact First Name</th>
            <th class="rgb-effect">Contact Last Name</th>
            <th class="rgb-effect">Grade Score</th>
            <th>Update Grade</th>
        </tr>
        <?php foreach ($courseGradeData as $row) : ?>
            <tr>
                <td class="rgb-effect"><?= $row['student_epita_email'] ?></td>
                <td class="rgb-effect"><?= $row['contact_first_name'] ?></td>
                <td class="rgb-effect"><?= $row['contact_last_name'] ?></td>
                <td class="rgb-effect"><?= $row['grade_score'] ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="student_email" value="<?= $row['student_epita_email'] ?>">
                        <input type="number" name="new_grade" value="<?= $row['grade_score'] ?>">
                        <button type="submit" class="update-button">Update</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
