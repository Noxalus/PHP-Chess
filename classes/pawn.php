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
        
        // "En passant"
        $colorFactor = ($this->GetColor() === Color::White) ? 1 : -1;
        $left = new Position($this->GetPosition()->x - 1, $this->GetPosition()->y);
        if ((!Board::Out($left) && $board->GetPiece($left) != null))
        {
            $leftPiece = $board->GetPiece($left);
            if (get_class($leftPiece) == 'Pawn' && $leftPiece->GetColor() != $this->GetColor())
            {
                $history = $leftPiece->GetHistory();
                $lastMove = $history[count($history) - 1];
                if ($lastMove !== null && $lastMove[0] == $board->GetTurnCounter() - 1)
                {
                    $lastPosition = $lastMove[1];
                    if ($lastPosition->y == $left->y + (2 * $colorFactor))
                    {
                        $this->possibleCells[] = new Position($left->x, $left->y + 1 * $colorFactor);
                    }
                }
            }
        }
        
        $right = new Position($this->GetPosition()->x + 1, $this->GetPosition()->y);
        if ((!Board::Out($right) && $board->GetPiece($right) != null))
        {
            $rightPiece = $board->GetPiece($right);
            if (get_class($rightPiece) == 'Pawn' && $rightPiece->GetColor() != $this->GetColor())
            {
                $history = $rightPiece->GetHistory();
                $lastMove = $history[count($history) - 1];
                
                if ($lastMove !== null && $lastMove[0] == $board->GetTurnCounter() - 1)
                {
                    $lastPosition = $lastMove[1];
                    if ($lastPosition->y == $right->y + (2 * $colorFactor))
                    {
                        $this->possibleCells[] = new Position($right->x, $right->y + 1 * $colorFactor);
                    }
                }
            }
        }

        
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