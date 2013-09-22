var marlinbuilder = function () {

    return {
        //main function to initiate the module
        init: function () {
//             $('.header').click(function(){
//                 $(this).nextUntil('tr.header').toggle(
//                     function() {  
//                         $(this).find('th').eq(0).html("Click row to expand");
//                     },
//                     function(){
//                         $(this).find('th').eq(0).html("Click row to");
//                     }
//                 );                
//             });
            // DISABLED until fixed //
            $("input[name$='formPIDDebug']").prop("disabled", true);

            // get URL params
            //grab the entire query string
            var query = document.location.search.replace('?', '');
            
            // Fill the form data
            $("form").deserialize(query, {checkboxesAsBools: true});
            
            function getAbsolutePath() {
                var loc = window.location;
                var pathName = loc.pathname.substring(0, loc.pathname.lastIndexOf('/') + 1);
                return loc.href.substring(0, loc.href.length - ((loc.pathname + loc.search + loc.hash).length - pathName.length));
            }
            
            function showValues() {
                $("#hidSettingsString").val("");
                var str = $("form").serialize({ checkboxesAsBools: true });
                //get URL
                $("#settingslink").empty();
                
                var URLbase = getAbsolutePath() + "?" +  str;
                var newURL = '<a href="' + URLbase + '">Bookmarkable link</a>';
                $("#settingslink").append( newURL );
                $("#hidSettingsString").val(URLbase);
            }
            $("input[type='checkbox'], input[type='radio']").on( "click", showValues );
            $("select").on( "change", showValues );
            $("input").on( "change", showValues );
            showValues();



            $("#chkUltiPanel").change(function() {
                if (this.checked) {
                    $("#chkFilamentChange").prop("disabled", false);
                    $("input[name$='formSDCardEn']").prop("checked",  true);
                }
                else {
                    $("#chkFilamentChange").prop("disabled", true);
                    $("#chkFilamentChange").prop("checked", false);
                }                    
            });
            
            
    
            var machineOptions = [
                { machine: "Ultimaker", optionID: 1, value: "Basic Ultimaker, Recommended settings"},
                { machine: "Ultimaker", optionID: 2, value: "Experimental Ultimaker, Experimental settings!"},
                { machine: "Ultimaker", optionID: 3, value: "Basic Ultimaker + heated bed (100k therm, relay driven)"},
                { machine: "Ultimaker", optionID: 4, value: "Basic Ultimaker + Ulticontroller"},
                { machine: "Ultimaker", optionID: 5, value: "Experimental Ultimaker + Ulticontroller"},
                { machine: "Ultimaker", optionID: 6, value: "Basic Ultimaker + heated bed:(100k, relay) + Ulticontroller"},
                { machine: "Ultimaker", optionID: 7, value: "Experimental Ultimaker + heated bed:(100k, relay) + Ulticontroller"},
                { machine: "Ultimaker", optionID: 8, value: "Dual Extrusion Ultimaker + heated bed:(100k, relay) + Ulticontroller"},
                { machine: "Mendel 90", optionID: 9, value: "Suggest your settings!"},
                { machine: "Mendel Max", optionID: 10, value: "Suggest your settings!"},
            ];
            
            // Add options based on machine type
            
            // on loadup work out our machine types from the machine options
            $("#machinetemplate").empty();
            $('#machinetemplate').append($('<option>', { value: -1, text: "Select your machine" }));
            var arr = [];
            $.each(machineOptions, function(key, value) {
                console.log("INIT");
                if (arr.indexOf(value.machine) === -1) {
                    arr.push(value.machine);
                }
            });
            // add them to the dropdown
            $.each(arr, function(key, value) {
                $('#machinetemplate')
                    .append($('<option>', { value: key, text: value }));
            });
            // disable the template
            $("#template").prop("disabled", true);
            
            
            $("#machinetemplate").change(function() {
                showValues();
                $('#template').empty();
                $('#template').append($('<option>', { value: -1, text: "Select a template" }));
                // add options
                $.each(machineOptions, function(key, value) {   
                    if (value.machine == $("#machinetemplate option:selected").text()) {
                        $('#template').append($('<option>', { value: value.optionID, text: value.value }));
                    }
                });
                
                $("#template").prop("disabled", false);
            });
            
            $("#template").change(function() {
                switch ($(this).val()) {
                    case '0':
                        $("select[name$='formMachine']").val('7');
                        $("select[name$='formSensor']").val('-1');
                        $("select[name$='formBedSensor']").val("0");
                        $("input[name$='formFastFanPwmEn']").prop("checked",  false);
                        //$("input[name$='formPIDDebug']").prop("checked",  false);
                        $("input[name$='formSDCardEn']").prop("checked",  false);
                        $("input[name$='formUltipanelEn']").prop("checked",  false);
                        break;
                    case '1':
                        $("select[name$='formMachine']").val('7');
                        $("select[name$='formSensor']").val('-1');
                        $("select[name$='formBedSensor']").val("0");
                        $("input[name$='formFastFanPwmEn']").prop("checked",  false);
                        //$("input[name$='formPIDDebug']").prop("checked",  false);
                        $("input[name$='formSDCardEn']").prop("checked",  false);
                        $("input[name$='formUltipanelEn']").prop("checked",  false);                        
                        break
                    case '2':
                        $("select[name$='formMachine']").val('7');
                        $("select[name$='formSensor']").val('-1');
                        $("select[name$='formBedSensor']").val("0");
                        $("input[name$='formFastFanPwmEn']").prop("checked",  true);
                        //$("input[name$='formPIDDebug']").prop("checked",  true);
                        $("input[name$='formSDCardEn']").prop("checked",  false);
                        $("input[name$='formUltipanelEn']").prop("checked",  false);
                        break;
                    case '3':
                        $("select[name$='formMachine']").val('7');
                        $("select[name$='formSensor']").val('-1');
                        $("select[name$='formBedSensor']").val("1");
                        $("input[name$='formFastFanPwmEn']").prop("checked",  true);
                        //$("input[name$='formPIDDebug']").prop("checked",  true);
                        $("input[name$='formSDCardEn']").prop("checked",  false);
                        $("input[name$='formUltipanelEn']").prop("checked",  false);
                        break;
                    case '4':
                        $("select[name$='formMachine']").val('7');
                        $("select[name$='formSensor']").val('-1');
                        $("select[name$='formBedSensor']").val("0");
                        $("input[name$='formFastFanPwmEn']").prop("checked",  true);
                        //$("input[name$='formPIDDebug']").prop("checked",  false);
                        $("input[name$='formSDCardEn']").prop("checked",  true);
                        $("input[name$='formUltipanelEn']").prop("checked",  true);
                        break;
                    case '5':
                        $("select[name$='formMachine']").val('7');
                        $("select[name$='formSensor']").val('-1');
                        $("select[name$='formBedSensor']").val("1");
                        $("input[name$='formFastFanPwmEn']").prop("checked",  true);
                        //$("input[name$='formPIDDebug']").prop("checked",  true);
                        $("input[name$='formSDCardEn']").prop("checked",  true);
                        $("input[name$='formUltipanelEn']").prop("checked",  true);
                        break;
                    case '6':
                        $("select[name$='formMachine']").val('7');
                        $("select[name$='formSensor']").val('-1');
                        $("select[name$='formBedSensor']").val("1");
                        $("input[name$='formFastFanPwmEn']").prop("checked",  true);
                        //$("input[name$='formPIDDebug']").prop("checked",  false);
                        $("input[name$='formSDCardEn']").prop("checked",  true);
                        $("input[name$='formUltipanelEn']").prop("checked",  true);
                        break;
                    case '7':
                        $("select[name$='formMachine']").val('7');
                        $("select[name$='formSensor']").val('-1');
                        $("select[name$='formBedSensor']").val("1");
                        $("input[name$='formFastFanPwmEn']").prop("checked",  true);
                        //$("input[name$='formPIDDebug']").prop("checked",  true);
                        $("input[name$='formSDCardEn']").prop("checked",  true);
                        $("input[name$='formUltipanelEn']").prop("checked",  true);
                        $("input[name$='formFilamentChangeEn']").prop("checked",  true);
                        break;
                    case '8':
                        $("select[name$='formMachine']").val('7');
                        $("select[name$='formSensor']").val('-1');
                        $("select[name$='formSensor1']").val('-1');
                        $("select[name$='formBedSensor']").val("1");
                        $("input[name$='formFastFanPwmEn']").prop("checked",  true);
                        //$("input[name$='formPIDDebug']").prop("checked",  true);
                        $("input[name$='formSDCardEn']").prop("checked",  true);
                        $("input[name$='formUltipanelEn']").prop("checked",  true);
                        $("input[name$='formFilamentChangeEn']").prop("checked",  true);
                        break;
                }
                $("#chkUltiPanel").change();                // manually trigger so it updates all deps
            });
        }
    };
}();




// utils:
// http://tdanemar.wordpress.com/2010/08/24/jquery-serialize-method-and-checkboxes/
// to get value of all checkboxes
(function ($) {
 
     $.fn.serialize = function (options) {
         return $.param(this.serializeArray(options));
     };
 
     $.fn.serializeArray = function (options) {
         var o = $.extend({
         checkboxesAsBools: false
     }, options || {});
 
     var rselectTextarea = /select|textarea/i;
     var rinput = /text|hidden|password|search/i;
 
     return this.map(function () {
         return this.elements ? $.makeArray(this.elements) : this;
     })
     .filter(function () {
         return this.name && !this.disabled &&
             (this.checked
             || (o.checkboxesAsBools && this.type === 'checkbox')
             || rselectTextarea.test(this.nodeName)
             || rinput.test(this.type));
         })
         .map(function (i, elem) {
             var val = $(this).val();
             return val == null ?
             null :
             $.isArray(val) ?
             $.map(val, function (val, i) {
                 return { name: elem.name, value: val };
             }) :
             {
                 name: elem.name,
                 value: (o.checkboxesAsBools && this.type === 'checkbox') ? //moar ternaries!
                        (this.checked ? 'true' : 'false') :
                        val
             };
         }).get();
     };
 
})(jQuery);

/**
* jQuery Deserialize plugin
*
* Deserializes a query string (taken for example from window.location.hash string) into the appropriate form elements.
*
* Usage
* $("form").deserialize(string);
*
* do not trigger change events on elements
* $("form").deserialize(string, {noEvents: true});
*
* expect checkboxes to be serialized as boolean (true/false) rather than standard (present/missing)
* $("form").deserialize(string, {checkboxesAsBools: true});
**/
(function($) {
    $.fn.deserialize = function(s, options) {
      function optionallyTrigger(element,event) {
        if (options.noEvents)
          return;
        element.trigger(event);
      }

      function changeChecked($input, newState) {
        var oldState = $input.is(":checked");
        $input.attr("checked", newState);
        if (oldState != newState)
          optionallyTrigger($input, 'change');
      }

      options = options || {};
      var data = {};
      var parts = s.split("&");

      for (var i = 0; i < parts.length; i++) {
        var pair = $.map(parts[i].replace(/\+/g, '%20').split("="), function(d) {
          return decodeURIComponent(d);
        });

        //collect data for checkbox handling
        data[pair[0]] = pair[1];

        var $input = $("[name='" + pair[0] + "']", this);
        var type = $input.attr('type');

        if (type == 'radio') {
          $input = $input.filter("[value='" + pair[1] + "']");
          changeChecked($input, true);
        } else if (type == 'checkbox') {
          // see below
        } else {
          var oldVal = $input.val();
          var newVal = pair[1];
          $input.val(newVal);
          if (oldVal != newVal)
            optionallyTrigger($input, 'change');
        }
      }

      $("input[type=checkbox]", this).each(function() {
        var $name = this["name"];
        var $input = $(this);
        if (options.checkboxesAsBools) {
          //checkboxes are serialized as non-standard true/false, so only change value if provided (as explicit
          // boolean) in the data. (so checkboxes behave like other fields - unspecified fields are unchanged)
          if (data[$name] == 'true')
            changeChecked($input, true);
          else if (data[$name] == 'false')
            changeChecked($input, false);
        }
        else {
          //standard serialization, so checkboxes are not serialized -> ANY missing value means unchecked
          // (no difference betwen "missing" and "false").
          changeChecked($input, ($input.attr("name") in data));
        }
      });
    };
})(jQuery);