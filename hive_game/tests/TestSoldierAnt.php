<?php

use Joyce0398\HiveGame\BoardGame;
use Joyce0398\HiveGame\Hand;
use Joyce0398\HiveGame\pieces\SoldierAnt;
use Joyce0398\HiveGame\Player;
use PHPUnit\Framework\TestCase;

class TestSoldierAnt extends TestCase
{
    public function testMoveToOccupiedTile()
    {
        $board = new BoardGame([
            '0,0' => [[0, 'B']],
            '1,0' => [[1, 'B']],
            '1,1' => [[1, 'B']],
            '2,0' => [[0, 'A']],
        ]);
        $hand = new Hand();
        $player = new Player(0, $board, $hand);
        $ant = new SoldierAnt($player);
        $this->expectException(Exception::class);
        $this->assertTrue($ant->validateMove('1,-1', '1,0'));
    }

    public function testMove()
    {
        $board = new BoardGame([
            '0,0' => [[0, 'B']],
            '1,0' => [[1, 'B']],
            '1,1' => [[1, 'B']],
            '2,0' => [[0, 'A']],
        ]);
        $hand = new Hand();
        $player = new Player(0, $board, $hand);
        $ant = new SoldierAnt($player);
        $this->assertTrue($ant->validateMove('1,-1', '0,1'));
    }
}