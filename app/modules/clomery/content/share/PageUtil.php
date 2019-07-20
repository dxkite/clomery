<?php
namespace clomery\content;

use support\openmethod\PageData;

class PageUtil
{
    /**
     * @param PageData $page
     * @param array $parseKey
     * @return PageData
     */
    public static function parseKey(PageData $page, array $parseKey = []):PageData
    {
        if ($page->getSize() > 0) {
            $rows = $page->getRows();
            foreach ($parseKey as $key => $callback) {
                foreach ($rows as $index => $row) {
                    $rows[$index][$key] = call_user_func_array($callback, [$row[$key], $row]);
                }
            }
            $page->setRows($rows);
        }
        return $page;
    }

    /**
     * 转换Key到值
     *
     * @param PageData $page
     * @param string $key
     * @param array $parseKey
     * @param null $default
     * @return PageData
     */
    public static function parseKeyToKey(PageData $page, string $key, array $parseKey = [], $default = null):PageData
    {
        if ($page->getSize() > 0) {
            $rows = $page->getRows();
            $keyData = [];
            foreach ($rows as $data) {
                $keyData[$key][] = $data[$key];
            }
            $data = [];
            foreach ($parseKey as $setKey => $callback) {
                $data[$setKey] = \call_user_func_array($callback, array_values($keyData));
            }
            foreach ($parseKey as $setKey => $callback) {
                foreach ($rows as $index => $row) {
                    $rows[$index][$setKey] = $data[$setKey][$row[$key]] ?? $default;
                }
            }
            $page->setRows($rows);
        }
        return $page;
    }

    /**
     * @param PageData $page
     * @param string $key
     * @param array $parseKey
     * @return PageData
     */
    public static function parseKeyToColumn(PageData $page, string $key, array $parseKey = []): PageData
    {
        if ($page->getSize() > 0) {
            $rows = $page->getRows();
            foreach ($parseKey as $setKey => $callback) {
                foreach ($rows as $index => $row) {
                    $rows[$index][$setKey] = \call_user_func_array($callback, [$row[$key], $row]);
                }
            }
            $page->setRows($rows);
        }
        return $page;
    }
}