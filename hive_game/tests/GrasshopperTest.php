<?php

use Joyce0398\HiveGame\BoardGame;
use Joyce0398\HiveGame\Hand;
use Joyce0398\HiveGame\HiveGameException;
use Joyce0398\HiveGame\pieces\Grasshopper;
use Joyce0398\HiveGame\Player;
use PHPUnit\Framework\TestCase;

class GrasshopperTest extends TestCase
{
    public function testGrasshopperJump()
    {
        $board = new BoardGame(['0,0' => [[0, 'Q']], '1,0' => [[1, 'Q']], '-1,0' => [[0, 'G']]]);
        $hand = new Hand();
        $player = new Player(0, $board, $hand);
        $grassHopper = new Grasshopper($player);
        $result = $grassHopper->validateMove('-1,0', '2,0');
        $this->assertTrue($result);
    }

    public function testGrassHopperJumpNonEmptyTileHorizontal()
    {
        $board = new Boardgame([
            '0,0' => [[0, 'B']],
            '1,0' => [[1, 'B']],
            '-1,0' => [[0, 'G']],
            '2,0' => [[1, 'B']],
        ]);

        $hand = new Hand();
        $player = new Player(0, $board, $hand);
        $grassHopper = new Grasshopper($player);
        $this->expectException(HiveGameException::class);
        $grassHopper->validateMove('-1,0', '2,0');
    }

    public function testGrassHopperJumpNonEmptyTileVertical()
    {
        $board = new BoardGame([
            '0,0' => [[0, 'B']],
            '0,1' => [[1, 'B']],
            '0,-1' => [[0, 'G']],
        ]);

        $hand = new Hand();
        $player = new Player(0, $board, $hand);
        $grassHopper = new Grasshopper($player);
        $this->assertTrue($grassHopper->validateMove('0,-1', '0,2'));
    }

    public function testGrassHopperJumpDiagonal()
    {
        $board = new BoardGame([
            '0,0' => [[0, 'B']],
            '1,0' => [[1, 'B']],
            '-1,0' => [[0, 'G']],
            '2,0' => [[1, 'B']],
        ]);

        $hand = new Hand();
        $player = new Player(0, $board, $hand);
        $grassHopper = new Grasshopper($player);
        $this->expectException(HiveGameException::class);
        $grassHopper->validateMove('-1,0', '1,1');
    }

    public function testGrassHopperEmptyTileJump(){
        $board = new BoardGame([
            '0,0' => [[0, 'B']],
            '0,1' => [[1, 'B']],
            '-1,0' => [[0, 'G']],
            '0,2' => [[1, 'B']],
            '1,1' => [[1, 'B']],
            '2,0' => [[1, 'B']],
        ]);

        $hand = new Hand();
        $player = new Player(0, $board, $hand);
        $grassHopper = new Grasshopper($player);
        $this->expectException(HiveGameException::class);
        $grassHopper->validateMove('-1,0', '3,0');
    }

    public function testGrassHopperOneTileJump()
    {
        $board = new BoardGame([
            '0,0' => [[0, 'B']],
            '1,0' => [[1, 'B']],
            '0,-1' => [[0, 'G']]]);

        $hand = new Hand();
        $player = new Player(0, $board, $hand);
        $grassHopper = new Grasshopper($player);
        $this->expectException(HiveGameException::class);
        $grassHopper->validateMove('0,-1', '1,-1');
    }
}
