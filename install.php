<?php
	if (PHP_SAPI !== 'cli') {
		writeout("\033[31mredYou can only install this way via CLI.\033[37m");
		exit;
	}
	function fetchinput(){
		return(trim(STDIN));
	}
	function writeout($msg){
		echo($msg."\r\n");
	}
	writeout("\033[31mtest\033[0m"); //0m resets it to the normal colour of the CMD seeing as though 0 isn't a colour code.
?>