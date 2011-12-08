<?php

#########################
## DeadBot v1.0 Stable ##
### An IRC Bot in PHP ###
#### Read README.md ####
#########################

// Welcome to the main DeadBot file
// You don't really need to change anything in here without good reason
// To change commands, look in the cmd directory or use the addcmd command.
// To change configuration, edit config.php.
// Thank you for using DeadBot!

// Get the version of the bot - please leave this intact
$version = '1.0 STABLE';

// Error reporting settings
error_reporting(E_ALL ^ E_NOTICE);

// Output an initializing text - helpful for debugging
echo "\nInitializing";

// Get the configuration
require "config.php";

// Get the functions
require "functions.php";

// Connect to the dataabase
if (!mysql_connect($dbhost, $dbuser, $dbpass)) echo "...database connection failed";
if (!mysql_select_db($dbname)) echo "...database selection failed";

// Output text that confirms the config/functions worked
echo "...\n\n";

// Detect uninstalled version
if (!isset($installed) && $argv[1] != 'installed') {
	echo "Welcome to DeadBot {$version}!\n\n";
	echo "It appears that you have not setup the bot yet, so please follow our easy installation wizard which will guide you through the process of getting DeadBot working and customized to your needs.\n\n";
	echo "To begin the installation, please run this in your command line terminal while inside the bot directory: php install.php\n\n";
	echo "Thank you for using DeadBot!\n\n";
	die;
}

// Ensure that the bot stays alive
set_time_limit(0);

// Synchronize the admins, hostmasks and commands
sync();

// Get the start date for the status command
$startseconds = time();

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
if (isset($pass)) raw("NS IDENTIFY {$pass}");

// Join the channels
$channel = explode(",", $channels);
foreach($channel as $join) {
	raw("JOIN {$join}");
	sleep(2);
	normal("DeadBot {$version} Loaded", $join);
}

// Join the staff channel
if (find($staffchannel, $channels) == 0) raw("JOIN {$staffchannel} {$staffkey}");
sleep(2);
normal("Diagnostics Activated for DeadBot {$version}", $staffchannel);

// Echo the success message to confirm DeadBot's operation
echo "###############################\n";
echo "##### DeadBot PHP IRC Bot #####\n";
echo "## Version {$version} Loaded ##\n";
echo "###############################\n\n";

// Start looping
while(1) {
	while($data = fgets($socket, 522)) {
		
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
		
		$usernick = strtolower(substr($userinfo[0], 1));
		
		// Detect if the user is an admin
		if (find(','.$usernick.',', $admins) == 1 && find($userinfo[1], $hostmasks) == 1) {
			$admin = 1;
		}elseif (isset($admin)) {
			unset($admin);
		}
		
		// Get the direct and command which was sent
		$direct = substr(strtolower(str_replace(array(chr(10), chr(13)), '', $ex[3])), 1);
		$command = strtolower(str_replace(array(chr(10), chr(13)), '', $ex[4]));
		$command = str_replace(array("/", "."), '', $command);
		$value = strtolower(str_replace(array(chr(10), chr(13)), '', $ex[5]));
		
		// Detect if the message is privately messaged
		if (strtolower($ex[2]) == 'deadbot') $ex[2] = $recipient;
		
		// Logging
		if (find("{$ex[2]},", $logchannels) == 1 && $ex[1] == "PRIVMSG") {
			$result = mysql_query("SELECT FROM {$loggingtable} ORDER BY id DESC");
			$result = mysql_fetch_array($result);
			
			if (!empty($result['id'])) {
				$newid = $result['id'] + 1;
				$datestring = date('ymdhis');
				$newdatestring = $datestring - $logtime;
			}else{
				$newid = 1;
			}
			
			mysql_query("INSERT INTO {$loggingtable} VALUES ({$newid}, ".content("{$ex[2]} :").", {$usernick}, {$ex[2]}, {$datestring});");
			mysql_query("DELETE FROM {$loggingtable} WHERE timestamp >= {$newdatestring};");
		}
		
		// Attempt to detect excess flooding and hacking
		$current = date('ymdHis');
		
		// If the command was found, execute the external command
		if (($direct == strtolower($nick) || $direct == strtolower($nick).':' || $direct == $shortdirect) && (find(",{$recipient}", $ignorelist) != 1) && (!(($current - $lastmsg) < 1 && $abuser == $userinfo[0]) && $recipient[0] != '!')) {
			$dirname = str_replace("#", "", $ex[2]);
			
			if (file_exists("{$dirname}/{$command}")) {
			
				try{
					eval(file_get_contents("{$dirname}/{$command}"));
				} catch (Exception $e) {
					normal($e->getMessage(), $ex[2]);
				}
				
			}elseif (file_exists("cmd/{$command}")) {
				
				try{
					eval(file_get_contents("cmd/{$command}"));
				} catch (Exception $e) {
					normal($e->getMessage(), $ex[2]);
				}
				
				if (strtolower($ex[2]) == 'deadbot') normal("Private Command Received from {$usernick}: {$command}", $staffchannel);
				
			}else{
				send("Sorry, the command requested is invalid. Please run '{$nick} help' to see a list of commands.");
			}
			$lastmsg = date('ymdHis');
			$abuser = $userinfo[0];
		}
		
		
		// If DeadBot is kicked
		if ($ex[1] == 'KICK' && $ex[3] == $nick) {
			raw("JOIN {$ex[2]}");
			sleep(1);
			normal("If you need me to leave, please ask an admin to run the 'part' command on me.", $ex[2]);
		}
		
		// Pausing to prevent excessive server load
		sleep(0.4);
		
	}
}