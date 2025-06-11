<?php

namespace Azulphp;

use Azulphp\Helpers\ResponseStatus;
use JsonException;

abstract class ApiResource
{
    public function __construct(protected array $data)
    {
        //
    }

    /**
     * Convert array of data to json.
     *
     * @throws JsonException
     */
    public function arrayToJson(): void
    {
        $result = [];

        foreach ($this->data as $element)
        {
            $result[] = $this->toArray($element);
        }

        $this->toJson($result);
    }

    /**
     * Json response.
     *
     * @throws JsonException
     */
    protected function toJson(array $result): void
    {
        http_response_code(ResponseStatus::OK);
        header('Content-Type: application/json');

        echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    /**
     * Convert a resources object to array.
     *
     * @param  $resource
     * @return array
     */
    abstract protected function toArray($resource): array;
}