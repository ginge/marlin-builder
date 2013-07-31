<?php
/*
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
    
    marlin firmware builder
    Copyright 2013 
    Barry Carter <barry.carter@gmail.com>
    */

$varBaudRate = "115200";

$varMaxFeedX = "250";
$varMaxFeedY = "250";
$varMaxFeedZ = "10";
$varMaxFeedE = "45";

$varMaxAccelX = "9000";
$varMaxAccelY = "9000";
$varMaxAccelZ = "100";
$varMaxAccelE = "10000";

$varHomeRateX = "50";
$varHomeRateY = "50";
$varHomeRateZ = "4";

$varNotUseX = True;
$varNotUseY = True;
$varNotUseZ = False;
$varNotUseE = True;

$varOnlyHoming = False;

$varSoftwareEndstopsEn = false;
$varSoftwareEndstopsX = "205";
$varSoftwareEndstopsY = "205";
$varSoftwareEndstopsZ = "200";

$varPIDEn = True;
$varPIDKp = "22.2";
$varPIDKi = "1.08";
$varPIDKd = "114";

$varPIDSpeedEn = True;
$varPIDKc = "1";

$varMinExtTemp = "170";
$varMaxExtTemp = "275";

$varM109Hyster = "3";
$varM109Wait = "6";

$varAD595Gain = "1.0";
$varAD595Offset = "0.0";

$varAutoTempEn = True;
$varLateZEn = False;
$varWatchdogEn = False;

$varExtruderRunoutEn = True ;
$varExtruderAdvanceEn = False;

$varEndstopPullupEn = True;
$varEndstopInvertedXEn = True;
$varEndstopInvertedYEn = True;
$varEndstopInvertedZEn = True;
$varEnPinsActiveLowEn = True;

$varInvAxisXEn = True;
$varInvAxisYEn = False;
$varInvAxisZEn = True;
$varInvAxisEEn = False;

$varStepsPerUnitX = "78.740";
$varStepsPerUnitY = "78.740";
$varStepsPerUnitZ = "533.33";
$varStepsPerUnitE = "865.888";

$varSDCardEn = True;
$varUltipanelEn = True;
$varUltipanelClickEn = True;
$varLCDEn = False;


$varHardware = 7;
$varExtruderSensor = 7;
$varBedSensor = 7;

function chktag($tag)
{
    if ($tag == false) 
    {
	return '';
    }
    else
    {
	return 'checked="true"';
    }
}

function startsWith($haystack, $needle)
{
    return !strncmp($haystack, $needle, strlen($needle));
}


// Checks a line from configuration* and updates any found value.
// This is a box of magic. Do not touch unless you want to break stuff.
// it has to take into account defines, const values and also comments.
function checkLine($line, $var, $newval)
{
	if (empty($line)) {
	    return $line;
	}
	
	// If the config keyword is even in this line?
	if (stristr($line, $var) !== False)
	{
	      $parts = explode(' ', $line);
	      
	      // ignore the crap
	      if (!startsWith($line, "#define") && !startsWith($line, "const")) {
		  return $line;
	      }
	      
	      // go through every token in the cnofig line
	      // we first look for the keyword. The the spaces leading to the value (we want to preserve those). Then the comment
	      $indent = false;
	      $findingval = false;
	      $comment = false;
	      $novalue = false;
	      $findequals = false;
	      $newstr = "";
	      $commentval = "";
	      
	      foreach ($parts as $part)
	      {
		  if ($part == "const") {
		      $findequals = true;
		  }  
		  // found the value after the indent
		  if ($indent == true && $part != "") {
		      if ($findequals && $part == "=") {
			  // this is a const var and has an equals after the $var
			  ;
		      }
		      else if (startsWith($part, "//")) {
			  // this is one of those defines with no value, we can just return the string here
			  if ($newval == true) {
				
				if (startsWith($line, "//")) {
				    return substr(2, strlen($line));
				}
				else
				{
				    return $line;
				    }
			  }
			  else {
				if (startsWith($line, "//")) {
				  // line is enabled. string those comments
				    return $line;
				}
				else {
				    return "// " . $line;
				}
			  }
		      }
		      else
		      {
			  $indent = false;
			  $findingval = true;
			  continue;
		      }
		  }
		  if ($findingval == true) {
		      //count blank lines
		      // if we find the comment we went too far
		      $str = stristr($part, "//");
		      if ($str != false)
		      {
			    $findingval = false;
			    $comment = true;
		      }
		      continue;
		  }
		  if ($comment == true) {
		      $commentval = $commentval . (($part=="") ? "" : " ") . $part;
		      continue;
		  } 
		  //matched the keyword the rest is indents
		  if ($part == $var && $indent == false && $findingval == false) {
		      $indent = true;
		  }
		  $newstr = $newstr . (($part == "") ? "" : (($newstr=="") ? "" : " ") . $part);
	      }

	      // some configs are {1,2,3,4} type config arrays. deal with here
	      if (is_array($newval)) {
		  $anewstr = "{";
		  $cnv = $newval;
		  $last_key = end(array_keys($newval));
		  foreach($cnv as $key => $nv) {
		      if ($key == $last_key) {
			  $anewstr = $anewstr . $nv;
		      }
		      else {
			  $anewstr = $anewstr . $nv . ", ";
		      }
		  }
		  $anewstr = $anewstr . "}";
		  $newval = $anewstr;
	      }
		     
	      return $newstr . (($newstr=="") ? "" : " ") . (($newval=="") ? "" : $newval) . (startsWith($line, "const") ? ";" : "") . (($commentval!="") ?  "      //" . $commentval : "");
	}
	
	return $line;
}

if(isset($_POST["formSubmit"]) && $_POST["formSubmit"] == "Build It")
{
	$errorMessage = "";

	// get the variables
	$varBaudRate = $_POST["formBaudRate"];
	$varMaxFeedX = $_POST["formMaxFeedX"];
	$varMaxFeedY = $_POST["formMaxFeedY"];
	$varMaxFeedZ = $_POST["formMaxFeedZ"];
	$varMaxFeedE = $_POST["formMaxFeedE"];

	$varMaxAccelX = $_POST["formMaxAccelX"];
	$varMaxAccelY = $_POST["formMaxAccelY"];
	$varMaxAccelZ = $_POST["formMaxAccelZ"];
	$varMaxAccelE = $_POST["formMaxAccelE"];

	$varHomeRateX = $_POST["formHomeRateX"];
	$varHomeRateY = $_POST["formHomeRateY"];
	$varHomeRateZ = $_POST["formHomeRateZ"];

	$varNotUseX = (isset($_POST["formNotUseX"]) ? true : false);
	$varNotUseY = (isset($_POST["formNotUseY"]) ? true : false);
	$varNotUseZ = (isset($_POST["formNotUseZ"]) ? true : false);
	$varNotUseE = (isset($_POST["formNotUseE"]) ? true : false);
	$varSoftwareEndstopsEn = (isset($_POST["formSoftwareEndstopsEn"]) ? true : false);
	$varSoftwareEndstopsX = $_POST["formSoftwareEndstopsX"];
	$varSoftwareEndstopsY = $_POST["formSoftwareEndstopsY"];
	$varSoftwareEndstopsZ = $_POST["formSoftwareEndstopsZ"];

	$varPIDEn = (isset($_POST["formPIDEn"]) ? true : false);
	$varPIDKp = $_POST["formPIDKp"];
	$varPIDKi = $_POST["formPIDKi"];
	$varPIDKd = $_POST["formPIDKd"];

	$varPIDSpeedEn = (isset($_POST["formPIDspeedEn"]) ? true : false);
	$varPIDKc = $_POST["formPIDKc"];
	
	$varMinExtTemp = $_POST["formMinExtTemp"];
	$varMaxExtTemp = $_POST["formMaxExtTemp"];

	$varM109Hyster = $_POST["formM109Hyster"];
	$varM109Wait = $_POST["formM109Wait"];

	$varAD595Gain = $_POST["formAD595Gain"];
	$varAD595Offset = $_POST["formAD595Offset"];

	$varAutoTempEn = (isset($_POST["formAutoTempEn"]) ? true : false);
	$varLateZEn = (isset($_POST["formLateZEn"]) ? true : false);
	$varWatchdogEn = (isset($_POST["formWatchdogEn"]) ? true : false);

	$varExtruderRunoutEn = (isset($_POST["formExtruderRunoutEn"]) ? true : false);
	$varExtruderAdvanceEn = (isset($_POST["formExtruderAdvanceEn"]) ? true : false);

	$varEndstopPullupEn = (isset($_POST["formEndstopPullupEn"]) ? true : false);
	$varEndstopInvertedXEn = (isset($_POST["formEndstopInvertedXEn"]) ? true : false);
	$varEndstopInvertedYEn = (isset($_POST["formEndstopInvertedYEn"]) ? true : false);
	$varEndstopInvertedZEn = (isset($_POST["formEndstopInvertedZEn"]) ? true : false);
	$varEnPinsActiveLowEn = (isset($_POST["formEnPinsActiveLowEn"]) ? true : false);

	$varInvAxisXEn = (isset($_POST["formInvAxisXEn"]) ? true : false);
	$varInvAxisYEn = (isset($_POST["formInvAxisYEn"]) ? true : false);
	$varInvAxisZEn = (isset($_POST["formInvAxisZEn"]) ? true : false);
	$varInvAxisEEn = (isset($_POST["formInvAxisEEn"]) ? true : false);
	
	$varStepsPerUnitX = $_POST["formStepsPerUnitX"];
	$varStepsPerUnitY = $_POST["formStepsPerUnitY"];
	$varStepsPerUnitZ = $_POST["formStepsPerUnitZ"];
	$varStepsPerUnitE = $_POST["formStepsPerUnitE"];

	$varSDCardEn = (isset($_POST["formSDCardEn"]) ? true : false);
	$varUltipanelEn = (isset($_POST["formUltipanelEn"]) ? true : false);
	$varUltipanelClickEn = (isset($_POST["formUltipanelClickEn"]) ? true : false);
	$varLCDEn = (isset($_POST["formLCDEn"]) ? true : false);

	$varHardware = $_POST["formMachine"];
	$varExtruderSensor = $_POST["formSensor"];
	$varBedSensor = $_POST["formBedSensor"];

	if(empty($errorMessage)) 
	{
		$newconfig = "";
		
		// This holds all of the config keys and the associated variable
		$arrargs = array(
		    "BAUDRATE" => $varBaudRate,
		    "DEFAULT_MAX_ACCELERATION" => array($varMaxAccelX, $varMaxAccelY, $varMaxAccelZ, $varMaxAccelE),
		    "HOMING_FEEDRATE" => array($varHomeRateX."*60", $varHomeRateX."*60", $varHomeRateX."*60", 0),
		    "DISABLE_X" => ($varNotUseX) ? 'true' : 'false',
		    "DISABLE_Y" => ($varNotUseY) ? 'true' : 'false',
		    "DISABLE_Z" => ($varNotUseZ) ? 'true' : 'false',
		    "DISABLE_E" => ($varNotUseE) ? 'true' : 'false',
		    "max_software_endstops" => ($varSoftwareEndstopsEn) ? 'true' : 'false',
		    "PIDTEMP" => ($varPIDEn) ? 'true' : 'false',
		    "DEFAULT_Kp" => $varPIDKp,
		    "DEFAULT_Ki" => $varPIDKi,
		    "DEFAULT_Kd" => $varPIDKd,
		    "EXTRUDE_MINTEMP" =>  $varMinExtTemp,
		    "HEATER_0_MAXTEMP" => $varMaxExtTemp,
		    "TEMP_HYSTERESIS" => $varM109Hyster,
		    "TEMP_RESIDENCY_TIME" => $varM109Wait,
		    "ENDSTOPPULLUPS" => ($varEndstopPullupEn) ? 'true' : 'false',
		    "X_ENDSTOPS_INVERTING" => ($varEndstopInvertedXEn) ? 'true' : 'false',
		    "Y_ENDSTOPS_INVERTING" => ($varEndstopInvertedYEn) ? 'true' : 'false',
		    "Z_ENDSTOPS_INVERTING" =>($varEndstopInvertedZEn) ? 'true' : 'false',
		    "X_ENABLE_ON" => ($varEnPinsActiveLowEn) ? '0' : '1',   // this is backwards I know
		    "Y_ENABLE_ON" => ($varEnPinsActiveLowEn) ? '0' : '1',   // this is backwards I know
		    "Z_ENABLE_ON" => ($varEnPinsActiveLowEn) ? '0' : '1',   // this is backwards I know
		    "E_ENABLE_ON" =>($varEnPinsActiveLowEn) ? '0' : '1',   // this is backwards I know
		    "INVERT_X_DIR" => ($varInvAxisXEn) ? 'true' : 'false',
		    "INVERT_Y_DIR" => ($varInvAxisYEn) ? 'true' : 'false',
		    "INVERT_Z_DIR" => ($varInvAxisZEn) ? 'true' : 'false',
		    "INVERT_E0_DIR" => ($varInvAxisEEn) ? 'true' : 'false',
		    "DEFAULT_AXIS_STEPS_PER_UNIT" => array($varStepsPerUnitX, $varStepsPerUnitY, $varStepsPerUnitZ, $varStepsPerUnitE),
		    "USE_WATCHDOG" => ($varWatchdogEn) ? 'deftrue' : 'deffalse',
		    "AUTOTEMP" => ($varAutoTempEn) ? 'deftrue' : 'deffalse',
		    "ENDSTOPS_ONLY_FOR_HOMING" => ($varOnlyHoming) ? 'deftrue' : 'deffalse',
		    "PIDTEMP" => ($varPIDSpeedEn) ? 'deftrue' : 'deffalse',
		    "DEFAULT_Kc" => '('. $varPIDKc .')',
		    "TEMP_SENSOR_AD595_GAIN" => $varAD595Gain,
		    "TEMP_SENSOR_AD595_OFFSET" => $varAD595Offset,
		    "Z_LATE_ENABLE" => ($varLateZEn) ? 'deftrue' : 'deffalse',
		    "EXTRUDER_RUNOUT_PREVENT" => ($varExtruderRunoutEn) ? 'deftrue' : 'deffalse',
		    "ADVANCE" => ($varExtruderAdvanceEn) ? 'deftrue' : 'deffalse',
		    "SDSUPPORT" => ($varSDCardEn) ? 'deftrue' : 'deffalse',
		    "ULTIMAKERCONTROLLER" => ($varUltipanelEn) ? 'deftrue' : 'deffalse',
		    "ULTIPANEL" => ($varUltipanelEn) ? 'deftrue' : 'deffalse',
		    "NEWPANEL" => ($varUltipanelClickEn) ? 'deftrue' : 'deffalse',
		    "ULTRA_LCD" => ($varLCDEn) ? 'deftrue' : 'deffalse',		
		    "DUMMY" => "111" // To stop me missing the , at the end of this list
		);
		
		//first parse config file
		$file_handle = fopen("Configuration.h", "rb");
		while (!feof($file_handle) ) 
		{
		      $line_of_text = fgets($file_handle);
		      // remove all signs of newlines
		      $string = preg_replace('~[\r\n]+~', '', $line_of_text);
		      $string = preg_replace('~[\n]+~', '', $string);
		      $string = preg_replace('~[\r]+~', '', $string);
		      $newline = "";
		      foreach ($arrargs as $key => $val) {
			    $newline = checkLine($string, $key, $val);
			    if ($string != $newline) { // found a change, so remove stop it looking for the keyword again
				unset($arrargs[$key]);
				break;
			    }
		      }
		      
		      $newconfig = $newconfig . $newline ."\r\n";
		      
		}
		fclose($file_handle);
		
		// create tmp if not aleady there
		if ( !file_exists('./tmp') ) {
		  $old = umask(0); 
		  mkdir("./tmp",0777); 
		  umask($old); 
		}
		
		// create unique dir name
		$dir = "builder_".time().'-'.uniqid();
		if ( !file_exists($dir) ) {
		  $old = umask(0); 
		  mkdir("./tmp/".$dir,0777); 
		  umask($old); 
		}
		
		// copy from Marlin template, and then delete the configs
		$output = shell_exec('cp -Rf ./tmp/Marlin ./tmp/'.$dir.'/ > /dev/null 2>&1');
		$output = shell_exec('rm -f ./tmp/'.$dir.'/Marlin/Marlin/Configuration.h  > /dev/null 2>&1');
		$output = shell_exec('rm -f ./tmp/'.$dir.'/Marlin/marlin/Configuration_adv.h  > /dev/null 2>&1');
		$output = shell_exec('rm -f ./tmp/'.$dir.'/Marlin/marlin/Makefile  > /dev/null 2>&1');

		// write the config file out
		file_put_contents("./tmp/".$dir."/Marlin/Marlin/Configuration.h", $newconfig);

		$newconfig = "";
		
		// check the advanced file
		$file_handle = fopen("Configuration_adv.h", "rb");
		while (!feof($file_handle) ) 
		{
		      $line_of_text = fgets($file_handle);
		      $string = preg_replace('~[\r\n]+~', '', $line_of_text);
		      $string = preg_replace('~[\n]+~', '', $string);
		      $string = preg_replace('~[\r]+~', '', $string);
		      
		      foreach ($arrargs as $key => $val) {
			    $newline = checkLine($string, $key, $val);
			    if ($string != $newline) { // found a change, so remove stop it looking for the keywork again
				unset($arrargs[$key]);
			    }
		      }
		      
		      
		     
		      // End of file pretty much. Check what params we have left and stuff them in the file
		      if ($line_of_text == "#endif //__CONFIGURATION_ADV_H") {
			    foreach ($arrargs as $key => $arg) {
				if ($arg == "deftrue"   ) {
				    $newline = $newline . "\r\n#define " . $key ."\r\n";
				}
				else if ($arg == "deffalse") {
				    $newline = $newline . "\r\n// #define " . $key ."\r\n";
				}
				else {
				    $newline = $newline . "\r\n#define " . $key . " " . $arg;
				}
			    }
			    $newline = $newline + "\r\n" . $line_of_text;
		      }
		      
		      $newconfig = $newconfig . $newline ."\r\n";
		}
		fclose($file_handle);
		
		file_put_contents("./tmp/".$dir."/Marlin/Marlin/Configuration_adv.h", $newconfig);
		
		// now do the makefile
		
		$newconfig = "";
		
		$file_handle = fopen("./tmp/Marlin/Marlin/Makefile", "rb");
		while (!feof($file_handle) ) 
		{
		      $line_of_text = fgets($file_handle);

		      if (startsWith($line_of_text, "HARDWARE_MOTHERBOARD")) { // we found it
			  $line_of_text = "HARDWARE_MOTHERBOARD ?= ". $varHardware ."\r\n";
		      }
		      
		      $newconfig = $newconfig . $line_of_text."";
		}
		fclose($file_handle);
		
		file_put_contents("./tmp/".$dir."/Marlin/Marlin/Makefile", $newconfig);

		// build it
		shell_exec('cd ./tmp/'.$dir.'/Marlin/Marlin/; make  >  build_summary.txt');
	
		// copy files from build and cleanup
		shell_exec('cp -f ./tmp/'.$dir.'/Marlin/Marlin/Makefile ./tmp/'.$dir.'/ > /dev/null 2>&1');
		shell_exec('cp ./tmp/'.$dir.'/Marlin/Marlin/Configuration.h ./tmp/'.$dir.'/ > /dev/null 2>&1');
		shell_exec('cp ./tmp/'.$dir.'/Marlin/Marlin/Configuration_adv.h ./tmp/'.$dir.'/ > /dev/null 2>&1');
		shell_exec('cp ./tmp/'.$dir.'/Marlin/Marlin/applet/Marlin.hex ./tmp/'.$dir.'/ > /dev/null 2>&1');
		shell_exec('cp ./tmp/'.$dir.'/Marlin/Marlin/build_summary.txt ./tmp/'.$dir.'/ > /dev/null 2>&1');
		shell_exec('rm -rf ./tmp/'.$dir.'/Marlin > /dev/null 2>&1');
		shell_exec('zip ./tmp/'.$dir.'/marlin-'.$dir.'.zip Makefile Configuration.h Configuration_adv.h Marlin.hex build_summary.txt > /dev/null 2>&1');
		
		// generate build summary
		$summaryarr = file("./tmp/".$dir."/build_summary.txt");
		$i=0;
		$fcnt = count($summaryarr);
		$newarr = Array();
		foreach($summaryarr as $sum) {
		    
		    if ($i < $fcnt-15) {
		    }
		    else {
			array_push($newarr, $sum);
		    }
		    
		    $i++;
		}
		$newsum = join($newarr,'');
		
		echo("<h1>Build Completed</h1>");
		echo("<br><br><h2>Build Summary</h2>");
		echo("<pre>".$newsum."</pre>");
		echo("<br><h2>Download Files</h2>");
		echo('<a href="./tmp/'.$dir.'/build_summary.txt">Build Summary</a>');
		echo('<br/><a href="./tmp/'.$dir.'/Configuration.h">Configuration.h</a>');
		echo('<br/><a href="./tmp/'.$dir.'/Configuration_adv.h">Configuration_adv.h</a>');
		echo('<br/><a href="./tmp/'.$dir.'/Makefile">Makefile</a>');
		echo('<br/><a href="./tmp/'.$dir.'/Marlin.hex">HEX File</a>');
		echo('<br/><a href="./tmp/'.$dir.'/marlin-'.$dir.'.zip">All of it as a ZIP</a>');
		//echo ($newconfig);
		//header("Location: thankyou.html");
		exit;
	}
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
<head>
	<LINK href="style.css" rel="stylesheet" type="text/css">
	<title>Ginge's Marlin Builder</title>
</head>

<body>
	<?php
		if(!empty($errorMessage)) 
		{
			echo("<p>There was an error with your form:</p>\n");
			echo("<ul>" . $errorMessage . "</ul>\n");
		} 
	?>
        <h1>Ginge's Marlin Builder</h1>
        barry.carter@gmail.com
        <br/>
        <a href="https://github.com/ginge/marlin-builder">source at GitHub</a>
        <br/>
	<form action="index.php" method="post">
		<table id="mytable">
			<tr>
				<th></th>
				<th>Software Basic Configuration</th>
				<th>?</th>
			</tr>
			<tr>
				<td>Baud Rate:</td>
				<td><input type="text" name="formBaudRate" value="<?php echo($varBaudRate);?>" /></td>
				<td></td>
			</tr>
			<tr>
                                <td>Max Feed Rate:</td>
                                <td>X:<input type="text" name="formMaxFeedX" value="<?php echo($varMaxFeedX);?>" />
				    Y:<input type="text" name="formMaxFeedY" value="<?php echo($varMaxFeedY);?>" />
				    Z:<input type="text" name="formMaxFeedZ" value="<?php echo($varMaxFeedZ);?>" />
				    E:<input type="text" name="formMaxFeedE" value="<?php echo($varMaxFeedE);?>" />
				</td>
                                <td></td>
			</tr>
                        <tr>
                                <td>Max Acceleration Rate:</td>
                                <td>X:<input type="text" name="formMaxAccelX" value="<?php echo($varMaxAccelX);?>" />
                                    Y:<input type="text" name="formMaxAccelY" value="<?php echo($varMaxAccelY);?>" />
                                    Z:<input type="text" name="formMaxAccelZ" value="<?php echo($varMaxAccelZ);?>" />
                                    E:<input type="text" name="formMaxAccelE" value="<?php echo($varMaxAccelE);?>" />
                                </td> 
                                <td></td>
                        </tr>
			<tr>
                                <td>Homing Feed Rate:</td>
                                <td>X:<input type="text" name="formHomeRateX" value="<?php echo($varHomeRateX);?>" />
                                    Y:<input type="text" name="formHomeRateY" value="<?php echo($varHomeRateY);?>" />
                                    Z:<input type="text" name="formHomeRateZ" value="<?php echo($varHomeRateZ);?>" />
                                </td> 
                                <td></td>
                        </tr>
			<tr>
				<td>Disable axis when not in use:</td>
				<td>X:<input type="checkbox" name="formNotUseX" <?php echo(chktag($varNotUseX));?> value="<?php echo($varNotUseX);?>" />
				    Y:<input type="checkbox" name="formNotUseY" <?php echo(chktag($varNotUseY));?> value="<?php echo($varNotUseY);?>"/>
				    Z:<input type="checkbox" name="formNotUseZ" <?php echo(chktag($varNotUseZ));?> value="<?php echo($varNotUseZ);?>"/>
				    E:<input type="checkbox" name="formNotUseE" <?php echo(chktag($varNotUseE));?> value="<?php echo($varNotUseE);?>"/>
				</td>
				<td></td>
			</tr>
                        <tr>
                                <td>Endstop only for homing:</td>
                                <td><input type="checkbox" name="formOnlyHoming" value="<?php echo($varOnlyHoming);?>" />
                                </td>
                                <td></td>
                        </tr>
                        <tr>
                                <td>Software Endstops:</td>
                                <td>Enable: <input type="checkbox" name="formSoftwareEndstopsEn" <?php echo(chktag($varSoftwareEndstopsEn));?> value="<?php echo($varSoftwareEndstopsEn);?>"/>
				    max X:<input type="text" name="formSoftwareEndstopsX" <?php echo(chktag($varSoftwareEndstopsX));?> value="<?php echo($varSoftwareEndstopsX);?>"/>
				    max Y:<input type="text" name="formSoftwareEndstopsY" <?php echo(chktag($varSoftwareEndstopsY));?> value="<?php echo($varSoftwareEndstopsY);?>"/>
				    max Z:<input type="text" name="formSoftwareEndstopsZ" <?php echo(chktag($varSoftwareEndstopsZ));?> value="<?php echo($varSoftwareEndstopsZ);?>"/>

                                </td>
                                <td></td>
                        </tr>
			<tr>
				<th></th>
				<th>Software Advanced</th>
				<th></th>
			<tr>
                        <tr> 
                                <td>Enable PID temperature control:</td>
                                <td>Enable: <input type="checkbox" name="formPIDEn" <?php echo(chktag($varPIDEn));?> /> 
                                    Kp:<input type="text" name="formPIDKp" value="<?php echo($varPIDKp);?>" />
                                    Ki:<input type="text" name="formPIDKi" value="<?php echo($varPIDKi);?>" />
                                    Kd:<input type="text" name="formPIDKd" value="<?php echo($varPIDKd);?>" />
                                </td> 
                                <td></td>
                        </tr>
                        <tr> 
                                <td>Add extrusion speed to PID:</td>
                                <td>Enable: <input type="checkbox" name="formPIDSpeedEn" <?php echo(chktag($varPIDSpeedEn));?> value="<?php echo($varPIDSpeedEn);?>" />
                                    <input type="text" name="formPIDKc" value="<?php echo($varPIDKc);?>" />                             
                                </td> 
                                <td></td>
                        </tr>
                        <tr>
                                <td>Minimum extrusion temperature:</td>
                                <td><input type="text" name="formMinExtTemp" value="<?php echo($varMinExtTemp);?>" /></td>
                                <td></td>
                        </tr>
                        <tr>
                                <td>Maximum extrusion temperature:</td>
                                <td><input type="text" name="formMaxExtTemp" value="<?php echo($varMaxExtTemp);?>" /></td>
                                <td></td>
                        </tr>
                        <tr>
                                <td>M109 Params:</td>
                                <td>Hysteresis:<input type="text" name="formM109Hyster" value="<?php echo($varM109Hyster);?>" />
                                    Wait Time:<input type="text" name="formM109Wait" value="<?php echo($varM109Wait);?>" />
                                <td></td>
			</tr>
                        <tr>
                                <td>AD595 Calibration:</td>
                                <td>Gain:<input type="text" name="formAD595Gain" value="<?php echo($varAD595Gain);?>" />
                                    Offset:<input type="text" name="formAD595Offset" value="<?php echo($varAD595Offset);?>" /></td>
                                <td></td>
                        </tr>
                        <tr>
                                <th></th>
                                <th>Software Experimental</th>
                                <th></th>
                        </tr>
                        <tr>
                                <td>Enable auto temp:</td>
                                <td><input type="checkbox" name="formAutoTempEn" <?php echo(chktag($varAutoTempEn));?> value="<?php echo($varAutoTempEn);?>"/>
                                </td>
                                <td></td>
                        </tr>
                        <tr>
                                <td>Late Z enable:</td>
                                <td><input type="checkbox" name="formLateZEn" <?php echo(chktag($varLateZEn));?> value="1"/>
                                </td>
                                <td></td>
                        </tr>
                        <tr>
                                <td>Enable watchdog:</td>
                                <td><input type="checkbox" name="formWatchdogEn" <?php echo(chktag($varWatchdogEn));?> value="1"/>
                                </td>
                                <td></td>
                        </tr>
                        <tr>
                                <td>Extruder runout protection:</td>
                                <td><input type="checkbox" name="formExtruderRunoutEn" <?php echo(chktag($varExtruderRunoutEn));?> value="1"/>
                                </td>
                                <td></td>
                        </tr>
                        <tr>
                                <td>Enable extruder advance:</td>
                                <td><input type="checkbox" name="formExtruderAdvanceEn" <?php echo(chktag($varExtruderAdvanceEn));?> value="1"/>
                                </td>
                                <td></td>
                        </tr>
                        <tr>
                                <th></th>
                                <th>Hardware</th>
                                <th></th>
                        </tr>
                        <tr>
                                <td>Board Type:</td>
                                <td>
					<select name="formMachine">
						<option value="7">Ultimaker</option>
						<option value="10">Gen7 custom (Alfons3 Version)</option>
						<option value="11">Gen7 v1.1, v1.2">11</option>
						<option value="12">Gen7 v1.3</option>
						<option value="13">Gen7 v1.4</option>
						<option value="3 ">MEGA/RAMPS up to 1.2 3</option>
						<option value="33">RAMPS 1.3 / 1.4 (Power outputs: Extruder, Fan, Bed)</option>
						<option value="34">RAMPS 1.3 / 1.4 (Power outputs: Extruder0, Extruder1, Bed)</option>
						<option value="35">RAMPS 1.3 / 1.4 (Power outputs: Extruder, Fan, Fan)</option>
						<option value="4 ">Duemilanove w/ ATMega328P pin assignment</option>
						<option value="5 ">Gen6</option>
						<option value="51">Gen6 deluxe</option>
						<option value="6 ">Sanguinololu < 1.2</option>
						<option value="62">Sanguinololu 1.2 and above</option>
						<option value="63">Melzi</option>
						<option value="64">STB V1.1</option>
						<option value="65">Azteeg X1</option>
						<option value="66">Melzi with ATmega184 (MaKr3d version)</option>
						<option value="71">Ultimaker (Older electronics. Pre 1.5.4. This is rare)</option>
						<option value="77">3Drag Controller</option>
						<option value="8 ">Teensylu</option>
						<option value="80">Rumba</option>
						<option value="81">Printrboard (AT90USB1286)</option>
						<option value="82">Brainwave (AT90USB646)</option>
						<option value="9 ">Gen3+</option>
						<option value="70">Megatronics</option>
						<option value="701"> Megatronics v2.0</option>
						<option value="702"> Minitronics v1.0</option>
						<option value="90">Alpha OMCA board</option>
						<option value="91">Final OMCA board</option>
						<option value="301">Rambo</option>
						<option value="21">Elefu Ra Board (v3)</option>
					</select>
				</td>
                                <td></td>
                        </tr>
                        <tr>
                        	<td>Extruder temperature sensor:</td>
                                <td>
                                        <select name="formSensor">
						<option value="-1">thermocouple with AD595</option>
                                                <option value="-2">thermocouple with MAX6675 (only for sensor 0)</option>
						<option value="1">100k thermistor - best choice for EPCOS 100k (4.7k pullup)</option>
						<option value="2">200k thermistor - ATC Semitec 204GT-2 (4.7k pullup)</option>
						<option value="3">mendel-parts thermistor (4.7k pullup)</option>
						<option value="4">10k thermistor !! do not use it for a hotend. It gives bad resolution at high temp. !!</option>
						<option value="5">100K thermistor - ATC Semitec 104GT-2 (Used in ParCan) (4.7k pullup)</option>
						<option value="6">100k EPCOS - Not as accurate as table 1 (created using a fluke thermocouple) (4.7k pullup)</option>
						<option value="7">100k Honeywell thermistor 135-104LAG-J01 (4.7k pullup)</option>
						<option value="8">100k 0603 SMD Vishay NTCS0603E3104FXT (4.7k pullup)</option>
						<option value="9">100k GE Sensing AL03006-58.2K-97-G1 (4.7k pullup)</option>
						<option value="10">100k RS thermistor 198-961 (4.7k pullup)</option>
						<option value="60">100k Maker's Tool Works Kapton Bed Thermister</option>
						<option value="51">100k thermistor - EPCOS (1k pullup)</option>
						<option value="52">200k thermistor - ATC Semitec 204GT-2 (1k pullup)</option>
						<option value="55">100k thermistor - ATC Semitec 104GT-2 (Used in ParCan) (1k pullup)</option>
                                        </select>
                                </td>
                                <td></td>
                        </tr>
                        <tr>
                                <td>Heated bed temperature sensor:</td>
                                <td>
                                        <select name="formBedSensor">
						<option value="1">100k thermistor - best choice for EPCOS 100k (4.7k pullup)</option>
                                                <option value="-1">thermocouple with AD595</option>
                                                <option value="-2">thermocouple with MAX6675 (only for sensor 0)</option>
						<option value="2">200k thermistor - ATC Semitec 204GT-2 (4.7k pullup)</option>
						<option value="3">mendel-parts thermistor (4.7k pullup)</option>
						<option value="4">10k thermistor !! do not use it for a hotend. It gives bad resolution at high temp. !!</option>
						<option value="5">100K thermistor - ATC Semitec 104GT-2 (Used in ParCan) (4.7k pullup)</option>
						<option value="6">100k EPCOS - Not as accurate as table 1 (created using a fluke thermocouple) (4.7k pullup)</option>
						<option value="7">100k Honeywell thermistor 135-104LAG-J01 (4.7k pullup)</option>
						<option value="8">100k 0603 SMD Vishay NTCS0603E3104FXT (4.7k pullup)</option>
						<option value="9">100k GE Sensing AL03006-58.2K-97-G1 (4.7k pullup)</option>
						<option value="10">100k RS thermistor 198-961 (4.7k pullup)</option>
						<option value="60">100k Maker's Tool Works Kapton Bed Thermister</option>
						<option value="51">100k thermistor - EPCOS (1k pullup)</option>
						<option value="52">200k thermistor - ATC Semitec 204GT-2 (1k pullup)</option>
						<option value="55">100k thermistor - ATC Semitec 104GT-2 (Used in ParCan) (1k pullup)</option>
                                        </select>
                                </td>
                                <td></td>
                        </tr>
                        <tr>
                                <td>Enable endstop pullup resistors:</td>
                                <td><input type="checkbox" name="formEndstopPullupEn" <?php echo(chktag($varEndstopPullupEn));?> value="1"/>
                                </td>
                                <td></td>
                        </tr>
                        <tr>
                                <td>Endstops are inverted:</td>
                                <td><input type="checkbox" name="formEndstopInvertedXEn" <?php echo(chktag($varEndstopInvertedXEn));?> value="1"/>
                                    <input type="checkbox" name="formEndstopInvertedYEn" <?php echo(chktag($varEndstopInvertedYEn));?> value="1"/>
                                    <input type="checkbox" name="formEndstopInvertedZEn" <?php echo(chktag($varEndstopInvertedZEn));?> value="1"/>
                                </td>
                                <td></td>
                        </tr>
                        <tr>
                                <td>Enable pins are active low:</td>
                                <td><input type="checkbox" name="formEnPinsActiveLowEn" <?php echo(chktag($varEnPinsActiveLowEn));?> value="1" />
                                </td>
                                <td></td>
                        </tr>
                        <tr> 
                                <td>Invert axis:</td>
                                <td>X:<input type="checkbox" name="formInvAxisXEn" <?php echo(chktag($varInvAxisXEn));?> value="1"/>
                                    Y:<input type="checkbox" name="formInvAxisYEn" <?php echo(chktag($varInvAxisYEn));?> value="1"/>
                                    Z:<input type="checkbox" name="formInvAxisZEn" <?php echo(chktag($varInvAxisZEn));?> value="1"/>
                                    E:<input type="checkbox" name="formInvAxisEEn" <?php echo(chktag($varInvAxisEEn));?> value="1"/>
                                </td> 
                                <td></td>
                        </tr>
                        <tr>
                                <td>Steps Per Unit:</td>
                                <td>X:<input type="text" name="formStepsPerUnitX" value="<?php echo($varStepsPerUnitX);?>" />
                                    Y:<input type="text" name="formStepsPerUnitY" value="<?php echo($varStepsPerUnitY);?>" />
                                    Z:<input type="text" name="formStepsPerUnitZ" value="<?php echo($varStepsPerUnitZ);?>" />
                                    E:<input type="text" name="formStepsPerUnitE" value="<?php echo($varStepsPerUnitE);?>" />
                                </td> 
                                <td></td>
                        </tr>
			<tr>
				<th></th>
				<th>Hardware Addons</th>
				<th>?</th>
			<tr>
                        <tr>
                                <td>Enable SD Card:</td>
                                <td><input type="checkbox" name="formSDCardEn" <?php echo(chktag($varSDCardEn));?> value="1"/>
                                </td>
                                <td></td>
                        </tr>
                        <tr>
                                <td>Enable UntiPanel:</td>
                                <td><input type="checkbox" name="formUltipanelEn" <?php echo(chktag($varUltipanelEn));?> value="1"/>
                                    + click encoder: <input type="checkbox" name="formUltipanelClickEn" <?php echo(chktag($varUltipanelClickEn));?> value="1"/>
                                </td> 
                                <td></td>
                        </tr>
                        <tr>
                                <td>Enable generic 16x2 LCD:</td>
                                <td><input type="checkbox" name="formLCDEn" <?php echo(chktag($varLCDEn));?> value="1"/>
                                </td> 
                                <td></td>
                        </tr>
		</table>
		<input type="submit" name="formSubmit" value="Build It" />
	</form>
</body>
</html>
