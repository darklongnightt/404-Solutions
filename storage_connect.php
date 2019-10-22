<?php
# Includes the autoloader for libraries installed with composer
require __DIR__ . "/vendor/autoload.php";

# Imports the Google Cloud client library
use Google\Cloud\Storage\StorageClient;

# Your Google Cloud Platform project ID
$projectId = 'super-data-fyp';

# Instantiates a client
$storage = new StorageClient([
    'projectId' => $projectId,
    'keyFilePath' => '../../super-data-key.json'
]);

# The name for bucket
$bucketName = 'super-data-fyp.appspot.com';

// To resize image before uploading to reduce consumed space
function image_resize($src, $dst, $width, $height, $crop = 0)
{
    if (!list($w, $h) = getimagesize($src)) return "Unsupported picture type!";

    $type = strtolower(substr(strrchr($src, "."), 1));
    if ($type == 'jpeg') $type = 'jpg';
    switch ($type) {
        case 'bmp':
            $img = imagecreatefromwbmp($src);
            break;
        case 'gif':
            $img = imagecreatefromgif($src);
            break;
        case 'jpg':
            $img = imagecreatefromjpeg($src);
            break;
        case 'png':
            $img = imagecreatefrompng($src);
            break;
        default:
            return "Unsupported picture type!";
    }

    // Resize with option of cropping
    if ($crop) {
        if ($w < $width or $h < $height) return "Picture is too small!";
        $ratio = max($width / $w, $height / $h);
        $h = $height / $ratio;
        $x = ($w - $width / $ratio) / 2;
        $w = $width / $ratio;
    } else {
        if ($w < $width and $h < $height) return "Picture is too small!";
        $ratio = min($width / $w, $height / $h);
        $width = $w * $ratio;
        $height = $h * $ratio;
        $x = 0;
    }

    $new = imagecreatetruecolor($width, $height);

    // Preserve transparency
    if ($type == "gif" or $type == "png") {
        imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
        imagealphablending($new, false);
        imagesavealpha($new, true);
    }

    imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);

    switch ($type) {
        case 'bmp':
            imagewbmp($new, $dst);
            break;
        case 'gif':
            imagegif($new, $dst);
            break;
        case 'jpeg':
        case 'jpg':
            imagejpeg($new, $dst);
            break;
        case 'png':
            imagepng($new, $dst);
            break;
    }

    return true;
}

// To upload file to bucket
function upload_object($bucketName, $objectName, $source)
{
    $file = fopen($source, 'r');
    $bucket = $GLOBALS['storage']->bucket($bucketName);
    $object = $bucket->upload($file, [
        'name' => $objectName
    ]);
}

function delete_object($bucketName, $objectName)
{
    $bucket = $GLOBALS['storage']->bucket($bucketName);
    $object = $bucket->object($objectName);
    $object->delete();
}

?>
