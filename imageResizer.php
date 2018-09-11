<?php
/**
 * Created by PhpStorm.
 * User: nikita
 * Date: 11.09.18
 * Time: 21:20
 */


class ImageResizer
{
    public $dir;
    public $allowedExist = [1, 2, 3];
    public $quality = 85;

    /**
     * @param $source
     * @param $destination
     * @param $quality
     *
     * @return mixed
     */
    public static function compress($source, $destination, $quality)
    {

        $info = getimagesize($source);

        if ($info['mime'] == 'image/jpeg')
            $image = imagecreatefromjpeg($source);

        elseif ($info['mime'] == 'image/gif')
            $image = imagecreatefromgif($source);

        elseif ($info['mime'] == 'image/png')
            $image = imagecreatefrompng($source);

        imagejpeg($image, $destination, $quality);

        return $destination;

    }


    /**
     *
     * recursing scan tree dirs and resize allowed format files
     *
     * @param $dir
     *
     * @return bool
     */
    public function scanDirForImage($dir)
    {

        $result = array();

        $cdir = scandir($dir);
        foreach ($cdir as $key => $value) {
            if (!in_array($value, array(".", ".."))) {
                $fullFilename = $dir . DIRECTORY_SEPARATOR . $value;
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $result[$value] = $this->scanDirForImage($fullFilename);
                } else {
                    if (in_array(exif_imagetype($fullFilename), $this->allowedExist)) {

                        self::compress($fullFilename, $fullFilename, $this->quality);
                    }
                }
            }
        }

        return true;
    }

}

$test = new ImageResizer();

$test->scanDirForImage(__DIR__);