<?php

require_once 'piece.php';

class Pawn extends Piece
{
    public function __construct($x, $y, $color)
    {
        parent::__construct($x, $y, $color);
    }

    public function __toString()
    {
        return '<img src="sprites/' . $this->color . '_pawn.png" />';
    }

    public function ComputePossibleCells($collisionBoard)
    {
        parent::ComputePossibleCells($collisionBoard);
        
        $lenght = 1;
        if (count($this->history) == 0)
            $lenght = 2;
        
        if ($this->color == 0)
            $lenght *= -1;
        
        $this->possibleCells[] = new Position($this->position->x, $this->position->y + $lenght);
    }
}