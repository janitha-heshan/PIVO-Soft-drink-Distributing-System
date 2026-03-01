<?php
$conn = new mysqli("localhost", "root", "", "pivo_holdings_db");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }
?>