<?php
header('Content-Type: application/json');

require_once '../lib/config.php';
require_once '../lib/db.php';
require_once '../lib/printdb.php';
require_once '../lib/image.php';
require_once '../lib/log.php';

if (empty($_GET['filename'])) {
    $errormsg = basename($_SERVER['PHP_SELF']) . ': No file provided!';
    logErrorAndDie($errormsg);
}

if (isPrintLocked()) {
    $errormsg = $config['print']['limit_msg'];
    logErrorAndDie($errormsg);
}

$imageHandler = new Image();
$random = $imageHandler->create_new_filename('random');
$filename = $_GET['filename'];
$uniquename = substr($filename, 0, -4) . '-' . $random;
$filename_source = $config['foldersAbs']['images'] . DIRECTORY_SEPARATOR . $filename;
$filename_print = $config['foldersAbs']['print'] . DIRECTORY_SEPARATOR . $uniquename;

$status = false;

// exit with error if file does not exist
if (!file_exists($filename_source)) {
    $errormsg = "File $filename not found";
    logErrorAndDie($errormsg);
}

// Only jpg/jpeg are supported
$imginfo = getimagesize($filename_source);
$mimetype = $imginfo['mime'];
if ($mimetype != 'image/jpg' && $mimetype != 'image/jpeg') {
    $errormsg = basename($_SERVER['PHP_SELF']) . ': The source file type ' . $mimetype . ' is not supported';
    logErrorAndDie($errormsg);
}

if (!file_exists($filename_print)) {
    try {
        $source = $imageHandler->createFromImage($filename_source);

        // rotate image if needed
        list($width, $height) = getimagesize($filename_source);
        if ($width > $height || $config['print']['no_rotate'] === true) {
            $imageHandler->qrRotate = false;
        } else {
            $source = imagerotate($source, 90, 0);
            $imageHandler->qrRotate = true;
        }

        if ($config['print']['print_frame']) {
            $imageHandler->framePath = $config['print']['frame'];
            $imageHandler->frameExtend = false;
            $source = $imageHandler->applyFrame($source);
        }

        if ($config['print']['qrcode'] && $imageHandler->qrAvailable) {
            // create qr code
            $imageHandler->qrUrl = $config['qr']['url'];
            if ($config['qr']['append_filename']) {
                $imageHandler->qrUrl = $config['qr']['url'] . $filename;
            }
            $imageHandler->qrEcLevel = $config['qr']['ecLevel'];
            $imageHandler->qrSize = $config['print']['qrSize'];
            $imageHandler->qrMargin = $config['print']['qrMargin'];
            $imageHandler->qrColor = $config['print']['qrBgColor'];
            $imageHandler->qrOffset = $config['print']['qrOffset'];
            $imageHandler->qrPosition = $config['print']['qrPosition'];

            $qrCode = $imageHandler->createQr();
            if (!$qrCode) {
                throw new Exception('Can\'t create QR Code resource.');
            }
            $source = $imageHandler->applyQr($qrCode, $source);
            if (!$source) {
                throw new Exception('Can\'t apply QR Code to image resource.');
            }
            imagedestroy($qrCode);
        }

        if ($config['textonprint']['enabled']) {
            $imageHandler->fontSize = $config['textonprint']['font_size'];
            $imageHandler->fontRotation = $config['textonprint']['rotation'];
            $imageHandler->fontLocationX = $config['textonprint']['locationx'];
            $imageHandler->fontLocationY = $config['textonprint']['locationy'];
            $imageHandler->fontColor = $config['textonprint']['font_color'];
            $imageHandler->fontPath = $config['textonprint']['font'];
            $imageHandler->textLine1 = $config['textonprint']['line1'];
            $imageHandler->textLine1 = $config['textonprint']['line2'];
            $imageHandler->textLine3 = $config['textonprint']['line3'];
            $imageHandler->textLineSpacing = $config['textonprint']['linespace'];

            $source = $imageHandler->applyText($source);
        }

        if ($config['print']['crop']) {
            $imageHandler->resizeMaxWidth = $config['print']['crop_width'];
            $imageHandler->resizeMaxHeight = $config['print']['crop_height'];
            $source = $imageHandler->resizeCropImage($source);
        }

        $imageHandler->jpegQuality = 100;
        if (!$imageHandler->saveJpeg($source, $filename_print)) {
            throw new Exception('Can\'t save print image.');
        }
    } catch (Exception $e) {
        $errormsg = basename($_SERVER['PHP_SELF']) . ': ' . $e->getMessage();
        logErrorAndDie($errormsg);
    }
}

// print image
$status = 'ok';
$cmd = sprintf($config['print']['cmd'], $filename_print);
$cmd .= ' 2>&1'; //Redirect stderr to stdout, otherwise error messages get lost.

exec($cmd, $output, $returnValue);

addToPrintDB($filename, $uniquename);

$linecount = 0;
if ($config['print']['limit'] > 0) {
    $linecount = getPrintCountFromDB();
    if ($linecount % $config['print']['limit'] == 0) {
        if (lockPrint()) {
            $status = 'locking';
        } else {
            if ($config['dev']['loglevel'] > 1) {
                $errormsg = basename($_SERVER['PHP_SELF']) . ': Error creating the file ' . PRINT_LOCKFILE;
                logError($errormsg);
            }
        }
    }
    file_put_contents(PRINT_COUNTER, $linecount);
}

$LogData = [
    'status' => $status,
    'count' => $linecount,
    'msg' => $cmd,
    'returnValue' => $returnValue,
    'output' => $output,
    'php' => basename($_SERVER['PHP_SELF']),
];
$LogString = json_encode($LogData);
if ($config['dev']['loglevel'] > 1) {
    logError($LogData);
}

die($LogString);
