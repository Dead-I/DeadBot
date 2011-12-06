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
	$i = 0;
	$tag = "";
	if (PHP_SAPI !== 'cli') {
		writeout("{$red}You can only install this way via CLI.");
		die();
	}
	function resetcolour(){
		echo $normal;
	}
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
		server:
		question("What server do you want to connect to (without port)? [{$red}REQUIRED{$normal}]");
		$server = fetchinput();
		preg_match("/(.*)\:(.*)/",$server,$matches);
		if($server == ""){
			writeout("You need to specify a server!");
			goto server;
		}elseif(count($matches) > 0){
			writeout("Server address invalid. Found a :.");
			goto server;
		}
		port:
		question("What is the server port? [{$red}REQUIRED{$normal}]");
		$port = fetchinput();
		preg_match("/[0-9]/",$server,$matches);
		if(count($matches) < 1){
			writeout("Incorrect formatting for port.");
			goto port;
		}elseif($port > 65535){
			writeout("Port number out of range.");
			goto port;
		}
		nick:
		question("What would you like your bot's nick to be? [{$red}REQUIRED{$normal}]");
		$nick = fetchinput();
		if($nick == ""){
			writeout("You need to specify a nickname for your bot.");
			goto nick;
		}
		name:
		question("What would you like your bot's realname to be? [{$red}REQUIRED{$normal}]");
		$name = fetchinput();
		if($name == ""){
			writeout("You need to specify a realname for your bot.");
			goto name;
		}
		pass:
		question("What is the nickserv password for your bot? [{$green}OPTIONAL{$normal}]");
		$pass = fetchinput();
		staffpass:
		question("What would you like the password for trust to be? [{$red}REQUIRED{$normal}]");
		$staffpass = fetchinput();
		if($staffpass == ""){
			writeout("You must provide a staff password!");
			goto staffpass;
		}
		channels:
		question("What channels would you like the bot to join by default? (Seperate by commas) [{$red}REQUIRED{$normal}]");
		$channels = fetchinput();
		if($channels == ""){
			writeout("No channels specified.");
			goto channels;
		}elseif(substr($channels,0,1) != "#"){
			writeout("Incorrect channel(s) specified.");
			goto channels;
		}
		staffchan:
		question("What do you want the staff channel to be (Where all debugging information will be. One channel only) [{$red}REQUIRED{$normal}]");
		$staffchannel = fetchinput();
		if($staffchannel == ""){
			writeout("No channel specified.");
			goto staffchan;
		}elseif(substr($channels,0,1) != "#"){
			writeout("Incorrect channel specified.");
			goto staffchan;
		}
		physical:
		question("Where is the bot located? [{$red}REQUIRED{$normal}]");
		$physical = fetchinput();
		if(!file_exists("{$physical}index.php")){
			writeout("Path incorrect or missing index.php.");
			goto physical;
		}
		shortdirect:
		question("What would you like the short prefix to be (e.g. @, !, ~, db) [{$red}REQUIRED{$normal}]");
		$shortdirect = fetchinput();
		if($shortdirect == ""){
			writeout("You must specify a short prefix!");
			goto shortdirect;
		}
		goto configend;
	}else{
		goto end;
	}
	configend:
	$fp = fopen("./config.php","w");
	$write = "<?php\r\n\$installed = 1;\r\n\$server = '$server';\r\n\$port = $port;\r\n\$nick = '$nick';\r\n\$name = '$name';\r\n\$pass = '$pass';\r\n\$staffpass = '$staffpass';\r\n\$channels = '$channels';\r\n$staffchannel = '$staffchannel';\r\n\$physical = '$physical';\r\n\$shortdirect = '$shortdirect';\r\n?>";
	fwrite($fp,$write);
	fclose($fp);
	writeout("{$green}CONGRATULATIONS! Installation was successful!{$normal}");
	end: //DO NOT TOUCH THIS LABEL PLEASE!
	resetcolour();
	exit;
?>