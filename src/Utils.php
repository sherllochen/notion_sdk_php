<?php

namespace SherlloChen\NotionSdkPhp;

class Utils
{
    /**
     * Get title of data item
     * @param array $dataItem Database item or page item.
     * @return string
     */
    static public function parseTitleOfDataItem(array $dataItem): string
    {
        return $dataItem['properties']['Name']['title'][0]['text']['content'];
    }
}