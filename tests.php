<?php
//tests

function l($name, $value, $options = array()) {
	$c = new Option();
	$c->Name = $name;
	$c->Value = $value;
	$c->Options = $options;
	return $c;
}

function doTest($testnum, $str, $val, $newval, $expected) {
	$opts = array();
	if ($newval=="undefine") {
	    array_push($opts, "define");
	    $newval = false;
	}
	else if ($newval=="define") {
	    array_push($opts, "define");
	    $newval = true;
	}
	$newline = checkLine($str, l($val, $newval, $opts));
	if ($newline[0] != $expected) {
		testFailed("Toggling a value:: Before: ".$str." After: ".$newline[0] ."<br/>");
		return;
	}
	
	echo("TEST PASSED!!::  Test ".$testnum.": ". $newval." <br />");
}


function testFailed($why) {
    echo("TEST FAILED:: ". $why);
}


function doAllTests() {
	//int tests
	echo("<pre>");
	doTest(1,"#define X_ENABLE_ON 0", "X_ENABLE_ON", '1', "#define X_ENABLE_ON 1");
	doTest(2,"#define Y_ENABLE_ON 0", "Y_ENABLE_ON", '1', "#define Y_ENABLE_ON 1");
	doTest(3,"#define E_ENABLE_ON 0 // For all extruders", "E_ENABLE_ON", '1', "#define E_ENABLE_ON 1      // For all extruders");

	// string tests
	dotest(4,"#define INVERT_X_DIR true    // for Mendel set to false, for Orca set to true", "INVERT_X_DIR", "false", "#define INVERT_X_DIR false      // for Mendel set to false, for Orca set to true");
	dotest(5,"#define INVERT_Y_DIR false    // for Mendel set to true, for Orca set to false", "INVERT_Y_DIR", "true", "#define INVERT_Y_DIR true      // for Mendel set to true, for Orca set to false");

	// define tests
	dotest(6,"#define PIDTEMP", "PIDTEMP", 'undefine', "//#define PIDTEMP");
	dotest(7,"#define PIDTEMP // fsdfsfsdfsdfsfsdfsdfsdf", "PIDTEMP", 'undefine', "//#define PIDTEMP // fsdfsfsdfsdfsfsdfsdfsdf");
	dotest(8,"// #define PIDTEMP", "PIDTEMP", 'define', " #define PIDTEMP");
	dotest(9,"// #define PIDTEMP // fsdfsfsdfsdfsfsdfsdfsdf", "PIDTEMP", 'define', " #define PIDTEMP // fsdfsfsdfsdfsfsdfsdfsdf");
	dotest(10,"// #define PIDTEMP", "PIDTEMP", 'undefine', "// #define PIDTEMP");
	dotest(11,"// #define PIDTEMP // fsdfsfsdfsdfsfsdfsdfsdf", "PIDTEMP", 'undefine', "// #define PIDTEMP // fsdfsfsdfsdfsfsdfsdfsdf");


	//const tests
	doTest(12,"const bool X_ENDSTOPS_INVERTING = true; // set to true to invert the logic of the endstops.", "X_ENDSTOPS_INVERTING", "false", "const bool X_ENDSTOPS_INVERTING = false;      // set to true to invert the logic of the endstops.");
	doTest(13,"const bool X_ENDSTOPS_INVERTING = false; // set to true to invert the logic of the endstops.", "X_ENDSTOPS_INVERTING", "true", "const bool X_ENDSTOPS_INVERTING = true;      // set to true to invert the logic of the endstops.");
	doTest(13,"  #define PID_FUNCTIONAL_RANGE 10 // If the temperature difference between the target temperature and the actual temperature", "PID_FUNCTIONAL_RANGE", "1000", "#define PID_FUNCTIONAL_RANGE 1000      // If the temperature difference between the target temperature and the actual temperature");
	doTest(14, "  //#define PID_DEBUG // Sends debug data to the serial port.", "PID_DEBUG", "define",   "#define PID_DEBUG // Sends debug data to the serial port.");
	//big bad machine id parser test
	/*#ifdef PIDTEMP
	  //#define PID_DEBUG // Sends debug data to the serial port.
	  //#define PID_OPENLOOP 1 // Puts PID in open loop. M104/M140 sets the output power from 0 to PID_MAX
	  #define PID_FUNCTIONAL_RANGE 10 // If the temperature difference between the target temperature and the actual temperature
					  // is more then PID_FUNCTIONAL_RANGE then the PID will be shut off and the heater will be set to min/max.
	  #define PID_INTEGRAL_DRIVE_MAX 255  //limit for the integral term
	  #define K1 0.95 //smoothing factor within the PID
	  #define PID_dT ((16.0 * 8.0)/(F_CPU / 64.0 / 256.0)) //sampling period of the temperature routine

	// If you are using a preconfigured hotend then you can use one of the value sets by uncommenting it
	// Ultimaker
	    #define  DEFAULT_Kp 22.2
	    #define  DEFAULT_Ki 1.08
	    #define  DEFAULT_Kd 114

	// Makergear
	//    #define  DEFAULT_Kp 7.0
	//    #define  DEFAULT_Ki 0.1
	//    #define  DEFAULT_Kd 12

	// Mendel Parts V9 on 12V
	//    #define  DEFAULT_Kp 63.0
	//    #define  DEFAULT_Ki 2.25
	//    #define  DEFAULT_Kd 440
	#endif // PIDTEMP
	*/
	echo("</pre>");
}
?>