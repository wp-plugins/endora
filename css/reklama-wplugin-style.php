<?php
// We'll be outputting CSS
header('Content-type: text/css');
require_once('../../../../wp-load.php');
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
<?php /* konec */ ?>