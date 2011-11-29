<?php

#########################
##ÊDeadBot v1.0 Stable ##
###ÊAn IRC Bot in PHP ###
#### Functions File #####
#########################

// Send raw message function
function raw($command) {
	global $socket;
	fputs($socket, $command."\n");
	echo "::: Command Sent - {$command} ::: \n\n";
}

// Send normal message with recipient function
function send($command, $sendchannel) {
	global $socket;
	fputs($socket, "PRIVMSG {$sendchannel} {$recipient} :{$command}\n");
	echo "::: Message Sent to {$recipient} in {$sendchannel} - {$command} ::: \n\n";
}

// Send normal message without recipient function
function normal($command, $sendchannel) {
	global $socket;
	fputs($socket, "PRIVMSG {$sendchannel} :{$command}\n");
	echo "::: Message Sent in {$sendchannel} - {$command} ::: \n\n";
	
}

// A find function - useful in many cases
function find($delimiter, $string) {
	$string = $string.'.';
	$explode = explode($delimiter, $string);
	if (isset($explode[1])) {
		return 1;
	}else{
		return 2;
	}
	
}