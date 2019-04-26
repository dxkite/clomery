<?php
namespace support\upload;

/**
 * 文件写入工具
 */
class FileWriter
{

    /**
     * 文件
     *
     * @var resource
     */
    protected $file;

    public function __construct(string $path)
    {
        $file = fopen($path, 'wb+');
        if (\is_resource($file)) {
            $this->file = $file;
            flock($this->file, LOCK_EX);
        }
    }

    /**
     * 写入数据
     *
     * @param string $buffer
     * @param integer $start
     * @param integer|null $stop
     * @return bool
     */
    public function write(string $buffer, int $start, ?int $stop = null)
    {
        if (is_resource($this->file)) {
            $length = $stop === null ? null : $stop - $start + 1;
            rewind($this->file);
            fseek($this->file, $start, SEEK_CUR);
            if ($length !== null) {
                fwrite($this->file, $buffer, $length);
            } else {
                fwrite($this->file, $buffer);
            }
            return true;
        }
        return false;
    }

    /**
     * 写入块数据
     *
     * @param string $path
     * @param integer $start
     * @param integer|null $stop
     * @return void
     */
    public function writeBlock(string $path, int $start, ?int $stop = null)
    {
        $block = fopen($path, 'r');
        if (is_resource($this->file) && is_resource($block)) {
            $length = $stop === null ? -1 : $stop - $start + 1;
            rewind($this->file);
            fseek($this->file, $start, SEEK_CUR);
            stream_copy_to_stream($block, $this->file, $length);
            return true;
        }
        return false;
    }
    
    public function __destruct()
    {
        if (is_resource($this->file)) {
            flock($this->file, LOCK_UN);
            fclose($this->file);
        }
    }
}