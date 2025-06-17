<?php

namespace Azulphp\Routing\Response;

use Azulphp\Routing\Response\Api\ApiResponse;
use Azulphp\Routing\Response\View\ViewResponse;
use JsonException;

class ResponseHandler
{
    /**
     * Create the response that will be returned to the user.
     *
     * @throws JsonException
     */
    public static function handle($response): void
    {
        if ($response instanceof Response)
            echo $response->response();
        else
            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }
}