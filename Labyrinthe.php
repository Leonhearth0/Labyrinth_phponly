<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php


    session_start();

    if (!isset($_SESSION['maze'])) {
        if (rand(0, 1) == 0) {
            $_SESSION['maze'] = [
                [1, 2, 1, 1, 1, 1, 1, 1, 1, 1],
                [1, 0, 0, 0, 0, 0, 0, 0, 1, 1],
                [1, 1, 0, 1, 1, 1, 1, 1, 0, 1],
                [1, 1, 0, 0, 0, 0, 0, 0, 0, 1],
                [1, 1, 0, 1, 1, 1, 1, 1, 1, 1],
                [1, 1, 0, 0, 0, 0, 0, 0, 0, 1],
                [1, 1, 1, 1, 1, 1, 1, 1, 0, 1],
                [1, 1, 0, 0, 0, 0, 0, 1, 0, 1],
                [1, 4, 0, 1, 0, 0, 0, 0, 0, 1],
                [1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            ];

            $_SESSION['foggymaze'] = [
                [1, 2, 1, 3, 3, 3, 3, 3, 3, 3],
                [3, 0, 3, 3, 3, 3, 3, 3, 3, 3],
                [3, 3, 3, 3, 3, 3, 3, 3, 3, 3],
                [3, 3, 3, 3, 3, 3, 3, 3, 3, 3],
                [3, 3, 3, 3, 3, 3, 3, 3, 3, 3],
                [3, 3, 3, 3, 3, 3, 3, 3, 3, 3],
                [3, 3, 3, 3, 3, 3, 3, 3, 3, 3],
                [3, 3, 3, 3, 3, 3, 3, 3, 3, 3],
                [3, 3, 3, 3, 3, 3, 3, 3, 3, 3],
                [3, 3, 3, 3, 3, 3, 3, 3, 3, 3],
            ]
            ;
        } else {
            $_SESSION['maze'] = [
                [1, 1, 1, 1, 1, 1, 1],
                [1, 0, 1, 4, 1, 0, 1],
                [1, 0, 1, 0, 1, 0, 1],
                [1, 0, 1, 0, 1, 0, 1],
                [1, 0, 1, 0, 0, 0, 1],
                [1, 0, 1, 0, 1, 1, 1],
                [1, 0, 0, 0, 0, 1, 1],
                [1, 0, 1, 1, 1, 1, 1],
                [1, 0, 0, 0, 0, 1, 1],
                [1, 1, 1, 1, 0, 0, 1],
                [1, 0, 0, 0, 0, 0, 2],
                [1, 1, 1, 1, 1, 1, 1],
            ];

            $_SESSION['foggymaze'] = [
                [3, 3, 3, 3, 3, 3, 3],
                [3, 3, 3, 3, 3, 3, 3],
                [3, 3, 3, 3, 3, 3, 3],
                [3, 3, 3, 3, 3, 3, 3],
                [3, 3, 3, 3, 3, 3, 3],
                [3, 3, 3, 3, 3, 3, 3],
                [3, 3, 3, 3, 3, 3, 3],
                [3, 3, 3, 3, 3, 3, 3],
                [3, 3, 3, 3, 3, 3, 3],
                [3, 3, 3, 3, 3, 3, 1],
                [3, 3, 3, 3, 3, 0, 2],
                [3, 3, 3, 3, 3, 3, 1],
            ];
        }
    }
    ;

    $maze = $_SESSION['maze'];
    $playerPosition = [null, null];
    $gameWon = isset($_SESSION['gameWon']) && $_SESSION['gameWon'];

    define('WALL', 1);
    define('PLAYER', 2);
    define('GOAL', 4);
    define('FOG', 3);

    function movePlayer($maze, $playerPosition, $Yaxis, $Xaxis)
    {
        $currentY = $playerPosition[0] + $Yaxis;
        $currentX = $playerPosition[1] + $Xaxis;
        if ($currentY >= 0 && $currentY < count($maze) && $currentX >= 0 && $currentX < count($maze[0]) && $maze[$currentY][$currentX] != WALL) {
            if ($maze[$currentY][$currentX] == GOAL) {
                $_SESSION['message'] = "Vous avez gagné.";
                $_SESSION['gameWon'] = true;
                header("Refresh:0");
            }
            $maze[$playerPosition[0]][$playerPosition[1]] = 0;
            $playerPosition[0] = $currentY;
            $playerPosition[1] = $currentX;
            $maze[$playerPosition[0]][$playerPosition[1]] = PLAYER;
        } else {
            $_SESSION['message'] = "Pas par là !";
        }
        return [$maze, $playerPosition];
    }

    function revealMaze($maze, $foggymaze, $playerPosition)
    {
        for ($y = 0; $y < count($maze); $y++) {
            for ($x = 0; $x < count($maze[0]); $x++) {
                $foggymaze[$y][$x] = FOG;
            }
        }
        $aroundPlayer = 1;
        $yStart = max(0, $playerPosition[0] - $aroundPlayer);
        $yEnd = min(count($maze) - 1, $playerPosition[0] + $aroundPlayer);
        $xStart = max(0, $playerPosition[1] - $aroundPlayer);
        $xEnd = min(count($maze[0]) - 1, $playerPosition[1] + $aroundPlayer);
        for ($y = $yStart; $y <= $yEnd; $y++) {
            for ($x = $xStart; $x <= $xEnd; $x++) {
                if ($y == $playerPosition[0] || $x == $playerPosition[1]) {
                    $foggymaze[$y][$x] = $maze[$y][$x];
                }
            }
        }
        return $foggymaze;
    }

    for ($y = 0; $y < count($maze); $y++) {
        for ($x = 0; $x < count($maze[$y]); $x++) {
            if ($maze[$y][$x] == 2) {
                $playerPosition = [$y, $x];
            }
        }
    }


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['restart'])) {
            session_destroy();
            header("Refresh:0");
            exit();
        } elseif (isset($_POST['up'])) {
            list($maze, $playerPosition) = movePlayer($maze, $playerPosition, -1, 0);
        } elseif (isset($_POST['down'])) {
            list($maze, $playerPosition) = movePlayer($maze, $playerPosition, 1, 0);
        } elseif (isset($_POST['left'])) {
            list($maze, $playerPosition) = movePlayer($maze, $playerPosition, 0, -1);
        } elseif (isset($_POST['right'])) {
            list($maze, $playerPosition) = movePlayer($maze, $playerPosition, 0, 1);
        }
        $_SESSION['foggymaze'] = revealMaze($maze, $_SESSION['foggymaze'], $playerPosition);
    }




    echo '<div class="flex-container">
    <div class="table-container">
        <table>';
    foreach ($_SESSION['foggymaze'] as $y => $row) {
        echo "<tr>";
        foreach ($row as $x => $cell) {
            $class = '';
            if ($cell == 1) {
                $class = 'wall';
            } elseif ($cell == 0) {
                $class = 'empty';
            } elseif ($cell == 2) {
                $class = 'player';
            } elseif ($cell == 4) {
                $class = 'goal';
            } elseif ($cell == 3) {
                $class = 'fog';
            }
            echo "<td class='$class'></td>";
        }
        "</tr>";
    }
    $_SESSION['playerPosition'] = $playerPosition;


    echo '
    </table>
    </div>
    <div class="form-container">
        <form action="" method="post">
            <div class="button-container">
                <input id="upbutton" type="submit" name="up" ' . ($gameWon ? 'disabled' : '') . '>
                <input id="downbutton" type="submit" name="down" ' . ($gameWon ? 'disabled' : '') . '>
                <input id="leftbutton" type="submit" name="left" ' . ($gameWon ? 'disabled' : '') . '>
                <input id="rightbutton" type="submit" name="right" ' . ($gameWon ? 'disabled' : '') . '>
            </div>
            <div>
                <input type="submit" name="restart" value="Recommencer">
            </div>
        </form>
    </div>
</div>';

    if (isset($_SESSION['message'])) {
        echo "<div class='centertext'><p>" . $_SESSION['message'] . "</p></div>";
        unset($_SESSION['message']);
    }
    $_SESSION['maze'] = $maze;
    ?>
</body>

</html>