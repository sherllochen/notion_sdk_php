<?php

namespace SherlloChen\NotionSdkPhp;

use JetBrains\PhpStorm\ArrayShape;

class Utils
{
    static public function parsePageData(string $pageData): array
    {

    }

    /**
     * Get title of data item
     * @param array $dataItem Database item or page item.
     * @return string
     */
    static public function parseTitleOfDataItem(array $dataItem): string
    {
        return $dataItem['properties']['Name']['title'][0]['text']['content'];
    }

    /**
     * Parse all options of specific from database data.
     * With a page id, it will return all block children within the page, it's the first step for getting page contents.
     * @param array $databaseData Data from retrieveDatabase
     * @param string $selectPropertyName Name of select property
     * @return array
     * @throws \Exception
     */
    static public function parseSelectPropertyOptions(array $databaseData, string $selectPropertyName): array
    {
        $result = [];
        if (!array_key_exists($selectPropertyName, $databaseData['properties'])) {
            throw new \InvalidArgumentException("${selectPropertyName} is not existed in this database");
        }
        $propertyArray = $databaseData['properties'][$selectPropertyName];
        $result = $propertyArray['select']['options'];
        return $result;
    }

    /**
     * Simplify page
     *
     * @param $fullPageData
     * @return array of simplify page
     * [
     * "pageId" => "a2442b8e-d1f4-4784-86f3-6b7840401422",
     * "cover" => "https://www.notion.so/images/page-cover/gradients_8.png",
     * "title" => "xxxx",
     * "created_time" => "2020-02-14T07:08:00.000Z",
     * ]
     **/
    #[ArrayShape(['pageId' => "string", 'cover' => "string", 'title' => "string", 'created_time' => "string"])] public function simplifyPageArray($fullPageData): array
    {
        $cover = '';
        if ($this::arrayNestedKeyExists(['cover', 'external', 'url'], $fullPageData)) {
            $cover = $fullPageData['cover']['external']['url'];
        }
        $createdDate = $fullPageData['created_time'];
        $title = \SherlloChen\NotionSdkPhp\Utils::parseTitleOfDataItem($fullPageData);

        return [
            'pageId' => $fullPageData['id'],
            'cover' => $cover,
            'title' => $title,
            'created_time' => $createdDate
        ];
    }

    /**
     * Check exists of nested key in array
     * @param array $keyPath nested path of keys.
     * @param array $array
     * @return bool
     */
    static public function arrayNestedKeyExists(array $keyPath, array $array): bool
    {
        if (empty($keyPath)) {
            return false;
        }
        foreach ($keyPath as $key) {
            if (isset($array[$key]) || array_key_exists($key, $array)) {
                $array = $array[$key];
                continue;
            }

            return false;
        }

        return true;
    }
}