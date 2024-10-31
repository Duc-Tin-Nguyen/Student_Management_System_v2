<?php
include('./Connection.php');

$major = $_GET['major'];

function getDistinctiveStudentInfoByMajor($major) {
    global $mysqli;

    $query = $mysqli->prepare("SELECT sub.STUDENT_EPITA_EMAIL, 
                                    sub.CONTACT_FIRST_NAME, 
                                    sub.CONTACT_LAST_NAME,
                                    SUM(sub.COURSE_PASSED) AS PASSED, 
                                    COUNT(sub.COURSE_PASSED) AS TOTAL
                                FROM (
                                    SELECT s.STUDENT_EPITA_EMAIL, 
                                        c.CONTACT_FIRST_NAME, 
                                        c.CONTACT_LAST_NAME, 
                                        s.STUDENT_POPULATION_CODE_REF, 
                                        g.GRADE_COURSE_CODE_REF,
                                        CASE WHEN ROUND(SUM(g.GRADE_SCORE * e.EXAM_WEIGHT) / SUM(e.EXAM_WEIGHT)) >= 10 THEN 1 ELSE 0 END AS COURSE_PASSED
                                    FROM STUDENTS s
                                    INNER JOIN CONTACTS c ON s.STUDENT_CONTACT_REF = c.CONTACT_EMAIL
                                    INNER JOIN GRADES g ON s.STUDENT_EPITA_EMAIL = g.GRADE_STUDENT_EPITA_EMAIL_REF
                                    INNER JOIN EXAMS e ON g.GRADE_COURSE_CODE_REF = e.EXAM_COURSE_CODE
                                    WHERE s.STUDENT_POPULATION_CODE_REF = ?
                                    GROUP BY s.STUDENT_EPITA_EMAIL, 
                                        c.CONTACT_FIRST_NAME, 
                                        c.CONTACT_LAST_NAME, 
                                        s.STUDENT_POPULATION_CODE_REF, 
                                        g.GRADE_COURSE_CODE_REF
                                ) AS sub
                                GROUP BY sub.STUDENT_EPITA_EMAIL, 
                                    sub.CONTACT_FIRST_NAME, 
                                    sub.CONTACT_LAST_NAME;"
    );

    if (!$query) {
        die("Query failed: " . $mysqli->error);
    }

    $query->bind_param("s", $major);
    $query->execute();
    $result = $query->get_result();

    $distinctiveStudentInfoByMajor = array();
    while ($row = $result->fetch_assoc()) {
        $distinctiveStudentInfoByMajor[] = $row;
    }

    return $distinctiveStudentInfoByMajor;
}

function addStudent($email, $firstName, $lastName) {
    global $mysqli;
    global $major;

    $query = $mysqli->prepare("INSERT INTO STUDENTS (STUDENT_EPITA_EMAIL, STUDENT_POPULATION_CODE_REF) 
                                SELECT ?, ?
                                WHERE NOT EXISTS (
                                    SELECT 1 FROM STUDENTS
                                    WHERE STUDENT_EPITA_EMAIL = ? AND STUDENT_POPULATION_CODE_REF = ?
                                )");

    if (!$query) {
        die("Query preparation failed: " . $mysqli->error);
    }

    $populationCode = $major;

    $query->bind_param("ssss", $email, $populationCode, $email, $populationCode);

    if ($query->execute()) {
        return true;
    } else {
        return false;
    }
}

function getCourseDataForMajor($major) {
    global $mysqli;

    $query = $mysqli->prepare("SELECT
        c.course_code,
        c.course_name,
        COUNT(*) AS session_count
    FROM courses c
    JOIN sessions s ON c.course_code = s.session_course_ref
    JOIN programs p ON c.course_code = p.program_course_code_ref
    WHERE p.program_assignment LIKE CONCAT(?, '%')
    GROUP BY
        c.course_code, 
        c.course_name;"
    );

    if (!$query) {
        die("Query preparation failed: " . $mysqli->error);
    }

    $query->bind_param("s", $major);
    $query->execute();
    $result = $query->get_result();

    $courseData = array();
    while ($row = $result->fetch_assoc()) {
        $courseData[] = $row;
    }

    return $courseData;
}

$distinctiveStudentData = getDistinctiveStudentInfoByMajor($major);
$courseData = getCourseDataForMajor($major);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['studentEmail']) && isset($_POST['firstName']) && isset($_POST['lastName'])) {
    $studentEmail = $_POST['studentEmail'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];

    if (addStudent($studentEmail, $firstName, $lastName)) {
        
    } else {
        
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $major ?> Data</title>
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

        .color-change {
            animation: text-color-change 5s infinite alternate;
        }

        a.color-change {
            animation: text-color-change 5s infinite alternate;
        }

        .button.color-change {
            animation: button-color-change 5s infinite alternate;
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

        h1, h2, th, td {
            animation: none;
        }

        h2 {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #000;
            border: 2px solid rgba(255, 255, 255, 0.2);
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: center;
            vertical-align: middle;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        th.color-change, td.color-change {
            animation: text-color-change 5s infinite alternate;
        }

        th {
            background-color: rgba(0, 0, 0, 0.8);
            color: #ff5733;
        }

        tr {
            background-color: rgba(0, 0, 0, 0.8);
            color: #000;
            transition: background-color 0.3s ease-in-out;
        }

        tr:hover {
            background-color: rgba(255, 87, 51, 0.5);
        }

        a.button {
            background-color: #ff5733;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 3px;
            font-size: 16px;
            text-decoration: none;
            transition: background-color 0.3s ease-in-out;
            display: inline-block;
        }

        a.button.color-change {
            animation: button-color-change 5s infinite alternate;
        }

        td.white-text {
            color: #fff;
        }
    </style>
</head>
<body>
    <h1 class="color-change"><?= $major ?></h1>

    <h2 class="color-change">Distinctive Student Information</h2>
    <a href="add_student.php" class="button color-change">Add Student</a>
    <input type="text" id="studentSearchInput" class="color-change" placeholder="Search Students" oninput="searchStudents('studentTable')">

    <table id="studentTable" border="1">
        <tr>
            <th class="color-change">Student Email</th>
            <th class="color-change">Contact First Name</th>
            <th class="color-change">Contact Last Name</th>
            <th class="color-change">PASSED</th>
            <th class="color-change">TOTAL</th>
            <th class="color-change">Action</th>
        </tr>
        <?php foreach ($distinctiveStudentData as $row) : ?>
        <tr>
            <td><a href="student_grade.php?email=<?= $row['STUDENT_EPITA_EMAIL'] ?>" class="color-change"><?= $row['STUDENT_EPITA_EMAIL'] ?></a></td>
            <td class="white-text"><?= $row['CONTACT_FIRST_NAME'] ?></td>
            <td class="white-text"><?= $row['CONTACT_LAST_NAME'] ?></td>
            <td class="white-text"><?= $row['PASSED'] ?></td>
            <td class="white-text"><?= $row['TOTAL'] ?></td>
            <td class="color-change">
                <a href="edit_student.php?email=<?= $row['STUDENT_EPITA_EMAIL'] ?>" class="color-change">Edit</a>
                <a href="#" onclick="showConfirmationModal('<?= $row['STUDENT_EPITA_EMAIL'] ?>')" class="color-change">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <div id="addStudentModal" style="display: none;">
        <form method="post" action="add_student.php">
            <label for="studentEmail">Student Email:</label>
            <input type="email" id="studentEmail" name="studentEmail" required>

            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" name="firstName" required>

            <label for="lastName">Last Name:</label>
            <input type="text" id="lastName" name="lastName" required>

            <button type="submit" class="button color-change">Save</button>
        </form>
    </div>

    <h2 class="color-change">Course Data</h2>
    <a href="add_course.php" class="button color-change">Add Course</a>
    <input type="text" id="courseSearchInput" class="color-change" placeholder="Search Courses" oninput="searchStudents('courseTable')">

    <table id="courseTable" border="1">
        <tr>
            <th class="color-change">Course Code</th>
            <th class="color-change">Course Name</th>
            <th class="color-change">Session Count</th>
        </tr>
        <?php foreach ($courseData as $row) : ?>
        <tr>
            <td><a href="course_grade.php?course_code=<?= $row['course_code'] ?>" class="color-change"><?= $row['course_code'] ?></a></td>
            <td class="white-text"><?= $row['course_name'] ?></td>
            <td class="white-text"><?= $row['session_count'] ?></td>
            <td class="color-change">
                <a href="edit_course.php?course_code=<?= $row['course_code'] ?>" class="color-change">Edit</a>
                <a href="#" onclick="showCourseConfirmationModal('<?= $row['course_code'] ?>')" class="color-change">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <div id="addCourseModal" style="display: none;">
        <form method="post" action="add_course.php">
            <label for="courseCode">Course Code:</label>
            <input type="text" id="courseCode" name="courseCode" required>

            <label for="courseName">Course Name:</label>
            <input type="text" id="courseName" name="courseName" required>

            <button type="submit" class="button color-change">Save</button>
        </form>
    </div>

    <script>
        function showAddStudentModal() {
            document.getElementById("addStudentModal").style.display = "block";
        }

        function showConfirmationModal(email) {
            if (confirm("Are you sure you want to delete this student?")) {
                window.location.href = "delete.php?email=" + email;
            }
        }

        function showCourseConfirmationModal(courseCode) {
            if (confirm("Are you sure you want to delete this course?")) {
                window.location.href = "delete_course.php?course_code=" + courseCode;
            }
        }

        function searchStudents(tableId) {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById(tableId === 'studentTable' ? "studentSearchInput" : "courseSearchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById(tableId);
            tr = table.getElementsByTagName("tr");

            for (i = 1; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>
</body>
</html>
