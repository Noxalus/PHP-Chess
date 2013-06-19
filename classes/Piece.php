<?php

class Piece
{
    protected $position;
    protected $color;
    protected $possibleCells;
    protected $history;

    protected function __construct($x, $y, $color)
    {
        $this->position = new Position($x, $y);
        $this->color = $color;
        $this->possibleCells = array();
        $this->history = array();
    }

    public function GetPosition()
    {
        return $this->position;
    }

    public function GetColor()
    {
        return $this->color;
    }

    public function ComputePossibleCells($board)
    {
        return (count($this->possibleCells) == 0);
    }

    public function GetPossibleCells()
    {
        return $this->possibleCells;
    }
    
    public function CleanPossibleCells()
    {
        $this->possibleCells = array();
    }
    
    public function CleanUnsecuredCells($cells)
    {
        foreach($cells as $cell)
        {
            $count = count($this->possibleCells);
            for($i = 0; $i < $count; $i++)
            {
                if ($cell === $this->possibleCells[$i])
                {
                    unset($this->possibleCells[$i]);
                }
            }
            sort($this->possibleCells);
        }
    }

    public function SetPosition($position, $turn)
    {
        if ($turn === null)
        {
            $count = count($this->history);
            if ($count == 1)
                $this->history = array();
            else
                $this->history = array_slice($this->history, 0, $count - 1);
        }
        else
            $this->history[] = array($turn, $this->position);
        $this->position = $position;
    }

    public function IsFirstMove()
    {
        return (count($this->history) == 0);
    }

    public function GetHistory()
    {
        return $this->history;
    }

    public function SetHistory($history)
    {
        $this->history = $history;
    }

    public function CleanHistory()
    {
        $this->history = array();
    }

    public function __toString()
    {
        
    }

}