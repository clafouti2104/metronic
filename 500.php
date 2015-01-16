<?php
$includeCSS = $includeJS = array();
$includeCSS[] = "/assets/admin/pages/css/error.css";

include "modules/header.php";
include "modules/sidebar.php";

$GLOBALS["dbconnec"] = connectDB();
?>
<!-- BEGIN PAGE -->
<div class="page-content-wrapper">
<div class="page-content">
    <div class="row">
        <div class="col-md-12 page-500">
            <div class=" number">
                     500
            </div>
            <div class=" details">
                <h3>Oups! Une erreur est survenue lors de l'affichage de cette page.</h3>
                <p>
                    Nous sommes en train de corriger le problèmee!<br/>
                    Veuillez réessayer plus tard.<br/><br/>
                </p>
            </div>
        </div>
    </div>
</div>
</div>
<?php
include "modules/footer.php";
?>