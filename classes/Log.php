<?php

class Log
{   
    private static $colors = array
        (
            'info' => 'blue',
            'warning' => '#ffd200', // Yellow
            'error' => 'red', 
            'success' => '#006700', // Green
            'game' => 'black',
            'debug' => 'grey'
        );
    
    private $messages;
            
    public function __construct()
    {
        $this->messages = array();
        $this->Add('---------- Turn #1 ----------', 'game');
    }
    
    public function Add($message, $type)
    {
        $this->messages[] = array(
            'content' => $message,
            'date' => new DateTime(),
            'type' => $type
        );
    }
    
    public function Display($date = true)
    {
        $counter = 0;        
        foreach($this->messages as $message)
        {
            $string = '';
            
            if ($date)
            $string .= '[<i>' . $message['date']->format('h:i:s') . '</i>] ';
            
            $string .= '<span style="color:' . Log::$colors[$message['type']] . ';"> ' . $message['content'] . '</span>';
            
            echo $string . '<br />';
            $counter++;
        }
    }
    
    public function Clear()
    {
        $this->messages = array();
    }
}