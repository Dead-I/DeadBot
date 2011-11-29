<?php

#########################
##ÊDeadBot v1.0 Stable ##
###ÊAn IRC Bot in PHP ###
#### Read README.md ####
#########################

// Welcome to the main DeadBot file
// You don't really need to change anything in here without good reason
// To change commands, look in the cmd directory or use the addcmd command.
// To change configuration, edit config.php.
// Thank you for using DeadBot!

// Get the configuration
require 'config.php';

// Ensure that the bot stays alive
set_time_limit(0);

// Start the class the bot will run from
class IRCBot {
	
	var $socket;
	var $ex = array();
	
	function __construct($config) {
		$this->socket = fsockopen($config["server"], $config["port"]);
		$this->auth($config);
		$this->join($config);
		$this->bot($config);
	}
	
	function auth($config) {
		$this->raw('USER $config["nick"] $config["indent"] $config["nick"] :$config["name"]');
		$this->raw('NICK $config["nick"]');
		$this->raw('NS IDENTIFY $config["pass"]');
	}
	
	function join($config) {
		$channel = explode(',', $config["channels"]);
		foreach($channel as $joinchannel) {
			$this->raw('JOIN '.$joinchannel);
		}
	}
	
	function bot($config) {
		$data = fgets($this->socket, 522);
		echo nl2br($data);
		flush();
		
		$this->ex = explode(' ', $data);
		
		if ($this->$ex[0] == 'PING') $this->raw('PONG');
		
		bot($config);
		
		function raw($command) {
			fputs($this->socket, $command.'\r\n');
		}
	}
	
}

// Start up the bot
$bot = new IRCBot($config);