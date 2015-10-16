<?php
include '../../tools/config.php';
include_once "../../models/Page.php";
$GLOBALS["dbconnec"] = connectDB();

$output="";
if(!isset($_POST["idPageParent"]) || $_POST["idPageParent"]==""){
    echo "error";
    return FALSE;
}

//$pageParent=Page::getPage($_POST["idPageParent"]);
$output .= "$('#dashboard').empty();";
$output .= "$('.tiles').empty();";
$pagesFilles=Page::getPageFilles($_POST["idPageParent"]);
$output .= "$('#dashboard').append('";
foreach($pagesFilles as $pagesFille){
    $output .= "<div class=\"col-lg-4 col-md-4 col-sm-6 col-xs-12\" onclick=\"location.href=\'page.php?pageId=".$pagesFille->id."\'; \">";
    $output .= "<div class=\"dashboard-stat ".$pagesFille->color." \">";
    $output .= "<div class=\"visual\">";
    $output .= "<i class=\"fa ".$pagesFille->icon."\" ></i>";
    $output .= "</div>";
    $output .= "<div class=\"details\">";
    $output .= "<div class=\"number\">".$pagesFille->name."</div>";
    $output .= "<div class=\"desc\">".$pagesFille->description."</div>";
    $output .= "</div>";
    $output .= "<a class=\"more\" href=\"#\">";
    $output .= "&nbsp;<i class=\"m-icon-swapright m-icon-white\"></i>";
    $output .= "</a>";
    $output .= "</div>";
    $output .= "</div>";
    
}
$output .= "');";
echo $output;
/*
                                        
                                                        
                                                <a class="more" href="#">
                                                    &nbsp;<i class="m-icon-swapright m-icon-white"></i>
                                                </a>						
                                        
                                
 */

?>