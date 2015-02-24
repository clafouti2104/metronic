<?php
include '../../tools/config.php';
include_once "../../models/Device.php";

$idCond=$idCondAction=$idSchedule=NULL;
if(!isset($_GET["idCond"]) && !isset($_GET["idSchedule"])){
    return "Veuillez saisir un id de scenario conditionnel ou un id de tache planifiee";
}
if(isset($_GET["idCond"])){
    $idCond=$_GET["idCond"];
    $idCondAction= (isset($_POST["idCondAction"])) ? $_POST["idCondAction"] : "";
}
if(isset($_GET["idSchedule"])){
    $idSchedule=$_GET["idSchedule"];
}

$GLOBALS["dbconnec"] = connectDB();
$sqlDevices = "SELECT * FROM device ";
$sqlDevices .= " WHERE id IN (";
$sqlDevices .= " SELECT deviceId FROM messagedevice";
$sqlDevices .= ") ";
$sqlDevices .= " ORDER BY name ASC ";
$stmt = $GLOBALS["dbconnec"]->prepare($sqlDevices);
$stmt->execute(array());
$devicesTab = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $devicesTab[$row["id"]]=array(
        "id"=>$row["id"],
        "name"=>$row["name"],
        "type"=>$row["type"],
        "parameters"=>$row["parameters"]
    );
}

?>
<input type="hidden" id="condId" value="<?php echo $idCond; ?>" />
<input type="hidden" id="condActionId" value="<?php echo $idCondAction; ?>" />
<input type="hidden" id="scheduleId" value="<?php echo $idSchedule; ?>" />
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title"><i class="fa fa-plus"></i>&nbsp;&nbsp;Sélection d'une commande</h4>
    </div>
    <div class="modal-body">
        <div id="alert" class=""></div>
        <div class="row">
            <div class="col-md-6">
                <p class="text-center">Objet</p>
                <select name="selectObject" id="selectObject" style="width:100%;">
                    <option></option>
<?php
foreach($devicesTab as $id=>$info){
    $type=($info["type"] != "") ? " - ".$info["type"] : "";
    echo "<option value=\"".$id."\">".$info["name"].$type."</option>";
}
?>
                </select>
            </div>
            <div class="col-md-6">
                <p class="text-center">Commande</p>
                <select name="selectCommande" id="selectCommande" style="width:100%;">
                    <option></option>
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
    $('#selectObject').bind('change',function(e){
        $.ajax({
            url: "ajax/user/load_messages.php",
            type: "POST",
            data: {
                deviceId:  $('#selectObject').val()
            },
            complete: function(data){
                eval(data.responseText);
            }
        });
    });
    
    
    $('.btnAddCondAction').bind('click',function(e){
        $('#alert').removeClass('alert alert-danger');
        $('#alert').text('');
        //Control
        if($('#selectCommande').val() == ""){
            $('#alert').addClass('alert alert-danger');
            $('#alert').text('Veuillez sélectionner une action');
            return true;
        }
        
        $.ajax({
            url: "ajax/sce_cond_action_submit.php",
            type: "POST",
            data: {
                scheduleId:  $('#scheduleId').val(),
                condId:  $('#condId').val(),
                condActionId:  $('#condActionId').val(),
                type:  'action',
                commandId:  $('#selectCommande').val()
            },
            complete: function(data){
                //if(data.responseText == "success"){
                    $('#tableCondAction tbody:last').append('<tr id="line-condaction-'+data.responseText+'"><td>Action</td><td>'+$('#selectObject option:selected').text()+' - '+$('#selectCommande option:selected').text()+'</td><td><i class="fa fa-trash-o" style="cursor:pointer;" onclick="deleteCondAction('+data.responseText+');"></i></td></tr>');
                    $('.close').click();
                    toastr.info("Action ajoutée");
                //} 
            },
            error: function(data){
                $('#alert').text("Une erreur est survenue");
                $('#alert').addClass('alert alert-danger');
            }
        });
    });
});
</script>