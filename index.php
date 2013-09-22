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

$varBaudRate = "250000";

$varMaxFeedX = "350";
$varMaxFeedY = "350";
$varMaxFeedZ = "10";
$varMaxFeedE = "25";

$varMaxAccelX = "9000";
$varMaxAccelY = "9000";
$varMaxAccelZ = "200";
$varMaxAccelE = "10000";

$varHomeRateX = "50";
$varHomeRateY = "50";
$varHomeRateZ = "4";

$varNotUseX = false;
$varNotUseY = false;
$varNotUseZ = false;
$varNotUseE = false;

$varOnlyHoming = False;

$varSoftwareEndstopsEn = true;
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

$varStepsPerUnitX = "78.7402";
$varStepsPerUnitY = "78.7402";
$varStepsPerUnitZ = "533.3333333";
$varStepsPerUnitE = "865.888";

$varSDCardEn = false;
$varUltipanelEn = false;
$varUltipanelClickEn = false;
$varLCDEn = False;


$varHardware = 7;
$varExtruderSensor = 7;
$varExtruderSensor1 = 0;
$varBedSensor = 7;

$varFastFanPwmEn = true;

$varPIDDebug = false;

$varFilamentChangeEn = false;

$varAllSettings = "";

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


if (isset($_GET['dotests'])) {
    include "tests.php";
    doAllTests();
    return;
}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
<head>
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

    <script src="tooltip.js"></script>
    <script src="main.js"></script>  
    <link href="style.css" rel="stylesheet" type="text/css">
    <script>
        jQuery(document).ready(function(){
            marlinbuilder.init();
        });   
    </script>
    
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
    <table class="header">
        <tr>
            <td>
                <h1>Ginge's Marlin Builder</h1>
                <br />
                <i>Inspired by Daid's builder</i>
            </td>
            <td>
                <a href="https://github.com/ginge/marlin-builder">Source for this app at GitHub</a>
                <br />
                <a href="https://github.com/ErikZalm/Marlin">Current Marlin version from ErikZalm GitHub</a>
                <br />
                <strong>Marlin date:</strong>
                <?php $output = shell_exec('git --work-tree="Marlin" --git-dir=tmp/Marlin/.git  log -1 --format="%cD"'); echo($output); ?>
                <br />
                <strong>Current revision:</strong>
                <br />
                <?php 
                    $output = shell_exec('git --work-tree="Marlin" --git-dir=tmp/Marlin/.git  log -1 --format="%H"');
                    echo('<a href="https://github.com/ErikZalm/Marlin/tree/' . $output . '" >' . $output . '</a>');
                ?>

            </td>
        </tr>
        <tr>
            <td>
                <strong>Compiles Marlin firmware for 3d printers</strong>
            </td>
            <td>barry.carter@gmail.com</td>
    </table>
    
    <br/>
    <br/>
    <div class="templateselect">
        Templates:
        <select id="machinetemplate">
            <option value="-1">No Template Selected</option>
            <option value="1">Ultimaker</option>
            <option value="2">Mendel 90</option>
        <select>
        Choose options:
        <select id="template">
            <option value="-1">No Template Selected</option>

        <select>
    </div>
    <form action="build.php" method="post" class="mainform">
        <table id="mytable">
            <tr  class="header">
                <th></th>
                <th>Software Basic Configuration</th>
                <th></th>
            </tr>
            <tr>
                <td>Baud Rate:</td>
                <td><input type="text" name="formBaudRate" value="<?php echo($varBaudRate);?>" /></td>
                <td id="rowBaudRate">?</td>
            </tr>
            <tr>
                <td>Max Feed Rate:</td>
                <td>X:<input type="text" name="formMaxFeedX" value="<?php echo($varMaxFeedX);?>" />
                    Y:<input type="text" name="formMaxFeedY" value="<?php echo($varMaxFeedY);?>" />
                    Z:<input type="text" name="formMaxFeedZ" value="<?php echo($varMaxFeedZ);?>" />
                    E:<input type="text" name="formMaxFeedE" value="<?php echo($varMaxFeedE);?>" />
                </td>
                <td id="rowMaxFeed">?</td>
            </tr>
            <tr>
                <td>Max Acceleration Rate:</td>
                <td>X:<input type="text" name="formMaxAccelX" value="<?php echo($varMaxAccelX);?>" />
                    Y:<input type="text" name="formMaxAccelY" value="<?php echo($varMaxAccelY);?>" />
                    Z:<input type="text" name="formMaxAccelZ" value="<?php echo($varMaxAccelZ);?>" />
                    E:<input type="text" name="formMaxAccelE" value="<?php echo($varMaxAccelE);?>" />
                </td> 
                <td id="rowMaxAccel">?</td>
            </tr>
            <tr>
                <td>Homing Feed Rate:</td>
                <td>X:<input type="text" name="formHomeRateX" value="<?php echo($varHomeRateX);?>" />
                    Y:<input type="text" name="formHomeRateY" value="<?php echo($varHomeRateY);?>" />
                    Z:<input type="text" name="formHomeRateZ" value="<?php echo($varHomeRateZ);?>" />
                </td> 
                <td id="rowHomeRate">?</td>
            </tr>
            <tr>
                <td>Disable axis when not in use:</td>
                <td>X:<input type="checkbox" name="formNotUseX" <?php echo(chktag($varNotUseX));?> value="<?php echo($varNotUseX);?>" />
                    Y:<input type="checkbox" name="formNotUseY" <?php echo(chktag($varNotUseY));?> value="<?php echo($varNotUseY);?>"/>
                    Z:<input type="checkbox" name="formNotUseZ" <?php echo(chktag($varNotUseZ));?> value="<?php echo($varNotUseZ);?>"/>
                    E:<input type="checkbox" name="formNotUseE" <?php echo(chktag($varNotUseE));?> value="<?php echo($varNotUseE);?>"/>
                </td>
                <td id="rowNotUse">?</td>
            </tr>
            <tr>
                <td>Endstop only for homing:</td>
                <td><input type="checkbox" name="formOnlyHoming" value="<?php echo($varOnlyHoming);?>" />
                </td>
                <td id="rowOnlyHoming">?</td>
            </tr>
            <tr>
                <td>Software Endstops:</td>
                <td>Enable: <input type="checkbox" name="formSoftwareEndstopsEn" <?php echo(chktag($varSoftwareEndstopsEn));?> value="<?php echo($varSoftwareEndstopsEn);?>"/>
                    max X:<input type="text" name="formSoftwareEndstopsX" <?php echo(chktag($varSoftwareEndstopsX));?> value="<?php echo($varSoftwareEndstopsX);?>"/>
                    max Y:<input type="text" name="formSoftwareEndstopsY" <?php echo(chktag($varSoftwareEndstopsY));?> value="<?php echo($varSoftwareEndstopsY);?>"/>
                    max Z:<input type="text" name="formSoftwareEndstopsZ" <?php echo(chktag($varSoftwareEndstopsZ));?> value="<?php echo($varSoftwareEndstopsZ);?>"/>

                </td>
                <td id="rowSoftwareEndstops">?</td>
            </tr>
            <tr class="header">
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
                <td id="rowPIDEn">?</td>
            </tr>
            <tr>
                <td>Add extrusion speed to PID:</td>
                <td>Enable: <input type="checkbox" name="formPIDSpeedEn" <?php echo(chktag($varPIDSpeedEn));?> value="<?php echo($varPIDSpeedEn);?>" />
                    <input type="text" name="formPIDKc" value="<?php echo($varPIDKc);?>" />                             
                </td> 
                <td id="rowPIDSpeed">?</td>
            </tr>
            <tr>
                <td>Minimum extrusion temperature:</td>
                <td><input type="text" name="formMinExtTemp" value="<?php echo($varMinExtTemp);?>" /></td>
                <td id="rowMinExtTemp">?</td>
            </tr>
            <tr>
                <td>Maximum extrusion temperature:</td>
                <td><input type="text" name="formMaxExtTemp" value="<?php echo($varMaxExtTemp);?>" /></td>
                <td id="rowMaxExtTemp">?</td>
            </tr>
            <tr>
                <td>M109 Params:</td>
                <td>Hysteresis:<input type="text" name="formM109Hyster" value="<?php echo($varM109Hyster);?>" />
                    Wait Time:<input type="text" name="formM109Wait" value="<?php echo($varM109Wait);?>" />
                <td id="rowM109Hyter">?</td>
            </tr>
            <tr>
                <td>AD595 Calibration:</td>
                <td>Gain:<input type="text" name="formAD595Gain" value="<?php echo($varAD595Gain);?>" />
                    Offset:<input type="text" name="formAD595Offset" value="<?php echo($varAD595Offset);?>" /></td>
                <td id="AD595Gain">?</td>
            </tr>
            <tr class="header">
                <th></th>
                <th>Software Experimental</th>
                <th></th>
            </tr>
            <tr>
                <td>Enable auto temp:</td>
                <td><input type="checkbox" name="formAutoTempEn" <?php echo(chktag($varAutoTempEn));?> value="<?php echo($varAutoTempEn);?>"/>
                </td>
                <td id="rowAutoTempEn">?</td>
            </tr>
            <tr>
                <td>Late Z enable:</td>
                <td><input type="checkbox" name="formLateZEn" <?php echo(chktag($varLateZEn));?> value="1"/>
                </td>
                <td id="rowLateZ">?</td>
            </tr>
            <tr>
                <td>Enable watchdog:</td>
                <td><input type="checkbox" name="formWatchdogEn" <?php echo(chktag($varWatchdogEn));?> value="1"/>
                </td>
                <td id="rowWatchDog">?</td>
            </tr>
            <tr>
                <td>Extruder runout protection:</td>
                <td><input type="checkbox" name="formExtruderRunoutEn" <?php echo(chktag($varExtruderRunoutEn));?> value="1"/>
                </td>
                <td id="rowExtruderRunout">?</td>
            </tr>
            <tr>
                <td>Enable extruder advance:</td>
                <td><input type="checkbox" name="formExtruderAdvanceEn" <?php echo(chktag($varExtruderAdvanceEn));?> value="1"/>
                </td>
                <td id="rowExtruderAdvance">?</td>
            </tr>
            <tr class="header">
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
                <td id="rowhardware">?</td>
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
                <td id="rowSensor">?</td>
            </tr>
            <tr>
                <td>Second Extruder temperature sensor:</td>
                <td>
                    <select name="formSensor1">
                        <option value="0">No Sensor</option>
                        <option value="-1">thermocouple with AD595</option>
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
                <td id="rowSensor">?</td>
            </tr>
            <tr>
                <td>Heated bed temperature sensor:</td>
                <td>
                    <select name="formBedSensor">
                        <option value="0">NO heated bed</option>
                        <option value="1">100k thermistor - Usually this one!(4.7k pullup)</option>
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
                <td  id="rowBedSensor">?</td>
            </tr>
            <tr>
                <td>Enable endstop pullup resistors:</td>
                <td><input type="checkbox" name="formEndstopPullupEn" <?php echo(chktag($varEndstopPullupEn));?> value="1"/>
                </td>
                <td id="rowEndstopPullup">?</td>
            </tr>
            <tr>
                <td>Endstops are inverted:</td>
                <td>X:<input type="checkbox" name="formEndstopInvertedXEn" <?php echo(chktag($varEndstopInvertedXEn));?> value="1"/>
                    Y:<input type="checkbox" name="formEndstopInvertedYEn" <?php echo(chktag($varEndstopInvertedYEn));?> value="1"/>
                    Z:<input type="checkbox" name="formEndstopInvertedZEn" <?php echo(chktag($varEndstopInvertedZEn));?> value="1"/>
                </td>
                <td id="rowEndstopInverted">?</td>
            </tr>
            <tr>
                <td>Enable pins are active low:</td>
                <td><input type="checkbox" name="formEnPinsActiveLowEn" <?php echo(chktag($varEnPinsActiveLowEn));?> value="1" />
                </td>
                <td id="rowEnPinsActiveLow">?</td>
            </tr>
            <tr> 
                <td>Invert axis:</td>
                <td>X:<input type="checkbox" name="formInvAxisXEn" <?php echo(chktag($varInvAxisXEn));?> value="1"/>
                    Y:<input type="checkbox" name="formInvAxisYEn" <?php echo(chktag($varInvAxisYEn));?> value="1"/>
                    Z:<input type="checkbox" name="formInvAxisZEn" <?php echo(chktag($varInvAxisZEn));?> value="1"/>
                    E:<input type="checkbox" name="formInvAxisEEn" <?php echo(chktag($varInvAxisEEn));?> value="1"/>
                </td> 
                <td id="rowInvAxis">?</td>
            </tr>
            <tr>
                <td>Steps Per Unit:</td>
                <td>X:<input type="text" name="formStepsPerUnitX" value="<?php echo($varStepsPerUnitX);?>" />
                    Y:<input type="text" name="formStepsPerUnitY" value="<?php echo($varStepsPerUnitY);?>" />
                    Z:<input type="text" name="formStepsPerUnitZ" value="<?php echo($varStepsPerUnitZ);?>" />
                    E:<input type="text" name="formStepsPerUnitE" value="<?php echo($varStepsPerUnitE);?>" />
                </td> 
                <td id="rowStepsPerUnit">?</td>
            </tr>
            <tr class="header">
                <th></th>
                <th>Hardware Experimental</th>
                <th></th>
            </tr>
            <tr>
                <td>Increase PWM frequency:</td>
                <td><input type="checkbox" name="formFastFanPwmEn" <?php echo(chktag($varFastFanPwmEn));?> value="1"/>
                </td> 
                <td id="rowFastFanPwm">?</td>
            </tr>
            <tr>
                <td>Enable PID debug output:</td>
                <td><input type="checkbox" name="formPIDDebug" <?php echo(chktag($varPIDDebug));?> value="1"/>
                </td> 
                <td id="rowPIDDebug">?</td>
            </tr>
            <tr class="header">
                <th></th>
                <th>Hardware Addons</th>
                <th></th>
            </tr>
            <tr>
                <td>Enable SD Card:</td>
                <td><input type="checkbox" name="formSDCardEn" <?php echo(chktag($varSDCardEn));?> value="1"/>
                </td>
                <td id="rowSDCard">?</td>
            </tr>
            <tr>
                <td>Enable UltiPanel:</td>
                <td><input type="checkbox" name="formUltipanelEn" <?php echo(chktag($varUltipanelEn));?> value="1" id="chkUltiPanel"/>
                    Enable "Filament Change" menu<input type="checkbox" name="formFilamentChangeEn" <?php echo(chktag($varFilamentChangeEn));?> value="1" id="chkFilamentChange"/>
                
                </td> 
                <td  id="rowUltipanel">?</td>
            </tr>
            <tr>
                <td>Enable generic 16x2 LCD:</td>
                <td><input type="checkbox" name="formLCDEn" <?php echo(chktag($varLCDEn));?> value="1"/>
                </td>
                <td id="rowLCD">?</td>
            </tr>
            <tr>
                <th>Bookmark a link to these settings
                    <div id="settingslink"></div>
                    <input type="hidden" name="formSettingsString" id="hidSettingsString">
                </th>
                <th><input type="submit" name="formSubmit" value="Build It" class="button big"/></th> 
                <th></th>
            </tr>
        </table>
        
    </form>
</body>
</html>
