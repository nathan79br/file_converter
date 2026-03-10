<?php
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

if (!isset($_POST['file'], $_POST['quality'])) {
    echo json_encode([
        "success" => false,
        "error" => "Dados não enviados corretamente"
    ]);
    exit;
}

$inputFile = basename($_POST['file']);
$quality   = $_POST['quality'];

$allowedQuality = ["max", "high", "balanced"];
if (!in_array($quality, $allowedQuality)) {
    $quality = "balanced";
}

$inputPath = __DIR__ . "/uploads/" . $inputFile;
$outputDir = __DIR__ . "/compressed/";

if (!file_exists($inputPath)) {
    echo json_encode([
        "success" => false,
        "error" => "Arquivo original não encontrado"
    ]);
    exit;
}

$fileName  = pathinfo($inputFile, PATHINFO_FILENAME);
$extension = strtolower(pathinfo($inputFile, PATHINFO_EXTENSION));
$outputFile = $fileName . "_compressed." . $extension;
$outputPath = $outputDir . $outputFile;

// Caminhos
$magickPath      = '"C:\\Program Files\\ImageMagick-7.1.2-Q16-HDRI\\magick.exe"';
$libreOfficePath = '"C:\\Program Files\\LibreOffice\\program\\soffice.exe"';
$ffmpegPath      = '"C:\\ProgramData\\chocolatey\\lib\\ffmpeg-full\\tools\\ffmpeg\\bin\\ffmpeg.exe"';

// Grupos 
$image = ["jpg","jpeg","png","webp","gif","bmp","tiff"];
$audio = ["mp3","wav","ogg","m4a"];
$video = ["mp4","avi","mov","mkv","webm"];

    // Compressão de imagem
if (in_array($extension, $image)) {

    switch ($quality) {
        case "max":  $q = 90; break;
        case "high": $q = 85; break;
        default:     $q = 80;
    }

    $command = "$magickPath \"$inputPath\" -strip -quality $q \"$outputPath\"";

    //compressão para audio
} elseif (in_array($extension, $audio)) {

    switch ($quality) {
        case "max":  $bitrate = "256k"; break;
        case "high": $bitrate = "192k"; break;
        default:     $bitrate = "160k";
    }

    $command = "$ffmpegPath -y -i \"$inputPath\" -ab $bitrate \"$outputPath\"";

    //compresssão para video
} elseif (in_array($extension, $video)) {

    switch ($quality) {
        case "max":  $crf = 18; break;
        case "high": $crf = 20; break;
        default:     $crf = 22;
    }

    $command = "$ffmpegPath -y -i \"$inputPath\" -vcodec libx264 -crf $crf \"$outputPath\"";

    //Erro arquivo não suportado
} else {
    echo json_encode([
        "success" => false,
        "error" => "Tipo de arquivo não suportado para compressão"
    ]);
    exit;
}

exec($command, $output, $code);

    //comprimido com sucesso
if ($code === 0 && file_exists($outputPath)) {
    echo json_encode([
        "success" => true,
        "download" => "compressed/" . $outputFile
    ]);

    //falha na compressão
} else {
    echo json_encode([
        "success" => false,
        "error" => "Falha na compressão",
        "debug" => $output,
        "code" => $code
    ]);
}