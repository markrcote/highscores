<html>
<body>
<?
include 'hs_common.inc';

mysql_connect(localhost,$username,$password);
@mysql_select_db($database) or die("Unable to select database");

$newgame = $_POST['newgame'];
$newplayer = $_POST['newplayer'];

if ($newgame)
{
    $query = "INSERT INTO `games` (id, name) VALUES (NULL, \"$newgame\")";
    $result = mysql_query($query) or die (mysql_error());
    echo "<p>Game \"$newgame\" added to database.</p>";
}

if ($newplayer)
{
    $query = "INSERT INTO `players` (id, name) VALUES (NULL, \"$newplayer\")";
    $result = mysql_query($query) or die (mysql_error());
    echo "<p>Player \"$newplayer\" added to database.</p>";
}

mysql_close();

back_button();
?>
</body>
</html>


