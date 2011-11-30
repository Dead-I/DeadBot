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

// Get the version of the bot - please leave this intact
$version = '1.0 STABLE';

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

// Synchronize the admins, hostmasks and commands
sync();

// Connect to the IRC server
//$socket = fsockopen($server, $port);
$socket = fsockopen("irc.x10hosting.com", 6667);

// Default configuration variables
if (!isset($installed)) {
	$nick = "DeadBot_{rand()}";
	$name = "DeadBot";
}

// Authorize the bot
raw("USER {$nick} {$name} {$name} :{$nick}");
raw("NICK {$nick}");
if (isset($pass)) raw("NS IDENTIFY {$pass}");

// Join the channels
$channel = explode(",", $channels);
foreach($channel as $join) {
	raw("JOIN {$join}");
	sleep(2);
	normal("DeadBot {$version} Loaded", $join);
}

// Echo the success message to confirm DeadBot"s operation
echo "###############################\n";
echo "##### DeadBot PHP IRC Bot #####\n";
echo "## Version {$version} Loaded ##\n";
echo "###############################\n\n";

// Start looping
while(1) {
	while($data = fgets($socket, 522)) {
		
		// Run install script if not installed
		if (!isset($installed)) {
			
			// All the install scripts are in a separate file
			require "install.php";
			
		}else{
			
			// Flush data
			flush();
			
			// Debugging feature
			if ($argv[1] == 'debug') echo nl2br($data);
			
			// Separate all the data that has been received
			$ex = explode(" ", $data);
			
			// Play PING PONG with the server to keep the bot alive
			if($ex[0] == "PING") raw("PONG {$ex[1]}");
			
			// Detect if the message was directed toward someone
			$userinfo = explode("!", $ex[0]);
			$directionexplode = explode(' @ ', $data);
			if (!isset($directionexplode[1])) {
				$recipient = substr($userinfo[0], 1);
			}else{
				$recipient = trim($directionexplode[1]);
			}
			
			// Get the direct and command which was sent
			$direct = substr(strtolower(str_replace(array(chr(10), chr(13)), '', $ex[3])), 1);
			$command = strtolower(str_replace(array(chr(10), chr(13)), '', $ex[4]));
			
			// If the command was found, execute the external command
			if ($direct == strtolower($nick) || $direct == strtolower($nick).':' || $direct == $shortdirect) {
				if (find($command, $commands) == 1) {
					include 'cmd/{$command}';
				}else{
					send("Sorry, the command requested is invalid. Please run '{$nick} help' to see a list of commands.");
				}
			}
			
			
			// If DeadBot is kicked
			if ($ex[1] == 'KICK' && $ex[3] == $nick) {
				raw("JOIN {$ex[2]}");
				sleep(1);
				normal("If you need me to leave, please ask an admin to run the 'part' command on me.", $ex[2]);
			}
			
		}
		
		
	}
}