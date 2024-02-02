<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Joyce0398\HiveGame\Hand;

use PHPUnit\Framework\TestCase;

class TestHand extends TestCase
{
    public function testGetAvailablePiecesSingle()
    {
        $hand = new Hand(["Q" => 0, "B" => 0, "S" => 0, "A" => 1, "G" => 0]);
        $this->assertEquals(['A' => 1], $hand->getAvailablePieces());
    }

    public function testGetAvailablePiecesEmpty()
    {
        $hand = new Hand(["Q" => 0, "B" => 0, "S" => 0, "A" => 0, "G" => 0]);
        $this->assertEquals([], $hand->getAvailablePieces());
    }

    public function testGetAvailablePiecesMultiple()
    {
        $hand = new Hand(["Q" => 0, "B" => 0, "S" => 2, "A" => 1, "G" => 0]);
        $this->assertEquals(['S' => 2, 'A' => 1], $hand->getAvailablePieces());
    }
}
