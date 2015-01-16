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
        <div class="col-md-12 page-404">
            <div class="number">
                 404
            </div>
            <div class="details">
                <h3>Oups! Vous êtes perdu.</h3>
                <p>
                    Nous ne pouvons trouvé la page que vous cherchez.<br/>
                    <a href="index.php">Retour à l'accueil </a>
                </p>
            </div>
        </div>
    </div>
</div>
</div>
<?php
include "modules/footer.php";
?>