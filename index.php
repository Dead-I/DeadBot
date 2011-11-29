<?php

#########################
##ÊDeadBot v1.0 Stable ##
###ÊAn IRC Bot in PHP ###
#### Read README.md ####
#########################

// Welcome to the main DeadBot file
// You don"t really need to change anything in here without good reason
// To change commands, look in the cmd directory or use the addcmd command.
// To change configuration, edit config.php.
// Thank you for using DeadBot!

// Output an initializing text - helpful for debugging
echo "\nInitializing";

// Get the configuration
require "config.php";

// Get the functions
require "functions.php";

// Output text that confirms the config/functions worked
echo "...\n\n";

// Ensure that the bot stays alive
set_time_limit(0);

// Connect to the IRC server
$socket = fsockopen($server, $port);

// Default configuration variables
if (!isset($installed)) {
	$nick = "DeadBot_{rand()}";
	$name = "DeadBot";
}

// Authorize the bot
raw("USER {$nick} {$name} {$name} :{$nick}");
raw("NICK {$nick}");
if (isset($pass)) raw("NS IDENTIFY {$password}");

// Join the channels
$channel = explode(",", $channels);
foreach($channel as $join) {
	raw("JOIN {$join}");
}

// Echo the success message to confirm DeadBot"s operation
echo "###############################\n";
echo "##### DeadBot PHP IRC Bot #####\n";
echo "## Version 1.0 Stable Loaded ##\n";
echo "###############################\n\n";

// Start looping
while(1) {
	while($data = fgets($socket, 522)) {
		
		// Run install script if not installed
		if (!isset($installed)) {
			
			// All the install scripts are in a separate file
			require "install.php";
			
		}else{
			
			// Separate all the data that has been received
			$ex = explode(" ", $data);
			
			// Play PING PONG with the server to keep the bot alive
			if($ex[0] == "PING") raw("PONG {$ex[1]}");
			
		}
		
		
	}
}