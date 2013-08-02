

$(document).tooltip({
    items:'tr td',
    tooltipClass:'preview-tip',
    position: { my: "left+50 top", at: "right center" },
    content:function(callback) {
        var attr = $(this).attr("id");
        if (typeof attr !== 'undefined' && attr !== false) {
	    tipnkey = tooltipArray[$(this).attr("id")];
	  
	  callback(tipnkey); //**call the callback function to  
	}
	else
	  callback("");
    },
});


function getToolTip(tipKey) {
    
}

var tooltipArray = {
"rowBaudRate"          : 'Baudrate at which the machine talks. Higher baudrates will have less chance for buffer underruns, but won\'t work with all computers/software.<br>Good values are: 250000, 115200, 38400. Needs to match your ReplicatorG or PrintRun settings.',
"rowMaxFeed"           : 'Maximum feedrates in mm/sec',
"rowMaxAccel"          : 'Maximum acceleration in mm/sec<sup>2</sup>',
"rowHomeRate"          : 'Homing feedrates in mm/sec',
"rowNotUse"            : 'Enabling this option causes the step motor to be disabled when not being moving. Normally the step motors are kept in position, which acts as a brake. If your drivers overheat this might help.',
"rowOnlyHoming"        : 'The endstops will only be used for homing, not during printing. Useful if motors cause interference on the endstops.',
"rowSoftwareEndstops"  : 'Enable software endstops, this will make your printer not move below 0,0,0 and above the set maximum.<br>Note: This setting is known to cause some greef to users, as Skeinforge uses 0,0,0 as center by default. You can disable this option, or use the Skeinforge Multiply plugin to set the center to 100,100',
"rowPIDEn"             : 'Use the PID controller to manage the temperature.<br>On proper configuration this will give a stable temperature. But on wrong configuration this will make the temperature swing larger.<br><br>PID configuration values.<br>22.2/1.08/114 are good for the Ultimaker<br>7.0/0.78/1.5 are good for Makergear<br>63/2.25/440 are good for Mendel Parts V9 on 12V',
"rowPIDSpeed"          : 'This adds an experimental additional term to the heatingpower, proportional to the extrusion speed.<br>if Kc is choosen well, the additional required power due to increased melting should be compensated.',
"rowMinExtTemp"        : 'this prevents dangerous Extruder moves, i.e. if the temperature is under the limit it will not extrude anything.',
"rowMaxExtTemp"        : 'When temperature exceeds max temp, your heater will be switched off.<br>This feature exists to protect your hotend from overheating accidentally, but *NOT* from thermistor short/failure!<br>In most cases you DO NOT WANT to change this value. Overheating could damage your machine, use with care!',
"rowM109Hyter"         : 'These controll the M109 actions, the Hysteresis is the allowed difference between the set temperature and the actuall temperature that is allowed before the M109 continues.<br>The wait time is the time waited after the M109 command before it continues. This time is a fixed wait time to make sure the printer head is heated all the way trough.',
"AD595Gain"            : 'These settings can be used to calibrate the temperature reading from the AD595.<br>In most cases you don\'t need to do this. But sometimes measurements are way off.',
"rowAutoTempEn"        : 'automatic temperature: The hot end target temperature is calculated by all the buffered lines of gcode.<br>You enter the autotemp mode by a M109 S[mintemp] T[maxtemp] F[factor]<br>the target temperature is set to mintemp+factor*se[steps/sec] and limited by mintemp and maxtemp',
"rowLateZ"             : 'Enable the Z step driver as late as possible, useful if your Z step driver overheats.',
"rowWatchDog"          : 'The watchdog will guard the firmware, if the firmware hangs it will reset the firmware, causing the heater/steppers to be disabled. Note, that due to the bootloader used by arduinos this will hang the controller forever.',
"rowExtruderRunout"    : 'If the machine is idle, and the temperature over MINTEMP, every couple of SECONDS some filament is extruded',
"rowExtruderAdvance"   : 'Does some fancy pressure calculated extrusion amount, which never worked as far as I know.',
"rowhardware"          : 'Choose your hardware board',
"rowSensor"            : 'The Ultimaker uses a Thermocouple with AD595',
"rowBedSensor"         : 'On the Ultimaker the heated bed is connected to the 3rd heater output.',
"rowEndstopPullup"     : 'Disable this to disable the endstop pullup resistors. The pullups are needed if you directly connect a mechanical endswitch between the signal and ground pins. This is done in the Ultimaker.',
"rowEndstopInverted"   : 'Set to invert the logic of the endstops.<br>Ultimaker requires this.<br>For optos H21LOB set to true, for Mendel-Parts newer optos TCST2103 set to false.',
"rowEnPinsActiveLow"   : 'If the enable pins are active low (0V = active), default for pololu drivers.',
"rowInvAxis"           : 'For Mendel: x/y/z = n/y/n<br>For Orca/Ultimaker: x/y/z = y,n,y<br>For direct drive extruder: e* = y<br>For geared extruder: e* = n',
"rowStepsPerUnit"      : 'Amount of steps per mm.<br>Ultimaker: 78.7402 ,78.7402, 533.333333, 865.8888<br>Sells Mendel + v9 Extruder: 40, 40, 3333.92, 360<br>SEA Prusa + Wade Extruder: 80.3232, 80.8900, 2284.7651, 757.2218<br>If you are using NetFabb the E value should be 14!',
"rowUltipanel"         : 'Enables the offical UltiController hardware. This will also enable SD card support.',
"rowSDCard"            : 'Enable the SD card feature in the firmware. Allows you to run gcode from an SD card.',
"rowLCD"               : 'General 16x2 LCD panel support',
"rowFastFanPwm"        : 'Increases the PWM freqency of the cooling FAN, this removes the noise when running the fan on lower levels, but increases heat in the controlling circuits.',
"rowFixPIDRange"       : 'Fix PID heatup speed bug. This is a workaround until we sync with https://github.com/ErikZalm/Marlin/commit/a504c883468829d487f63a048996fe0db037daf2.',
"rowPIDDebug"          : "Enable PID Debugging output. This is mainly for Cura's new graphs",
};