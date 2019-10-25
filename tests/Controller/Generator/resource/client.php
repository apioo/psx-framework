<?php
namespace Api;
use GuzzleHttp\Client;
use PSX\Json\Parser;
use PSX\Schema\Parser\Popo\Dumper;
use PSX\Schema\SchemaManager;
use PSX\Schema\SchemaTraverser;
use PSX\Schema\Visitor\TypeVisitor;
class Resource
{
    /**
     * @var string
     */
    private $url;
    /**
     * @var string
     */
    private $token;
    /**
     * @var Client
     */
    private $httpClient;
    /**
     * @var SchemaManager
     */
    private $schemaManager;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $type;
    public function __construct(string $name, string $type, string $baseUrl, string $token, ?Client $httpClient = null, ?SchemaManager $schemaManager = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->url = $baseUrl . '/api';
        $this->token = $token;
        $this->httpClient = $httpClient ? $httpClient : new Client();
        $this->schemaManager = $schemaManager ? $schemaManager : new SchemaManager();
    }
    /**
     * Returns a collection
     *
     * @param GetQuery $query
     * @return Collection
     */
    public function get(GetQuery $query): Collection
    {
        $options = [
            'query' => $this->convertToArray($query),
        ];
        $response = $this->httpClient->request('GET', $this->url, $options);
        $data     = (string) $response->getBody();
        return $this->convertToObject($data, Collection::class);
    }
    /**
     * @param Item $data
     * @return Message
     */
    public function post(Item $data): Message
    {
        $options = [
            'json' => $this->convertToArray($data)
        ];
        $response = $this->httpClient->request('POST', $this->url, $options);
        $data     = (string) $response->getBody();
        return $this->convertToObject($data, Message::class);
    }
    /**
     * @param Item $data
     * @return Message
     */
    public function put(Item $data): Message
    {
        $options = [
            'json' => $this->convertToArray($data)
        ];
        $response = $this->httpClient->request('PUT', $this->url, $options);
        $data     = (string) $response->getBody();
        return $this->convertToObject($data, Message::class);
    }
    /**
     * @return Message
     */
    public function delete(): Message
    {
        $options = [
        ];
        $response = $this->httpClient->request('DELETE', $this->url, $options);
        $data     = (string) $response->getBody();
        return $this->convertToObject($data, Message::class);
    }
    /**
     * @param Item $data
     * @return Message
     */
    public function patch(Item $data): Message
    {
        $options = [
            'json' => $this->convertToArray($data)
        ];
        $response = $this->httpClient->request('PATCH', $this->url, $options);
        $data     = (string) $response->getBody();
        return $this->convertToObject($data, Message::class);
    }
    private function convertToArray($object)
    {
        return (new Dumper())->dump($object);
    }
    private function convertToObject(string $data, ?string $class)
    {
        $data = Parser::decode($data);
        if ($class !== null) {
            $schema = $this->schemaManager->getSchema($class);
            return (new SchemaTraverser())->traverse($data, $schema, new TypeVisitor());
        } else {
            return $data;
        }
    }
}
/**
 * @Title("message")
 */
class Message
{
    /**
     * @Key("success")
     * @Type("boolean")
     */
    protected $success;
    /**
     * @Key("message")
     * @Type("string")
     */
    protected $message;
    /**
     * @param bool $success
     */
    public function setSuccess(?bool $success)
    {
        $this->success = $success;
    }
    /**
     * @return bool
     */
    public function getSuccess() : ?bool
    {
        return $this->success;
    }
    /**
     * @param string $message
     */
    public function setMessage(?string $message)
    {
        $this->message = $message;
    }
    /**
     * @return string
     */
    public function getMessage() : ?string
    {
        return $this->message;
    }
}
/**
 * @Title("item")
 * @Required({"id"})
 */
class Item
{
    /**
     * @Key("id")
     * @Type("integer")
     */
    protected $id;
    /**
     * @Key("userId")
     * @Type("integer")
     */
    protected $userId;
    /**
     * @Key("title")
     * @Type("string")
     * @MaxLength(16)
     * @MinLength(3)
     * @Pattern("[A-z]+")
     */
    protected $title;
    /**
     * @Key("date")
     * @Type("string")
     * @Format("date-time")
     */
    protected $date;
    /**
     * @param int $id
     */
    public function setId(?int $id)
    {
        $this->id = $id;
    }
    /**
     * @return int
     */
    public function getId() : ?int
    {
        return $this->id;
    }
    /**
     * @param int $userId
     */
    public function setUserId(?int $userId)
    {
        $this->userId = $userId;
    }
    /**
     * @return int
     */
    public function getUserId() : ?int
    {
        return $this->userId;
    }
    /**
     * @param string $title
     */
    public function setTitle(?string $title)
    {
        $this->title = $title;
    }
    /**
     * @return string
     */
    public function getTitle() : ?string
    {
        return $this->title;
    }
    /**
     * @param \DateTime $date
     */
    public function setDate(?\DateTime $date)
    {
        $this->date = $date;
    }
    /**
     * @return \DateTime
     */
    public function getDate() : ?\DateTime
    {
        return $this->date;
    }
}
/**
 * @Title("collection")
 */
class Collection
{
    /**
     * @Key("entry")
     * @Type("array")
     * @Items(@Ref("Api\Item"))
     */
    protected $entry;
    /**
     * @param array<Item> $entry
     */
    public function setEntry(?array $entry)
    {
        $this->entry = $entry;
    }
    /**
     * @return array<Item>
     */
    public function getEntry() : ?array
    {
        return $this->entry;
    }
}
/**
 * @Title("GetQuery")
 */
class GetQuery
{
    /**
     * @Key("startIndex")
     * @Description("startIndex parameter")
     * @Type("integer")
     * @Maximum(32)
     * @Minimum(0)
     */
    protected $startIndex;
    /**
     * @Key("float")
     * @Type("number")
     */
    protected $float;
    /**
     * @Key("boolean")
     * @Type("boolean")
     */
    protected $boolean;
    /**
     * @Key("date")
     * @Type("string")
     * @Format("date")
     */
    protected $date;
    /**
     * @Key("datetime")
     * @Type("string")
     * @Format("date-time")
     */
    protected $datetime;
    /**
     * @param int $startIndex
     */
    public function setStartIndex(?int $startIndex)
    {
        $this->startIndex = $startIndex;
    }
    /**
     * @return int
     */
    public function getStartIndex() : ?int
    {
        return $this->startIndex;
    }
    /**
     * @param float $float
     */
    public function setFloat(?float $float)
    {
        $this->float = $float;
    }
    /**
     * @return float
     */
    public function getFloat() : ?float
    {
        return $this->float;
    }
    /**
     * @param bool $boolean
     */
    public function setBoolean(?bool $boolean)
    {
        $this->boolean = $boolean;
    }
    /**
     * @return bool
     */
    public function getBoolean() : ?bool
    {
        return $this->boolean;
    }
    /**
     * @param \DateTime $date
     */
    public function setDate(?\DateTime $date)
    {
        $this->date = $date;
    }
    /**
     * @return \DateTime
     */
    public function getDate() : ?\DateTime
    {
        return $this->date;
    }
    /**
     * @param \DateTime $datetime
     */
    public function setDatetime(?\DateTime $datetime)
    {
        $this->datetime = $datetime;
    }
    /**
     * @return \DateTime
     */
    public function getDatetime() : ?\DateTime
    {
        return $this->datetime;
    }
}
/**
 * @Title("Endpoint")
 */
class Endpoint
{
    /**
     * @Key("GetQuery")
     * @Ref("Api\GetQuery")
     */
    protected $GetQuery;
    /**
     * @Key("Collection")
     * @Ref("Api\Collection")
     */
    protected $Collection;
    /**
     * @Key("Item")
     * @Ref("Api\Item")
     */
    protected $Item;
    /**
     * @Key("Message")
     * @Ref("Api\Message")
     */
    protected $Message;
    /**
     * @param GetQuery $GetQuery
     */
    public function setGetQuery(?GetQuery $GetQuery)
    {
        $this->GetQuery = $GetQuery;
    }
    /**
     * @return GetQuery
     */
    public function getGetQuery() : ?GetQuery
    {
        return $this->GetQuery;
    }
    /**
     * @param Collection $Collection
     */
    public function setCollection(?Collection $Collection)
    {
        $this->Collection = $Collection;
    }
    /**
     * @return Collection
     */
    public function getCollection() : ?Collection
    {
        return $this->Collection;
    }
    /**
     * @param Item $Item
     */
    public function setItem(?Item $Item)
    {
        $this->Item = $Item;
    }
    /**
     * @return Item
     */
    public function getItem() : ?Item
    {
        return $this->Item;
    }
    /**
     * @param Message $Message
     */
    public function setMessage(?Message $Message)
    {
        $this->Message = $Message;
    }
    /**
     * @return Message
     */
    public function getMessage() : ?Message
    {
        return $this->Message;
    }
}

