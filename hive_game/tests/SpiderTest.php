<?php

use Joyce0398\HiveGame\BoardGame;
use Joyce0398\HiveGame\Hand;
use Joyce0398\HiveGame\HiveGameException;
use Joyce0398\HiveGame\pieces\Spider;
use Joyce0398\HiveGame\Player;
use PHPUnit\Framework\TestCase;

class SpiderTest extends TestCase
{
    public function testMoveToOccupiedTile()
    {
        $board = new BoardGame([
            '0,0' => [[0, 'B']],
            '1,0' => [[1, 'B']],
            '0,-1' => [[0, 'S']],
            '2,0' => [[1, 'B']],
            '3,0' => [[1, 'B']],
        ]);
        $hand = new Hand();
        $player = new Player(0, $board, $hand);
        $spider = new Spider($player);
        $this->expectException(HiveGameException::class);
        $this->assertTrue($spider->validateMove('0,-1', '2,0'));
    }
    public function testIsLessThanThree()
    {
        $board = new BoardGame([
            '0,0' => [[0, 'B']],
            '1,0' => [[1, 'B']],
            '0,-1' => [[0, 'S']],
            '2,0' => [[1, 'B']],
            '3,0' => [[1, 'B']],
        ]);
        $hand = new Hand();
        $player = new Player(0, $board, $hand);
        $spider = new Spider($player);
        $this->expectException(HiveGameException::class);
        $this->assertTrue($spider->validateMove('0,-1', '2,-1'));
    }

    public function testIsMoreThanThree()
    {
        $board = new BoardGame([
            '0,0' => [[0, 'B']],
            '1,0' => [[1, 'B']],
            '0,-1' => [[0, 'S']],
            '2,0' => [[1, 'B']],
            '3,0' => [[1, 'B']],
        ]);
        $hand = new Hand();
        $player = new Player(0, $board, $hand);
        $spider = new Spider($player);
        $this->expectException(HiveGameException::class);
        $this->assertTrue($spider->validateMove('0,-1', '4,-1'));
    }

    public function testIsThree()
    {
        $board = new BoardGame([
            '0,0' => [[0, 'B']],
            '1,0' => [[1, 'B']],
            '0,-1' => [[0, 'S']],
            '2,0' => [[1, 'B']],
            '3,0' => [[1, 'B']],
        ]);
        $hand = new Hand();
        $player = new Player(0, $board, $hand);
        $spider = new Spider($player);
        $this->assertTrue($spider->validateMove('0,-1', '3,-1'));
    }
}
