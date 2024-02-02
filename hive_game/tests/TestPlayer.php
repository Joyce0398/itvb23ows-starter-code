<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Joyce0398\HiveGame\BoardGame;
use Joyce0398\HiveGame\Hand;
use Joyce0398\HiveGame\Player;

use PHPUnit\Framework\TestCase;

class TestPlayer extends TestCase
{
    public function testGetOwnedTilesSingle()
    {
        $board = new BoardGame(['0,0' => [[0, 'Q']], '0,1' => [[1, 'B']]]);
        $hand = new Hand();
        $player = new Player(0, $board, $hand);
        $this->assertEquals(['0,0'], $player->getOwnedTiles());
    }

    public function testGetOwnedTilesMultiple()
    {
        $board = new BoardGame(['0,0' => [[0, 'Q']], '0,1' => [[0, 'B']]]);
        $hand = new Hand();
        $player = new Player(0, $board, $hand);
        $this->assertEquals(['0,0', '0,1'], $player->getOwnedTiles());
    }

    public function testGetAvailablePositionsEmptyBoard()
    {
        $board = new BoardGame([]);
        $hand = new Hand();
        $player = new Player(0, $board, $hand);
        $this->assertEquals([], $player->getAvailablePositions());
    }

    public function testGetAvailablePositions()
    {
        $board = new BoardGame([
            '0,0' => [[0, 'Q']],
            '1,0' => [[1, 'Q']],
        ]);
        $hand = new Hand(["Q" => 0, "B" => 2, "S" => 2, "A" => 3, "G" => 3]);
        $player = new Player(0, $board, $hand);
        $this->assertEquals(['0,-1', '-1,0', '-1,1'], $player->getAvailablePositions());
    }
}
