<?php

declare(strict_types=1);

const IMG_W = 300;
const IMG_H = 200;

function save_product_image(array $file, string $artikul): string
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Не удалось загрузить файл изображения.');
    }
    $info = @getimagesize($file['tmp_name']);
    if ($info === false) {
        throw new RuntimeException('Выбранный файл не является изображением.');
    }
    [$w, $h] = $info;
    switch ($info[2]) {
        case IMAGETYPE_JPEG: $src = imagecreatefromjpeg($file['tmp_name']); break;
        case IMAGETYPE_PNG:  $src = imagecreatefrompng($file['tmp_name']);  break;
        case IMAGETYPE_GIF:  $src = imagecreatefromgif($file['tmp_name']);  break;
        default:
            throw new RuntimeException('Поддерживаются только форматы JPEG, PNG, GIF.');
    }

    $dst = imagecreatetruecolor(IMG_W, IMG_H);

    imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
    imagecopyresampled($dst, $src, 0, 0, 0, 0, IMG_W, IMG_H, $w, $h);

    $name = preg_replace('/[^A-Za-z0-9_-]/', '', $artikul) . '_' . time() . '.jpg';
    $path = __DIR__ . '/../uploads/' . $name;
    imagejpeg($dst, $path, 90);
    imagedestroy($src);
    imagedestroy($dst);
    return $name;
}

function delete_product_image(?string $foto): void
{
    if (!$foto) {
        return;
    }
    $path = __DIR__ . '/../uploads/' . basename($foto);
    if (is_file($path)) {
        @unlink($path);
    }
}
