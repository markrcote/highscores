<?php
$username = "";
$password = "";
$database = "highscores";

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
?>
