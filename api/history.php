<?php
header("Content-Type: application/json");
$file = __DIR__ . "/../data/history.json";

if (!file_exists($file)) {
    echo json_encode([]);
    exit;
}

echo file_get_contents($file);