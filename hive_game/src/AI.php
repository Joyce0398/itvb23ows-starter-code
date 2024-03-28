<?php

namespace Joyce0398\HiveGame;

use GuzzleHttp\Client;

class AI
{
    private $moveNumber;

    public function __construct(int $moveNumber, ?Client $client = null)
    {
        $this->moveNumber = $moveNumber;
        if(!$client) {
            $this->client = new Client([
                'base_uri' => 'http://hive-ai:5000',
            ]);
        } else {
            $this->client = $client;
        }
    }

    public function aiMove(BoardGame $board, array $hands)
    {
        $data = [
            'move_number' => $this->moveNumber,
            'hand' => $hands,
            'board' => $board->getBoard()
        ];

        $response = $this->client->request('POST', '', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => $data
        ]);

        $result = json_decode($response->getBody());
        return $result;
    }
}