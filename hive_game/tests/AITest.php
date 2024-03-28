<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Joyce0398\HiveGame\AI;
use Joyce0398\HiveGame\BoardGame;
use PHPUnit\Framework\TestCase;

class AITest extends TestCase
{
    public function testAIMove()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(array (
                0 => 'play',
                1 => 'Q',
                2 => '0,0',
            ))),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $board = new BoardGame();

        $ai = new AI(1, $client);
        $response = $ai->aiMove($board, [["Q" => 0, "B" => 0, "S" => 0, "A" => 1, "G" => 0],
            ["Q" => 0, "B" => 0, "S" => 0, "A" => 1, "G" => 0]]);

        $expectedResponse = array (
            0 => 'play',
            1 => 'Q',
            2 => '0,0',
        );

        $this->assertEquals($expectedResponse, $response);
    }
}
