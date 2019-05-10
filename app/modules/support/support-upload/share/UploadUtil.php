<?php
namespace support\upload;

use SplFileInfo;
use support\openmethod\parameter\File;
use suda\framework\filesystem\FileSystem;
use support\upload\table\UploadTable;


/**
 * 上传处理工具
 */
class UploadUtil
{
    /**
     * 计算文件Hash
     *
     * @param string $path
     * @return boolean
     */
    public static function hash(string $path)
    {
        return static::md5encode(\md5_file($path, true));
    }

    /**
     * Hash掩码
     *
     * @param string $path
     * @return boolean
     */
    public static function md5encode(string $md5)
    {
        return \str_replace(['+','/', '='], ['$','_',''], \base64_encode(\hex2bin($md5)));
    }

    /**
     * 保存文件
     *
     * @param \support\openmethod\parameter\File $file
     * @return string
     */
    public static function save(File $file):string
    {
        $hash = static::hash($file->getPathname());
        return static::saveFile($file, $hash);
    }

    /**
     * 保存文件
     *
     * @param \support\openmethod\parameter\File $file
     * @param string $hash
     * @return string
     */
    protected static function saveFile(File $file, string $hash)
    {
        if ($file->isImage()) {
            $extension = strtolower($file->getExtension());
            $path = 'image/'.$extension.'/'.$hash.'/0.jpg';
            $savePath = $extension.'/'.$hash.'.'.$extension;
        } else {
            $extension = strtolower(\pathinfo($file->getOriginalName(), PATHINFO_EXTENSION));
            $savePath = $extension.'/'.$hash.'.'.$extension;
            $path = $savePath;
        }
        $save = SUDA_DATA.'/upload/'.$savePath;
        FileSystem::make(dirname($save));
        FileSystem::copy($file->getPathname(), $save);
        return $path;
    }

    public static function saveFileDatabase(File $file, string $user, string $ip)
    {
        $md5 = md5_file($file->getPathname());
        $fileInfo = BlockFileWriter::create($file->getOriginalName(), $md5, $user, $ip, UploadTable::CHECKED);
        if ($fileInfo !== null) {
            $path = static::saveFile($file, static::md5encode($md5));
            $fileInfo['path'] = $path;
            return $fileInfo;
        }
        return null;
    }

    public static function thumb(
        string $input,
        string $output,
        ?int $width = null,
        ?int $height = null,
        ?int $size = null,
        ?int $quality = null,
        ?string $cut = '',
        string $outType = 'jpeg'
    ) {
        list($_width, $_height, $_mime, $_attr) = getimagesize($input);
        $ext = image_type_to_extension($_mime, false);
        $imageCreate = 'imagecreatefrom'.$ext;
        $source = $imageCreate($input);
        if (!$source) {
            return false;
        }
        if ($size !== null) {
            if ($size <= 0) {
                $size = 100;
            }
            $width = $_width * $size / 100;
            $height = $_height * $size / 100;
        } elseif ($width !== null) {
            if ($height !== null) {
            } else {
                $height = ($width / $_width) * $_height;
            }
            if ($width > $_width) {
                $width = $_width;
                $height = $_height;
            }
        } else {
            $width = $_width;
            $height = $_height;
        }
        $thumb = imagecreatetruecolor($width, $height);
        if (empty($cut)) {
            if ($thumb && !imagecopyresized($thumb, $source, 0, 0, 0, 0, $width, $height, $_width, $_height)) {
                return false;
            }
        }
        if ($quality !== null) {
            if ($quality < 0 && $quality > 100) {
                $quality = 75;
            }
            return imagejpeg($thumb, $output, $quality);
        } else {
            return imagepng($thumb, $output);
        }
    }
}
