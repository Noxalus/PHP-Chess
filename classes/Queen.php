<?php

require_once 'piece.php';

class Queen extends Piece
{
    public function __construct($x, $y, $color)
    {
        parent::__construct($x, $y, $color);
    }

    public function ComputePossibleCells($board)
    {
        parent::ComputePossibleCells($board);
     
        $collisionBoard = $board->ComputeCollisionBoard($this->color);
        
        for($x = $this->position->x + 1; $x < 8; $x++)
        {
            if (!$collisionBoard[$x][$this->position->y])
            {
                $this->possibleCells[] = new Position($x, $this->position->y);

                $piece = $board->GetPiece(new Position($x, $this->position->y));
                if ($piece != null && $piece->GetColor() != $this->color)
                    break;
            }
            else
                break;
        }
        
        for($x = $this->position->x - 1; $x >= 0; $x--)
        {
            if (!$collisionBoard[$x][$this->position->y])
            {
                $this->possibleCells[] = new Position($x, $this->position->y);
                 
                $piece = $board->GetPiece(new Position($x, $this->position->y));
                if ($piece != null && $piece->GetColor() != $this->color)
                    break;
            }
            else
                break;
        }
        
        for($y = $this->position->y + 1; $y < 8; $y++)
        {
            if (!$collisionBoard[$this->position->x][$y])
            {
                $this->possibleCells[] = new Position($this->position->x, $y);
                
                $piece = $board->GetPiece(new Position($this->position->x, $y));
                if ($piece != null && $piece->GetColor() != $this->color)
                    break;
            }
            else
                break;
        }
        
        for($y = $this->position->y - 1; $y >= 0; $y--)
        {
            if (!$collisionBoard[$this->position->x][$y])
            {
                $this->possibleCells[] = new Position($this->position->x, $y);

                $piece = $board->GetPiece(new Position($this->position->x, $y));
                if ($piece != null && $piece->GetColor() != $this->color)
                    break;
            }
            else
                break;
        }
        
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
        return '<img src="sprites/' . $this->color . '_queen.png" class="piece" />';
    }
}