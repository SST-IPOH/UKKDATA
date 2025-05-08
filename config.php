<?php
// error_reporting(E_ALL); // Uncomment untuk debug
// ini_set('display_errors', 1); // Uncomment untuk debug

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$servername = "localhost";
$db_username = "root"; // Guna nama pembolehubah berbeza jika mahu
$db_password = "";
$database = "sdp_db";

$conn = new mysqli($servername, $db_username, $db_password, $database);

if ($conn->connect_error) {
    http_response_code(500);
    error_log("DB Connection Error: (" . $conn->connect_errno . ") " . $conn->connect_error);
    die("Sambungan Gagal."); // Ringkas untuk produksi
}

if (!$conn->set_charset("utf8mb4")) {
    error_log("Error loading character set utf8mb4: " . $conn->error);
}
?>