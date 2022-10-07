<?php

    $student_name = "none";
    if (!isset($_COOKIE["name"])) {
        header("Location: login.php");
        return;
    } else {
        $student_name = $_COOKIE["name"];
    }
    $connection = new mysqli("localhost", "root", "", "student_portal");
    $sql = "SELECT id FROM students WHERE name = '$student_name'";
    $id = $connection->query($sql)->fetch_assoc()["id"];

    if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
        $code = file_get_contents("php://input");
        $sql = "DELETE FROM student_course WHERE student_id = '$id' AND course_code = '$code'";
        $result = $connection->query($sql);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $code = file_get_contents("php://input");
        $sql = "INSERT INTO student_course(course_code, student_id) VALUES ('$code', $id)";
        $result = $connection->query($sql);
    }

    $connection->close();

?>