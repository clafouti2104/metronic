<?php

?>
<style type="text/css" media="screen">
#circle2 {
    background: none repeat scroll 0 0 red;
    height: 125px;
    width: 125px;
}
.circle {
    border-radius: 50% !important;
    display: inline-block;
    margin-right: 20px;
}
#advanced {
	width: 100%;
	height: 100%;
	
	background-image: -moz-radial-gradient(45px 45px 45deg, circle cover, yellow 0%, orange 100%, red 95%);
	background-image: -webkit-radial-gradient(45px 45px, circle cover, yellow, orange);
	background-image: radial-gradient(45px 45px 45deg, circle cover, yellow 0%, orange 100%, red 95%);
	
	/*animation-name: spin; 
	animation-duration: 3s; 
	animation-iteration-count: infinite; 
	animation-timing-function: linear;*/
}
</style>

<div class="circle" id="advanced"> </div>