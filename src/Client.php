<?php

namespace SherlloChen\NotionSdkPhp;

use http\Exception\InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use WpOrg\Requests\Requests;

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
        return json_decode($resp->body);
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
        return json_decode($resp->body);
    }

    /**
     * Retrive a database with database id.
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
    public function retriveDatabase($databaseId): array
    {
        $url = "https://api.notion.com/v1/databases/${databaseId}";
        $resp = $this->get($url);
        return json_decode($resp->body);
    }

    /**
     * Retrieve a user with user id.
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
        $resp = Requests::get($url, $this->constructHeaders());
        if ($resp->status != '200') {
            throw new \Exception($resp->body);
        }
        return $resp;
    }

    /**
     * @throws \Exception
     */
    protected
    function post($url, $data): \WpOrg\Requests\Response
    {
        $resp = Requests::post($url, $this->constructHeaders(), json_encode($data));
        if ($resp->status_code != '200') {
            throw new \Exception($resp->body);
        }
        return $resp;
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
