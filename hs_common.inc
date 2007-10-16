<?php
$username = "mcote";
$password = "E^bLL0";
$database = "highscores";
//$database = "hs_test";

function load_array($table, $field, &$array)
{
    $query = "SELECT * FROM $table ORDER BY $field";
    $result = mysql_query($query);

    $num = mysql_numrows($result);

    for ($i = 0; $i < $num; $i++)
    {
        $id = mysql_result($result,$i,'id');
        $name = mysql_result($result,$i, $field);
        $array[$id] = utf8_encode($name);
    }
    return $num;
}


function back_button()
{
    echo "<p><a href=\"highscores.php\">Back</a></p>";
}


function match_results($match_num, &$winners, &$losers, &$winning_score)
{
    $query = "SELECT * FROM `scores` WHERE `match`='$match_num'";
    $scores_result = mysql_query($query) or die(mysql_error());
    $num_scores = mysql_numrows($scores_result);

    $winning_score = 0;
    $players = array();

    for ($i = 0; $i < $num_scores; $i++)
    {
        $score = mysql_result($scores_result, $i, 'score');
        $player = mysql_result($scores_result, $i, 'player');
        array_push($players, $player);

        if ($score > $winning_score)
        {
            $winners = array($player);
            $winning_score = $score;
        }
        elseif ($score == $winning_score)
            array_push($winners, $player);
    }

    $losers = array_diff($players, $winners);
}


// Load arrays into javascript.
function js_arrays($num_players, &$players, $num_games, &$games)
{
    echo "var num_players = $num_players;\n";
    echo "var num_games = $num_games;\n";
    echo "var players = new Array($num_players);\n";
    echo "var games = new Array($num_games);\n";

    foreach ($players as $key => $value)
        echo "players[$key] = \"", $value, "\";\n";

    foreach ($games as $key => $value)
        echo "games[$key] = \"", $value, "\";\n";
}


function get_name($table, $id)
{
    $query = "SELECT * FROM `$table` WHERE `id`='$id'";
    $result = mysql_query($query) or die (mysql_error());
    return utf8_encode(mysql_result($result, 0, 'name'));
}

?>
