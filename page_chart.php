<?php
$chart=Chart::getChart($item->chartId);
$heightChart=$chart->size*80;
?>
<div class="cell col-lg-<?php echo $chart->size; ?> col-md-6 col-sm-6 col-xs-12 boxPackery itempage itempage-<?php echo $item->id; ?>" iditempage="<?php echo $item->id; ?>" style="height:400px;">
    <div class="container-<?php echo $item->id; ?> dashboard-stat">
    </div>
    <div class="deleteItemPage" style="margin-bottom:10px;position: absolute;top:370px;right:10px;">
        <a class=" btnDeletePageItem" iditempage="<?php echo $item->id; ?>" data-toggle="modal" href="page.php#deleteItemPage">
          &nbsp;<i class="fa fa-trash-o" ></i>
        </a>
    </div>
</div>