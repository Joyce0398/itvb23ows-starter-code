<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Joyce0398\HiveGame\BoardGame;
use Joyce0398\HiveGame\Hand;

use PHPUnit\Framework\TestCase;

class TestBoardGame extends TestCase
{
    public function testGetAvailablePiecesSingle()
    {
        $hand = new Hand(["Q" => 0, "B" => 0, "S" => 0, "A" => 1, "G" => 0]);
        $this->assertEquals(['A' => 1], $hand->getAvailablePieces());
    }

    public function testPopTileSinglePiece()
    {
        $board = new BoardGame(['0,0' => [[0, 'Q']]]);
        $board->popTile('0,0');
        $this->assertEquals([], $board->getBoard());
    }

    public function testPopTileMultiplePieces()
    {
        $board = new BoardGame(['0,0' => [[0, 'Q'], [1, 'B']]]);
        $board->popTile('0,0');
        $this->assertEquals(['0,0' => [[0, 'Q']]], $board->getBoard());
    }
}
