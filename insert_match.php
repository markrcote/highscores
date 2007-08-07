<html>
<body>
<?
include 'hs_common.inc';

mysql_connect(localhost,$username,$password);
@mysql_select_db($database) or die("Unable to select database");

$game = $_POST['game'];
$date = $_POST['date1'];
$hour = $_POST['hour'];
$minute = $_POST['minute'];
$time = "$hour:$minute:00";
$datetime = "$date $time";

$query = "INSERT INTO `matches` (id, date, game) VALUES (NULL, \"$datetime\", $game)";
$result = mysql_query($query) or die (mysql_error());

$query = "SELECT * FROM `matches` ORDER BY id";
$result = mysql_query($query) or die (mysql_error());
$num = mysql_numrows($result);
$match_id = mysql_result($result,$num-1,'id');

$num_players = $_POST['num_players'];

$i = 0;
while ($i < $num_players)
{
    $player[$i] = $_POST["player$i"];
    $score[$i] = $_POST["score$i"];
    $query = "INSERT INTO `scores` VALUES (NULL, $match_id, $player[$i], $score[$i])";
    $result = mysql_query($query) or die (mysql_error());
    $i++;
}

echo "<h3>Match added.</h3>";

mysql_close();

back_button();

?>
</body>
</html>