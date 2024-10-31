<?php
include('./Connection.php');

$email = $_GET['email'];

function getStudentGradeData($email) {
    global $mysqli;

    $query = $mysqli->prepare("SELECT grade_course_code_ref, grade_score FROM grades WHERE grade_student_epita_email_ref = ?");

    if (!$query) {
        die("Query preparation failed: " . $mysqli->error);
    }

    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();

    $gradeData = array();
    while ($row = $result->fetch_assoc()) {
        $gradeData[] = $row;
    }

    return $gradeData;
}

$studentGradeData = getStudentGradeData($email);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $email ?> Grade Data</title>
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

        header {
            text-align: center;
            background-color: #ff5733;
            padding: 20px;
        }

        h1 {
            color: #fff;
            animation: text-color-change 5s infinite alternate;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        table {
            border-collapse: collapse;
            width: 80%;
            margin-top: 20px;
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
            border: 1px solid black;
        }

        th, td {
            padding: 12px;
            text-align: center;
            color: #fff;
            animation: text-color-change 5s infinite alternate;
        }

        th {
            background-color: rgba(255, 87, 51, 0.8);
        }

        @keyframes text-color-change {
            0% {
                color: #ff5733;
            }
            50% {
                color: #00ff00;
            }
            100% {
                color: #0000ff;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1><?= $email ?></h1>
    </header>
    
    <div class="container">
        <table>
            <tr>
                <th>Course Code</th>
                <th>Grade Score</th>
            </tr>
            <?php foreach ($studentGradeData as $row) : ?>
                <tr>
                    <td><?= $row['grade_course_code_ref'] ?></td>
                    <td><?= $row['grade_score'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
