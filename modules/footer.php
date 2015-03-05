	<!-- END CONTAINER -->
	<!-- BEGIN FOOTER -->
<div class="page-footer">
	<div class="page-footer-inner">
		 <?php echo date('Y'); ?> &copy; Pouchkine.
	</div>
        <!--<div style="float: left;display: inline-block;width: 70%;">
            <marquee behaviour="scroll" style="color:#d9d9d9;">This is basic example of marquee</marquee>
        </div>-->
	<div class="page-footer-tools">
		<span class="go-top">
		<i class="fa fa-angle-up"></i>
		</span>
	</div>
</div>
<!-- END FOOTER -->
<!--[if lt IE 9]>
<script src="<?php echo $GLOBALS['path']; ?>/assets/global/plugins/respond.min.js"></script>
<script src="<?php echo $GLOBALS['path']; ?>/assets/global/plugins/excanvas.min.js"></script> 
<![endif]-->
<?php
if(isset($includeCSS)){
    foreach ($includeCSS as $cssFile){
        echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$GLOBALS['path'].$cssFile."\" />";
    }
}
?>
<script src="<?php echo $GLOBALS['path']; ?>/assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['path']; ?>/assets/js/packery.pkgd.min.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['path']; ?>/assets/js/draggabilly.pkgd.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['path']; ?>/assets/global/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['path']; ?>/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['path']; ?>/assets/global/plugins/bootstrap/js/bootstrap2-typeahead.min.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['path']; ?>/assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['path']; ?>/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['path']; ?>/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['path']; ?>/assets/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['path']; ?>/assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo $GLOBALS['path']; ?>/assets/global/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['path']; ?>/assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>
<script src="<?php echo $GLOBALS['path']; ?>/assets/global/plugins/bootstrap-markdown/lib/markdown.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['path']; ?>/assets/global/plugins/bootstrap-markdown/js/bootstrap-markdown.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['path']; ?>/assets/global/plugins/bootstrap-summernote/summernote.min.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['path']; ?>/assets/global/plugins/bootstrap-toastr/toastr.min.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['path']; ?>/assets/admin/pages/scripts/ui-toastr.js" type="text/javascript"></script>
<?php
if(isset($includeJS)){
    foreach ($includeJS as $jsFile){
        echo "<script type=\"text/javascript\" src=\"".$GLOBALS['path'].$jsFile."\"></script>";
    }
}
?>

<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['path']; ?>/assets/global/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['path']; ?>/assets/admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['path']; ?>/assets/admin/pages/scripts/components-editors.js"></script>
<script src="<?php echo $GLOBALS['path']; ?>/assets/admin/pages/scripts/components-pickers.js"></script>
<script src="<?php echo $GLOBALS['path']; ?>/assets/highcharts/js/highcharts.js"></script>
<script src="<?php echo $GLOBALS['path']; ?>/assets/highcharts/js/modules/exporting.js"></script>
<script src="<?php echo $GLOBALS['path']; ?>/assets/js/ios_splash.js"></script>

<!-- END PAGE LEVEL SCRIPTS -->
<script type="text/javascript">
jQuery(document).ready(function() {     
    $('.btnReboot').bind('click', function(){
        $.ajax({
            url: "script/reboot.php",
            type: "POST",
            data: {
               type:  'device'
            },
            error: function(data){
                toastr.error("Une erreur est survenue");
            },
            complete: function(data){
                if(data == "rebooting"){
                    toastr.info("Redémarrage en cours");
                } else {
                    toastr.error("Une erreur est survenue");
                }
            }
        });
    });
    
   // initiate layout and plugins
   Metronic.init(); // init metronic core components
   Layout.init(); // init current layout
   //ComponentsEditors.init();
   UIToastr.init();
   if(typeof ui !== 'undefined' && ui=="validation"){
       FormValidation.init();
   }
   if(typeof ui !== 'undefined' && ui=="dropdown"){
       ComponentsDropdowns.init();
   }
   if(typeof ui !== 'undefined' && ui=="blockui"){
       UIBlockUI.init();
   }
   if(typeof ui !== 'undefined' && ui=="wizard"){
       FormWizard.init();
   }
   if(typeof ui2 !== 'undefined' && ui2=="datatable"){
       //TableManaged.init();
   }
   //ComponentsPickers.init();
   
   /*if ($(this).attr('target') !== '_blank') {
        e.preventDefault();
        window.location = $(this).attr('href');
    }*/

});   

if(("standalone" in window.navigator) && window.navigator.standalone){

    var noddy, remotes = false;

    document.addEventListener('click', function(event) {

    noddy = event.target;

    while(noddy.nodeName !== "A" && noddy.nodeName !== "HTML") {
     noddy = noddy.parentNode;
     }

    if('href' in noddy && noddy.href.indexOf('http') !== -1 && (noddy.href.indexOf(document.location.host) !== -1 || remotes))
     {
     event.preventDefault();
     document.location.href = noddy.href;
     }

    },false);
 }
</script>
</body>
<!-- END BODY -->
</html>