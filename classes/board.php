<?php

class Board
{
    private $board;
    private $turn;

    public function __construct()
    {
        $this->turn = 0;
        
        for ($x = 0; $x < 8; $x++)
        {
            $piece = null;

            $this->board[$x] = array();
            for ($y = 0; $y < 8; $y++)
            {
                if ($y == 7)
                {
                    switch ($x)
                    {
                        case 0:
                        case 7:
                            $piece = new Rook($x, $y, 0);
                            break;
                        case 1:
                        case 6:
                            $piece = new Knight($x, $y, 0);
                            break;
                        case 2:
                        case 5:
                            $piece = new Bishop($x, $y, 0);
                            break;
                        case 3:
                            $piece = new Queen($x, $y, 0);
                            break;
                        case 4:
                            $piece = new King($x, $y, 0);
                            break;
                        default:
                            $piece = null;
                            break;
                    }
                }
                else if ($y == 6)
                    $piece = new Pawn($x, $y, 0);
                else if ($y == 1)
                    $piece = new Pawn($x, $y, 1);
                else if ($y == 0)
                {
                    switch ($x)
                    {
                        case 0:
                        case 7:
                            $piece = new Rook($x, $y, 1);
                            break;
                        case 1:
                        case 6:
                            $piece = new Knight($x, $y, 1);
                            break;
                        case 2:
                        case 5:
                            $piece = new Bishop($x, $y, 1);
                            break;
                        case 3:
                            $piece = new Queen($x, $y, 1);
                            break;
                        case 4:
                            $piece = new King($x, $y, 1);
                            break;
                        default:
                            $piece = null;
                            break;
                    }
                }
                else
                    $piece = null;

                $this->board[$x][$y] = $piece;
            }
        }
    }

    public function DrawBoard()
    {
        /*
          echo '<pre>';
          print_r($this->board);
          echo '</pre>';
         */

        echo '<table style="border: 1px solid black;">';
        
        for ($y = 7; $y >= 0; $y--)
        {
            echo '<tr>';
                
            for ($x = 7; $x >= 0; $x--)
            {
                $blackCell = 'background-color: #e7d0a7';
                
                if (($x + $y) % 2 == 1)
                    $blackCell = 'background-color: #a67e5b;';
                
                if (isset($_SESSION['origin']))
                { 
                    $origin = unserialize($_SESSION['origin']);
                
                    if($this->GetPiece($origin) !== null &&
                        in_array(new Position($x, $y), $this->GetPiece($origin)->GetPossibleCells()))
                    {
                        $blackCell = 'background-color: yellow;';
                    }
                }
                
                echo '<td style="width: 100px; height: 100px; text-align: center; vertical-align: center; border: 1px solid black;' . $blackCell . '">';
                echo '[' . $x . ',' . $y . ']<br />';

                if ($this->board[$x][$y] !== null)
                {
                    if (isset($_SESSION['origin']))
                        echo '<a href="index.php?action=move_target&x=' . $x . '&y=' . $y . '">';
                    else
                        echo '<a href="index.php?action=move_origin&x=' . $x . '&y=' . $y . '">';
                }
                
                if ($this->board[$x][$y] !== null)
                {
                    echo $this->board[$x][$y];
                }
                else if (isset($_SESSION['origin']))
                { 
                    $origin = unserialize($_SESSION['origin']);
                
                    if($this->GetPiece($origin) !== null &&
                        in_array(new Position($x, $y), $this->GetPiece($origin)->GetPossibleCells()))
                    {
                        echo '<a href="index.php?action=move_target&x=' . $x . '&y=' . $y . '">';
                        echo 'Click here!';
                    }
                }

                echo '</a>';

                echo '</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    }
    
    public function Move($origin, $target)
    {
        if (in_array($target, $this->GetPiece($origin)->GetPossibleCells()))
        {
            $this->board[$target->x][$target->y] = $this->GetPiece($origin);
            $this->board[$origin->x][$origin->y] = null;
            $this->board[$target->x][$target->y]->SetPosition($target);

            return true;
        }
        else
            return false;
    }
    
    public function NextTurn()
    {
        $this->turn = ($this->turn + 1) % 2;
    }
    
    public function DisplayTurn()
    {
        if ($this->turn == 0)
            echo 'Au tour du joueur blanc !';
        else if ($this->turn == 1)
            echo 'Au tour du joueur noir !';
    }
    
    public function GetTurn()
    {
        return $this->turn;
    }
    
    public function GetPiece($position)
    {
        return $this->board[$position->x][$position->y];
    }
    
    public function ComputeCollisionBoard($color)
    {
        $collisionBoard = array();
        for($x = 0; $x < 8; $x++)
        {
            for($y = 0; $y < 8; $y++)
            {
                $position = new Position($x, $y);
                if ($this->GetPiece($position) == null || $this->GetPiece($position)->GetColor() != $color)
                {
                    $collisionBoard[$x][$y] = false;
                }
                else
                    $collisionBoard[$x][$y] = true;
            }
        }
        
        return $collisionBoard;
    }
    
    public static function Out($position)
    {
        if ($position->x > 7 || $position->x < 0 ||
                $position->y > 7 || $position->y < 0)
        {
            return true;
        }
        
        return false;
    }
}