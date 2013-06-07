<?php
session_start();

$validation = '';

if (isset($_GET['reset']) && $_GET['reset'] == 1)
{
    session_destroy();
    header('Location: index.php');
    $validation = 'Game have been reseted !';
}
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="style/style.css" type="text/css" rel="stylesheet" media="all" />
        <title>Jeu d'Echec</title>
    </head>
    <body>
        <?php
        if (!empty($validation))
            echo '<p style="color: green;">' . $validation . '</p>';

        function autoloader($classname)
        {
            include 'classes/' . $classname . '.php';
        }

        spl_autoload_register('autoloader');

        if (!isset($_SESSION['board']))
            $board = new Board();
        else
        {
            $board = unserialize($_SESSION['board']);
        }

        if (!isset($_SESSION['logs']))
            $logs = new Log();
        else
        {
            $logs = unserialize($_SESSION['logs']);
        }

        if (empty($_GET))
        {
            $logs->Add($board->DisplayTurn());
        }

        if (isset($_GET['action']))
        {
            switch ($_GET['action'])
            {
                case 'move_origin':
                    if (isset($_GET['x']) && ctype_digit($_GET['x']) &&
                            isset($_GET['y']) && ctype_digit($_GET['y']))
                    {
                        $origin = new Position($_GET['x'], $_GET['y']);
                        $piece = $board->GetPiece($origin);
                        if ($piece !== null)
                        {
                            if ($piece->GetColor() == $board->GetTurn())
                            {
                                // King is in check ?
                                if ($piece->GetColor() == Color::White && $board->GetWhiteKing()->InCheck() && $piece !== $board->GetWhiteKing() ||
                                    $piece->GetColor() == Color::Black && $board->GetBlackKing()->InCheck() && $piece !== $board->GetBlackKing())
                                {
                                    $logs->Add('Your king is under attack, you have to move it quickly !');
                                    header('Location: index.php');
                                }
                                else
                                {
                                    $piece->ComputePossibleCells($board);

                                    if (count($piece->GetPossibleCells()) == 0)
                                    {
                                        $logs->Add('No move available for this piece !');
                                        header('Location: index.php');
                                    }
                                    else
                                        $_SESSION['origin'] = serialize($origin);
                                }
                            }
                            else
                            {
                                $logs->Add('This is not your turn !');
                            }
                        }
                    }
                    break;
                case 'move_target':
                    if (isset($_SESSION['origin']))
                    {
                        if (isset($_GET['x']) && ctype_digit($_GET['x']) &&
                            isset($_GET['y']) && ctype_digit($_GET['y']))
                        {
                            $target = new Position($_GET['x'], $_GET['y']);
                            if ($board->Move(unserialize($_SESSION['origin']), $target))
                            {
                                $origin = unserialize($_SESSION['origin']);

                                $board->NextTurn();
                            }
                            else
                            {
                                $logs->Add('Invalid move !!');
                            }
                        }
                        else
                        {
                            $logs->Add('Invalid move !');
                        }

                        unset($_SESSION['origin']);

                        header('Location: index.php');
                    }
                    break;
                default:
                    break;
            }
        }

        if (!empty($_GET))
        {
            $board->KingCheck();
        }
        
        if (isset($_GET['clear']) && $_GET['clear'] == 1)
        {
            $logs->Clear();
        }

        $_SESSION['board'] = serialize($board);
        $_SESSION['logs'] = serialize($logs);
        ?>

        <div id="board">
        <?php $board->DrawBoard(); ?>
        </div>

        <div id="logs">
            <p style="text-align: center;">
                <a href="index.php?reset=1" style="font-weight: bold; font-size: xx-large;">Reset the game !</a><br />
                <a href="index.php?clear=1" style="font-style: italic;">(Clear logs)</a>
            </p>
            <br />
            <?php
            /*
            echo '<pre>';
            print_r($_SESSION);
            echo '</pre>';
            */
            $logs->Display();
            ?>
        </div>

    </body>
</html>