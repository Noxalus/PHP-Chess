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
        if (parent::ComputePossibleCells($board))
        {
            $collisionBoard = $board->ComputeCollisionBoard($this->color);

            // Right
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

            // Left
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

            // Top
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

            // Bottom
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

            $UpRightCorner = new Position($this->position->x + 1, $this->position->y + 1);
            while(!Board::Out($UpRightCorner))
            {
                if (!$collisionBoard[$UpRightCorner->x][$UpRightCorner->y])
                {
                    $this->possibleCells[] = $UpRightCorner;

                    $piece = $board->GetPiece($UpRightCorner);
                    if ($piece != null && $piece->GetColor() != $this->color)
                        break;
                }
                else
                    break;

                $UpRightCorner = new Position($UpRightCorner->x + 1, $UpRightCorner->y + 1);
            }

            $BottomLeftCorner = new Position($this->position->x - 1, $this->position->y - 1);
            while(!Board::Out($BottomLeftCorner))
            {
                if (!$collisionBoard[$BottomLeftCorner->x][$BottomLeftCorner->y])
                {
                    $this->possibleCells[] = $BottomLeftCorner;

                    $piece = $board->GetPiece($BottomLeftCorner);
                    if ($piece != null && $piece->GetColor() != $this->color)
                        break;
                }
                else
                    break;

                $BottomLeftCorner = new Position($BottomLeftCorner->x - 1, $BottomLeftCorner->y - 1);
            }

            $BottomRightCorner = new Position($this->position->x + 1, $this->position->y - 1);
            while(!Board::Out($BottomRightCorner))
            {
                if (!$collisionBoard[$BottomRightCorner->x][$BottomRightCorner->y])
                {
                    $this->possibleCells[] = $BottomRightCorner;

                    $piece = $board->GetPiece($BottomRightCorner);
                    if ($piece != null && $piece->GetColor() != $this->color)
                        break;
                }
                else
                    break;

                $BottomRightCorner = new Position($BottomRightCorner->x + 1, $BottomRightCorner->y - 1);
            }

            $TopLeftCorner = new Position($this->position->x - 1, $this->position->y + 1);
            while(!Board::Out($TopLeftCorner))
            {
                if (!$collisionBoard[$TopLeftCorner->x][$TopLeftCorner->y])
                {
                    $this->possibleCells[] = $TopLeftCorner;

                    $piece = $board->GetPiece($TopLeftCorner);
                    if ($piece != null && $piece->GetColor() != $this->color)
                        break;
                }
                else
                    break;

                $TopLeftCorner = new Position($TopLeftCorner->x - 1, $TopLeftCorner->y + 1);
            }
        }
    }
    
    public function __toString()
    {
        return '<img src="sprites/' . $this->color . '_queen.png" class="piece" />';
    }
}