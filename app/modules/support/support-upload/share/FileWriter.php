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
     * @var string
     */
    protected $file;

    public function __construct(string $path)
    {
        $this->file = fopen($path, 'wb+');
        flock($this->file, LOCK_EX);
    }

    /**
     * 写入数据
     *
     * @param string $buffer
     * @param integer $start
     * @param integer|null $end
     * @return bool
     */
    public function write(string $buffer, int $start, ?int $end = null)
    {
        if (is_resource($this->file)) {
            $length = $end === null ? null : $end - $start;
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
     * @param integer|null $end
     * @return void
     */
    public function writeBlock(string $path, int $start, ?int $end = null)
    {
        $block = fopen($path, 'r');
        if (is_resource($this->file) && is_resource($block)) {
            $length = $end === null ? -1 : $end - $start;
            rewind($this->file);
            fseek($this->file, $start, SEEK_CUR);
            stream_copy_to_stream($block, $this->file, $length);
            return true;
        }
        return false;
    }
    
    public function __destruct()
    {
        flock($this->file, LOCK_UN);
        fclose($this->file);
    }
}