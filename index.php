<?php

#########################
##ÊDeadBot v1.0 Stable ##
###ÊAn IRC Bot in PHP ###
#### Read README.md ####
#########################

## Welcome to the main DeadBot file
## You don't really need to change anything in here without good reason
## To change commands, look in the cmd directory or use the addcmd command.
## To change configuration, edit config.php.
## Thank you for using DeadBot!

// Get the configuration
require 'config.php';

// Ensure that the bot stays alive
set_time_limit(0);

// Start the class the bot will run from
class IRCBot {
	
	function __construct($server, $port, $nick, $indent, $name, $channels) {
		$this->socket = fsockopen($server, $port);
		$this->auth($nick, $indent, $name);
		$this->join($channels);
	}
	
	function auth($nick, $indent, $name) {
		$this->raw('USER $nick $indent $nick :$name');
		$this->raw('NICK $nick');
		$this->raw('NS IDENTIFY $pass');
	}
	
	function join($channels) {
		$channel = explode(',', $channels);
		foreach($channel as $joinchannel) {
			$this->raw('JOIN '.$joinchannel);
		}
	}
	
	function bot($installed, $server, $port, $nick, $indent, $name, $channels, $physical) {
		$data = fgets($this->socket, 522);
		echo nl2br($data);
		flush();
		
		$this->ex = explode(' ', $data);
		
		if ($this->$ex[0] == 'PING') $this->raw('PONG');
		
		$this->bot($installed, $server, $port, $nick, $indent, $name, $channels, $physical);
	}
	
	function raw($command) {
		fputs($this->socket, $command.'\r\n');
	}
	
}

// Start up the bot
$bot = new IRCBot();