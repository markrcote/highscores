<html>
<title>Add new match, game, or player</title>
<head>
<?php
include 'hs_common.inc';

mysql_connect(localhost,$username,$password);
@mysql_select_db($database) or die("Unable to select database");

$num_players = load_array('players', 'name', $players);
$num_games = load_array('games', 'name', $games);

mysql_close();
?>

<script type="text/javascript" src="/hs_common.js"></script>
<script type="text/javascript" src="/CalendarPopup.js"></script>
<script type="text/javascript">
document.write(getCalendarStyles());
var cal = new CalendarPopup("testdiv1");
var num_playing = 0;

<?php
js_arrays($num_players, $players, $num_games, $games);
?>

function createScoreRow(formname)
{
    row_name = "player";
    row_name = row_name.concat(num_playing);
    form = document.getElementById(formname);
    var p = document.createElement("p");
    form.appendChild(p);
    createSelect(formname, row_name, players, num_players);

    var score = document.createElement("input");
    score.setAttribute("type", "text");
    score_name = "score";
    score_name = score_name.concat(num_playing);
    score.setAttribute("name", score_name);
    score.setAttribute("size", "5");

    form.appendChild(score);
    num_playing++;
    num_players_hidden = document.getElementById("num_players_hidden");
    num_players_hidden.setAttribute("value", num_playing);
}
</script>
</head>

<body>

<table>
<tr><td>

<table bgcolor=#E0A0A0 cellpadding=5 width=100%>
<tr><td><h3>Add new match</h3></td></tr>
<tr><td>
<form name="match" action="insert_match.php" method="post" id="matchform">
<input type="hidden" name="num_players" id="num_players_hidden" value="0"/>

Game:
<select name="game" id="gamechoice">
</select>


Date:
<INPUT TYPE="text" NAME="date1" VALUE="" SIZE=10>
<A HREF="#"
   onClick="cal.select(document.forms['match'].date1,'anchor1','yyyy-MM-dd'); return false;"
NAME="anchor1" ID="anchor1">+</A>&nbsp;&nbsp;<input type="text" name="hour" value="" size=2>:<input type="text" name="minute" value="" size=2>
</td></tr>
<tr><td>
<input type="button" onClick="createScoreRow('scorerows')" value="Add Player"/>
</td></tr>
<tr><td id="scorerows"></td></tr>
<tr><td>
<input type="submit" value="Submit">
</td></tr>
</table>
</form>

<script type="text/javascript">
createScoreRow('scorerows');
createOptions(document.getElementById("gamechoice"), games, num_games);
</script>

</td></tr>
<tr><td>

<table bgcolor=#E0A0A0 cellpadding=5 width=100%>
<form action="insert_game_player.php" method="post">
<tr><td><b>Add game:</b></td><td><input type="text" size="25" name="newgame"></td></tr>
<tr><td><b>Add player:</b></td><td><input type="text" size="25" name="newplayer">
<input type="submit" value="Add"></td></tr>
</form>
</table>

</td></tr></table>
</p>

<DIV ID="testdiv1" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>

<?php back_button(); ?>
</body>
</html>
