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
        $this->board = array();
        $this->turn = Color::White;
        $this->blackPieces = array();
        $this->blackKing = null;
        $this->whitePieces = array();
        $this->whiteKing = null;
        $this->turnCounter = 0;
        $this->cycle = 0;
        $this->history = new History();
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

                    $target = new Position($x, $y);
                    if ($this->GetPiece($origin) !== null &&
                        in_array($target, $this->GetPiece($origin)->GetPossibleCells()))
                    {
                        if ($this->GetPiece($target) !== null)
                            $blackCell = 'background-color: red;';
                        else
                            $blackCell = 'background-color: green;';
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
        global $logs;

        $piece = $this->GetPiece($origin);
        $targetPiece = $this->GetPiece($target);
        $type = 'classic';
        if (in_array($target, $piece->GetPossibleCells()))
        {
            $logs->Add(get_class($piece) . ' moved from ' . $origin . ' to ' . $target, 'info');
            
            // Eat a piece ?
            if ($targetPiece !== null)
            {
                $logs->Add('Look ! This ' . Color::ColorToString($piece->GetColor()) . ' ' . get_class($piece) . 
                        ' just ate a poor little ' . Color::ColorToString($targetPiece->GetColor()) . ' ' . 
                        get_class($targetPiece) . ' !', 'success');

                $this->RemovePiece($targetPiece);
            }
            // "En passant" ?
            else if (get_class($piece) == 'Pawn')
            {
                $evilPawn = $this->GetPiece(new Position($target->x, $target->y + (-1) * (Color::Factor($piece->GetColor()))));
                
                if ($evilPawn !== null && $evilPawn->GetColor() != $piece->GetColor() && get_class($evilPawn) == 'Pawn')
                {
                    $this->board[$evilPawn->GetPosition()->x][$evilPawn->GetPosition()->y] = null;
                    $this->RemovePiece($evilPawn);
                    $logs->Add('Oh My God ! O_o This is an "en passant" capture, unbelievable !!', 'success');
                    $type = 'en passant';
                    $targetPiece = $evilPawn;
                }
            }
            
            // Promotion
            if ($this->IsPromotion($piece, $target))
            {
                $promotion = ucfirst($_SESSION['promotion']);
                if (!in_array($promotion, array('Rook', 'Knight', 'Bishop', 'Queen')))
                        $promotion = 'Queen';
                
                $this->RemovePiece($piece);
                $piece = $this->AddPiece($promotion, $piece->GetColor(), $target);
                unset($_SESSION['promotion']);
                $logs->Add('This small pawn got a promotion and became very great !', 'success');
                $type = 'promotion';
            }
            // Castling
            else if (get_class($piece) == 'Rook' && $piece->IsFirstMove())
            {
                $king = &$this->GetKing($piece->GetColor());
                
                if (!$king->CheckAtLeastOnce() && $king->IsFirstMove())
                {
                    // Castling short
                    if (($king->GetPosition()->x - 3) == $piece->GetPosition()->x && $king->GetPosition()->y == $target->y)
                    {
                        $this->board[$king->GetPosition()->x][$king->GetPosition()->y] = null;
                        $logs->Add('Castling short !!', 'success');
                        $newKingPosition = new Position($king->GetPosition()->x - 2, $king->GetPosition()->y);
                        $this->board[$newKingPosition->x][$newKingPosition->y] = $king;
                        $king->SetPosition($newKingPosition, $this->turnCounter);
                        $type = 'castling short';
                    }
                    // Castling long
                    else if (($king->GetPosition()->x + 4) == $piece->GetPosition()->x && $king->GetPosition()->y == $target->y)
                    {
                        $this->board[$king->GetPosition()->x][$king->GetPosition()->y] = null;
                        $logs->Add('Castling long !!!', 'success');
                        $newKingPosition = new Position($king->GetPosition()->x + 2, $king->GetPosition()->y);
                        $this->board[$newKingPosition->x][$newKingPosition->y] = $king;
                        $king->SetPosition($newKingPosition, $this->turnCounter);
                        $type = 'castling long';
                    }
                }
                $this->board[$target->x][$target->y] = $piece;
            }
            else
            {
                $this->board[$target->x][$target->y] = $piece;
            }

            $this->board[$origin->x][$origin->y] = null;
            $piece->SetPosition($target, $this->turnCounter);

            $this->history->Add($origin, $target, $piece, $targetPiece, $type);
            
            return true;
        }
        else
            return false;
    }

    public function NextTurn($kingCheck = true)
    {
        global $logs;
        
        $this->turn = ($this->turn == Color::White) ? Color::Black : Color::White;
        
        $this->CleanPossibleCells(Color::White);
        $this->CleanPossibleCells(Color::Black);
        
        if ($kingCheck)
        {
            $this->KingCheck(Color::White);
            $this->KingCheck(Color::Black);
        }
        
        if ($this->turn === Color::White)
        {
            $logs->Add('---------- Turn #' . (round($this->turnCounter / 2) + 1)  . ' ----------', 'game');
        }
        
        $this->turnCounter++;
    }
    
    public function PreviousTurn()
    {
        global $logs;
        
        $this->turn = ($this->turn == Color::White) ? Color::Black : Color::White;
        
        $this->turnCounter--;

        if ($this->turn === Color::White)
        {
            $logs->Add('---------- Turn #' . (round($this->turnCounter / 2) + 1)  . ' ----------', 'game');
        }
        
    }

    public function DisplayTurn()
    {
        return ($this->turn === Color::White) ? 'White player\'s turn !' : 'Black player\'s turn !';
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
    
    public function &GetKing($color)
    {
        $king = &$this->whiteKing;
        if ($color === Color::Black)
            $king = &$this->blackKing;
        
        return $king;
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
    
    public function RemovePiece($target)
    {
        $pieces = &$this->GetPieces($target->GetColor());
        
        $count = count($pieces);
        for($i = 0; $i < $count; $i++)
        {
            if ($pieces[$i] === $target)
            {
                $this->board[$pieces[$i]->GetPosition()->x][$pieces[$i]->GetPosition()->y] = null;
                unset($pieces[$i]);
                sort($pieces);
                return;
            }
        }
        
        echo 'COUCOU';
        exit;
    }
    
    /** King check **/
    public function KingCheck($color)
    {
        $king = $this->GetKing($color);
        $piece = $this->IsUnsecuredCell($color, $king->GetPosition());
        
        if ($piece != null)
        {
            global $logs;

            $logs->Add(ucfirst(Color::ColorToString($color)) . ' king in check (by ' . get_class($piece) . ' in ' . $piece->GetPosition() . ') !', 'warning');

            return true;
        }
        
        return false;
    }
    
    public function IsUnsecuredCell($color, $position)
    {
        $pieces = $this->GetPieces(Color::Invert($color));
        
        foreach($pieces as $piece)
        {
            if (get_class($piece) != 'King')
            {
                $piece->ComputePossibleCells($this);
                if (in_array($position, $piece->GetPossibleCells()))
                {
                    return $piece;
                }
            }
        }
        
        return null;
    }
    
    public function GetUnsecuredCells($color)
    {
        $unsecuredCells = array();
        $pieces = $this->GetPieces($color);
        foreach($pieces as $piece)
        {
            $x = $piece->GetPosition()->x;
            $y = $piece->GetPosition()->y;
            if ($x == 5 && $y == 2)
            {
                echo 'COUCOU';
            }
            
            $currentUnsecuredCells = array();
            foreach($piece->GetPossibleCells() as $cell)
            {
                $board = clone $this;
                $board->CleanPossibleCells($color);
                $board->SimpleMove($piece->GetPosition(), $cell, false);
                
                if ($board->KingCheck($color))
                    $currentUnsecuredCells[] = $cell;
            }
            
            if (!empty($currentUnsecuredCells))
                $unsecuredCells[] = array($piece, $currentUnsecuredCells);
        }
        
        return $unsecuredCells;
    }
    
    public function CleanUnsecuredCells($piece, $unsecuredCells)
    {
        $piece->CleanUnsecuredCells($unsecuredCells);
    }
    
    public function GetTurnCounter()
    {
        return $this->turnCounter;
    }
    
    public function IsPromotion($piece, $target)
    {
        return ((($piece->GetColor() == Color::White && $target->y == 7) || ($piece->GetColor() == Color::Black && $target->y == 0)) && get_class($piece) == 'Pawn');
    }
    
    private function &GetPieces($color)
    {
        $pieces = &$this->whitePieces;
        if ($color === Color::Black)
            $pieces = &$this->blackPieces;
            
        return $pieces;
    }
    
    /** History **/
    public function Previous()
    {
        if ($this->history->Previous($this))
        {
            global $logs;
            
            $this->PreviousTurn();
            $logs->Add('Please let me play again this move !', 'warning');
        }
    }
    
    public function Next()
    {
        $this->history->Next($this);
    }
    
    public function SimpleMove($origin, $target, $setPosition = true)
    {
        global $logs;
        
        $piece = $this->GetPiece($origin);
        
        if ($piece === null)
        {
            $logs->Add('No piece at this position => ' . $origin . ', we can\'t move it to ' . $target, 'error');
            return false;
        }
        
        $this->board[$target->x][$target->y] = $piece;
        $this->board[$origin->x][$origin->y] = null;
        
        if ($setPosition)
            $piece->SetPosition($target, null);
        
        return true;
    }
    
    public function AddPiece($type, $color, $position)
    {
        $pieces = &$this->GetPieces($color);
        $piece = null;
        switch ($type)
        {
            case 'Pawn':
                $piece = new Pawn($position->x, $position->y, $color);
                break;
            case 'Rook':
                $piece = new Rook($position->x, $position->y, $color);
                break;
            case 'Knight':
                $piece = new Knight($position->x, $position->y, $color);
                break;
            case 'Bishop':
                $piece = new Bishop($position->x, $position->y, $color);
                break;
            case 'Queen':
                $piece = new Queen($position->x, $position->y, $color);
                break;
            case 'King':
                $piece = new King($position->x, $position->y, $color);
                break;
            default:
                // Error
                $piece = new Pawn($position->x, $position->y, $color);
                break;
        }
        
        if ($this->GetPiece($position) !== null)
            $this->RemovePiece ($position);
        
        $this->board[$position->x][$position->y] = $piece;
        $pieces[] = $piece;
        
        return $piece;
    }
    
    public function CleanPossibleCells($color)
    {
        foreach($this->GetPieces($color) as $piece)
        {
            $piece->CleanPossibleCells();
        }
    }
    
    /** Debug **/
    public function DisplayPossibleCells($color)
    {
        global $logs;
        $pieces = ($color === Color::White) ? $this->whitePieces : $this->blackPieces;
        
        foreach($pieces as $piece)
        {
            $logs->Add(get_class($piece) . ' at ' . $piece->GetPosition() . ':<br />', 'debug');
            foreach($piece->GetPossibleCells() as $cell)
            {
                $logs->Add("\t" . '=> ' . $cell . '<br />', 'debug');
            }
        }
    }
    
    public function DisplayHistory()
    {
        $this->history->Display();
    }
}