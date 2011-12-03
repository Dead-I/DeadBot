<?php
	$normal	= "\033[0m"; //Sets to default colour of user's shell. - DEPRECATED - use resetcolour() instead if the user has system(); enabled.
	$black	= "\033[30m";
	$red	= "\033[31m";
	$green	= "\033[32m";
	$brown	= "\033[33m";
	$blue	= "\033[34m";
	$purple = "\033[35m";
	$cyan	= "\033[36m";
	$white	= "\033[37m";
	if (PHP_SAPI !== 'cli') {
		writeout("{$red}You can only install this way via CLI.");
		goto end;
	}
	function resetcolour(){
		system("tput sgr0");
	function fetchinput(){
		return(trim(fgets(STDIN)));
	}
	function writeout($msg="",$newline=true){
		if($newline){
			echo($msg."\r\n");
		}else{
			echo($msg);
		}
	}
	function question($msg){
		writeout($msg.": ",false);
	}
	writeout("Have you read the license info?");
	question("And do you agree with it? [{$cyan}y/n{$normal}]");
	$input = fetchinput();
	if($input == "y"){
		writeout("Yes");
	}else{
		goto end;
	}
	end: //DO NOT TOUCH THIS LABEL PLEASE!
		resetcolour();
		exit;
?>