<?php

namespace Azulphp\Routing\Response\Api;

use Azulphp\Collections\Collection;
use Azulphp\Collections\Pagination;
use Azulphp\Routing\Response\Response;
use Azulphp\Routing\Response\ResponseStatus;
use JsonException;

abstract class ApiResponse implements Response
{
    protected array|object $data;
    protected array $pagination = [];

    /**
     * Convert array of data to json.
     *
     * @throws JsonException
     */
    public function response(): string
    {
        $result = ['data' => $this->data];
        $result += !empty($this->pagination) ? ['meta' => $this->pagination] : [];
        
        return $this->toJson($result);
    }

    /**
     * Set data form an object.
     *
     * @param  object  $object
     * @return static
     */
    public static function fromObject(object $object): static
    {
        $instance = new static();
        $instance->data = $instance->toArray($object);

        return $instance;
    }

    /**
     * Set data from array, collection or a paginated list.
     *
     * @param  array|Collection|Pagination  $list
     * @return static
     */
    public static function fromList(array|Collection|Pagination $list): static
    {
        $instance = new static();

        if ($list instanceof Pagination)
        {
            $dataListing = $list->getCollection();
            $instance->setPagination($list);
        }
        else
            $dataListing = $list;

        foreach ($dataListing as $data)
        {
            $instance->data[] = $instance->toArray($data);
        }

        return $instance;
    }

    /**
     * Json response.
     *
     * @throws JsonException
     */
    protected function toJson(array $result): string
    {
        http_response_code(ResponseStatus::OK);
        header('Content-Type: application/json');

        return json_encode($result, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    /**
     * Set the value of pagination;
     *
     * @param  Pagination  $pagination
     * @return void
     */
    public function setPagination(Pagination $pagination): void
    {
        $this->pagination = [
            'page' => $pagination->currentPage(),
            'previous' => $pagination->previousPage(),
            'next' => $pagination->nextPage(),
            'total_pages' => $pagination->totalPages(),
            'per_page' => $pagination->perPage(),
        ];
    }

    /**
     * Convert a resources object to array.
     *
     * @param  $resource
     * @return array
     */
    abstract protected function toArray($resource): array;
}