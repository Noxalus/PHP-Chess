<?php

require_once 'piece.php';

class Bishop extends Piece
{
    public function __construct($x, $y, $color)
    {
        parent::__construct($x, $y, $color);
    }

    public function ComputePossibleCells($board)
    {
        parent::ComputePossibleCells($board);
     
        $collisionBoard = $board->ComputeCollisionBoard($this->color);
        
        $position = new Position($this->position->x + 1, $this->position->y + 1);
        while(!Board::Out($position))
        {
            if (!$collisionBoard[$position->x][$position->y])
            {
                $this->possibleCells[] = $position;
                
                $piece = $board->GetPiece($position);
                if ($piece != null && $piece->GetColor() != $this->color)
                    break;
            }
            else
                break;
            
            $position = new Position($position->x + 1, $position->y + 1);
        }
        
        $position = new Position($this->position->x - 1, $this->position->y - 1);
        while(!Board::Out($position))
        {
            if (!$collisionBoard[$position->x][$position->y])
            {
                $this->possibleCells[] = $position;
                
                $piece = $board->GetPiece($position);
                if ($piece != null && $piece->GetColor() != $this->color)
                    break;
            }
            else
                break;
            
            $position = new Position($position->x - 1, $position->y - 1);
        }
        
        $position = new Position($this->position->x + 1, $this->position->y - 1);
        while(!Board::Out($position))
        {
            if (!$collisionBoard[$position->x][$position->y])
            {
                $this->possibleCells[] = $position;
                
                $piece = $board->GetPiece($position);
                if ($piece != null && $piece->GetColor() != $this->color)
                    break;
            }
            else
                break;
            
            $position = new Position($position->x + 1, $position->y - 1);
        }
        
        $position = new Position($this->position->x - 1, $this->position->y + 1);
        while(!Board::Out($position))
        {
            if (!$collisionBoard[$position->x][$position->y])
            {
                $this->possibleCells[] = $position;
                
                $piece = $board->GetPiece($position);
                if ($piece != null && $piece->GetColor() != $this->color)
                    break;
            }
            else
                break;
            
            $position = new Position($position->x - 1, $position->y + 1);
        }
    }
    
    public function __toString()
    {
        return '<img src="sprites/' . $this->color . '_bishop.png" class="piece" />';
    }
}