<?php

namespace App\Spiders\SpiderMiddleware;

use App\Models\FootballMatch;
use RoachPHP\Http\Request;
use RoachPHP\Http\Response;
use RoachPHP\ResponseProcessing\Handlers\Handler;
use RoachPHP\ResponseProcessing\Handlers\RequestHandlerInterface;

class CheckMatchAlreadyExistsMiddleware extends Handler implements RequestHandlerInterface
{
    public function handleRequest(Request $request, Response $response): Request
    {
        $id = $request->getMeta('id');

        if ($id === null) {
            return $request->drop('No match id exists on request');
        }

        $exists = FootballMatch::where('external_id', $id)->exists();

        if ($exists) {
            return $request->drop("Match with id [$id] already exists in database");
        }

        return $request;
    }
}
