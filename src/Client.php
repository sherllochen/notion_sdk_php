<?php

namespace SherlloChen\NotionSdkPhp;

use http\Exception\InvalidArgumentException;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use WpOrg\Requests\Requests;
use SherlloChen\NotionSdkPhp\Utils;

class Client
{
    public string $apiToken;
    public string $notionVersion;
    public string $apiBaseUrl;
    public string $bearToken;

    /**
     * Client constructor.
     * These three variables can be set in params or in env file.
     *
     * @param string|null $apiToken Notion api token.
     * @param string|null $apiBaseUrl Notion api base url.
     * @param string|null $notionVersion Notion version.
     */
    public function __construct(string $apiToken = null, string $apiBaseUrl = null, string $notionVersion = null)
    {
        $this->apiToken = $apiToken ?? env('API_TOKEN');
        $this->notionVersion = $notionVersion ?? env('NOTION_VERSION');
        $this->apiBaseUrl = $apiBaseUrl ?? env('NOTION_BASE_URL');
        if (!(isset($this->apiToken) && isset($this->notionVersion) && isset($this->apiBaseUrl))) {
            throw new \InvalidArgumentException('Can not get apiToken,notionVersion,apiBaseUrl from arguments or env variables');
        }
        $this->constructHeaders();
    }

    /**
     * Retrieve a user with user id.
     *
     * @param $userID
     *     {
     * "object": "user",
     * "id": "6794760a-1f15-45cd-9c65-0dfe42f5135a",
     * "name": "Aman Gupta",
     * "avatar_url": null,
     * "type": "person",
     * "person": {
     * "email": "XXXXXXX@makenotion.com"
     * }
     * }
     * @return array
     * @throws \Exception
     */
    public function retrieveUser(string $userID): array
    {
        $url = "https://api.notion.com/v1/users/${userID}";
        $resp = $this->get($url);
        return json_decode($resp->body, true);
    }

    /**
     * List all user.
     *
     * @return array
     *
     * {
     * "object": "list",
     * "results": [
     * {
     * "object": "user",
     * "id": "6794760a-1f15-45cd-9c65-0dfe42f5135a",
     * "name": "Aman Gupta",
     * "avatar_url": null,
     * "type": "person",
     * "person": {
     * "email": "XXXXXXXXX@makenotion.com"
     * }
     * },
     * {
     * "object": "user",
     * "id": "92a680bb-6970-4726-952b-4f4c03bff617",
     * "name": "TestBot",
     * "avatar_url": null,
     * "type": "bot",
     * "bot": {}
     * }
     * ],
     * "next_cursor": null,
     * "has_more": false
     * }
     * @throws \Exception
     */
    public function listAllUsers(): array
    {
        $url = 'https://api.notion.com/v1/users';
        $resp = $this->get($url);
        return json_decode($resp->body, true);
    }

    /**
     * Retrieve a database with database id.
     *
     * @return array
     * {
     * "object": "database",
     * "id": "8e2c2b76-9e1d-47d2-87b9-ed3035d607ae",
     * "created_time": "2021-04-27T20:38:19.437Z",
     * "last_edited_time": "2021-04-27T21:15:00.000Z",
     * "title": [
     * {
     * "type": "text",
     * "text": {
     * "content": "Media",
     * "link": null
     * },
     * "annotations": {
     * "bold": false,
     * "italic": false,
     * "strikethrough": false,
     * "underline": false,
     * "code": false,
     * "color": "default"
     * },
     * "plain_text": "Media",
     * "href": null
     * }
     * ],
     * "properties": {
     * "Score /5": {
     * "id": ")Y7\"",
     * "type": "select",
     * "select": {
     * "options": [
     * {
     * "id": "5c944de7-3f4b-4567-b3a1-fa2c71c540b6",
     * "name": "⭐️⭐️⭐️⭐️⭐️",
     * "color": "default"
     * },
     * {
     * "id": "b7307e35-c80a-4cb5-bb6b-6054523b394a",
     * "name": "⭐️⭐️⭐️⭐️",
     * "color": "default"
     * },
     * {
     * "id": "9b1e1349-8e24-40ba-bbca-84a61296bc81",
     * "name": "⭐️⭐️⭐️",
     * "color": "default"
     * },
     * {
     * "id": "66d3d050-086c-4a91-8c56-d55dc67e7789",
     * "name": "⭐️⭐️",
     * "color": "default"
     * },
     * {
     * "id": "d3782c76-0396-467f-928e-46bf0c9d5fba",
     * "name": "⭐️",
     * "color": "default"
     * }
     * ]
     * }
     * },
     * "Type": {
     * "id": "/7eo",
     * "type": "select",
     * "select": {
     * "options": [
     * {
     * "id": "f96d0d0a-5564-4a20-ab15-5f040d49759e",
     * "name": "Article",
     * "color": "default"
     * },
     * {
     * "id": "4ac85597-5db1-4e0a-9c02-445575c38f76",
     * "name": "TV Series",
     * "color": "default"
     * },
     * {
     * "id": "2991748a-5745-4c3b-9c9b-2d6846a6fa1f",
     * "name": "Film",
     * "color": "default"
     * },
     * {
     * "id": "82f3bace-be25-410d-87fe-561c9c22492f",
     * "name": "Podcast",
     * "color": "default"
     * },
     * {
     * "id": "861f1076-1cc4-429a-a781-54947d727a4a",
     * "name": "Academic Journal",
     * "color": "default"
     * },
     * {
     * "id": "9cc30548-59d6-4cd3-94bc-d234081525c4",
     * "name": "Essay Resource",
     * "color": "default"
     * }
     * ]
     * }
     * },
     * "Publisher": {
     * "id": ">$Pb",
     * "type": "select",
     * "select": {
     * "options": [
     * {
     * "id": "c5ee409a-f307-4176-99ee-6e424fa89afa",
     * "name": "NYT",
     * "color": "default"
     * },
     * {
     * "id": "1b9b0c0c-17b0-4292-ad12-1364a51849de",
     * "name": "Netflix",
     * "color": "blue"
     * },
     * {
     * "id": "f3533637-278f-4501-b394-d9753bf3c101",
     * "name": "Indie",
     * "color": "brown"
     * },
     * {
     * "id": "e70d713c-4be4-4b40-a44d-fb413c8b9d7e",
     * "name": "Bon Appetit",
     * "color": "yellow"
     * },
     * {
     * "id": "9c2bd667-0a10-4be4-a044-35a537a14ab9",
     * "name": "Franklin Institute",
     * "color": "pink"
     * },
     * {
     * "id": "6849b5f0-e641-4ec5-83cb-1ffe23011060",
     * "name": "Springer",
     * "color": "orange"
     * },
     * {
     * "id": "6a5bff63-a72d-4464-a5d0-1a601af2adf6",
     * "name": "Emerald Group",
     * "color": "gray"
     * },
     * {
     * "id": "01f82d08-aa1f-4884-a4e0-3bc32f909ec4",
     * "name": "The Atlantic",
     * "color": "red"
     * }
     * ]
     * }
     * },
     * "Summary": {
     * "id": "?\\25",
     * "type": "text",
     * "text": {}
     * },
     * "Publishing/Release Date": {
     * "id": "?ex+",
     * "type": "date",
     * "date": {}
     * },
     * "Link": {
     * "id": "VVMi",
     * "type": "url",
     * "url": {}
     * },
     * "Read": {
     * "id": "_MWJ",
     * "type": "checkbox",
     * "checkbox": {}
     * },
     * "Status": {
     * "id": "`zz5",
     * "type": "select",
     * "select": {
     * "options": [
     * {
     * "id": "8c4a056e-6709-4dd1-ba58-d34d9480855a",
     * "name": "Ready to Start",
     * "color": "yellow"
     * },
     * {
     * "id": "5925ba22-0126-4b58-90c7-b8bbb2c3c895",
     * "name": "Reading",
     * "color": "red"
     * },
     * {
     * "id": "59aa9043-07b4-4bf4-8734-3164b13af44a",
     * "name": "Finished",
     * "color": "blue"
     * },
     * {
     * "id": "f961978d-02eb-4998-933a-33c2ec378564",
     * "name": "Listening",
     * "color": "red"
     * },
     * {
     * "id": "1d450853-b27a-45e2-979f-448aa1bd35de",
     * "name": "Watching",
     * "color": "red"
     * }
     * ]
     * }
     * },
     * "Author": {
     * "id": "qNw_",
     * "type": "multi_select",
     * "multi_select": {
     * "options": [
     * {
     * "id": "15592971-7b30-43d5-9406-2eb69b13fcae",
     * "name": "Spencer Greenberg",
     * "color": "default"
     * },
     * {
     * "id": "b80a988e-dccf-4f74-b764-6ca0e49ed1b8",
     * "name": "Seth Stephens-Davidowitz",
     * "color": "default"
     * },
     * {
     * "id": "0e71ee06-199d-46a4-834c-01084c8f76cb",
     * "name": "Andrew Russell",
     * "color": "default"
     * },
     * {
     * "id": "5807ec38-4879-4455-9f30-5352e90e8b79",
     * "name": "Lee Vinsel",
     * "color": "default"
     * },
     * {
     * "id": "4cf10a64-f3da-449c-8e04-ce6e338bbdbd",
     * "name": "Megan Greenwell",
     * "color": "default"
     * },
     * {
     * "id": "833e2c78-35ed-4601-badc-50c323341d76",
     * "name": "Kara Swisher",
     * "color": "default"
     * },
     * {
     * "id": "82e594e2-b1c5-4271-ac19-1a723a94a533",
     * "name": "Paul Romer",
     * "color": "default"
     * },
     * {
     * "id": "ae3a2cbe-1fc9-4376-be35-331628b34623",
     * "name": "Karen Swallow Prior",
     * "color": "default"
     * },
     * {
     * "id": "da068e78-dfe2-4434-9fd7-b7450b3e5830",
     * "name": "Judith Shulevitz",
     * "color": "default"
     * }
     * ]
     * }
     * },
     * "Name": {
     * "id": "title",
     * "type": "title",
     * "title": {}
     * }
     * }
     * }
     * @throws \Exception
     */
    public function retrieveDatabase($databaseId): array
    {
        return $this->getModelInfoWithId('databases', $databaseId);
    }

    /**
     * Retrieve block children with block id.
     * With a page id, it will return all block children within the page, it's the first step for getting page contents.
     * @param string $blockId Page Id or Block id
     * @param array $pageArguments start_cursor and page_size
     * @return array
     * @throws \Exception
     */
    public function retrieveBlockChildren(string $blockId, array $pageArguments = []): array
    {
        $url = "https://api.notion.com/v1/blocks/${blockId}/children";
        if ($pageArguments != []) {
            $url = $url . '?' . http_build_query($pageArguments);
        }
        $resp = $this->get($url);
        return json_decode($resp->body, true);
    }

    /**
     * Query database data with database id.
     *
     * @param string $databaseId Database ID.
     * @param array $queryArguments include sort array, filter array, page_size and start_cursor.
     * @return array List of data.
     * array:4 [▼
     * "object" => "list"
     * "results" => array:1 [▼
     * 0 => array:10 [▼
     * "object" => "page"
     * "id" => "01000398-5d75-4a61-acc6-743d6971c0f1"
     * "created_time" => "2021-12-09T02:00:00.000Z"
     * "last_edited_time" => "2021-12-09T07:12:00.000Z"
     * "cover" => null
     * "icon" => null
     * "parent" => array:2 [▶]
     * "archived" => false
     * "properties" => array:2 [▶]
     * "url" => "https://www.notion.so/composer-010003985d754a61acc6743d6971c0f1"
     * ]
     * ]
     * "next_cursor" => "3ddadb8a-08c6-4d9f-ac84-a1e86f5d8b63"
     * "has_more" => true
     * ]
     */
    public function queryADatabase(string $databaseId, array $queryArguments = []): array
    {
        $url = "https://api.notion.com/v1/databases/${databaseId}/query";
        $data['sort'] = $queryArguments['sort'] ?? array('direction' => 'descending', 'timestamp' => 'last_edited_time');
        if (isset($queryArguments['filter'])) {
            $data['filter'] = $queryArguments['filter'];
        }
        if (isset($queryArguments['start_cursor'])) {
            $data['start_cursor'] = $queryArguments['start_cursor'];
        }
        $data['page_size'] = $queryArguments['page_size'] ?? 10;
        $resp = $this->post($url, $data);
        return json_decode($resp->body, true);
    }

    /**
     * Search database with database name.
     *
     * @param string $databaseName Full database name.
     * @return array Database.
     */
    public function searchDatabaseByName(string $databaseName): array
    {
        $database = [];
        $databaseList = $this->search($databaseName, 'database')['results'];
        foreach ($databaseList as $databaseItem) {
            if ($databaseItem['title'][0]['plain_text'] == $databaseName) {
                $database = $databaseItem;
                break;
            }
        }
        return $database;
    }


//    Page related methods

    /**
     * Retrieve a page with page id.
     *
     * @param $pageId
     * @return array of page
     *{
     *"object": "page",
     *"id": "a2442b8e-d1f4-4784-86f3-6b7840401422",
     *"created_time": "2020-02-14T07:08:00.000Z",
     *"last_edited_time": "2021-12-19T06:42:00.000Z",
     *"cover": {
     *"type": "external",
     *"external": {
     *"url": "https://images.unsplash.com/photo-1627483262769-04d0a1401487?ixlib=rb-1.2.1&q=85&fm=jpg&crop=entropy&cs=srgb"
     *}
     *},
     *"icon": null,
     *"parent": {
     *"type": "database_id",
     *"database_id": "2bf03ec4-e12d-4faf-96e5-99407f889357"
     *},
     *"archived": false,
     *"properties": {
     *"Last Edited": {
     *"id": "(WIG",
     *"type": "last_edited_time",
     *"last_edited_time": "2021-12-19T06:42:00.000Z"
     *},
     *"Property": {
     *"id": "1^Kq",
     *"type": "rich_text",
     *"rich_text": []
     *},
     *"Category": {
     *"id": "4k#/",
     *"type": "select",
     *"select": {
     *"id": "510095c1-d11d-4879-a138-e3f6fec58d62",
     *"name": "tech",
     *"color": "pink"
     *}
     *},
     *"Status": {
     *"id": "EA8B",
     *"type": "select",
     *"select": {
     *"id": "142beb34-989b-4412-bc5f-411d9a142c93",
     *"name": "done",
     *"color": "purple"
     *}
     *},
     *"Published": {
     *"id": "gPb;",
     *"type": "checkbox",
     *"checkbox": false
     *},
     *"Name": {
     *"id": "title",
     *"type": "title",
     *"title": [
     *{
     *"type": "text",
     *"text": {
     *"content": "个人简历（as 开发）",
     *"link": null
     *},
     *"annotations": {
     *"bold": false,
     *"italic": false,
     *"strikethrough": false,
     *"underline": false,
     *"code": false,
     *"color": "default"
     *},
     *"plain_text": "个人简历（as 开发）",
     *"href": null
     *}
     *]
     *},
     *"Created By": {
     *"id": "prop_1",
     *"type": "created_by",
     *"created_by": {
     *"object": "user",
     *"id": "117e2f2a-2abc-4b5b-b6f5-a83ec26ddc68",
     *"name": "Sherllo Chen",
     *"avatar_url": "https://lh4.googleusercontent.com/-lIBhIYWvzsM/AAAAAAAAAAI/AAAAAAAAABY/iAycQ8o9VFk/photo.jpg?sz=50",
     *"type": "person",
     *"person": {
     *"email": "jjchen@xmu.edu.cn"
     *}
     *}
     *},
     *"Tags": {
     *"id": "prop_2",
     *"type": "multi_select",
     *"multi_select": [
     *{
     *"id": "075a29ef-ed38-4102-b259-47b4c103cb57",
     *"name": "Important",
     *"color": "red"
     *}
     *]
     *}
     *},
     *"url": "https://www.notion.so/as-a2442b8ed1f4478486f36b7840401422"
     *}
     * @throws \Exception
     */
    public function retrievePage($pageId): array
    {
        return $this->getModelInfoWithId('pages', $pageId);
    }
//    End Page related

    /**
     * Search for databases.
     *
     * @param string $query Query string.
     * @param string|null $objectType Type of object, can only be null, page or database.
     * @return array
     * @throws \Exception
     */
    public function search(string $query, string $objectType = null): array
    {
        if (isset($objectType) && $objectType != 'page' && $objectType != 'database') {
            throw new \InvalidArgumentException('objectType can only be null, page or database');
        }
        $url = 'https://api.notion.com/v1/search';
        $data = array('query' => $query);
        $data['sort'] = array('direction' => 'descending', 'timestamp' => 'last_edited_time');
        if (isset($objectType)) {
            $data['filter'] = array('value' => $objectType, 'property' => 'object');
        }
        $resp = $this->post($url, $data);
        return json_decode($resp->body, true);
    }

    protected
    function get($url): \WpOrg\Requests\Response
    {
        $resp = null;
        try {
            $resp = Requests::get($url, $this->constructHeaders());
            if ($resp->status_code != '200') {
                throw new \Exception($resp->body);
            }
        } catch (\WpOrg\Requests\Exception $e) {
            throw new \Exception('Encounter error when requesting Notion api: ' . $e->getMessage());
        }
        return $resp;
    }

    /**
     * @throws \Exception
     */
    protected
    function post($url, $data): \WpOrg\Requests\Response
    {
        $resp = null;
        try {
            $resp = Requests::post($url, $this->constructHeaders(), json_encode($data));
            if ($resp->status_code != '200') {
                throw new \Exception($resp->body);
            }
        } catch (\WpOrg\Requests\Exception $e) {
            throw new \Exception('Encounter error when requesting Notion api: ' . $e->getMessage());
        }
        return $resp;
    }


    /**
     * Retrieve information of model with id.
     *
     * @param string $modelName Plural form of model, such as pages, databases.
     * @param string $modelId pageId,databaseId
     * @return array
     * @throws \Exception
     */
    protected
    function getModelInfoWithId(string $modelName, string $modelId): array
    {
        $url = "https://api.notion.com/v1/${modelName}/${modelId}";
        $resp = $this->get($url);
        return json_decode($resp->body, true);
    }

    /**
     * Construct headers for request.
     *
     * @return array of headers
     *
     * {
     * "Content-Type":"application/json",
     * "Authorization":"SOME_BEAR_TOKEN_FROM_ENV",
     * "Notion-Version": "VERSION_FROM_ENV"
     *
     * }
     */
    protected
    function constructHeaders(): array
    {
        $this->bearToken = 'Bearer ' . $this->apiToken;
        return array('Content-Type' => 'application/json', 'Authorization' => $this->bearToken, 'Notion-Version' => $this->notionVersion);
    }

}
