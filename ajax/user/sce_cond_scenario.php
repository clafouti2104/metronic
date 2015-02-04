<?php
include '../../tools/config.php';
include_once "../../models/Scenario.php";

if(!isset($_GET["idCond"])){
    return "Veuillez saisir un id de scenario conditionnel";
}
$idCond=$_GET["idCond"];
$idCondAction= (isset($_POST["idCondAction"])) ? $_POST["idCondAction"] : "";

$GLOBALS["dbconnec"] = connectDB();
$scenarios=Scenario::getScenarios();

?>
<input type="hidden" id="condId" value="<?php echo $idCond; ?>" />
<input type="hidden" id="condActionId" value="<?php echo $idCondAction; ?>" />
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title"><i class="fa fa-plus"></i>&nbsp;&nbsp;Sélection d'un scénario</h4>
    </div>
    <div class="modal-body">
        <div id="alert" class=""></div>
        <div class="row">
            <div class="col-md-12">
                <p class="text-center">Scénario</p>
                <select name="selectObject" id="selectObject" style="width:100%;">
<?php
foreach($scenarios as $scenario){
    echo "<option value=\"".$scenario->id."\">".$scenario->name."</option>";
}
?>
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-primary btnAddCondAction">Ajouter</button>
    </div>
</div>
            <!-- /.modal-content -->
<script src="assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
<script type="text/javascript">
$( document ).ready(function() {
    $('.btnAddCondAction').bind('click',function(e){
        $('#alert').removeClass('alert alert-danger');
        $('#alert').text('');
        
        $.ajax({
            url: "ajax/sce_cond_action_submit.php",
            type: "POST",
            data: {
                condId:  $('#condId').val(),
                condActionId:  $('#condActionId').val(),
                type:  'scenario',
                commandId:  $('#selectObject').val()
            },
            complete: function(data){
                $('#tableCondAction tbody:last').append('<tr id="line-condaction-'+data.responseText+'"><td>Scénario</td><td>'+$('#selectObject option:selected').text()+'</td><td><i class="fa fa-trash-o" onclick="deleteCondAction('+data.responseText+');"></i></td></tr>');
                $('.close').click();
                toastr.info("Action ajoutée");
                /*if(data.responseText == "success"){
                    $('#alert').text("Scénario ajouté, veuillez recharger la page pour voir les modifications");
                    $('#alert').addClass('alert alert-success');
                } else {
                    $('#alert').text(data.responseText);
                    $('#alert').addClass('alert alert-danger');
                }*/
            },
            error: function(data){
                $('#alert').text("Une erreur est survenue");
                $('#alert').addClass('alert alert-danger');
            }
        });
    });
});
</script>