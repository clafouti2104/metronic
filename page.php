<?php
$includeJS=$includeCSS=array();
$includeJS[] = "/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js";   
$includeJS[] = "/assets/global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js";   
$includeJS[] = "/assets/js/raphael.2.1.0.min.js";   
$includeJS[] = "/assets/js/justgage.1.0.1.min.js";   
//$includeJS[] = "/assets/admin/pages/scripts/components-jqueryui-sliders.js";   

$includeJS[] = "/assets/js/wurfl.js";   
$includeCSS[] = "/assets/svg/fontcustom.css"; 
$includeCSS[] = "/assets/meteo/css/weather-icons.css"; 
$includeCSS[] = "/assets/global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.css"; 
$includeCSS[] = "/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css"; 
include "modules/header.php";
include "modules/sidebar.php";

include_once "models/PageItem.php";
include_once "models/Scenario.php";
include_once "models/Device.php";
include_once "models/Tuile.php";
include_once "models/Chart.php";
include_once "models/ChartDevice.php";
include_once "models/History.php";
include_once "models/Liste.php";
include_once "models/ListeMessage.php";
include_once "models/MessageDevice.php";

$GLOBALS["dbconnec"] = connectDB();
if(!isset($_GET["pageId"])){
    die('Aucune page définie');
}

$page= Page::getPage($_GET["pageId"]);
$items = PageItem::getPageItemsForPage($_GET["pageId"]);

?>
<!-- BEGIN PAGE -->
<div class="page-content-wrapper">
<div class="page-content">
    <input type="hidden" id="pageId" value="<?php echo $page->id; ?>" />
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN PAGE TITLE & BREADCRUMB-->			
                <h3 class="page-title">
                    <?php echo $page->name; ?>&nbsp;
                    <input type="hidden" id="editMode" value="0" />
                    <small><?php echo $page->description; ?></small>
                    <?php if($deviceType == "computer"){ ?>
                    <a style="float:right;" class="btn btn-primary" href="ajax/user/itempage_add.php?pageId=<?php echo $page->id; ?>" data-target="#ajax" data-toggle="modal">
                        <i class="fa fa-plus"></i>&nbsp;Ajouter un objet
                    </a>
                    <a style="float:right;margin-right:5px;" title="Gestion de l'ordre des objets" class="btn default btnEditMode" href="#" >
                        <i class="fa fa-wrench "></i>&nbsp;
                    </a>
                    <?php } ?>
                </h3>
                <!-- END PAGE TITLE & BREADCRUMB-->
            </div>
        </div>
        <div class="row">
            <div class="packery">
    <?php 
    foreach($items as $item){
        if($item->scenarioId != ""){
            include "page_scenario.php";
        }elseif($item->tuileId != ""){
            include "page_tuile.php";
        }elseif($item->chartId != ""){
            include "page_chart.php";
        }elseif($item->listeId != ""){
            include "page_liste.php";
        }elseif($item->deviceId != ""){
            $itemParams=json_decode($item->params);
            $device=Device::getDevice($item->deviceId);
            $msgs =MessageDevice::getMessageDevicesForDevice($item->deviceId);
            $slider=false;
            if(count($msgs) == 1){
                //Parcours msg
                foreach($msgs as $msg){
                    $params = json_decode($msg->parameters);
                    if(isset($params->slider)){
                        $slider=true;
                        break;
                    }
                }
            }
            
            if($slider){
                include "page_device_slider.php";
            } else {
                include "page_device.php";
            }
        }else{
            include "page_plugin.php";
        }
    }
    ?>
            </div>
        </div>
    </div>
</div>
    
<div class="modal fade" id="deleteItemPage" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <input type="hidden" id="iditempagetodelete" value="" />
        <div class="modal-content">
            <div class="modal-body">
                <p class="modal-title">Confirmez vous la suppression de l'objet?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default btnCancelPageItemDeletion" data-dismiss="modal" type="button">Annuler</button>
                <button class="btn red btnDeletePageItemConfirm" data-dismiss="modal" type="button">Supprimer</button>
            </div>
        </div>
    </div>
</div>
    
<div class="modal fade" id="ajax" role="basic" aria-hidden="true">
    <div class="page-loading page-loading-boxed">
        <img src="metronic/assets/global/img/loading-spinner-grey.gif" alt="" class="loading">
        <span>&nbsp;&nbsp;Loading... </span>
    </div>
    <div class="modal-dialog" style="width:800px;">
        <div class="modal-content">
        </div>
    </div>
</div>
<div class="modal fade" id="ajaxEditPageItem" role="basic" aria-hidden="true">
    <div class="page-loading page-loading-boxed">
        <img src="metronic/assets/global/img/loading-spinner-grey.gif" alt="" class="loading">
        <span>&nbsp;&nbsp;Loading... </span>
    </div>
    <div class="modal-dialog" style="width:800px;">
        <div class="modal-content">
        </div>
    </div>
</div>
<div class="modal fade" id="ajaxShowMoreMessages" tabindex="-1"  aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title modalTitleMoreMessage"></h4>
            </div>
            <div class="modal-body">
                <div class="scroller" style="height:300px" data-always-visible="1" data-rail-visible1="1">
                    <div class="row">
                        <div class="col-md-12 contentMoreMessage">
                                
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn default">Fermer</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="ajaxTendance" role="basic" aria-hidden="true">
    <div class="page-loading page-loading-boxed">
            <img src="metronic/assets/global/img/loading-spinner-grey.gif" alt="" class="loading">
            <span>
            &nbsp;&nbsp;Loading... </span>
    </div>
    <div class="modal-dialog" style="width:600px;">
        <div class="modal-content">
            <!--OKOK RENTRE-->
        </div>
    </div>
</div>
    <script type="text/javascript" src="//wurfl.io/wurfl.js"></script>
<script type="text/javascript">
    var chart56;
<?php
    foreach($items as $item){
        if($item->chartId == ""){
            continue;
        }
        $chart=Chart::getChart($item->chartId);
        if($chart->type == "ligne_temps_reel"){
            echo "var chart".$item->id.";";
        }
    }
?>
$( document ).ready(function() {
    $('#editMode').val('0');
    $('.btnEditPageItem').hide();
    //$('.btnMoreMessage').hide();
    
    $('.btnEditMode').bind('click',function(e){
        $('.btnEditPageItem').toggle();
        if($('#editMode').val() == "0"){
            $(this).removeClass("default");
            $(this).addClass("green");
            $('#editMode').val('1');
            if(WURFL.is_mobile){
                //dostuff();
            } else {
                $container.find('.boxPackery').each( function( i, itemElem ) {
                    // make element draggable with Draggabilly
                    var draggie = new Draggabilly( itemElem );
                    // bind Draggabilly events to Packery
                    $container.packery( 'bindDraggabillyEvents', draggie );
                });
                $container.packery( 'on', 'layoutComplete', orderItems );
                $container.packery( 'on', 'dragItemPositioned', orderItems );
            }
        } else {
            $('#editMode').val('0');
            location.reload();
        }
    });
    
    $('.btnDeletePageItemConfirm').bind('click',function(e){
        var iditempage=$('#iditempagetodelete').val();
        $.ajax({
            url: "ajax/delete_page_item.php",
            type: "POST",
            data: {
                iditempage:  iditempage
            },
            complete: function(data){
                if(data.responseText == "success"){
                    $('.btnCancelPageItemDeletion').click();
                    $('.itempage-'+iditempage).fadeOut(300, function(){ 
                       $('.itempage-'+iditempage).remove(); 
                    });
                }
            }
        });
    });
    
    $('.btnMoreMessage').bind('click',function(e){
        var idDevice=$(this).attr('idDevice');
        $.ajax({
            url: "ajax/user/more_messages.php",
            type: "GET",
            data: {
                idDevice:  idDevice
            },
            complete: function(data){
                eval(data.responseText);
            }
        });
    });
    
    
    
    $('.btnDeletePageItem').bind('click',function(e){
        $('#iditempagetodelete').val($(this).attr('iditempage'));
    });
    
    
    $(".slider-basic").slider({
        change: function(e,ui){
            if( typeof e.clientX != 'undefined'){
                $.ajax({
                    url: "ajax/action/execute.php",
                    type: "POST",
                    data: {
                       type:  encodeURIComponent('message'),
                       value:  ui.value,
                       elementId: $(this).attr('elementId')
                    },
                    error: function(data){
                        toastr.error("Une erreur est survenue");
                    },
                    complete: function(data){
                        toastr.success("Action exécutée");
                    }
                });
            }
        }
    });
    
    $('.box-action:visible').bind('click',function(e){
        //console.debug($(this).attr('class'));
        executeAction($(this).attr('type'), $(this).attr('elementId'));
    });
    
    
    $('.make-switch').on('switchChange.bootstrapSwitch', function (e,ui) {
        //console.log(this); // DOM element
        //console.debug(e);
        if($(this).is(':checked')){
            var action='on';
        }else{
            var action='off';
        }
        $.ajax({
            url: "ajax/action/execute.php",
            type: "POST",
            data: {
               type:  'device',
               elementId: $(this).attr('deviceId'),
               action: action
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            complete: function(data){
              toastr.success("Action exécutée");
            }
        });
    });
    
    
    <?php
    foreach($items as $item){
        if($item->chartId == "" && $item->tuileId == "" && $item->scenarioId == "" && $item->listeId == "" && $item->deviceId == "" && $item->params != ""){
            $itemParams=json_decode($item->params);
            if(!isset($itemParams->plugin) || $itemParams->plugin != gauge){
                continue;
            }
            
            echo "var valueGauge = $('#gauge-".$item->id."').attr('value');";
            echo "var min = $('#gauge-".$item->id."').attr('min');";
            echo "var max = $('#gauge-".$item->id."').attr('max');";
            echo "var titleText = $('#gauge-".$item->id."').attr('titleText');";
            echo "var v".$item->id."= new JustGage({";
            echo " id: \"gauge-".$item->id."\", ";
            echo " value: valueGauge, ";
            echo " min: min, ";
            echo " max: max, ";
            echo " title: titleText ";
            echo " }); ";
        }
        
        if($item->chartId == ""){
            continue;
        }
        $chart=Chart::getChart($item->chartId);
        $heightChart=$chart->size*80;
        
        if($chart->type == "temps" || $chart->type == "ligne"){
            echo "$('.container-".$item->id."').highcharts({";
            echo " chart: {";
            if($chart->type=='temps'){
                echo " zoomType: 'x'";
            }elseif($chart->type=='ligne'){
                echo " type: 'spline'";
            }
            //echo ",height:'".$heightChart."px'";
            //echo ",width:'375px'";
            echo " },";
            echo "title: {";
            echo " text: '".$chart->name."'";
            echo "},";
            echo "subtitle: {";
            echo "text: '".$chart->description." - ".$chart->getBorneDates()."'";
            echo "},";
            echo "xAxis: {";
            echo "type: 'datetime',";
            echo "title: {";
            echo "text: '".$chart->abscisse."'";
            echo " }";
            echo "},";
            echo "yAxis: {";
            $incremental=FALSE;
            foreach(ChartDevice::getChartDeviceForChart($item->chartId) as $chartDevice){
                $device=Device::getDevice($chartDevice->deviceid);

                if($device->incremental != "" && $device->incremental != "0"){
                    $incremental=TRUE;
                }
            }
            if(!$incremental){
                if($chart->scaleMin != ""){
                    echo " min: ".$chart->scaleMin.",";
                }
                if($chart->scaleMax != ""){
                    echo " max: ".$chart->scaleMax.",";
                }
                
            }
            echo "title: {";
            echo "text: '".$chart->ordonne."'";
            echo "}";
            echo "},";
            echo "series: [";
            $i=0;
            foreach(ChartDevice::getChartDeviceForChart($item->chartId) as $chartDevice){
                $device=Device::getDevice($chartDevice->deviceid);

                if($device->incremental != "" && $device->incremental != "0"){
                    $chartFormula=($chart->price && !is_null($device->chart_formula)) ? $device->chart_formula : NULL;
                    $data=History::getHistoryHighchartLineIncremental($chartDevice->deviceid, $chart->period, $chart->from, $chartFormula);
                } else {
                    $data=History::getHistoryHighchartLine($chartDevice->deviceid, $chart->period, $chart->from);
                }
                if($i>0){
                    echo ",";
                }
                echo "{";  
                if($chart->type=='temps'){
                    echo "type:'area',";  
                }
                echo "name:'".$device->name."',";  
                //echo "data:[ [Date.UTC(2014,8,21,0,8),18],[Date.UTC(2014,8,21,1,14),17.8],[Date.UTC(2014,8,21,2,20),17.4],[Date.UTC(2014,8,21,3,26),17.4] ]";  
                echo "data:[ ".$data." ]";  
                
                if($chart->price && !is_null($device->chart_formula)){
                    echo ",tooltip: {";
                    echo " valueDecimals: 2,";
                    echo "valuePrefix: '€',";
                    echo "valueSuffix: ' EUR'";
                    echo "}";
                }
                
                
                echo "}";
                $i++;
            }
            echo "]";
            echo "});";
        }
        
        if($chart->type == "barre"){
            echo "$('.container-".$item->id."').highcharts({";
            echo " chart: {";
            echo " type: 'column'";
            echo " },";
            echo "title: {";
            echo " text: '".$chart->name."'";
            echo "},";
            echo "subtitle: {";
            echo "text: '".$chart->description." - ".$chart->getBorneDates()."'";
            echo "},";
            echo "xAxis: {";
            echo "categories: [";
            switch($chart->period){
                case '1':
                    $j=$chart->getHeureFormatted();
                    for($i=0;$i<=23;$i++){
                        $j=($j > 23) ? 0 : $j;
                        if($i>0){echo ",";}
                        echo "'".$j."'";
                        $j++;
                    }
                    break;
                case '2':
                    echo $chart->getDaysForWeek();
                    /*for($i=0;$i<=23;$i++){
                        $j=($j > 23) ? 0 : $j;
                        if($i>0){echo ",";}
                        echo "'".$j."'";
                        $j++;
                    }*/
                    break;
            }
            echo "]";
            echo "},";
            echo "yAxis: {";
            echo "min: 0,";
            echo "title: {";
            echo "text: '".$chart->ordonne."'";
            echo "}";
            echo ",stackLabels: {";
            //echo "enabled: true,";
            echo "style: {";
            echo "fontWeight: 'bold',";
            echo "color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'";
            echo "}";
            echo "}";
            echo "},";
            echo "plotOptions: {";
            echo "column: {";
            echo "stacking: 'normal',";
            echo " dataLabels: {";
            echo " color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',";
            echo "style: {";
            echo "textShadow: '0 0 3px black'";
            echo "}";
            echo "}";
            echo "}";
            echo "},";
            echo "series: [";
            $k=0;
            foreach(ChartDevice::getChartDeviceForChart($item->chartId) as $chartDevice){
                $device=Device::getDevice($chartDevice->deviceid);

                $chartFormula=($chart->price && !is_null($device->chart_formula)) ? $device->chart_formula : NULL;
                $data=History::getHistoryHighchartBarre($chartDevice->deviceid, $chart->period, $chart->from, $chartFormula);
                //print_r($data);
                //exit;
                //$data=0;
                if($k>0){
                    echo ",";
                }
                echo "{";  
                echo "name:'".$device->name."',";  
                //echo "data:[ [Date.UTC(2014,8,21,0,8),18],[Date.UTC(2014,8,21,1,14),17.8],[Date.UTC(2014,8,21,2,20),17.4],[Date.UTC(2014,8,21,3,26),17.4] ]";  
                echo "data:[ ".$data." ]";
                if($chart->price && !is_null($device->chart_formula)){
                    echo ",tooltip: {";
                    echo " valueDecimals: 2,";
                    echo "valuePrefix: '€',";
                    echo "valueSuffix: ' EUR'";
                    echo "}";
                }
                echo "}";
                $k++;
            }
            echo "]";
            echo "});";
        }
        
        if($chart->type == "mix"){
            echo "$('.container-".$item->id."').highcharts({";
            echo " chart: {";
            echo " zoomType: 'xy' ";
            echo " }, ";
            echo "title: {";
            echo " text: '".$chart->name."'";
            echo "},";
            echo "subtitle: {";
            echo "text: '".$chart->description." - ".$chart->getBorneDates()."'";
            echo "},";
            echo "xAxis: {";
            echo "type: 'datetime'";
            echo "},";
            echo "yAxis: [{";
            echo "title: { text: 'Temperature'}";
            echo "},{";
            echo "title: { text: '".$chart->ordonne."' }";
            echo "}],";
            echo "tooltip: {";
            echo "shared: true";
            echo "},";
            echo "series: [";
            $k=0;
            foreach(ChartDevice::getChartDeviceForChart($item->chartId) as $chartDevice){
                $device=Device::getDevice($chartDevice->deviceid);

                $chartFormula=($chart->price && !is_null($device->chart_formula)) ? $device->chart_formula : NULL;
                $data=History::getHistoryHighchartBarre($chartDevice->deviceid, $chart->period, $chart->from, $chartFormula,TRUE);
                //print_r($data);
                //exit;
                //$data=0;
                if($k>0){
                    echo ",";
                }
                echo "{";  
                echo "yAxis: 1,";
                echo "type: 'column',";
                echo "name:'".$device->name."',";  
                //echo "data:[ [Date.UTC(2014,8,21,0,8),18],[Date.UTC(2014,8,21,1,14),17.8],[Date.UTC(2014,8,21,2,20),17.4],[Date.UTC(2014,8,21,3,26),17.4] ]";  
                echo "data:[ ".$data." ]";
                if($chart->price && !is_null($device->chart_formula)){
                    echo ",tooltip: {";
                    echo " valueDecimals: 2,";
                    echo "valuePrefix: '€',";
                    echo "valueSuffix: ' EUR'";
                    echo "}";
                }
                
                echo "}";
                $k++;
            }
            if($chart->deviceIdLine != ""){
                $devicesIdLine = explode(",", $chart->deviceIdLine);
                foreach($devicesIdLine as $deviceIdLine){
                    $device=Device::getDevice($deviceIdLine);

                    $data=History::getHistoryHighchartLine($deviceIdLine, $chart->period, $chart->from);
                    if($k>0){
                        echo ",";
                    }
                    echo "{";  
                    echo "name:'".$device->name."',";  
                    echo "data:[ ".$data." ]";
                    echo "}";
                    $k++;
                }
            }
            echo "]";
            echo "});";
        }
        
        if($chart->type == "ligne_temps_reel"){
            
            echo "chart".$item->id." = new Highcharts.Chart({ ";
            echo " chart: {";
            echo " renderTo: 'container-".$item->id."', ";
            echo " type: 'spline', ";
            echo " events: { ";
            echo " load: requestDataDomokine(".$chart->id.", ".$item->id.")";
            echo " }";
            echo " }, ";
            echo "title: {";
            echo " text: '".$chart->name."'";
            echo "},";
            echo "subtitle: {";
            echo "text: '".$chart->description." - ".$chart->getBorneDates()."'";
            echo "},";
            echo "xAxis: {";
            echo "type: 'datetime'";
            //echo "tickPixelInterval: 150,";
            //echo "maxZoom: 20 * 1000";
            echo "},";
            echo "yAxis: {";
            echo "title: { text: '".$chart->ordonne."'}";
            echo "},";
            echo "series: [";
            $k=0;
            foreach(ChartDevice::getChartDeviceForChart($item->chartId) as $chartDevice){
                $device=Device::getDevice($chartDevice->deviceid);

                if($k>0){
                    echo ",";
                }
                echo "{";  
                echo "name:'".$device->name."',";  
                echo "data:[]";
                echo "}";
                $k++;
            }
            echo "]";
            echo "});";
        }
    }
    ?>
    
    function requestDataDomokine(chartId, itemId) {
        $.ajax({
            url: 'controllers/live-server-data.php',
            type:'POST',
            data: {chartId: chartId, itemId: itemId},
            success: function(point) {
                eval(point);
                //console.debug('ok');
                setTimeout(requestData(chartId, itemId), 8000000);
            },
            cache: false
        });
        
        //setInterval(requestDataDomokine(chartId, itemId), 8000);
        //setTimeout(requestData(chartId, itemId), 8000000);
    }
    
    var $container = $('.packery').packery({
        columnWidth: 80,
        rowHeight: 80
    });
    
    if(WURFL.is_mobile){
	//dostuff();
    } else {
        $container.find('.boxPackery').each( function( i, itemElem ) {
            // make element draggable with Draggabilly
            //var draggie = new Draggabilly( itemElem );
            // bind Draggabilly events to Packery
            //$container.packery( 'bindDraggabillyEvents', draggie );
        });
    }
    
  
    function orderItems() {
        var itemElems = $container.packery('getItemElements');
        var sorts="";
        jQuery.each(itemElems, function( index ) {
            if(sorts != ""){
                sorts=sorts+"~";
            }
            sorts=sorts+index+":"+ $( this ).attr('iditempage');
            //console.log( index + ": " + $( this ).attr('iditempage') );
        });
        
        
        $.ajax({
            url: "ajax/page_item_update_order.php",
            type: "POST",
            data: {
                pageid:  $('#pageId').val(),
                params:  sorts
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            complete: function(data){
                if(data == "error"){
                    $('#alert').addClass('alert alert-danger');
                    $('#alert').text('Une erreur est survenue');
                } 
            }
        });
        //console.debug(sorts);
    }
});

function executeAction(type, elementId){
    if(type==0){
        var type='message';
    }
    $.ajax({
        url: "ajax/action/execute.php",
        type: "POST",
        data: {
           type:  type,
           elementId: elementId
        },
        error: function(data){
            toastr.error("Une erreur est survenue");
        },
        complete: function(data){
            toastr.success("Action exécutée");
        }
    });
}

setTimeout("refreshStatus()", 3000);
function refreshStatus(){
    var device_ids = new Array();
    //Status
    //$('.stateDeviceId:visible').each(function() {
    $('.stateDeviceId').each(function() {
        if ($(this).attr('stateDeviceId') != 0) {
            if ($.inArray($(this).attr('stateDeviceId'),device_ids) == -1) {
                device_ids.push($(this).attr('stateDeviceId'));
            }
        }
    });

    $.ajax({
        type: "POST",
        url: "scripts/status.php",
        dataType:"json",
        data: {ids: device_ids.join(',')},
        timeout: 2000,
        success: function(data){
            $.each(data, function(index, value) {
                value = utf8_decode(value);
                if(value.toLowerCase() != "on" && value.toLowerCase() != "off"){
                    if($('.slider-basic-'+index).size() == 1){
                        $('.slider-basic-'+index).slider({'value':value});
                    } else {
                        $('.stateDeviceId-'+index).text(value);
                    }
                }
                if(value.toLowerCase() == "on"){
                    if($('.icon-status-'+index).length >= 1){
                        if($('.icon-status-'+index).attr('type') == 'door'){
                            $('.icon-status-'+index).removeClass().addClass('status-icon-'+index+' stateDeviceId icon-unlocked');
                        }
                        if($('.icon-status-'+index).attr('type') == 'light'){
                            $('.icon-status-'+index).removeClass().addClass('status-icon-'+index+' stateDeviceId icon-light-on');
                        }
                    }
                    
                    $('.make-switch-'+index).bootstrapSwitch('state', true, false);
                    
                    $('.stateDeviceId-badge-'+index).removeClass("badge-danger");
                    $('.stateDeviceId-badge-'+index).addClass("badge-success");
                    $('.stateDeviceId-tile-'+index).removeClass("tile-danger");
                    $('.stateDeviceId-tile-'+index).addClass("tile-success");
                    $('.stateDeviceId-tile-'+index).text('');
                    //$('.stateDeviceId-'+index).text('');
                }
                if(value.toLowerCase() == "off"){
                    if($('.icon-status-'+index).length >= 1){
                        if($('.icon-status-'+index).attr('type') == 'door'){
                            $('.icon-status-'+index).removeClass().addClass('status-icon-'+index+' stateDeviceId icon-locked');
                        }
                        if($('.icon-status-'+index).attr('type') == 'light'){
                            $('.icon-status-'+index).removeClass().addClass('status-icon-'+index+' stateDeviceId icon-light-off');
                        }
                    }
                    $('.make-switch-'+index).bootstrapSwitch('state', false, false);

                    $('.stateDeviceId-badge-'+index).removeClass("badge-success");
                    $('.stateDeviceId-badge-'+index).addClass("badge-danger");
                    $('.stateDeviceId-tile-'+index).removeClass("tile-success");
                    $('.stateDeviceId-tile-'+index).addClass("tile-danger");
                    $('.stateDeviceId-tile-'+index).text('');
                    //$('.stateDeviceId-'+index).text('');
                }
            });
        }
    });

    if(device_ids.length > 0) setTimeout("refreshStatus()", 5000);
}
    
function utf8_decode(str_data) {

    var tmp_arr = [],
    i = 0,
    ac = 0,
    c1 = 0,
    c2 = 0,
    c3 = 0,
    c4 = 0;

    str_data += '';

    while (i < str_data.length) {
        c1 = str_data.charCodeAt(i);
        if (c1 <= 191) {
          tmp_arr[ac++] = String.fromCharCode(c1);
          i++;
        } else if (c1 <= 223) {
          c2 = str_data.charCodeAt(i + 1);
          tmp_arr[ac++] = String.fromCharCode(((c1 & 31) << 6) | (c2 & 63));
          i += 2;
        } else if (c1 <= 239) {
          // http://en.wikipedia.org/wiki/UTF-8#Codepage_layout
          c2 = str_data.charCodeAt(i + 1);
          c3 = str_data.charCodeAt(i + 2);
          tmp_arr[ac++] = String.fromCharCode(((c1 & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
          i += 3;
        } else {
          c2 = str_data.charCodeAt(i + 1);
          c3 = str_data.charCodeAt(i + 2);
          c4 = str_data.charCodeAt(i + 3);
          c1 = ((c1 & 7) << 18) | ((c2 & 63) << 12) | ((c3 & 63) << 6) | (c4 & 63);
          c1 -= 0x10000;
          tmp_arr[ac++] = String.fromCharCode(0xD800 | ((c1 >> 10) & 0x3FF));
          tmp_arr[ac++] = String.fromCharCode(0xDC00 | (c1 & 0x3FF));
          i += 4;
        }
    }

  return tmp_arr.join('');
}

</script>
<?php
include "modules/footer.php";
?>
