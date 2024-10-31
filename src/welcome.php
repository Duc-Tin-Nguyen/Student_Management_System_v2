<?php
include('./Connection.php');

function getPopulationCount() {
    global $mysqli;
    $query = $mysqli->prepare("SELECT STUDENT_POPULATION_CODE_REF, COUNT(*) AS population_count FROM STUDENTS s GROUP BY STUDENT_POPULATION_CODE_REF;");
    if (!$query) {
        die("Query failed: " . $mysqli->error);
    }
    $query->execute();
    $result = $query->get_result();
    $populationData = array();
    while ($row = $result->fetch_assoc()) {
        $populationData[] = $row;
    }
    return $populationData;
}

$populationData = getPopulationCount();

function getAttendance() {
    global $mysqli;
    $query = $mysqli->prepare("SELECT STUDENT_POPULATION_CODE_REF, SUM(a.ATTENDANCE_PRESENCE) AS present_count FROM STUDENTS s JOIN ATTENDANCE a ON s.STUDENT_EPITA_EMAIL = a.ATTENDANCE_STUDENT_REF GROUP BY STUDENT_POPULATION_CODE_REF;");
    if (!$query) {
        die("Query failed: " . $mysqli->error);
    }
    $query->execute();
    $result = $query->get_result();
    $attendanceData = array();
    while ($row = $result->fetch_assoc()) {
        $attendanceData[$row['STUDENT_POPULATION_CODE_REF']] = $row['present_count'];
    }
    return $attendanceData;
}

$attendanceData = getAttendance();

if (isset($_POST['logout'])) {
    session_start();
    session_destroy();
    header("Location: login.php");
    exit();
}

$name = "Guest";

if (isset($userId) && isset($username)) {
    $sql = "SELECT name FROM users WHERE username = '$username' AND id = $userId";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $name = $row['name'];
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

        .login-container {
            background-color: rgba(0, 0, 0, 0.8);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            max-width: 800px;
            width: 100%;
            margin: 20px;
            box-shadow: 0px 0px 10px rgba(255, 255, 255, 0.2);
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

        h1 {
            color: #ff5733;
            font-family: 'Cursive', sans-serif; 
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: rgba(0, 0, 0, 0.8);
            border: 2px solid rgba(255, 255, 255, 0.2); 
        }

        th, td {
            padding: 12px;
            text-align: center; 
            vertical-align: middle; 
            border: 2px solid rgba(255, 255, 255, 0.2); 
            color: #fff; 
            font-family: 'Verdana', sans-serif; 
        }

        th {
            background-color: rgba(0, 0, 0, 0.8); 
            color: #ff5733; 
        }

        /* Table row styles based on header values */
        tr.Population td:nth-child(1) {
            background-color: #445573; 
        }

        /* RGB hover effect for table rows */
        tr:hover {
            background-color: rgba(255, 87, 51, 0.5);
        }

        a {
            color: #ff5733;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .chart-note {
            font-size: 12px;
            color: #ccc;
            margin-top: 10px;
        }

        .table-container {
            width: 100%;
            margin: 20px 0;
        }

        .table-container table {
            width: 100%;
        }

        .chart-container {
            display: inline-block;
            width: 48%;
            margin: 1%;
            vertical-align: top;
        }

        .form-container {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        /* Modified logout button styles with RGB effect */
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
    <div class="form-container">
        <form method="POST">
            <button type="submit" name="logout" id="logoutButton">Logout</button>
        </form>
    </div>
    <div class="login-container">
        <div class="table-container">
            <table>
                <tr>
                    <th>Population</th>
                    <th>Count</th>
                </tr>
                <?php foreach ($populationData as $data) { ?>
                    <tr>
                        <td><a href="population.php?major=<?php echo urlencode($data['STUDENT_POPULATION_CODE_REF']); ?>"><?php echo $data['STUDENT_POPULATION_CODE_REF']; ?></a></td>
                        <td><?php echo $data['population_count']; ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>

        <div class="chart-container">
            <canvas id="populationChart"></canvas>
            <div class="chart-note">Click the chart to see details</div>
        </div>
    </div>

    <div class="login-container">
        <div class="table-container">
            <table>
                <tr>
                    <th>Major</th>
                    <th>Attendance Count</th>
                </tr>
                <?php foreach ($attendanceData as $major => $count) { ?>
                    <tr>
                        <td><a href="population.php?major=<?php echo urlencode($major); ?>"><?php echo $major; ?></a></td>
                        <td><?php echo $count; ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>

        <div class="chart-container">
            <canvas id="attendanceChart"></canvas>
            <div class="chart-note">Click the chart to see details</div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var populationData = <?php echo json_encode($populationData); ?>;
        var attendanceData = <?php echo json_encode($attendanceData); ?>;
    
        var populationCanvas = document.getElementById('populationChart');
        var attendanceCanvas = document.getElementById('attendanceChart');

        var populationChart = new Chart(populationCanvas, {
            type: 'pie',
            data: {
                labels: populationData.map(function (data) { return data.STUDENT_POPULATION_CODE_REF; }),
                datasets: [{
                    data: populationData.map(function (data) { return data.population_count; }),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)',
                    ],
                }],
            },
        });

        var attendanceChart = new Chart(attendanceCanvas, {
            type: 'bar',
            data: {
                labels: Object.keys(attendanceData),
                datasets: [{
                    label: 'Attendance Count',
                    data: Object.values(attendanceData),
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                }],
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                    },
                },
            },
        });
    </script>
</body>

</html>
