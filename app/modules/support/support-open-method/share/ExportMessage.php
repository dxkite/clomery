<?php
namespace support\openmethod;

use ReflectionMethod;
use support\openmethod\Permission;

class ExportMessage implements \JsonSerializable
{
    /**
     * 方法名
     *
     * @var ExportMethod
     */
    protected $method;

    /**
     * 创建导出类
     *
     * @param ExportMethod $method
     */
    public function __construct(ExportMethod $method)
    {
        $this->method = $method;
    }

    public function jsonSerialize()
    {
        $message = [];
        $returnDoc = null;
        $paramDocs = [];
        $method = $this->method->getReflectionMethod();
        $docs = $method->getDocComment();

        if (is_string($docs)) {
            list($description, $paramDocs, $returnDoc, $data) = self::getDoc($docs);
            $message['description'] = $description;
            if (preg_match('/@acl/i', $docs, $match)) {
                $message['permissions'] = Permission::createFromFunction($method);
            }
            if ($from = $this->getParameterFrom($docs)) {
                $message['parameter-from'] = $from;
            }
        }

        foreach ($method->getParameters() as $param) {
            if (\array_key_exists($param->getName(), $paramDocs)) {
                $message['parameters'][$param->getName()]['description'] = $paramDocs[$param->getName()]['description'];
            }
            $message['parameters'][$param->getName()]['position'] = $param->getPosition();
            if ($param->hasType()) {
                $message['parameters'][$param->getName()]['type'] = strval($param->getType());
            }
            if ($param->isDefaultValueAvailable()) {
                try {
                    $message['parameters'][$param->getName()]['default'] = $param->getDefaultValue();
                } catch (\ReflectionException $e) {
                    $message['parameters'][$param->getName()]['default'] = 'error:'.$e->getMessage();
                }
            }
            if ($param->allowsNull()) {
                $message['parameters'][$param->getName()]['nullable'] = true;
            }
        }
        $message['return'] = $returnDoc;
        return $message;
    }

    public function getParameterFrom(string $docs)
    {
        if (preg_match('/@param-source\s+([\w,]+)\s*$/ims', $docs, $match)) {
            return explode(',', strtoupper(trim($match[1], ',')));
        }
        return null;
    }

    protected static function getDoc(string $docs)
    {
        $docs = trim(preg_replace('/^\/\*\*(.+?)\*\//ms', '$1', $docs));
        $lines = preg_split('/\r?\n/', $docs);
        $params = [];
        $return = [];
        $docs = [];
        foreach ($lines as $index => $line) {
            $line = substr(ltrim(trim($line), '*'), 1) ?? ' ';
            if (preg_match('/^@param\s+(.+?)\s+(.+?)(\s+(.+))?$/', $line, $match)) {
                if (!isset($match[3])) {
                    $match[3] = null;
                }
                list($comment, $type, $name, $description) = $match;
                $name = ltrim($name, '$');
                $params[$name]['description'] = trim($description);
                $params[$name]['type'] = $type;
            } elseif (preg_match('/^@return\s+(.+?)(\s+(.+))?$/', $line, $match)) {
                if (!isset($match[2])) {
                    $match[2] = null;
                }
                list($comment, $type, $description) = $match;
                $return['type'] = $type;
                $return['description'] = trim($description);
            } else {
                $docs[] = $line;
            }
        }
        $datas = static::docField($docs);
        return [$datas['description'],$params,$return,$datas];
    }

    protected static function docField(array $lines)
    {
        $field = 'document';
        $datas = [
            'description' => array_shift($lines)
        ];
        foreach ($lines as $line) {
            if (preg_match('/^@(\w+?)(\s+)?$/', $line, $match)) {
                list($line, $field) = $match;
            } else {
                $datas[$field][] = $line;
            }
        }
        foreach ($datas as $name => $content) {
            if (is_array($content)) {
                $datas[$name] = implode("\r\n", $content);
            }
        }
        return $datas;
    }
}
