<?php
//METEO
if(isset($params->plugin) && $params->plugin == "meteo" && isset($params->id)){
    
    //$bgcolor='grey-gallery';
    $color='color:#FFF;';
    //Match Icon<->Code
    $icon=array();
    $icon[0]='tornado';
    $icon[1]='tornado';
    $icon[2]='hurricane';
    $icon[3]='thunderstorm';
    $icon[4]='thunderstorm';
    $icon[5]='day-rain-mix';
    $icon[6]='day-sleet-storm';
    $icon[7]='day-sleet-storm';
    $icon[8]='rain-wind';
    $icon[9]='rain-wind';
    $icon[10]='showers';
    $icon[11]='showers';
    $icon[12]='showers';
    $icon[13]='showers';
    $icon[14]='snow-wind';
    $icon[15]='snow-wind';
    $icon[16]='snow-wind';
    $icon[17]='hail';
    $icon[18]='sleet';
    $icon[19]='dust';
    $icon[20]='day-fog';
    $icon[21]='day-haze';
    $icon[22]='smoke';
    $icon[23]='windy';
    $icon[24]='windy';
    $icon[25]='snowflake-cold';
    $icon[26]='cloudy';
    $icon[27]='night-cloudy-windy';
    $icon[28]='cloudy-windy';
    $icon[29]='night-cloudy';
    $icon[30]='day-cloudy';
    $icon[31]='night-clear';
    $icon[32]='day-sunny';
    $icon[33]='night-clear';
    $icon[34]='sunset';
    $icon[35]='rain-mix';
    $icon[36]='hot';
    $icon[37]='thunderstorm';
    $icon[38]='thunderstorm';
    $icon[39]='thunderstorm';
    $icon[40]='showers';
    $icon[41]='snow';
    $icon[42]='snow';
    $icon[43]='snow';
    $icon[44]='cloudy';
    $icon[45]='storm-showers';
    $icon[46]='day-snow-thunderstorm';
    $icon[47]='showers';

    $days = array();
    $days["Mon"]="Lundi";
    $days["Tue"]="Mardi";
    $days["Wed"]="Mercredi";
    $days["Thu"]="Jeudi";
    $days["Fri"]="Vendredi";
    $days["Sat"]="Samedi";
    $days["Sun"]="Dimanche";
}

?>
<div class="cell col-lg-<?php echo $size; ?> col-md-<?php echo $size; ?> col-sm-6 col-xs-12 boxPackery itempage itempage-<?php echo $item->id; ?>" deviceid="" iditempage="<?php echo $item->id; ?>">
    <div class="dashboard-stat <?php echo $bgcolor; ?>" style="<?php echo $color; ?>">
        <div class="visual" style="padding-top: 1px;width: 120px;">
<?php 

echo "<div style=\"float:left;\"><i class=\"wi wi-up\"></i></div><span style=\"float:left;font-weight:lighter;font-size:50%;font-family:HelveticaNeue-UltraLight,\"Helvetica Neue\",HelveticaNeue-Light,HelveticaNeue,helvetica,arial,sans-serif\">".$details->today_high."°</span>";
echo "<div style=\"margin-bottom:10px;float:left;margin-left:10px;\"><i class=\"wi wi-down\"></i></div><span style=\"float:left;font-weight:lighter;font-size:50%;font-family:HelveticaNeue-UltraLight,\"Helvetica Neue\",HelveticaNeue-Light,HelveticaNeue,helvetica,arial,sans-serif\">".$details->today_low."°</span>";
echo "<span style='margin-top:10px;font-weight:lighter;font-size:200%;font-family:HelveticaNeue-UltraLight,\"Helvetica Neue\",HelveticaNeue-Light,HelveticaNeue,helvetica,arial,sans-serif'>".$details->actual_temp."°</span>";
?>
        </div>
        <div class="details">
            <!--<i style="float:left;" class="wi-2x wi wi-<?php echo $icon[$details->actual_code]; ?>"></i>-->
            <table class="table">
                <tr>
                    <td>Demain</td>
                    <td><?php echo "<i class=\"wi wi-".$icon[$details->day1_code]."\"></i>"; ?></td>
                    <td><?php echo $details->day1_high; ?>°</td>
                    <td style="color:#a5d6ff;"><?php echo $details->day1_low; ?>°</td>
                </tr>
                <tr>
                    <td><?php echo $days[$details->day2_day]; ?></td>
                    <td><?php echo "<i class=\"wi wi-".$icon[$details->day2_code]."\"></i>"; ?></td>
                    <td><?php echo $details->day2_high; ?>°</td>
                    <td style="color:#a5d6ff;"><?php echo $details->day2_low; ?>°</td>
                </tr>
                <tr>
                    <td><?php echo $days[$details->day3_day]; ?></td>
                    <td><?php echo "<i class=\"wi wi-".$icon[$details->day3_code]."\"></i>"; ?></td>
                    <td><?php echo $details->day3_high; ?>°</td>
                    <td style="color:#a5d6ff;"><?php echo $details->day3_low; ?>°</td>
                </tr>
            </table>
        </div>
        <a class="more btnDeletePageItem" iditempage="<?php echo $item->id; ?>" data-toggle="modal" href="page.php#deleteItemPage">
          <?php echo $name; ?><i class="fa fa-trash-o" ></i>
        </a>						
    </div>
</div>