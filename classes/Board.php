<?php

class Board
{
    private $board;
    private $blackPieces;
    private $blackKing;
    private $whitePieces;
    private $whiteKing;
    private $turn;
    private $turnCounter;
    private $cycle;
    private $history;
    
    public function __construct()
    {
        $this->turn = Color::White;
        $this->blackPieces = array();
        $this->blackKing = null;
        $this->whitePieces = array();
        $this->whiteKing = null;
        $this->turnCounter = 0;
        $this->cycle = 0;
        $this->history = array();
    }
    
    public function Init()
    {
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
                    if ($this->GetTurn() != $this->GetPiece(new Position($x, $y))->GetColor())
                    {
                        if (isset($_SESSION['origin']))
                            echo '<a href="index.php?action=move_target&x=' . $x . '&y=' . $y . '" class="cell">';
                        else
                            echo '<a href="#" class="cell">';
                    }
                    else
                        echo '<a href="index.php?action=move_origin&x=' . $x . '&y=' . $y . '" class="cell">';
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
                
                if ($this->board[$x][$y] !== null)
                {
                    echo $this->board[$x][$y];
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
            
            $logs->Add(get_class($piece) . ' moved from ' . $origin . ' to ' . $target);
            
            // Eat a piece ?
            if ($this->GetPiece($target) !== null)
            {
                $logs->Add('Look ! This ' . Color::ColorToString($piece->GetColor()) . ' ' . get_class($piece) . 
                        ' just ate a poor little ' . Color::ColorToString($this->GetPiece($target)->GetColor()) . ' ' . 
                        get_class($this->GetPiece($target)) . ' !');

                $this->RemovePiece($this->GetPiece($target));
            }
            // "En passant" ?
            else if (get_class($piece) == 'Pawn')
            {
                $evilPawn = $this->GetPiece(new Position($target->x, $target->y + (-1) * (Color::Factor($piece->GetColor()))));
                
                if ($evilPawn !== null && $evilPawn->GetColor() != $piece->GetColor() && get_class($evilPawn) == 'Pawn')
                {
                    $this->board[$evilPawn->GetPosition()->x][$evilPawn->GetPosition()->y] = null;
                    $this->RemovePiece($evilPawn);
                    $logs->Add('Oh My God ! O_o This is an "en passant" capture, unbelievable !!');
                }
            }
            
            // Promotion
            if ($this->IsPromotion($piece, $target))
            {
                $promotion = new Queen($target->x, $target->y, $piece->GetColor());
                switch($_SESSION['promotion'])
                {
                    case 'rook':
                        $promotion = new Rook($target->x, $target->y, $piece->GetColor());
                        break;
                    case 'knight':
                        $promotion = new Knight($target->x, $target->y, $piece->GetColor());
                        break;
                    case 'bishop':
                        $promotion = new Bishop($target->x, $target->y, $piece->GetColor());
                        break;
                    default:
                        break;
                }
                
                unset($_SESSION['promotion']);
                $this->board[$target->x][$target->y] = $promotion;
                $logs->Add('This small pawn became very great !');
            }
            // Castling
            else if (get_class($piece) == 'Rook' && $piece->IsFirstMove())
            {
                $king = &$this->whiteKing;
                if ($piece->GetColor() === Color::Black)
                    $king = &$this->blackKing;
                
                if (!$king->CheckAtLeastOnce() && $king->IsFirstMove())
                {
                    $logs->Add('Castling O_o !');
                    $this->board[$king->GetPosition()->x][$king->GetPosition()->y] = null;
                    // Castling shot
                    if (($king->GetPosition()->x - 3) == $piece->GetPosition()->x)
                    {
                        $logs->Add('Castling short !!');
                        $newKingPosition = new Position($king->GetPosition()->x - 2, $king->GetPosition()->y);
                        $this->board[$newKingPosition->x][$newKingPosition->y] = $king;
                        $king->SetPosition($newKingPosition, $this->turnCounter);
                    }
                    // Castling long
                    else if (($king->GetPosition()->x + 4) == $piece->GetPosition()->x)
                    {
                        $logs->Add('Castling long !!!');
                        $newKingPosition = new Position($king->GetPosition()->x + 2, $king->GetPosition()->y);
                        $this->board[$newKingPosition->x][$newKingPosition->y] = $king;
                        $king->SetPosition($newKingPosition, $this->turnCounter);
                    }
                    
                    $this->board[$target->x][$target->y] = $piece;
                }
            }
            else
            {
                $this->board[$target->x][$target->y] = $piece;
            }

            $this->board[$origin->x][$origin->y] = null;
            $piece->SetPosition($target, $this->turnCounter);

            $this->history[$this->turnCounter + 1] = array($origin, $target);
            
            return true;
        }
        else
            return false;
    }

    public function NextTurn()
    {
        global $logs;
        
        
        $this->turn = ($this->turn == Color::White) ? Color::Black : Color::White;
        $this->KingCheck(Color::White);
        $this->KingCheck(Color::Black);
        
        $this->cycle++;
        
        if ($this->cycle == 2)
        {
            $logs->Add('---------- Turn #' . (round($this->turnCounter / 2) + 1)  . ' ----------');
            $this->cycle = 0;
        }
        
        $this->turnCounter++;
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

    public function DisplayPieces()
    {
        foreach($this->whitePieces as $whitePiece)
        {
            echo $whitePiece;
        }
        echo '<br />';
        foreach($this->blackPieces as $blackPiece)
        {
            echo $blackPiece;
        }
    }
    
    private function RemovePiece($target)
    {
        $pieces = $this->GetPieces($target->GetColor());
        
        $count = count($pieces);
                
        for($i = 0; $i < $count; $i++)
        {
            if ($pieces[$i] === $target)
            {
                $this->board[$pieces[$i]->GetPosition()->x][$pieces[$i]->GetPosition()->y] = null;
                unset($pieces[$i]);
                return;
            }
        }
        
        echo 'COUCOU';
        exit;
    }
    
    public function KingCheck($color, $position = null)
    {
        global $logs;
        $pieces = &$this->blackPieces;
        $king = $this->whiteKing;
        
        if ($color === Color::Black)
        {
            $pieces = &$this->whitePieces;
            $king = $this->blackKing;
        }
        
        if ($position === null)
        {
            $position = $king->GetPosition();
            $save = $this->board[$position->x][$position->y];
        }
        else
        {
            // Save
            $save = $this->board[$position->x][$position->y];
            $this->board[$position->x][$position->y] = $king;
        }
        
        foreach($pieces as &$piece)
        {
            if (get_class($piece) !== 'King')
                $piece->ComputePossibleCells($this);
            
            if (in_array($position, $piece->GetPossibleCells()))
            {
                if ($position === null)
                {
                    $logs->Add(ucfirst(Color::ColorToString($color)) . ' king in check (by ' . get_class($piece) . ' in ' . $piece->GetPosition() . ') !');
                    $king->SetCheck(true);
                }
                else
                {
                    $this->board[$position->x][$position->y] = $save;
                }
                
                return true;
            }
        }
        
        if ($position === null)
        {
            $king->SetCheck(false);
        }
        else
        {
            $this->board[$position->x][$position->y] = $save;
        }
        
        return false;
    }
    
    public function GetTurnCounter()
    {
        return $this->turnCounter;
    }
    
    public function IsPromotion($piece, $target)
    {
        return ((($piece->GetColor() == Color::White && $target->y == 7) || ($piece->GetColor() == Color::Black && $target->y == 0)) && get_class($piece) == 'Pawn');
    }
    
    private function GetPieces($color)
    {
        $pieces = &$this->whitePieces;
        if ($color === Color::Black)
            $pieces = &$this->blackPieces;
            
        return $pieces;
    }
    
    /** Debug **/
    public function DisplayPossibleCells($color)
    {
        global $logs;
        $pieces = ($color === Color::White) ? $this->whitePieces : $this->blackPieces;
        
        foreach($pieces as $piece)
        {
            $logs->Add(get_class($piece) . ' at ' . $piece->GetPosition() . ':<br />', false);
            foreach($piece->GetPossibleCells() as $cell)
            {
                $logs->Add("\t" . '=> ' . $cell . '<br />', false);
            }
        }
    }
    
    public function DisplayHistory()
    {
        foreach($this->history as $turn => $positions)
        {
            echo $turn . ': ' . $positions[0] . ' => ' . $positions[1] . '<br />';
        }
    }
}