<?php
include_once "./tools/config.php";

$GLOBALS["dbconnec"] = connectDB();

$dst_dir  = $GLOBALS['path'].'/camera';
$cameras = array('salon');
// taille d'affichage des images
$width  = '640';
$height = '480';

// fonction qui renvoie la dernière image d'une caméra
function showLastImage ($cam_name) {
    global $dst_dir;
    header('Content-type: image/jpeg');
    $dir  = $dst_dir."/".$cam_name."/cam_".$cam_name."_*";
    $imgs = glob($dir);
    echo new Imagick(end($imgs));
}

if(isset($_REQUEST['get_cam_img'])){
    echo showLastImage($_REQUEST['get_cam_img']);
  }
  else{
    include "modules/header.php";
    include "modules/sidebar.php";
?>
<!-- BEGIN PAGE -->
<div class="page-content-wrapper">
<div class="page-content">
    <div class="container-fluid">
    <div class="row">
            <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->			
                    <h3 class="page-title">
                            Surveillance				
                            <small>Supervision des caméras</small>
                    </h3>
                    <!-- END PAGE TITLE & BREADCRUMB-->
            </div>
    </div>
    <div class="row">
        <div class="col-md-6 ">
            <div class="portlet box blue tabbable">
                <div class="portlet-title">
                    <h4><i class="icon-reorder"></i>Salon</h4>
                </div>
                <div class="portlet-body">
                    <div class="camera" id="salon">
                        <img class="camera" id="img_salon" src="">
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
<script type="text/javascript">
var refresh = 3000;

        $( document ).ready(function() {
          setInterval(function() {
            var now = new Date().getTime();
            $("div.camera").each(function( index ) {
              var camera_name = $( this ).attr("id");
              console.log( "refresh "+camera_name+"..." );
              var url = '<?php echo $GLOBALS['path']; ?>/camera.php?get_cam_img='+camera_name;
              var url = '/camera.php?get_cam_img=salon&1408985786429';
              var img_tmp  = $("<img />").attr("src", url+"&"+now);
              $("#img_"+camera_name).attr("src", url+"&"+now);
            });
          }, refresh);
        });
</script>
<?php
    include "modules/footer.php";
}
?>
