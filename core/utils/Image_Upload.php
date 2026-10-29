<?php
namespace utils;

class Image_Upload {
    private $PREFIX = "resources/images/";

    public function upload($dir, $image) {
        $img_prep = explode(",", $image);
        $image = base64_decode($img_prep[1]);
        $format_raw = explode("/", $img_prep[0]);
        $format = explode(";", $format_raw[1]);

        while (true) {
            $filename = bin2hex(random_bytes(32)) . "." . $format[0];
            $path = $this->PREFIX . $dir . "/" . $filename;
            if (!file_exists($path)) {
                break;
            }
        }

        try {
            file_put_contents($path, $image);
        } catch (Exception $e) {
            error_log("File Error: " . $e->getMessage());
        }

        return $path;
    }

    public function create_thumbnail($dir, $source_path, $target_width = 150) {
        if(!extension_loaded('gd')) {
            error_log("GD extension is not loaded.\n Please install the GD extension to create thumbnails.");
            return false;
        }

        $full_source = $source_path;
        $file_name = basename($full_source);

        $destination_path = $this->PREFIX . $dir . "/" . $file_name;


        list($width, $height, $type) = getimagesize($full_source);

        $ratio = $width / $height;
        $target_height = floor($target_width / $ratio);

        switch ($type) {
            case IMAGETYPE_JPEG:
                $source_image = imagecreatefromjpeg($full_source);
                break;
            case IMAGETYPE_PNG:
                $source_image = imagecreatefrompng($full_source);
                break;
            case IMAGETYPE_GIF:
                $source_image = imagecreatefromgif($full_source);
                break;
            case IMAGETYPE_WEBP:
                $source_image = imagecreatefromwebp($full_source);
                break;
            default:
                return false; // Unsupported format
        }

        $thumbnail = imagecreatetruecolor($target_width, $target_height);

        if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
        }

        imagecopyresampled(
            $thumbnail,   $source_image,
            0, 0, 0, 0,   // Destination X, Y, Source X, Y
            $target_width, $target_height,
            $width,       $height
        );

        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($thumbnail, $destination_path, 85); // 85 is quality percentage
                break;
            case IMAGETYPE_PNG:
                imagepng($thumbnail, $destination_path, 6);   // 6 is compression levels (0-9)
                break;
            case IMAGETYPE_GIF:
                imagegif($thumbnail, $destination_path);
                break;
            case IMAGETYPE_WEBP:
                imagewebp($thumbnail, $destination_path, 80); // Quality (0-100)
                break;
        }
        
        imagedestroy($source_image);
        imagedestroy($thumbnail);

        return true;
    }
}
