<?php
// CSS output
header('Content-type: text/css');
require_once('../../../../wp-load.php'); /* get_option(); */
$reklama = get_option('endora_reklama');
$api = get_option('endora_api');
?>
#reklama-wplugin {
	color: <?php echo $reklama['barva1']; ?> !important;
	background-color: <?php if($reklama['transparent']==1) echo 'transparent'; else echo $reklama['barva3']; ?> !important;
	font-size: <?php echo $reklama['velikost']; ?>px !important;
	text-align: <?php echo $reklama['zarovnani']; ?> !important;
	font-family: <?php echo $reklama['pismo']; ?> !important;
}

#reklama-wplugin a {
	color: <?php echo $reklama['barva2']; ?> !important;
}

#info-wplugin-admin {
	color: <?php echo $api['barva1']; ?> !important;
	position: absolute;
	top: 75px;
	right: 0%;
	padding: 15px;
	background-color: <?php echo $api['barva2']; ?> !important;
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
	color: <?php echo $api['barva1']; ?> !important;
	background-color: <?php if($api['transparent']==1) echo 'transparent'; else echo $api['barva2']; ?> !important;
	<?php if($api['padding']==1) { echo'padding: 10px;'; } ?>
}

.meter {
	height: 15px;
	position: relative;
	background-color: <?php echo $api['barva3']; ?> !important;
	padding: 2px;
	<?php if($api['rohy']=='1') {
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
	<?php if($api['rohy']==1) {
	echo '-moz-border-radius: 25px;
	-webkit-border-radius: 25px;
	border-radius: 25px;';
	} ?>
}

.green > span {	background-color: <?php echo $api['barva6']; ?> !important; }
.orange > span { background-color: <?php echo $api['barva5']; ?> !important; }
.red > span { background-color: <?php echo $api['barva4']; ?> !important; }

.poznamka {
	color: #888888;
	width: 250px;
}
<?php /* konec */ ?>