<?php

use Photobooth\Service\LanguageService;

$languageService = LanguageService::getInstance();
$previewFlipClass = $config['preview']['flip'];
$previewStyleClass = $config['preview']['style'];
$previewShowPictureFrame = $config['preview']['showFrame'] && !empty($config['picture']['frame']);
$previewShowCollageFrame = $config['preview']['showFrame'] && !empty($config['collage']['frame']);

echo '<div class="preview">';
echo '<video autoplay loop>';
echo '<source src="private/clip.mp4" type="video/mp4">';
echo 'Your browser does not support the video tag.';
echo '</video>';
echo '<video id="preview--video" class="' . $previewFlipClass . ' ' . $previewStyleClass . '" autoplay playsinline></video>';
echo '<div id="preview--ipcam" class="' . $previewFlipClass . ' ' . $previewStyleClass . '"></div>';
echo '<div id="preview--none">' . $languageService->translate('no_preview') . '</div>';

if ($previewShowPictureFrame) {
    echo '<img id="previewframe--picture" class="' . $previewFlipClass . '" src="' . $config['picture']['frame'] . '" alt="pictureFrame" />';
}
if ($previewShowCollageFrame) {
    echo '<img id="previewframe--collage" class="' . $previewFlipClass . '" src="' . $config['collage']['frame'] . '" alt="collageFrame" />';
}

echo '</div>';
