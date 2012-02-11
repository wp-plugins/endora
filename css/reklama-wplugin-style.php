<?php
// CSS output
header('Content-type: text/css');
require_once('../../../../wp-load.php'); /* get_option(); */
?>
#reklama-wplugin {
	color: <?php echo get_option('endora-barva-1'); ?> !important;
	background-color: <?php echo get_option('endora-barva-3'); ?> !important;
	font-size: <?php echo get_option('endora-velikost'); ?>px !important;
	text-align: <?php echo get_option('endora-zarovnani'); ?> !important;
	font-family: <?php echo get_option('endora-pismo'); ?> !important;
}

#reklama-wplugin a {
	color: <?php echo get_option('endora-barva-2'); ?> !important;
}

.poznamka {
	color: #888888;
	width: 250px;
}

#info-wplugin-admin {
	color: <?php echo get_option('endora-api-barva-1'); ?> !important;
	position: absolute;
	top: 75px;
	right: 0%;
	padding: 15px;
	background-color: <?php echo get_option('endora-api-barva-2'); ?> !important;
	-webkit-border-top-left-radius: 10px;
	-webkit-border-bottom-left-radius: 10px;
	-moz-border-radius-topleft: 10px;
	-moz-border-radius-bottomleft: 10px;
	border-top-left-radius: 10px;
	border-bottom-left-radius: 10px;
	width: 300px;
	z-index: -1;
}

#info-wplugin {
	color: <?php echo get_option('endora-api-barva-1'); ?> !important;
	background-color: <?php echo get_option('endora-api-barva-2'); ?> !important;
	<?php if(get_option('endora-api-padding')=='padding') { echo'padding: 10px;'; } ?>
}

.meter {
	height: 15px;
	position: relative;
	background-color: <?php echo get_option('endora-api-barva-3'); ?> !important;
	padding: 2px;
	<?php if(get_option('endora-api-radius')=='radius') {
	echo '-moz-border-radius: 25px;
	-webkit-border-radius: 25px;
	border-radius: 25px;';
	} ?>
}

.meter > span {
	display: block;
	height: 100%;
	background-color: green;
	position: relative;
	overflow: hidden;
	<?php if(get_option('endora-api-radius')=='radius') {
	echo '-moz-border-radius: 25px;
	-webkit-border-radius: 25px;
	border-radius: 25px;';
	} ?>
}

.green > span {	background-color: <?php echo get_option('endora-api-barva-6'); ?> !important; }
.orange > span { background-color: <?php echo get_option('endora-api-barva-5'); ?> !important; }
.red > span { background-color: <?php echo get_option('endora-api-barva-4'); ?> !important; }
<?php /* konec */ ?>