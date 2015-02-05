<?php
include '../../tools/config.php';
include_once "../../models/Device.php";

if(!isset($_GET["idCond"])){
    return "Veuillez saisir un id de scenario conditionnel";
}
$idCond=$_GET["idCond"];
$idCondAction= (isset($_POST["idCondAction"])) ? $_POST["idCondAction"] : "";

$GLOBALS["dbconnec"] = connectDB();
?>
<input type="hidden" id="condId" value="<?php echo $idCond; ?>" />
<input type="hidden" id="condActionId" value="<?php echo $idCondAction; ?>" />
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title"><i class="fa fa-plus"></i>&nbsp;&nbsp;Exécution d'une ligne de commande</h4>
    </div>
    <div class="modal-body">
        <div id="alert" class=""></div>
        <div class="row">
            <div class="col-md-12">
                <p class="text-center">Commande</p>
                <textarea name="txtCommandLine" id="txtCommandLine" style="width:100%;"></textarea>
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
        //Control
        if($('#inputValue').val() == ""){
            $('#alert').addClass('alert alert-danger');
            $('#alert').text('Veuillez sélectionner une valeur');
            return true;
        }
        
        $.ajax({
            url: "ajax/sce_cond_action_submit.php",
            type: "POST",
            data: {
                condId:  $('#condId').val(),
                condActionId:  $('#condActionId').val(),
                type:  'commandline',
                commandId:  $('#txtCommandLine').val()
            },
            complete: function(data){
                $('#tableCondAction tbody:last').append('<tr id="line-condaction-'+data.responseText+'"><td>Notification</td><td>'+$('#selectNotification option:selected').text()+'</td><td><i class="fa fa-trash-o" style="cursor:pointer;" onclick="deleteCondAction('+data.responseText+');"></i></td></tr>');
                $('.close').click();
                toastr.info("Notification ajoutée");
            },
            error: function(data){
                $('#alert').text("Une erreur est survenue");
                $('#alert').addClass('alert alert-danger');
            }
        });
    });
});
</script>