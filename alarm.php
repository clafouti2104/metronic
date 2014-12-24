<?php
$includeCSS = $includeJS = array();
//$includeJS[] = "/assets/jquery-ui/keyboard/jquery.keyboard.js";
//$includeJS[] = "/assets/jquery-ui/keyboard/jquery.keyboard.extension-typing.js";
//$includeJS[] = "/assets/jquery-ui/keyboard/jquery.keyboard.extension-autocomplete.js";
$includeCSS[] = "/assets/jquery-ui/jquery-ui.css";
include "modules/header.php";
include "modules/sidebar.php";

$GLOBALS["dbconnec"] = connectDB();
$ini = parse_ini_file("/var/www/metronic/tools/parameters.ini");

foreach($ini as $title => $value){
    if($title == "myfox_token" && $value != ""){
        $type="myfox";
        $token=$value;
        break;
    }
    if($title == "calaos_login" && $value != ""){
        $type="calaos";
    }
}

if($type=="myfox"){
    if($token == ""){
        $token=getToken();
    }
    $securityState = exec("curl https://api.myfox.me:443/v2/site/10562/security?access_token=".$token);
    $securityState = json_decode($securityState);
    if(isset($securityState->status) && $securityState->status == "KO" && $securityState->error == "invalid_token"){
        $token=getToken();
        $securityState = exec("curl https://api.myfox.me:443/v2/site/10562/security?access_token=".$token);
        $securityState = json_decode($securityState);
    }
    $status = $securityState->payload->statusLabel;
    switch(strtolower($status)){
        case "disarmed":
            $imgState="alarm_disarmed";
            $linkState="disarmed";
            $imgFirst="alarm_partial";
            $linkFirst="partial";
            $imgSecond="alarm_armed";
            $linkSecond="armed";
            break;
        case "armed":
            $imgState="alarm_armed";
            $linkState="armed";
            $imgFirst="alarm_partial";
            $linkFirst="partial";
            $imgSecond="alarm_disarmed";
            $linkSecond="disarmed";
            break;
        case "partial":
            $imgState="alarm_partial";
            $linkState="partial";
            $imgFirst="alarm_armed";
            $linkFirst="armed";
            $imgSecond="alarm_disarmed";
            $linkSecond="disarmed";
            break;
        default :
    }
} elseif($type=="calaos"){
    exec('cd /var/www/metronic/scripts/calaos;php getStateAlarm.php',$output);

    $stateAlarm=(is_array($output)) ? $output[0] : $output;
//print_r($stateAlarm);
    $stateAlarm="disarmed";

    switch(strtolower($stateAlarm)){
        case "disarmed":
            $imgState="alarm_disarmed";
            $linkState="disarmed";
            $imgFirst="alarm_partial";
            $linkFirst="partial";
            $imgSecond="alarm_armed";
            $linkSecond="armed";
            break;
        case "armed":
            $imgState="alarm_armed";
            $linkState="armed";
            $imgFirst="alarm_partial";
            $linkFirst="partial";
            $imgSecond="alarm_disarmed";
            $linkSecond="disarmed";
            break;
        case "partial":
            $imgState="alarm_partial";
            $linkState="partial";
            $imgFirst="alarm_armed";
            $linkFirst="armed";
            $imgSecond="alarm_disarmed";
            $linkSecond="disarmed";
            break;
        default :
    }
}

?>
<!-- BEGIN PAGE -->
<div class="page-content-wrapper">
<div class="page-content">
    <input type="hidden" id="action_tmp" value="" />
    <input type="hidden" id="element_tmp" value="" />
    <div class="container-fluid">
    <div class="row">
            <div class="col-md-12">
                <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                <h3 class="page-title">
                    Alarme
                    <small>Mise en/hors service</small>
                </h3>
                <!-- END PAGE TITLE & BREADCRUMB-->
            </div>
    </div>
    <div class="row">
        <div class="col-md-12 ">
            <img class="alarm-status" action="<?php echo $linkState; ?>" src="assets/img/<?php echo $imgState; ?>.png" width="200" />
        </div>
        <div class="row">
            <div class="col-md-6 ">
                <img class="alarm-action alarm-first" action="<?php echo $linkFirst; ?>" element="first" src="assets/img/<?php echo $imgFirst; ?>.png" width="110" style="margin-right:20px;" />
                <input type="text" id="hidden" class="keypad" style="display:none;" action="<?php echo $linkFirst; ?>" />
                <img class="alarm-action alarm-second" action="<?php echo $linkSecond; ?>" element="second" src="assets/img/<?php echo $imgSecond; ?>.png" width="110" />
                <input type="text" id="hidden2" class="keypad" style="display:none;" action="<?php echo $linkFirst; ?>" />
            </div>
        </div>
    </div>
    </div>
    <div id="homeKeypad" style="position: absolute; top: 50px;left: 50px; width: 300px; display: block;">
        <div class="keypadElem ui-dialog-titlebar-close" style="width:225px;height:20px;padding:0;text-align: right;" value="close">
            x
        </div>
        <div class="keypadElem ui-keyboard-button" value="7" style="clear:left;">7</div>
        <div class="keypadElem ui-keyboard-button" value="8">8</div>
        <div class="keypadElem" value="9">9</div>
        <div class="keypadElem" style="clear:left;" value="4">4</div>
        <div class="keypadElem" value="5">5</div>
        <div class="keypadElem" value="6">6</div>
        <div class="keypadElem" style="clear:left;" value="1">1</div>
        <div class="keypadElem" value="2">2</div>
        <div class="keypadElem" value="3">3</div>
        <div class="keypadElem" value="clear" style="clear:left;">⌫</div>
        <div class="keypadElem" value="0">0</div>
        <div class="keypadElem" value="accept" style="background:#f58400;">✔</div>
    </div>
    <input type="hidden" id="actionType" value="" />
    <input type="hidden" id="codePin" value="" />
    <div id="keypad"></div>
</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $('#homeKeypad').hide();
    $('.keypadElem').click(function() {
        if($(this).attr('value') == 'close'){
            $('#codePin').val('');
            $('#homeKeypad').hide();
            return true;
        }
        if($(this).attr('value') == 'clear'){
            $('#codePin').val('');
            return true;
        }
        if($(this).attr('value') == 'accept'){
            var value=$('#codePin').val();
            $.ajax({
                url: "ajax/check_keypad_alarm.php    ",
                type: "POST",
                data: {
                    action: $('#actionType').val(),
                    pin: value
                },
                error: function(data){
                    toastr.error("Une erreur est survenue");
                },
                complete: function(data){
                    if(data.responseText == "codePin"){
                        toastr.error("Mauvais Code");
                        value = "";
                        return false;
                    }
                    if(data == "codePin"){
                        toastr.error("Mauvais Code");
                        $('#codePin').val('');
                        value = "";
                        return false;
                    }
                    if(data.responseText == "error"){
                        toastr.error("Une erreur est survenue");
                    } else {
                        toastr.success("Action exécutée");
                        var exImgState=$('.alarm-status').attr('src');
                        var exThis=$('.alarm-'+$('#element_tmp').val()).attr('src');
                        $('alarm-'+$('#element_tmp').val()).attr('src',exImgState);
                        $('.alarm-status').attr('src',exThis);
                        $('#codePin').val('');
                        $('#homeKeypad').hide();
                        //$('.ui-keyboard:not(.ui-keyboard-always-open)').hide();
                    }
                }
            });
            return true;
        }
        $('#codePin').val($('#codePin').val()+$(this).attr('value'));
    });
    

    /*$('.ui-keyboard-button').click(function() {
        $('.ui-keyboard-preview-wrapper input').focusout();
        $('.ui-keyboard-preview').focusout();
        $("input").blur();
        $('#hidden').blur();
    });*/
    $('.alarm-first').click(function() {
        $('#homeKeypad').show();
        $('#actionType').val($(this).attr('action'));
        /*$("input").blur();
        $('#action_tmp').val($(this).attr('action'));
        $('#element_tmp').val($(this).attr('element'));
        //$('#hidden').trigger('focus.keyboard');
        $('#hidden').getkeyboard().reveal();
        $('#hidden').blur();
        //$('.ui-keyboard-preview-wrapper').hide();
        $('.ui-keyboard-preview-wrapper input').focusout();
        $('.ui-keyboard-preview').focusout();
        $("input").blur();
        $('.ui-widget-content').css({'margin-top':'150px','margin-left':'50px'});
        $('.ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default').css({'width':'73px','height':'60px'});*/
    });

    $('.alarm-second').click(function() {
        $('#homeKeypad').show();
        $('#actionType').val($(this).attr('action'));
        /*$("input").blur();
        $('#action_tmp').val($(this).attr('action'));
        $('#element_tmp').val($(this).attr('element'));
        //$('#hidden2').trigger('focus.keyboard');
        $('#hidden2').getkeyboard().reveal();
        $('#hidden2').blur();
        //$('.ui-keyboard-preview-wrapper').hide();
        $('.ui-keyboard-preview-wrapper input').focusout();
        $('.ui-keyboard-preview').focusout();
        $("input").blur();
        $('.ui-widget-content').css({'margin-top':'150px','margin-left':'50px'});
        $('.ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default').css({'width':'73px','height':'60px'});*/
    });

    
    /*$('.alarm-action').click(function() {
        $('#action_tmp').val($(this).attr('action'));
        $('#element_tmp').val($(this).attr('element'));
        $('.keypad').getkeyboard().reveal();
        $('.ui-keyboard').css('top','80%');
        //on centre la position du keyboard
        var keyboard = document.getElementsByClassName('ui-keyboard ui-widget-content ui-widget ui-corner-all ui-helper-clearfix ui-keyboard-has-focus').item();
                keyboard.style.top='10%';
                keyboard.style.left='50%';
                keyboard.style.marginLeft='-131px';
                keyboard.style.marginTop='-30%';
    });*/
    /*$('#hidden').keyboard({
        //openOn : null,
        layout: 'custom',
        customLayout: {
           'default' : [
            '7 8 9',
            '4 5 6',
            '1 2 3',
            '{b} 0 {a}'
        ]
        },
        maxLength : 6,
        restrictInput : true, // Prevent keys not in the displayed keyboard from being typed in
        preventPaste : true,  // prevent ctrl-v and right click
        position: {
            of : $('.alarm-first'),
            my : 'center top',
            at : 'center top'
        },
        validate    : function(keyboard, value, isClosing) {
            $.ajax({
                url: "ajax/check_keypad_alarm.php    ",
                type: "POST",
                data: {
                    action: $('#action_tmp').val(),
                    pin: value
                },
                error: function(data){
                    toastr.error("Une erreur est survenue");
                },
                complete: function(data){
                    if(data.responseText == "codePin"){
                        toastr.error("Mauvais Code");
                        value = "";
                        return false;
                    }
                    if(data == "codePin"){
                        toastr.error("Mauvais Code");
                        value = "";
                        return false;
                    }
                    if(data.responseText == "error"){
                        toastr.error("Une erreur est survenue");
                    } else {
                        toastr.success("Action exécutée");
                        var exImgState=$('.alarm-status').attr('src');
                        var exThis=$('.alarm-'+$('#element_tmp').val()).attr('src');
                        $('alarm-'+$('#element_tmp').val()).attr('src',exImgState);
                        $('.alarm-status').attr('src',exThis);
                        $('.ui-keyboard:not(.ui-keyboard-always-open)').hide();
                    }
                }
            });
            value = "";
        }
    })
    .addTyping();*/

    /*$('#hidden2').keyboard({
        //openOn : null,
        layout: 'custom',
        customLayout: {
           'default' : [
            '7 8 9',
            '4 5 6',
            '1 2 3',
            '{b} 0 {a}'
        ]
        },
        maxLength : 6,
        restrictInput : true, // Prevent keys not in the displayed keyboard from being typed in
        preventPaste : true,  // prevent ctrl-v and right click
        position: {
            of : $('.alarm-first'),
            my : 'center top',
            at : 'center top'
        },
        validate    : function(keyboard, value, isClosing) {
            $.ajax({
                url: "ajax/check_keypad_alarm.php    ",
                type: "POST",
                data: {
                    action: $('#action_tmp').val(),
                    pin: value
                },
                error: function(data){
                    toastr.error("Une erreur est survenue");
                },
                complete: function(data){
                    if(data.responseText == "codePin"){
                        toastr.error("Mauvais Code");
                        value = "";
                        return false;
                    }
                    if(data == "codePin"){
                        toastr.error("Mauvais Code");
                        value = "";
                        return false;
                    }
                    if(data.responseText == "error"){
                        toastr.error("Une erreur est survenue");
                    } else {
                        toastr.success("Action exécutée");
                        var exImgState=$('.alarm-status').attr('src');
                        var exThis=$('.alarm-'+$('#element_tmp').val()).attr('src');
                        $('alarm-'+$('#element_tmp').val()).attr('src',exImgState);
                        $('.alarm-status').attr('src',exThis);
                        $('.ui-keyboard:not(.ui-keyboard-always-open)').hide();
                    }
                }
            });
            value = "";
        }
    })
    .addTyping();*/
});
</script>
<?php
include "modules/footer.php";
?>