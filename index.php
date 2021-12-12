<?php

# session start stuff
session_start();

# initialize stuff
extract($_SESSION);

# check to see $_REQUEST
extract($_REQUEST);

# do we have a name?
if ($name) {
    include 'credentials.php';
    $username = "austin-gruenberg";
    $dbname = "austin-gruenberg_ajax";
    $database = mysqli_connect( "localhost", $username, $password,$dbname);

	# prevent HTML injection by quoting the $name
	# (should make no change if it is a reasonable name)
	$name = htmlentities($name);
	$_SESSION['name'] = $name;
	# are we preparing the page for the user to use to chat?
	# or are we receiving the submission from that page?
	if ($ajax) {
		# we are receiving the submission from the page
		if ($input_text) {
			# we have the text that we need to add to the chat
			add_to_chat($name,$input_text,$database);
		};
		# we return what is in the chat
		current_chat_text($database);
	} else {
		# we are providing the page for the user to chat
		chat_as($name);
	};
} else {
	prompt_for_name();
};

# functions

function add_to_chat($name,$input_text,$database) {
    include 'credentials.php';
    $username = "austin-gruenberg";
    $dbname = "austin-gruenberg_ajax";
    
   if (!($database)){
      die( "Could not connect to database" );
    }
   $query = "INSERT INTO messages(username,message) VALUES (";
   $query = $query . "'$name','$input_text')";
   $result = mysqli_query( $database,$query );
   if ( !( $result ) ) {
      echo "Could not execute query! <br />";
      die( mysqli_error() );
   }

};

function current_chat_text($database) {
	# output the entire content of the chat_text.txt file
	# but insert <br/> in-between each line
	# and prevents HTML injection by quoting it using HTML entities
	echo "<div><h1>Chat Text</h1>";
	
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
};

function prompt_for_name() {
	echo <<< HERE
	<html>
	<body>
	<form>
	What is your name?
	<input type="text" name="name" />
	<input type="submit" />
	</form>
	</body>
	</html>
HERE;

};

function chat_as($name) {
	# here we output all the HTML, JS, etc. needed to actually do the chatting
	# and we need to remember that the ajax submitted stuff needs to have
	# $ajax as true and the chat line in $input_text
	
	echo <<< HERE
<!DOCTYPE html>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
\$(document).ready(function(){
  \$("#chat").click(function(){
		  \$.post("index.php",{

		  name: "$name",
		  ajax: 1,
		  input_text: \$("#input_text").val()

	  },function(data,status){
		  \$("#data").html(data);
		  // \$("#status").html(status+\$("#input_text").val());
	  });
  });
  \$("#clearDebug").click(function(){
	  \$("#status").html("");
  });
});
</script>
	<style>
	#status , #clearDebug { display: none; }
	</style>
</head>
<body>

<div id="data">Chat Text Here</div>

<div id="status">Debugging Information Here</div>

<input id="input_text" type="text" onfocus = "this.value = ''" name="input_text" />

<button id="chat">Chat</button>

<button id="clearDebug">Clear Debugging</button>

</body>
</html>


HERE;
};
?>
