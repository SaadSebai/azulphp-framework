<?php

namespace Azulphp\Response;

use Azulphp\Response\Api\ApiResponse;
use Azulphp\Response\View\ViewResponse;
use JsonException;

class ResponseHandler
{
    /**
     * Create the response that will be returned to the user.
     *
     * @throws JsonException
     */
    public static function handle($response)
    {
        if ($response instanceof ViewResponse)
            $response->response();
        elseif ($response instanceof ApiResponse)
            echo $response->response();
        else {
            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        }

        return;
    }
}