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

if (isset($_GET['action']))
{
    switch($_GET['action'])
    {
        case 'move_origin':
            if (isset($_GET['x']) && ctype_digit($_GET['x']) && 
                isset($_GET['y']) && ctype_digit($_GET['y']))
            {
                $_SESSION['origin_x'] = $_GET['x'];
                $_SESSION['origin_y'] = $_GET['y'];
            }
            break;
        case 'move_target':
            if (isset($_SESSION['origin_x']) && isset($_SESSION['origin_y']))
            {                
                if (isset($_GET['x']) && ctype_digit($_GET['x']) && 
                    isset($_GET['y']) && ctype_digit($_GET['y']))
                {
                    $board->Move($_SESSION['origin_x'], $_SESSION['origin_y'], $_GET['x'], $_GET['y']);
                }
                
                unset($_SESSION['origin_x']);
                unset($_SESSION['origin_y']);
                
                header('Location: index.php');
            }
            break;
        default:
            break;
    }
}

$board->DrawBoard();

$_SESSION['board'] = serialize($board);

echo '<pre>';
print_r($_SESSION);
echo '</pre>';

?>

<a href="index.php?reset=1">Relancer une partie !</a>

</body>
</html>