<?php

class board
{
    private $board;

    public function __construct()
    {
        for ($x = 0; $x < 8; $x++)
        {
            $piece = null;

            $this->board[$x] = array();
            for ($y = 0; $y < 8; $y++)
            {
                if ($x == 0)
                {
                    switch ($y)
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
                            $piece = new King($x, $y, 0);
                            break;
                        case 4:
                            $piece = new Queen($x, $y, 0);
                            break;
                        default:
                            $piece = null;
                            break;
                    }
                }
                else if ($x == 1)
                    $piece = new Pawn($x, $y, 0);
                else if ($x == 6)
                    $piece = new Pawn($x, $y, 1);
                else if ($x == 7)
                {
                    switch ($y)
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
                            $piece = new King($x, $y, 1);
                            break;
                        case 4:
                            $piece = new Queen($x, $y, 1);
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
        for ($x = 0; $x < 8; $x++)
        {
            echo '<tr>';
            for ($y = 0; $y < 8; $y++)
            {
                echo '<td style="width: 100px; height: 100px; text-align: center; vertical-align: center; border: 1px solid black;">';
                echo '[' . $x . ',' . $y . ']<br />';

                if (isset($_SESSION['origin_x']) && isset($_SESSION['origin_y']))
                    echo '<a href="index.php?action=move_target&x=' . $x . '&y=' . $y . '">';
                else
                    echo '<a href="index.php?action=move_origin&x=' . $x . '&y=' . $y . '">';
                
                if ($this->board[$x][$y] !== null)
                {
                    $this->board[$x][$y]->Draw();
                }
                else
                {
                    echo 'Click here!';
                }

                echo '</a>';

                echo '</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    }
    
    public function Move($origin_x, $origin_y, $target_x, $target_y)
    {
        $this->board[$target_x][$target_y] = $this->board[$origin_x][$origin_y];
        $this->board[$origin_x][$origin_y] = null;
    }

}