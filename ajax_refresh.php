
<div id="data">Chat Text Here</div>

<div id="status">Debugging Information Here</div>

<input id="input_text" type="text" onfocus = "this.value = ''" name="input_text" />

<button id="chat">Chat</button>

<button id="clearDebug">Clear Debugging</button>
<?php
include 'credentials.php';
$username = "austin-gruenberg";
$dbname = "austin-gruenberg_ajax";
$database = mysqli_connect( "localhost", $username, $password,$dbname);

$sql = "SELECT username,message FROM ( SELECT * FROM messages ORDER BY messageId DESC LIMIT 10)Var1 ORDER BY messageId ASC";
$result = mysqli_query($database,$sql);
if ( !( $result ) ) {
  echo "Could not execute query! <br />";
  die( mysqli_error() );
}
while ($row = mysqli_fetch_row($result))
{
    echo $row[0].": " .$row[1];
    echo "<br>";
}
?>