<?php
function my_autoload ($pClassName) {
    include(__DIR__ . "/classes/" . $pClassName . ".php");
}
spl_autoload_register("my_autoload");
/*
 * Statuses
 * 0 - Free spot
 * 1 - Ship
 * 2 - Miss
 * 3 - Hit
 * 4 - Sank
 */
session_start();
$game = new game();
if (isset($_GET['y']) && isset($_GET['x'])) {$game->makeShot($_GET['y'], $_GET['x']);}
$currentMap = $game->map;

if ($game->targetsLeft() == 0) {
    echo "<h1>You WON!</h1>";
}

echo "<table border='1'>";
echo "<tr><td></td><td>A</td><td>B</td><td>C</td><td>D</td><td>E</td><td>F</td><td>G</td><td>H</td><td>I</td><td>J</td></tr>";
for ($i = 0; $i < 10; $i++) {
    echo "<tr>";
    echo "<td width='10' height='10'>$i</td>";
    for ($j = 0; $j < 10; $j++) {
        if ($currentMap[$i][$j] === 1) {
            echo "<td class='". $i ." ". $j ."' width='10' height='10' style='cursor: pointer;' onclick='location.href = \"?y=". $i ."&x=". $j ."\";'>&nbsp;</td>";
        }
        else if ($currentMap[$i][$j] === 2) {
            echo "<td bgcolor='silver' class='". $i ." ". $j ."' width='10' height='10'>&nbsp;</td>";
        }
        else if ($currentMap[$i][$j] === 3) {
            echo "<td bgcolor='green' class='". $i ." ". $j ."' width='10' height='10'>&nbsp;</td>";
        }
        else if ($currentMap[$i][$j] === 4) {
            echo "<td bgcolor='red' class='". $i ." ". $j ."' width='10' height='10'>&nbsp;</td>";
        }
        else {
            echo "<td width='10' class='". $i ." ". $j ."' height='10' style='cursor: pointer;' onclick='location.href = \"?y=". $i ."&x=". $j ."\";'>&nbsp;</td>";
        }

    }
    echo "</tr>";
}
echo "<tr><td></td><td colspan='10'>Not shot yet</td></tr>";
echo "<tr><td bgcolor='silver'></td><td colspan='10'>Miss</td></tr>";
echo "<tr><td bgcolor='green'></td><td colspan='10'>Hit!</td></tr>";
echo "<tr><td bgcolor='red'></td><td colspan='10'>Ship down!</td></tr>";
echo "<tr><td colspan='11'><b>Targets left: ". $game->targetsLeft() ."</b></td></tr>";
echo "</table>";
