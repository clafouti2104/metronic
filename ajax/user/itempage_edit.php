<?php
include '../../tools/config.php';
include_once "../../models/PageItem.php";
include_once "../../models/Scenario.php";
include_once "../../models/Device.php";
include_once "../../models/Tuile.php";
include_once "../../models/Chart.php";
include_once "../../models/Liste.php";

$GLOBALS["dbconnec"] = connectDB();
if(!isset($_GET["itemPageId"])){
    echo "ERROR";
    exit;
}
$pageItem=PageItem::getPageItem($_GET["itemPageId"]);
$params = (is_object($pageItem) && $pageItem->params) ? json_decode($pageItem->params) : "";
$width = ($params != "" && $params->width) ? $params->width : "";
$color = ($params != "" && $params->color) ? $params->color : "";
?>
<input type="hidden" id="pageItemId" value="<?php echo $pageItem->id; ?>" />
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title"><i class="fa fa-edit"></i>&nbsp;&nbsp;Edition d'un objet</h4>
    </div>
    <div class="modal-body">
        <div id="alert" class=""></div>
        <div class="row">
            <div class="col-md-4 ">
                <p class="text-center">Couleur</p>
                <select id="selectColor" style="width: 100%;">
<?php
                    foreach($colors as $colorType=>$array){
                        echo "<optgroup label=\"".ucwords($colorType)."\">";
                        foreach($array as $key=>$colorTmp){
                            $selected = ($key == $color) ? " selected=\"selected\" " : "";
                           echo "<option value=\"".$key."\" $selected>".ucwords($colorTmp)."</option>";
                        }
                        echo "</optgroup>";
                    }
?>
                </select>
            </div>
            <div class="col-md-4 ">
                <p class="text-center">Largeur</p>
                <select id="selectWidth" style="width: 100%;">
<?php
                for($widthTmp=2;$widthTmp<=12;$widthTmp++){
                    $selected = ($widthTmp == $width) ? " selected=\"selected\" " : "";
                    echo "<option value=\"".$widthTmp."\" $selected>".$widthTmp."</option>";
                }
?>
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-primary btnSubmitEditPageItem">Modifier</button>
    </div>
</div>
            <!-- /.modal-content -->
<script src="assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
<script type="text/javascript">
$( document ).ready(function() {
    $('.btnSubmitEditPageItem').bind('click',function(e){
        $('#alert').removeClass('alert alert-danger');
        $('#alert').text('');
        //Control
        if($('#selectColor').val() == "" && $('#selectWidth').val() == ""){
            $('#alert').addClass('alert alert-danger');
            $('#alert').text('Veuillez remplir les listes déroulantes');
            return true;
        }
        
        $.ajax({
            url: "ajax/edit_page_item.php",
            type: "POST",
            data: {
                pageItemid:  $('#pageItemId').val(),
                width:  $('#selectWidth').val(),
                color:  $('#selectColor').val()
            },
            complete: function(data){
                if(data.responseText == "success"){
                    $('#alert').text("");
                    $('#alert').addClass('alert alert-success');
                    toastr.info("Modification effectuée");
                    $('.cell-'+$('#pageItemId').val()).removeClass().addClass("cell cell-"+$('#pageItemId').val()+" col-lg-"+$('#selectWidth').val()+" col-md-"+$('#selectWidth').val()+" col-sm-6 col-xs-12 boxPackery itempage itempage-"+$('#pageItemId').val());
                    $('.dashboard-stat-'+$('#pageItemId').val()).removeClass().addClass("dashboard-stat dashboard-stat-"+$('#pageItemId').val()+" "+$('#selectColor').val());
                    $('.close').click();
                } else {
                    $('#alert').text(data.responseText);
                    $('#alert').addClass('alert alert-danger');
                }
            }
        });
    });
});
</script>