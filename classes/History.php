<?php

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
            $this->history = array_slice($this->history, $this->index);
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
            $typeData = array(
              'name' => $type  
            );
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
        if ($move['type']['name'] == 'classic')
        {
            $board->SimpleMove($move['piece']['target'], $move['piece']['origin']);
            if ($move['target'] !== null)
            {
                $board->AddPiece($move['target']['type'], Color::Invert($move['piece']['color']), $move['piece']['target']);
            }
        }
        
        $this->index--;
        return true;
    }
    
    public function Next(&$board)
    {
        if ($this->index == count($this->history) - 1)
        {
            return false;
        }
        
        $this->index++;
        return true;
    }
    
    public function Display()
    {
        global $logs;
        foreach($this->history as $move)
        {
            $logs->Add(Color::ColorToString($move['piece']['color']) . ' ' . $move['piece']['origin'] . ' => ' . $move['piece']['target'], 'success');
        }
    }
}