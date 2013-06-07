<?php

class Board
{
    private $board;
    private $blackPieces;
    private $blackKing;
    private $whitePieces;
    private $whiteKing;
    private $turn;

    public function __construct()
    {
        $this->turn = Color::White;
        $this->blackPieces = array();
        $this->blackKing = null;
        $this->whitePieces = array();
        $this->whiteKing = null;

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
                            $piece = new Rook($x, $y, Color::Black);
                            break;
                        case 1:
                        case 6:
                            $piece = new Knight($x, $y, Color::Black);
                            break;
                        case 2:
                        case 5:
                            $piece = new Bishop($x, $y, Color::Black);
                            break;
                        case 3:
                            $piece = new King($x, $y, Color::Black);
                            $this->blackKing = $piece;
                            break;
                        case 4:
                            $piece = new Queen($x, $y, Color::Black);
                            break;
                        default:
                            $piece = null;
                            break;
                    }
                }
                else if ($y == 6)
                    $piece = new Pawn($x, $y, Color::Black);
                else if ($y == 1)
                    $piece = new Pawn($x, $y, Color::White);
                else if ($y == 0)
                {
                    switch ($x)
                    {
                        case 0:
                        case 7:
                            $piece = new Rook($x, $y, Color::White);
                            break;
                        case 1:
                        case 6:
                            $piece = new Knight($x, $y, Color::White);
                            break;
                        case 2:
                        case 5:
                            $piece = new Bishop($x, $y, Color::White);
                            break;
                        case 3:
                            $piece = new King($x, $y, Color::White);
                            $this->whiteKing = $piece;
                            break;
                        case 4:
                            $piece = new Queen($x, $y, Color::White);
                            break;
                        default:
                            $piece = null;
                            break;
                    }
                }
                else
                    $piece = null;

                if ($piece !== null)
                {
                    if ($piece->GetColor() == Color::Black)
                        $this->blackPieces[] = $piece;
                    else
                        $this->whitePieces[] = $piece;
                }

                $this->board[$x][$y] = $piece;
            }
        }
    }

    public function DrawBoard()
    {
        echo '<table style="border: 1px solid black;"><tr>';
        for ($i = 0; $i <= 9; $i++)
        {
            if ($i > 0 && $i <= 8)
                echo '<th>' . chr(96 + $i) . '</th>';
            else
                echo '<th></th>';
        }
        echo '</tr>';

        for ($y = 7; $y >= 0; $y--)
        {
            echo '<tr><td style="font-weight: bold;">' . ($y + 1) . '</td>';

            for ($x = 7; $x >= 0; $x--)
            {
                $blackCell = 'background-color: #e7d0a7';

                if (($x + $y) % 2 == 1)
                    $blackCell = 'background-color: #a67e5b;';

                if (isset($_SESSION['origin']))
                {
                    $origin = unserialize($_SESSION['origin']);

                    if ($this->GetPiece($origin) !== null &&
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
                        echo '<a href="index.php?action=move_target&x=' . $x . '&y=' . $y . '" class="cell">';
                    else
                        echo '<a href="index.php?action=move_origin&x=' . $x . '&y=' . $y . '" class="cell">';
                }

                if ($this->board[$x][$y] !== null)
                {
                    echo $this->board[$x][$y];
                }
                else if (isset($_SESSION['origin']))
                {
                    $origin = unserialize($_SESSION['origin']);

                    if ($this->GetPiece($origin) !== null &&
                            in_array(new Position($x, $y), $this->GetPiece($origin)->GetPossibleCells()))
                    {
                        echo '<a href="index.php?action=move_target&x=' . $x . '&y=' . $y . '" class="cell">';
                    }
                }

                echo '</a>';

                echo '</td>';
            }
            echo '<td style="font-weight: bold;">' . ($y + 1) . '</td></tr>';
        }

        echo '<tr>';

        for ($i = 0; $i <= 8; $i++)
        {
            if ($i > 0)
                echo '<th>' . chr(96 + $i) . '</th>';
            else
                echo '<th></th>';
        }
        echo '</tr>';

        echo '</table>';
    }

    public function Move($origin, $target)
    {
        $piece = $this->GetPiece($origin);
        if (in_array($target, $piece->GetPossibleCells()))
        {
            global $logs;
            
            $logs->Add(get_class($this->GetPiece($target)) . ' moved from ' . $origin . ' to ' . $target);
            
            
            
            // Eat a piece ?
            if ($this->GetPiece($target) !== null)
            {
                $logs->Add('Look ! This ' . Color::ColorToString($piece->GetColor()) . ' ' . get_class($piece) . 
                        ' just ate a poor little ' . Color::ColorToString($this->GetPiece($target)->GetColor()) . ' ' . get_class($this->GetPiece($target)) . ' !');
            }
            
            // Promotion
            if ((($piece->GetColor() == Color::White && $target->y == 7) || ($piece->GetColor() == Color::Black && $target->y == 0)) && get_class($piece) == 'Pawn')
            {
                $promotion = new Queen($target->x, $target->y, $piece->GetColor());
                $this->board[$target->x][$target->y] = $promotion;
                $logs->Add('This small pawn became very great !');
            }
            // Castling
            else if (false)
            {
                $logs->Add('Castling O_o !');
            }
            else
            {
                $this->board[$target->x][$target->y] = $piece;
            }

            $this->board[$origin->x][$origin->y] = null;
            $piece->SetPosition($target);
            
            return true;
        }
        else
            return false;
    }

    public function NextTurn()
    {
        $this->turn = ($this->turn == Color::White) ? Color::Black : Color::White;
    }

    public function DisplayTurn()
    {
        if ($this->turn == 0)
            return 'White player\'s turn !';
        else if ($this->turn == 1)
            return 'Black player\'s turn !';
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
        for ($x = 0; $x < 8; $x++)
        {
            for ($y = 0; $y < 8; $y++)
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
    
    public function GetWhiteKing()
    {
        return $this->whiteKing;
    }
    
    public function GetBlackKing()
    {
        return $this->blackKing;
    }

    public function KingCheck()
    {
        if ($this->turn == Color::White)
        {
            foreach ($this->blackPieces as $piece)
            {
                $piece->ComputePossibleCells($this);
                if (in_array($this->whiteKing->GetPosition(), $piece->GetPossibleCells()))
                {
                    global $logs;
                    $logs->Add('White king in check (by ' . get_class($piece) . ' in ' . $piece->GetPosition() . ') !');
                    $this->whiteKing->Check(true);
                }
            }
        }
        else
        {
            foreach ($this->whitePieces as $piece)
            {
                $piece->ComputePossibleCells($this);
                if (in_array($this->blackKing->GetPosition(), $piece->GetPossibleCells()))
                {
                    global $logs;
                    $logs->Add('Black king in check (by ' . get_class($piece) . ' in ' . $piece->GetPosition() . ') !');
                    $this->blackKing->Check(true);
                }
            }
        }
    }

}