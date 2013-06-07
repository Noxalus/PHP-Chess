<?php

require_once 'piece.php';

class Pawn extends Piece
{
    public function __construct($x, $y, $color)
    {
        parent::__construct($x, $y, $color);
    }

    public function ComputePossibleCells($board)
    {
        parent::ComputePossibleCells($board);

        $lenght = 1;
        
        if ($this->color == Color::Black)
            $lenght *= -1;
        
        $position = new Position($this->position->x, $this->position->y + $lenght);
        if (!Board::Out($position) && $board->GetPiece($position) == null)
            $this->possibleCells[] = clone($position);
        
        // Piece to eat
        $position->x += 1;
        if (!Board::Out($position) && $board->GetPiece($position) !== null && $board->GetPiece($position)->GetColor() != $this->color)
             $this->possibleCells[] = clone($position);
        $position->x -= 2;
        if (!Board::Out($position) && $board->GetPiece($position) !== null && $board->GetPiece($position)->GetColor() != $this->color)
             $this->possibleCells[] = clone($position);
        
        if (count($this->history) == 0)
        {
            if ($this->color == Color::Black)
                $position->y -= 1;
            else
                $position->y += 1;
            
            $position->x = $this->position->x;
            
            if ($board->GetPiece($position) == null)
                $this->possibleCells[] = clone($position);
        }
    }
    
    public function __toString()
    {
        return '<img src="sprites/' . $this->color . '_pawn.png" class="piece" />';
    }
}