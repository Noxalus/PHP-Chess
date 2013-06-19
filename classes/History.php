<?php

/**
 * Class to store the history of the game
 */
class History
{
    private $history;
    private $index;

    public function __construct()
    {
        $this->history = array();
        $this->index = -1;
    }

    public function Add($origin, $target, $piece, $targetPiece, $type)
    {
        // Previous and move ? => we have to delete all former "next moves"
        if ($this->index != count($this->history) - 1)
        {
            if ($this->index == -1)
                $this->history = array();
            else
                $this->history = array_slice($this->history, 0, $this->index + 1);
        }

        $targetData = null;
        if ($targetPiece !== null)
        {
            $targetData = array(
                'type' => get_class($targetPiece)
            );
        }

        $typeData = null;
        if ($type !== null)
        {
            $typeData = array('name' => $type);
            if ($type == 'promotion')
            {
                // We save the pawn's history
                $typeData['history'] = $piece->GetHistory();
            }
            else if ($type == 'en passant')
            {
                // We save the target pawn's history
                $typeData['targetHistory'] = $targetPiece->GetHistory();
            }
        }

        $move = array(
            'piece' => array(
                'color' => $piece->GetColor(),
                'type' => get_class($piece),
                'origin' => $origin,
                'target' => $target
            ),
            'target' => $targetData,
            'type' => $typeData
        );

        $this->history[] = $move;

        $this->index++;
    }

    public function Previous(&$board)
    {
        if ($this->index == -1)
        {
            return false;
        }

        $move = $this->history[$this->index];
        $piece = $board->GetPiece($move['piece']['target']);
        
        if ($piece !== null)
        {
            $piece->Previous();
            switch ($move['type']['name'])
            {
                case 'classic':
                    $board->SimpleMove($move['piece']['target'], $move['piece']['origin']);
                    if ($move['target'] !== null)
                    {
                        $board->AddPiece($move['target']['type'], Color::Invert($move['piece']['color']), $move['piece']['target']);
                    }
                    break;

                case 'promotion':
                    $board->RemovePiece($board->GetPiece($move['piece']['target']));
                    $piece = $board->AddPiece('Pawn', $move['piece']['color'], $move['piece']['origin']);
                    $piece->SetHistory($move['type']['history']);
                    if ($move['target'] !== null)
                    {
                        $board->AddPiece($move['target']['type'], Color::Invert($move['piece']['color']), $move['piece']['target']);
                    }
                    break;
                case 'en passant':
                    $target = $move['piece']['target'];
                    $origin = $move['piece']['origin'];
                    $board->SimpleMove($target, $origin);
                    $evilPawnPosition = new Position($target->x, $target->y + (-1) * (Color::Factor($move['piece']['color'])));
                    $piece = $board->AddPiece('Pawn', Color::Invert($move['piece']['color']), $evilPawnPosition);
                    $piece->SetHistory($move['type']['targetHistory']);
                    break;
                case 'castling short':
                    $board->SimpleMove($move['piece']['target'], $move['piece']['origin']);
                    $king = $board->GetKing($move['piece']['color']);
                    $this->board[$king->GetPosition()->x][$king->GetPosition()->y] = null;
                    $newKingPosition = new Position($king->GetPosition()->x + 2, $king->GetPosition()->y);
                    $board->SimpleMove($king->GetPosition(), $newKingPosition);
                    break;
                case 'castling long':
                    $board->SimpleMove($move['piece']['target'], $move['piece']['origin']);
                    $king = $board->GetKing($move['piece']['color']);
                    $this->board[$king->GetPosition()->x][$king->GetPosition()->y] = null;
                    $newKingPosition = new Position($king->GetPosition()->x - 2, $king->GetPosition()->y);
                    $board->SimpleMove($king->GetPosition(), $newKingPosition);
                    break;
            }

            $this->index--;
            return true;
        }
        else
        {
            return false;
        }
    }

    public function Next(&$board)
    {
        $this->index++;
        if ($this->index > count($this->history) - 1 || $this->index < 0)
        {
            return false;
        }

        $move = $this->history[$this->index];
        $piece = $board->GetPiece($move['piece']['origin']);
        
        if ($piece !== null)
        {
            $piece->Next();
            switch ($move['type']['name'])
            {
                case 'classic':
                    if ($move['target'] !== null)
                    {
                        $board->RemovePiece($board->GetPiece($move['piece']['target']));
                    }
                    $board->SimpleMove($move['piece']['origin'], $move['piece']['target']);

                    break;

                case 'promotion':
                    break;
                case 'en passant':
                case 'castling short':
                    break;
                case 'castling long':
                    break;
            }

            return true;
        }
        else
        {
            return false;
        }
    }

    public function Display()
    {
        global $logs;
        foreach ($this->history as $move)
        {
            $logs->Add(Color::ColorToString($move['piece']['color']) . ' ' . $move['piece']['origin'] . ' => ' . $move['piece']['target'], 'success');
        }
    }

}