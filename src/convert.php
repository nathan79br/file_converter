<?php
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

if (!isset($_POST['filename']) || !isset($_POST['format'])) {
    echo json_encode([
        "success" => false,
        "error" => "Dados não enviados corretamente"
    ]);
    exit;
}

$inputFile = basename($_POST['filename']);
$format    = strtolower($_POST['format']);

$inputPath = __DIR__ . "/../storage/uploads/" . $inputFile;
$outputDir = __DIR__ . "/../storage/converted/";

if (!file_exists($inputPath)) {
    echo json_encode([
        "success" => false,
        "error" => "Arquivo original não encontrado"
    ]);
    exit;
}

$fileName  = pathinfo($inputFile, PATHINFO_FILENAME);
$extension = strtolower(pathinfo($inputFile, PATHINFO_EXTENSION));
$outputFile = $fileName . "." . $format;
$outputPath = $outputDir . $outputFile;

/* Caminhos dos executáveis */
$magickPath      = '"C:\\Program Files\\ImageMagick-7.1.2-Q16-HDRI\\magick.exe"';
$libreOfficePath = '"C:\\Program Files\\LibreOffice\\program\\soffice.exe"';
$ffmpegPath      = '"C:\\ProgramData\\chocolatey\\lib\\ffmpeg-full\\tools\\ffmpeg\\bin\\ffmpeg.exe"';

/* Mapa de conversões permitidas */
$groups = [
    "image"    => ["jpg","jpeg","png","webp","gif","bmp","tiff"],
    "audio"    => ["mp3","wav","ogg","m4a"],
    "video"    => ["mp4","avi","mov","mkv","webm"],
    "document" => ["pdf","docx","odt","xlsx","pptx"]
];

$group = null;
foreach ($groups as $g => $exts) {
    if (in_array($extension, $exts)) {
        $group = $g;
        break;
    }
}

if (!$group || !in_array($format, $groups[$group])) {
    echo json_encode([
        "success" => false,
        "error" => "Conversão não permitida"
    ]);
    exit;
}

/* Monta o comando */
switch ($group) {

    case "image":
        $command = "$magickPath \"$inputPath\" \"$outputPath\"";
        break;

    case "audio":
        $command = "$libreOfficePath --headless --convert-to $format --outdir \"$outputDir\" \"$inputPath\"";
        break;

    case "video":
        $command = "$ffmpegPath -y -i \"$inputPath\" \"$outputPath\"";
        break;

    case "document":
        $command = "$libreOfficePath --headless --convert-to $format --outdir \"$outputDir\" \"$inputPath\"";
        break;
}

exec($command, $output, $returnCode);

/* LibreOffice gera o arquivo com nome fixo */
if ($group === "document") {
    $generated = $outputDir . $fileName . "." . $format;
    if (file_exists($generated) && $generated !== $outputPath) {
        rename($generated, $outputPath);
    }
}

if ($returnCode === 0 && file_exists($outputPath)) {
    echo json_encode([
        "success" => true,
        "download" => "storage/converted/" . $outputFile
    ]);
} else {
    echo json_encode([
        "success" => false,
        "error" => "Falha na conversão",
        "debug" => $output,
        "code" => $returnCode
    ]);
}

$historyFile = __DIR__ . "/data/history.json";
$history = [];

if (file_exists($historyFile)) {
    $history = json_decode(file_get_contents($historyFile), true);
}

$newEntry = [
    "id" => uniqid(),
    "original" => "uploads/" . $inputFile,
    "converted" => "converted/" . $outputFile,
    "name" => $fileName,
    "date" => date("Y-m-d H:i")
];

$history[] = $newEntry;
file_put_contents($historyFile, json_encode($history, JSON_PRETTY_PRINT));