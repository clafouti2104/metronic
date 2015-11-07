<?php
$scenario=Scenario::getScenario($item->scenarioId);
$icon=($scenario->icon == "") ? "fa-play-circle" : $scenario->icon;
?>
<div class="grid-stack-item cell itempage itempage-<?php echo $item->id; ?>"  data-gs-x="<?php echo $item->position; ?>" data-gs-y="<?php echo $item->positiony; ?>" data-gs-width="<?php echo $item->width; ?>" data-gs-height="<?php echo $item->height; ?>"   type="scenario" elementId="<?php echo $scenario->id; ?>" iditempage="<?php echo $item->id; ?>">
    <div class="grid-stack-item-content dashboard-stat <?php echo $scenario->color; ?>">
        <div class="visual box-action" type="scenario" elementId="<?php echo $scenario->id; ?>">
                <i class="fa <?php echo $scenario->icon; ?>"></i>
        </div>
        <div class="details box-action" type="scenario" elementId="<?php echo $scenario->id; ?>">
                <div class="number">
                        <?php echo $scenario->name; ?>
                </div>
                <div class="desc">                                  
                        <?php echo $scenario->description; ?>
                </div>
        </div>
        <div class="more ">
          &nbsp;<i class="fa fa-info" style="float:left;" ></i>
          &nbsp;<a  class="btnDeletePageItem" iditempage="<?php echo $item->id; ?>" data-toggle="modal" href="page.php#deleteItemPage" style="float:right;color:#fff;"><i class="fa fa-trash-o" ></i></a>
        </div>
    </div>
</div>