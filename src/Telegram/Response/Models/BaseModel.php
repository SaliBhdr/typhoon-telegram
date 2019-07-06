<?php

namespace SaliBhdr\TyphoonTelegram\Telegram\Response\Models;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\HigherOrderCollectionProxy;
use Illuminate\Support\Str;
use SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramCollectionPropertyNotExistException;

/**
 * Class BaseObject.
 */
abstract class BaseModel extends Collection
{
    /**
     * Builds collection entity.
     *
     * @param array|mixed $data
     */
    public function __construct($data)
    {
        parent::__construct($this->getRawResult($data));

        $this->mapRelatives();
    }

    /**
     * Property relations.
     *
     * @return array
     */
    abstract public function relations();

    /**
     * Get an item from the collection by key.
     *
     * @param mixed $key
     * @param mixed $default
     *
     * @return mixed|static
     */
    public function get($key, $default = null)
    {
        if ($this->offsetExists($key)) {
            return is_array($this->items[$key]) ? new static($this->items[$key]) : $this->items[$key];
        }

        return value($default);
    }

    /**
     * Map property relatives to appropriate objects.
     *
     * @return array|bool
     */
    public function mapRelatives()
    {
        $relations = $this->relations();

        if (empty($relations) || !is_array($relations)) {
            return false;
        }

        $results = $this->all();
        foreach ($results as $key => $data) {
            foreach ($relations as $property => $class) {
                if (!is_object($data) && isset($results[$key][$property])) {
                    $results[$key][$property] = new $class($results[$key][$property]);
                    continue;
                }

                if ($key === $property) {
                    $results[$key] = new $class($results[$key]);
                }
            }
        }

        return $this->items = $results;
    }

    /**
     * Returns raw response.
     *
     * @return array|mixed
     */
    public function getRawResponse()
    {
        return $this->items;
    }

    /**
     * Returns raw result.
     *
     * @param $data
     *
     * @return mixed
     */
    public function getRawResult($data)
    {
        return Arr::get($data, 'result', $data);
    }

    /**
     * Get Status of request.
     *
     * @return mixed
     */
    public function getStatus()
    {
        return Arr::get($this->items, 'ok', false);
    }

    /**
     * Magic method to get properties dynamically.
     *
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $action = substr($name, 0, 3);

        if ($action === 'get') {
            $property = Str::snake(substr($name, 3));
            $response = $this->get($property);

            // Map relative property to an object
            $relations = $this->relations();
            if (null != $response && isset($relations[$property])) {
                return new $relations[$property]($response);
            }

            return $response;
        }

        return false;
    }

    /**
     * checks if property exists in update instance and not empty
     *
     * @param $property
     *
     * @return bool
     */
    public function isset($property)
    {
        if($this->has($property) && isset($this->{$property}))
            return true;

        return false;
    }
    /**
     * Dynamically access collection proxies.
     *
     * @param  string $key
     * @return mixed
     *
     * @throws \Exception
     */
    public function __get($key)
    {
        if (is_array($this->items) && array_key_exists($key, $this->items))
            return $this->items[$key];

        if (is_object($this->items) && property_exists($this->items, $key))
            return $this->items->{$key};

        if (!in_array($key, static::$proxies)) {
            return null;
        }

        return new HigherOrderCollectionProxy($this, $key);
    }

    public function isOk()
    {
        return $this->getStatus();
    }

    /**
     * checks if webhook update is a message
     *
     * @return bool
     */
    public function isMessage()
    {
        return $this->isset('message');
    }

    /**
     * checks if webhook update is a callback_query
     *
     * @return bool
     */
    public function isCallbackQuery()
    {
        return $this->isset('callback_query');
    }

    /**
     * checks if webhook update is a inline callback_query
     *
     * @return bool
     */
    public function isInlineCallbackQuery()
    {
        if ($this->isCallbackQuery())
            $this->isset($this->callback_query->inline_message_id);


        return false;
    }

    /**
     * checks if webhook update is a inline_query
     *
     * @return bool
     */
    public function isInlineQuery()
    {
        return $this->isset('inline_query');
    }
}
