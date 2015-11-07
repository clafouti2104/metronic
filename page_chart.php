<?php
$chart=Chart::getChart($item->chartId);
$heightChart=$chart->size*80;
?>
<div class="grid-stack-item cell boxPackery itempage itempage-<?php echo $item->id; ?>" data-gs-x="<?php echo $item->position; ?>" data-gs-y="<?php echo $item->positiony; ?>" data-gs-width="<?php echo $item->width; ?>" data-gs-height="<?php echo $item->height; ?>" iditempage="<?php echo $item->id; ?>" >
    <div id="container-<?php echo $item->id; ?>" class="grid-stack-item-content container-<?php echo $item->id; ?> dashboard-stat">
    </div>
    <div class="deleteItemPage" style="width: 100%;float: left;top:-10px;">
        <a class=" btnDeletePageItem" iditempage="<?php echo $item->id; ?>" data-toggle="modal" href="page.php#deleteItemPage">
          &nbsp;<i class="fa fa-trash-o" ></i>
        </a>
    </div>
</div>