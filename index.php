<?php
session_start();

$validation = '';

if (isset($_GET['reset']) && $_GET['reset'] == 1)
{
    session_destroy();
    header('Location: index.php');
    $validation = 'La partie a été réinitialisée !';
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
    $board = unserialize ($_SESSION['board']);
}

if (!isset($_SESSION['logs']))
    $logs = new Log();
else
{
    $logs = unserialize ($_SESSION['logs']);
}

if (isset($_GET['action']))
{
    switch($_GET['action'])
    {
        case 'move_origin':
            if (isset($_GET['x']) && ctype_digit($_GET['x']) && 
                isset($_GET['y']) && ctype_digit($_GET['y']))
            {
                $origin = new Position($_GET['x'], $_GET['y']);

                if ($board->GetPiece($origin) !== null)
                {
                    $board->GetPiece($origin)->ComputePossibleCells($board->ComputeCollisionBoard($board->GetPiece($origin)->GetColor()));
                    $_SESSION['origin'] = serialize($origin);
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

                        $logs->Add('Pièce déplacée de la case ' . $origin . ' à la case ' . $target);

                        $board->NextTurn();
                    }
                    else
                    {
                        $logs->Add('Déplacement invalide !!');
                    }
                }
                else
                {
                    $logs->Add('Déplacement invalide !');
                }
                
                unset($_SESSION['origin']);
                header('Location: index.php');
            }
            break;
        default:
            break;
    }
}

$_SESSION['board'] = serialize($board);
$_SESSION['logs'] = serialize($logs);

?>
    
    <div id="board">
        <?php $board->DrawBoard(); ?>
    </div>
    
    <div id="logs">
        <a href="index.php?reset=1">Relancer une partie !</a>
        <?php
        echo '<pre>';
        print_r($_SESSION);
        
        /*
        if (isset($_SESSION['origin']))
            print_r($board->GetPiece($_SESSION['origin']));*/
        echo '</pre>';
        
        $logs->Display();
        ?>
    </div>

</body>
</html>