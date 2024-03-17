<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Joyce0398\HiveGame\BoardGame;
use Joyce0398\HiveGame\Database;
use Joyce0398\HiveGame\GameLogic;
use Joyce0398\HiveGame\Hand;

use Joyce0398\HiveGame\Player;
use PHPUnit\Framework\TestCase;

class TestGameLogic extends TestCase
{
    public function testQueenMoveFour()
    {
        $board = new BoardGame([
            '0,0' => [[0, 'B']],
            '0,1' => [[1, 'S']],
            '0,-1' => [[0, 'A']],
            '0,2' => [[1, 'A']],
            '0,-2' => [[0, 'S']],
            '0,3' => [[1, 'B']]
        ]);
        $hand = new Hand(["Q" => 1, "B" => 1, "S" => 1, "A" => 1, "G" => 1]);
        $gameLogic = new GameLogic($board);
        $player = new Player(0, $board, $hand);
        $this->assertTrue($gameLogic->checkPlay($player, 'Q', '0,-3'));
    }

    public function testNoQueenPlacementMoveFour()
    {
        $board = new BoardGame([
            '0,0' => [[0, 'B']],
            '0,1' => [[1, 'S']],
            '0,-1' => [[0, 'A']],
            '0,2' => [[1, 'A']],
            '0,-2' => [[0, 'S']],
            '0,3' => [[1, 'B']]
        ]);
        $hand = new Hand(["Q" => 1, "B" => 1, "S" => 1, "A" => 1, "G" => 1]);
        $gameLogic = new GameLogic($board);
        $player = new Player(0, $board, $hand);
        $this->expectExceptionMessage('Must play queen bee');
        $gameLogic->checkPlay($player, 'B', '0,-3');
    }

    public function testUndo()
    {
        $double = Mockery::mock('overload:' . Database::class);

        $double->shouldReceive('getMove')->with(2)->andReturn([
            'id' => 2,
            'game_id' => 1,
            'type' => 'play',
            'move_from' => 'Q',
            'move_to' => '0,0',
            'previous_id' => 1,
            'state' => '',
        ])->once();

        $double->shouldReceive('deleteMove')->with(2)->andReturn(true)->once();

        $_SESSION = [];

        $board = new BoardGame([
            '0,0' => [[0, 'Q']]
        ]);

        $gameLogic = new GameLogic($board);
        $result = $gameLogic->undo(2);

        $this->assertEquals(1, $result);
    }
}
