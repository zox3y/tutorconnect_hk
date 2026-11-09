<?php
// db connection (mysqli)

$db_host = 'localhost';
$db_user = 'root';
$db_pass = ''; 
$db_name = 'tutor_platform';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Database Connection Error: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

//
// region/district
//
$hk_regions = [
    "Hong Kong Island" => [
        "Central & Western",
        "Wan Chai",
        "Eastern",
        "Southern"
    ],
    "Kowloon" => [
        "Yau Tsim Mong",
        "Sham Shui Po",
        "Kowloon City",
        "Wong Tai Sin",
        "Kwun Tong"
    ],
    "New Territories" => [
        "Sha Tin",
        "Sai Kung",
        "Tsuen Wan",
        "Kwai Tsing",
        "Tuen Mun",
        "Yuen Long",
        "North District",
        "Tai Po",
        "Tung Chung"
    ]
];