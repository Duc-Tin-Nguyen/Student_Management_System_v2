<?php
include('./Connection.php');

// Check if the student email is provided in the URL parameter
if (isset($_GET['email'])) {
    $studentEmailToDelete = $_GET['email'];

    // Fetch the student's major based on the provided email
    $query = $mysqli->prepare("SELECT STUDENT_POPULATION_CODE_REF FROM STUDENTS WHERE STUDENT_EPITA_EMAIL = ?");

    if (!$query) {
        die("Query preparation failed: " . $mysqli->error);
    }

    $query->bind_param("s", $studentEmailToDelete);
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

    // Delete attendance records for the student
    $deleteAttendanceQuery = $mysqli->prepare("DELETE FROM attendance WHERE attendance_student_ref = ?");
    $deleteAttendanceQuery->bind_param("s", $studentEmailToDelete);

    // Delete grade records for the student
    $deleteGradesQuery = $mysqli->prepare("DELETE FROM grades WHERE grade_student_epita_email_ref = ?");
    $deleteGradesQuery->bind_param("s", $studentEmailToDelete);

    // Delete the student record
    $deleteStudentQuery = $mysqli->prepare("DELETE FROM students WHERE student_epita_email = ?");
    $deleteStudentQuery->bind_param("s", $studentEmailToDelete);

    // Execute the queries
    if ($deleteAttendanceQuery->execute() && $deleteGradesQuery->execute() && $deleteStudentQuery->execute()) {
        // Redirect back to the page that contains the table of students for the specific major
        header("Location: population.php?major=" . $major);
        exit();
    } else {
        // Handle any errors that may occur during deletion
        echo "Error deleting the student: " . $mysqli->error;
    }

    // Close the query connections
    $deleteAttendanceQuery->close();
    $deleteGradesQuery->close();
    $deleteStudentQuery->close();
} else {
    // Handle the case where no email parameter is provided in the URL
    echo "No email parameter provided.";
}
?>
