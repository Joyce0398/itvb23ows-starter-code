<?php
namespace Joyce0398\HiveGame;

class Hand {
    private array $hand;

    public static array $DEFAULT_HAND = ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3];

    public function __construct(array $hand = null){
        if($hand == null) {
            $this->hand = Hand::$DEFAULT_HAND;
        } else {
        $this->hand = $hand;
        }
    }

    public function handSize() {
        return array_sum($this->hand);
    }

    public function hasPiece(string $piece): bool
    {
        return $this->hand[$piece] > 0;
    }

    public function getAvailablePieces()
    {
        return array_filter($this->hand);
    }

    public function toArray()
    {
        return $this->hand;
    }
}
