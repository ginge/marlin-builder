<?php
function startsWith($haystack, $needle)
{
    return !strncmp($haystack, $needle, strlen($needle));
}

function commentLine($line, $commented) {
    if (startsWith(trim($line), "//")) {
        if ($commented == false) {
            return $line;
        }
        else {
            return substr(trim($line), 2, strlen($line)-2);
        }
    }
    else {
        if ($commented == false) {
            return "//".$line;
        }
        else {
            return $line;
        }
    }
}

// Checks a line from configuration* and updates any found value.
// This is a box of magic. Do not touch unless you want to break stuff.
// it has to take into account defines, const values and also comments.
function checkLine($line, $confobj)
{
    $var = $confobj->Name;
    $newval = $confobj->Value;
    
    if (empty($line)) {
        return array($line, false);
    }

    // If the config keyword is even in this line?
    if (stristr($line, $var) !== False) {
        $parts = explode(' ', $line);

        // ignore the crap
        if (!startsWith(trim($line), "#define") && !startsWith(trim($line), "const") && !startsWith(trim($line), "//")) {
            return array($line, false);
        }

        // go through every token in the cnofig line
        // we first look for the keyword. The the spaces leading to the value (we want to preserve those). Then the comment
        $indent = false;
        $findingval = false;
        $comment = false;
        $novalue = false;
        $findequals = false;
        $found = false; // if we find the keyword
        $newstr = "";
        $commentval = "";

        foreach ($parts as $part) {
            if ($part == "const") {
                $findequals = true;
            }  
        
            // found the value after the indent
            if ($indent == true && $part != "") {
                if (in_array("ignore", $confobj->Options)) {
                    // we matched the keyword, but this should be ignored.
                    return array($line, true);
                }
                if ($findequals && $part == "=") {
                    // this is a const var and has an equals after the $var
                    ;
                }
                else if (startsWith($part, "//")) {
                    // this is one of those defines with no value, we can just return the string here
                    return array(commentLine($line, $newval), true);
                }
                else {
                    $indent = false;
                    $findingval = true;
                    continue;
                }
            }
            if ($findingval == true) {
                //count blank lines
                // if we find the comment we went too far
                $str = stristr($part, "//");
                if ($str != false) {
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
                if (in_array("define", $confobj->Options)) {
                    return array(commentLine($line, $newval), true);
                }
                $indent = true;
                $found = true;
            }
            $newstr = $newstr . (($part == "") ? "" : (($newstr=="") ? "" : " ") . $part);
        }

        // if we didnt get the found flag...
        if ($found == false) {
            return array($line, false);
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
        
        return array($newstr . (($newstr=="") ? "" : " ") . (($newval=="" || in_array("define", $confobj->Options)) ? "" : $newval) . (startsWith($line, "const") ? ";" : "") . (($commentval!="") ?  "      //" . $commentval : ""), true);
    }

    return array($line, false);
}

class Option {
    public $Name = "";      // The config key name to find
    public $Value = "";     // The new value for the key
    public $Options = array();   // Options for the config. Valid options are: "ignore": doesn't change the config. "define": flags this as a define with no value
    public $HardwareId = -1;  // Optional hardware ID for the config option. Allows sections to be controlled by hardware type
    public $SectionIdentifier = "";  //Closest line of code before our config option. i.e #ifdef SOMEOPTION
}


if(isset($_POST["sendconf"]) && $_POST["sendconf"] == "Send Config for consideration") {
    $varMachineName = $_POST["machine"];
    $varMachineDesc = $_POST["desc"];
    $varMachineConf = $_POST["config"];
    $varWhoFrom     = $_POST["whofrom"];
    $varWhoFromEmail= $_POST["email"];
    $varZipPath= $_POST["zippath"];
    $body  = "Submitter: " . $varWhoFrom . "\n";
    $body .= "Submitter email: " . $varWhoFromEmail . "\n";
    $body .= "Machine Name: " . $varMachineName . "\n";
    $body .= "Machine Desc: " . $varMachineDesc . "\n";
    $body .= "Machine Config: \n" . $varMachineConf . "\n";
    
    // include and start phpmailer
    require_once('PHPMailer/class.phpmailer.php');
    $mail = new PHPMailer();

    $mail->AddAddress("barry.carter@gmail.com");

    $mail->From         = "marlinbuilder@robotfuzz.com";
    $mail->FromName     = "Marlin Builder";
    $mail->Subject      = "Marlin Builder Template Request";
    $mail->Body         = $body;

    $mail->AddAttachment($varZipPath);
    
    if ($mail->send()) {
        echo ("<p>Message Sent. Thanks!");
    }
    else {
        echo ("<p>Message delivery failed! Boo!");
    }
    die();
}

if(isset($_POST["formSubmit"]) && $_POST["formSubmit"] == "Build It") {
    $errorMessage = "";

    // get the variables
    $varBaudRate                = $_POST["formBaudRate"];
    $varMaxFeedX                = $_POST["formMaxFeedX"];
    $varMaxFeedY                = $_POST["formMaxFeedY"];
    $varMaxFeedZ                = $_POST["formMaxFeedZ"];
    $varMaxFeedE                = $_POST["formMaxFeedE"];

    $varMaxAccelX               = $_POST["formMaxAccelX"];
    $varMaxAccelY               = $_POST["formMaxAccelY"];
    $varMaxAccelZ               = $_POST["formMaxAccelZ"];
    $varMaxAccelE               = $_POST["formMaxAccelE"];

    $varHomeRateX               = $_POST["formHomeRateX"];
    $varHomeRateY               = $_POST["formHomeRateY"];
    $varHomeRateZ               = $_POST["formHomeRateZ"];

    $varNotUseX                 = (isset($_POST["formNotUseX"]) ? true : false);
    $varNotUseY                 = (isset($_POST["formNotUseY"]) ? true : false);
    $varNotUseZ                 = (isset($_POST["formNotUseZ"]) ? true : false);
    $varNotUseE                 = (isset($_POST["formNotUseE"]) ? true : false);
    $varSoftwareEndstopsEn      = (isset($_POST["formSoftwareEndstopsEn"]) ? true : false);
    $varSoftwareEndstopsX       = $_POST["formSoftwareEndstopsX"];
    $varSoftwareEndstopsY       = $_POST["formSoftwareEndstopsY"];
    $varSoftwareEndstopsZ       = $_POST["formSoftwareEndstopsZ"];

    $varOnlyHoming              = (isset($_POST["formOnlyHoming"]) ? true : false);

    $varPIDEn                   = (isset($_POST["formPIDEn"]) ? true : false);
    $varPIDKp                   = $_POST["formPIDKp"];
    $varPIDKi                   = $_POST["formPIDKi"];
    $varPIDKd                   = $_POST["formPIDKd"];

    $varPIDSpeedEn              = (isset($_POST["formPIDspeedEn"]) ? true : false);
    $varPIDKc                   = $_POST["formPIDKc"];
    
    $varMinExtTemp              = $_POST["formMinExtTemp"];
    $varMaxExtTemp              = $_POST["formMaxExtTemp"];

    $varM109Hyster              = $_POST["formM109Hyster"];
    $varM109Wait                = $_POST["formM109Wait"];

    $varAD595Gain               = $_POST["formAD595Gain"];
    $varAD595Offset             = $_POST["formAD595Offset"];

    $varAutoTempEn              = (isset($_POST["formAutoTempEn"]) ? true : false);
    $varLateZEn                 = (isset($_POST["formLateZEn"]) ? true : false);
    $varWatchdogEn              = (isset($_POST["formWatchdogEn"]) ? true : false);

    $varExtruderRunoutEn        = (isset($_POST["formExtruderRunoutEn"]) ? true : false);
    $varExtruderAdvanceEn       = (isset($_POST["formExtruderAdvanceEn"]) ? true : false);

    $varEndstopPullupEn         = (isset($_POST["formEndstopPullupEn"]) ? true : false);
    $varEndstopInvertedXEn      = (isset($_POST["formEndstopInvertedXEn"]) ? true : false);
    $varEndstopInvertedYEn      = (isset($_POST["formEndstopInvertedYEn"]) ? true : false);
    $varEndstopInvertedZEn      = (isset($_POST["formEndstopInvertedZEn"]) ? true : false);
    $varEnPinsActiveLowEn       = (isset($_POST["formEnPinsActiveLowEn"]) ? true : false);

    $varInvAxisXEn              = (isset($_POST["formInvAxisXEn"]) ? true : false);
    $varInvAxisYEn              = (isset($_POST["formInvAxisYEn"]) ? true : false);
    $varInvAxisZEn              = (isset($_POST["formInvAxisZEn"]) ? true : false);
    $varInvAxisEEn              = (isset($_POST["formInvAxisEEn"]) ? true : false);
    
    $varStepsPerUnitX           = $_POST["formStepsPerUnitX"];
    $varStepsPerUnitY           = $_POST["formStepsPerUnitY"];
    $varStepsPerUnitZ           = $_POST["formStepsPerUnitZ"];
    $varStepsPerUnitE           = $_POST["formStepsPerUnitE"];

    $varSDCardEn                = (isset($_POST["formSDCardEn"]) ? true : false);
    $varUltipanelEn             = (isset($_POST["formUltipanelEn"]) ? true : false);
    $varUltipanelClickEn        = (isset($_POST["formUltipanelClickEn"]) ? true : false);
    $varLCDEn                   = (isset($_POST["formLCDEn"]) ? true : false);

    $varHardware                = $_POST["formMachine"];
    $varExtruderSensor          = $_POST["formSensor"];
    $varExtruderSensor1         = $_POST["formSensor1"];
    $varBedSensor               = $_POST["formBedSensor"];
    $varFastFanPwmEn            = (isset($_POST["formFastFanPwmEn"]) ? true: false);
    $varPIDDebug                = (isset($_POST["formPIDDebug"]) ? true: false);
    $varFilamentChangeEn        = (isset($_POST["formFilamentChangeEn"]) ? true: false);
    
    $varAllSettings             = $_POST["formSettingsString"];

    $arrclass = array();
    
    function l($name, $value, $options = array()) {
        global $arrclass;
        $c = new Option();
        $c->Name = $name;
        $c->Value = $value;
        $c->Options = $options;
        array_push($arrclass, $c);
    }
    
    if(empty($errorMessage)) {
        $newconfig = "";
        
        // This is where the magic happens. Put your config options here.
        l("BAUDRATE",                 $varBaudRate);
        l("DEFAULT_MAX_ACCELERATION", array($varMaxAccelX, $varMaxAccelY, $varMaxAccelZ, $varMaxAccelE));
        l("DEFAULT_MAX_FEEDRATE",     array($varMaxFeedX, $varMaxFeedY, $varMaxFeedZ, $varMaxFeedE));
        l("HOMING_FEEDRATE",          array($varHomeRateX."*60", $varHomeRateY."*60", $varHomeRateZ."*60", 0));
        l("DISABLE_X",                ($varNotUseX) ? 'true' : 'false');
        l("DISABLE_Y",                ($varNotUseY) ? 'true' : 'false');
        l("DISABLE_Z",                ($varNotUseZ) ? 'true' : 'false');
        l("DISABLE_E",                ($varNotUseE) ? 'true' : 'false');
        l("max_software_endstops",    ($varSoftwareEndstopsEn) ? 'true' : 'false');
        l("X_MAX_POS",                $varSoftwareEndstopsX);
        l("Y_MAX_POS",                $varSoftwareEndstopsY);
        l("Z_MAX_POS",                $varSoftwareEndstopsZ);
        l("PIDTEMP",                  $varPIDEn,       array("define"));
        l("DEFAULT_Kp",               $varPIDKp);  // this one is a problem because it is mentioned later. not a problem unless you own a makergear or mendel v9 on 12V
        l("DEFAULT_Ki",               $varPIDKi);  // this one is a problem
        l("DEFAULT_Kd",               $varPIDKd);  // this one is a problem
        l("EXTRUDE_MINTEMP",          $varMinExtTemp);
        l("HEATER_0_MAXTEMP",         $varMaxExtTemp);
        l("TEMP_HYSTERESIS",          $varM109Hyster);
        l("TEMP_RESIDENCY_TIME",      $varM109Wait);
        l("ENDSTOPPULLUPS",           $varEndstopPullupEn, array("define"));
        l("X_MIN_ENDSTOP_INVERTING",  ($varEndstopInvertedXEn) ? 'true' : 'false');
        l("Y_MIN_ENDSTOP_INVERTING",  ($varEndstopInvertedYEn) ? 'true' : 'false');
        l("Z_MIN_ENDSTOP_INVERTING",  ($varEndstopInvertedZEn) ? 'true' : 'false');
        l("X_MAX_ENDSTOP_INVERTING",  ($varEndstopInvertedXEn) ? 'true' : 'false');
        l("Y_MAX_ENDSTOP_INVERTING",  ($varEndstopInvertedYEn) ? 'true' : 'false');
        l("Z_MAX_ENDSTOP_INVERTING",  ($varEndstopInvertedZEn) ? 'true' : 'false');
        l("X_ENABLE_ON",              ($varEnPinsActiveLowEn) ? '0' : '1');   // this is backwards I know
        l("Y_ENABLE_ON",              ($varEnPinsActiveLowEn) ? '0' : '1');   // this is backwards I know
        l("Z_ENABLE_ON",              ($varEnPinsActiveLowEn) ? '0' : '1');   // this is backwards I know
        l("E_ENABLE_ON",              ($varEnPinsActiveLowEn) ? '0' : '1');   // this is backwards I know
        l("INVERT_X_DIR",             ($varInvAxisXEn) ? 'true' : 'false');
        l("INVERT_Y_DIR",             ($varInvAxisYEn) ? 'true' : 'false');
        l("INVERT_Z_DIR",             ($varInvAxisZEn) ? 'true' : 'false');
        l("INVERT_E0_DIR",            ($varInvAxisEEn) ? 'true' : 'false');
        l("DEFAULT_AXIS_STEPS_PER_UNIT", array($varStepsPerUnitX, $varStepsPerUnitY, $varStepsPerUnitZ, $varStepsPerUnitE));
        l("USE_WATCHDOG",             $varWatchdogEn, array("define"));
        l("AUTOTEMP",                 $varAutoTempEn, array("define"));
        l("ENDSTOPS_ONLY_FOR_HOMING", $varOnlyHoming, array("define"));
        l("PIDTEMP",                  $varPIDSpeedEn, array("define"));
        l("DEFAULT_Kc",               '('. $varPIDKc .')');
        l("TEMP_SENSOR_AD595_GAIN",   $varAD595Gain);
        l("TEMP_SENSOR_AD595_OFFSET", $varAD595Offset);
        l("Z_LATE_ENABLE",            $varLateZEn, array("define"));
        l("EXTRUDER_RUNOUT_PREVENT",  $varExtruderRunoutEn, array("define"));
        l("ADVANCE",                  $varExtruderAdvanceEn, array("define"));
        l("SDSUPPORT",                $varSDCardEn, array("define"));
        l("ULTIMAKERCONTROLLER",      $varUltipanelEn, array("define"));
        //l("ULTIPANEL",                $varUltipanelEn, array("define"));  // this is a dupicated key, but the first instance is th one that needs changing. phew.
        l("ULTRA_LCD",                $varLCDEn, array("define"));
        l("FAST_PWM_FAN",             $varFastFanPwmEn, array("define"));
        l("TEMP_SENSOR_0",            $varExtruderSensor);
        l("TEMP_SENSOR_1",            $varExtruderSensor1);
        l("EXTRUDERS",                ($varExtruderSensor1 == '0') ? '1' : '2'); // extruder count
        l("MOTHERBOARD",              $varHardware);
        l("TEMP_SENSOR_BED",          $varBedSensor);
        l("PID_DEBUG",                $varPIDDebug, array("define"));
        l("FILAMENTCHANGEENABLE",     $varFilamentChangeEn, array("define"));
        l("EEPROM_SETTINGS",          $varUltipanelEn, array("define"));
        l("EEPROM_CHITCHAT",          $varUltipanelEn, array("define"));



        //first parse config file. Go through each line and compare with our local settings.
        $file_handle = fopen("tmp/Marlin/Marlin/Configuration.h", "rb");
        while (!feof($file_handle) ) {
            $line_of_text = fgets($file_handle);
            // remove all signs of newlines
            $string = preg_replace('~[\r\n]+~', '', $line_of_text);
            $string = preg_replace('~[\n]+~', '', $string);
            $string = preg_replace('~[\r]+~', '', $string);
            $newline = "";
            foreach ($arrclass as $key => $val) {
                $lineobj = checkLine($string, $val);
                $newline = $lineobj[0];
                $processed = $lineobj[1];

                if ($processed == true || $processed == "true") { // found a change, so remove stop it looking for the keyword again
                    unset($arrclass[$key]);
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
        $file_handle = fopen("tmp/Marlin/Marlin/Configuration_adv.h", "rb");
        while (!feof($file_handle) ) 
        {
            $line_of_text = fgets($file_handle);
            $string = preg_replace('~[\r\n]+~', '', $line_of_text);
            $string = preg_replace('~[\n]+~', '', $string);
            $string = preg_replace('~[\r]+~', '', $string);
              
            foreach ($arrclass as $key => $val) {
                $lineobj = checkLine($string, $val);
                $newline = $lineobj[0];
                $processed = $lineobj[1];

                if ($processed == true || $processed == "true") { // found a change, so remove stop it looking for the keywork again
                    unset($arrclass[$key]);
                }
            }
              
              
            
              // End of file pretty much. Check what params we have left and stuff them in the file
            if ($line_of_text == "#endif //__CONFIGURATION_ADV_H") {
                foreach ($arrclass as $key => $arg) {
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
        while (!feof($file_handle) ) {
            $line_of_text = fgets($file_handle);
            if (startsWith($line_of_text, "HARDWARE_MOTHERBOARD")) { // we found it
                $line_of_text = "HARDWARE_MOTHERBOARD ?= ". $varHardware ."\r\n";
            }
            else if (startsWith($line_of_text, "ARDUINO_INSTALL_DIR")) { // we found it
                $line_of_text = "ARDUINO_INSTALL_DIR  ?= ../../../arduino-0022\r\n";
            }
            else if (startsWith($line_of_text, "ARDUINO_VERSION")) { // we found it
                $line_of_text = "ARDUINO_VERSION      ?= 22\r\n";
            }
            else if (startsWith($line_of_text, "UPLOAD_RATE")) { // we found it
                $line_of_text = "UPLOAD_RATE        ?= ".$varBaudRate."\r\n";
            }
              
            $newconfig = $newconfig . $line_of_text."";
        }
        fclose($file_handle);
        
        file_put_contents("./tmp/".$dir."/Marlin/Marlin/Makefile", $newconfig);

        // build it
        shell_exec('cd ./tmp/'.$dir.'/Marlin/Marlin/; make  >  build_summary.txt 2>&1');
    
        // copy files from build and cleanup
        shell_exec('cp -f ./tmp/'.$dir.'/Marlin/Marlin/Makefile ./tmp/'.$dir.'/ > /dev/null 2>&1');
        shell_exec('cp ./tmp/'.$dir.'/Marlin/Marlin/Configuration.h ./tmp/'.$dir.'/ > /dev/null 2>&1');
        shell_exec('cp ./tmp/'.$dir.'/Marlin/Marlin/Configuration_adv.h ./tmp/'.$dir.'/ > /dev/null 2>&1');
        shell_exec('cp ./tmp/'.$dir.'/Marlin/Marlin/applet/Marlin.hex ./tmp/'.$dir.'/ > /dev/null 2>&1');
        shell_exec('cp ./tmp/'.$dir.'/Marlin/Marlin/build_summary.txt ./tmp/'.$dir.'/ > /dev/null 2>&1');
        shell_exec('rm -rf ./tmp/'.$dir.'/Marlin > /dev/null 2>&1');
        shell_exec('diff --ignore-all-space ./tmp/'.$dir.'/Configuration.h ./tmp/Marlin/Marlin/Configuration.h > ./tmp/'.$dir.'/config_diff.diff');
        shell_exec('diff --ignore-all-space ./tmp/'.$dir.'/Configuration_adv.h ./tmp/Marlin/Marlin/Configuration_adv.h > ./tmp/'.$dir.'/config_adv_diff.diff');
        shell_exec('cd tmp/'.$dir.' && zip marlin-package.zip Makefile Configuration.h Configuration_adv.h Marlin.hex build_summary.txt config_diff.diff config_adv_diff.diff > /dev/null 2>&1');
        
        // generate build summary
        $summaryarr = file("./tmp/".$dir."/build_summary.txt");
        $i=0;
        $fcnt = count($summaryarr);
        $newarr = Array();

        // show only last few lines
        foreach($summaryarr as $sum) {
            if ($i < $fcnt-15) {
            }
            else {
                array_push($newarr, $sum);
            }

            $i++;
        }
        $newsum = join($newarr,'');
        
        
    }
}
else {
    // redirect back to homepage
    header("Location: index.php");
    die();
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
<head>
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
    <link href="style.css" rel="stylesheet" type="text/css">
    
    <title>Ginge's Marlin Builder - Finished Build</title>
</head>

<body>
    <h1>Build Completed</h1>
    <div id="buildsummary">
        <h2>Build Summary</h2>
        <pre><?php echo($newsum) ?></pre>
    </div>
    <div id="downloadfiles">
        <h2>Download Files</h2>
        You should take a good close look at <strong>Configuration.h and Configuration_adv.h</strong> Learn the features. Check the parameters. Is it sane? Is it safe?<br/><br/>
        <a href="<?php echo('./tmp/'.$dir.'/build_summary.txt') ?>">Build Summary</a>
        <br/><a href="<?php echo('./tmp/'.$dir.'/Configuration.h') ?>">Configuration.h</a>
        <br/><a href="<?php echo('./tmp/'.$dir.'/Configuration_adv.h') ?>">Configuration_adv.h</a>
        <br/><a href="<?php echo('./tmp/'.$dir.'/Makefile') ?>">Makefile</a>
        <br/><a href="<?php echo('./tmp/'.$dir.'/Marlin.hex') ?>">HEX File</a>
        <br/><a href="<?php echo('./tmp/'.$dir.'/config_diff.diff') ?>">Diff of Configuration.h changes</a>
        <br/><a href="<?php echo('./tmp/'.$dir.'/config_adv_diff.diff') ?>">Diff of Configuration_adv.h changes</a>
        <br/><a href="<?php echo('./tmp/'.$dir.'/marlin-package.zip') ?>">All of it as a ZIP</a>
    </div>
    <br/><br/><a href="<?php echo($varAllSettings) ?>">A bookmarkable link to your settings</a>
    <br/><br /><strong>Suggest your template!</strong> Fill in the below details and they may be added to the template selection.
    <form action="build.php" method="post" class="feedbackForm">
        <input type="hidden" name="config" value="<?php echo($varAllSettings) ?>">
        <input type="hidden" name="zippath" value="<?php echo('./tmp/'.$dir.'/marlin-package.zip') ?>">
        <table>
            <tr>
                <td>Your Name (optional):</td>
                <td><input type="text" name="whofrom"></td>
            </tr>
            <tr>
                <td>Your Email (optional):</td>
                <td><input type="text" name="email"></td>
            </tr>
            <tr>
                <td>Machine Name:</td>
                <td><input type="text" name="machine"></td>
            </tr>
            <tr>
                <td>Description:</td>
                <td><input type="text" name="desc">-- example "Basic BlahBox3d with LCD"</td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" name="sendconf" value="Send Config for consideration"></td>
            </tr>
        </table>
    </form>
</body>