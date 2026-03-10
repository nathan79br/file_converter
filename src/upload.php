<?php
header("Content-Type: application/json");

$uploadDir = __DIR__ . "/uploads/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Verifica se o arquivo foi enviado
if (!isset($_FILES['file'])) {
    echo json_encode([
        "status" => "error",
        "error" => "Nenhum arquivo enviado"
    ]);
    exit;
}

$file = $_FILES['file'];

// Verifica erro do PHP 
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode([
        "status" => "error",
        "error" => "Erro no upload",
        "code" => $file['error']
    ]);
    exit;
}

// Gera nome seguro 
$filename = uniqid() . "_" . preg_replace("/[^a-zA-Z0-9\._-]/", "", basename($file["name"]));
$targetPath = $uploadDir . $filename;

// Move arquivo
if (!move_uploaded_file($file["tmp_name"], $targetPath)) {
    echo json_encode([
        "status" => "error",
        "error" => "Falha ao salvar o arquivo"
    ]);
    exit;
}

// Retorno 
echo json_encode([
    "status" => "success",
    "filename" => $filename,
    "extension" => strtolower(pathinfo($filename, PATHINFO_EXTENSION)),
    "size" => filesize($targetPath)
]);