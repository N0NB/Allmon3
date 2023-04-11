<?php
#
# Copyright(C) 2023 AllStarLink
# Allmon3 and all components are Licensed under the AGPLv3
# see https://raw.githubusercontent.com/AllStarLink/Allmon3/develop/LICENSE
#

# Standard stuff
require_once("functions.php");
require_once("config.php");

if(!strcmp(php_sapi_name(),"cli")){
    header('Content-Type: application/json');
}

# Work with CLI or Web
$CMD = "";
if(defined('STDIN')){
    if(array_key_exists(1,$argv)){
        $CMD = $argv[1];
    } else {
        print "uiconfig.php ELEMENT [NODE]\n";
        exit(1);
    }
} else {
    $CMD = getGetVar("e");
}

# Parse Config
$allmon_cfg = readConfig();
if(! $allmon_cfg){
    print(getJSONError("could not parse config file - likely misformatted"));
    exit;
}

# Process the command
switch($CMD){

	# Get the complete configured node list
	case 'nodelist':
		print_r(getAllNodesJSON($allmon_cfg));
		break;

	case 'pollint':
		if(defined('STDIN')){
			if(array_key_exists(2, $argv)){
				$NODE = $argv[2];
			} else {
				print "uiconfig.php pollint [NODE]\n";
				exit(1);
			}
		} else {
			$NODE = getGetVar("n");
		}
		if(array_key_exists("webpinterval", $allmon_cfg[$NODE])){
			print(getJSONSuccess($allmon_cfg[$NODE]["webpinterval"]));
		} else {
			print(getJSONSuccess($DEFAULT_WEB_POLL_INTERVAL));
		}
		break;
		
	# Get the customization variables
	case 'customize':
		$customize = array(
			"HEADER_TITLE" => $CONFIG_HEADER_TITLE ,
			"HEADER_LOGO" => $CONFIG_HEADER_LOGO
		);
		print_r(json_encode($customize));
		break;

	# Get any custom menu
	case 'custmenu':
		if( ! file_exists("/etc/allmon3/menu.ini")){
		    print(getJSONError("no /etc/allmon3/menu.ini"));
	    	break;
		}
		$menu = parse_ini_file("/etc/allmon3/menu.ini", true);
		print(json_encode($menu));
		break;

	# Otherwise error...
	default:
		print(getJSONError("unknown command " . $CMD));
		break;
}
?>
