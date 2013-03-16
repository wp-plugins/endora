<?php
/*
Plugin Name: Endora
Plugin URI:
Description: Plugin freehostingu Endora umožňující snadnou editaci a umístění reklamy.
Version: 15.03.2013
Author: Martin Zlámal
Author URI: http://www.zeminem.cz/
License: GPLv2
*/

/** @version 15.03.2013 - update by Roman Chlebnicek */

/** @version 12.02.2012 */
define(ENDORA_WIDGET_ID1, 'endora_widget_reklama');
define(ENDORA_WIDGET_ID2, 'endora_widget_info');

/** @version 14.02.2012 */
register_activation_hook(__FILE__, 'endora_pluginInstall');
register_deactivation_hook(__FILE__, 'endora_pluginUninstall');

/** @version 12.02.2012 */
wp_enqueue_style('wplugin-style', plugins_url('css/reklama-wplugin-style.php', __FILE__));
wp_enqueue_script('wplugin-script', plugins_url('js/reklama-js.js', __FILE__));

/** @version 12.02.2012 */
add_action('admin_menu', 'endora_create_menu');
add_action('plugins_loaded', 'endora_widget_init');
add_action('wp_dashboard_setup', 'endora_dash_rss'); //poradi dash widgetu urci i poradi na dasboardu (POZPATKU!)
add_action('wp_dashboard_setup', 'endora_dash_api');

/** @version 15.03.2013 */
function endora_pluginInstall() {
	if( is_plugin_active('endora-lite/endora.php') ){
		add_action('update_option_active_plugins', 'deactivate_endora_lite');
     }
/*
	delete_option('endora-barva-1');
	delete_option('endora-barva-2');
	delete_option('endora-barva-3');
	delete_option('endora-velikost');
	delete_option('endora-zarovnani');
	delete_option('endora-pismo');
	delete_option('endora-api');
	delete_option('endora-api-disk');
	delete_option('endora-api-traf');
	delete_option('endora-api-prog');
	delete_option('endora-api-graf');
	delete_option('endora-api-mod');
	delete_option('endora-api-radius');
	delete_option('endora-api-padding');
	delete_option('endora-api-barva-1');
	delete_option('endora-api-barva-2');
	delete_option('endora-api-barva-3');
	delete_option('endora-api-barva-4');
	delete_option('endora-api-barva-5');
	delete_option('endora-api-barva-6');
	delete_option('endora-rss-count');
	delete_option('widget_widg01end');
	delete_option('widget_widg02end');
*/
}

/** @version 15.03.2013 */
function endora_pluginUninstall() {
/*
	if( is_plugin_active('endora/endora.php') ){
		delete_option('endora_widget_reklama');
		delete_option('endora_widget_info');
		delete_option('endora_reklama');
		delete_option('endora_api');
	}
*/
	delete_option('endora_rss');
	delete_option('endora_dash_rss');
	delete_option('endora_dash_api');
}

/** @version 15.03.2013 */
function deactivate_endora_lite(){
	deactivate_plugins('endora-lite/endora.php');
}

/** @version 12.02.2012 */
function endora_create_menu() {
	add_menu_page('Endora Nastavení', 'Endora Nastavení', 'administrator', __FILE__, /*fce*/'endora_settings_page', plugins_url('endora/img/endora.png', dirname(__FILE__)));
}

/** @version 12.02.2012 */
function endora_settings_page() {
	wp_enqueue_script('jscolor', plugins_url('js/jscolor.js', __FILE__));
	$current = 'reklama';
	if(isset($_GET['tab'])) $current = $_GET['tab'];
	$tabs = array('reklama'=>'Nastavení reklamy', 'rss'=>'Archiv novinek', 'info'=>'Informace');
	echo '<img src="'.plugins_url('img/endora32.png', __FILE__).'" class="icon32">';
    echo '<h2 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?page=endora/endora.php&tab=$tab'>$name</a>";
    }
    echo '</h2>';
	if(($_GET['page'] == 'endora/endora.php') && ($_GET['tab'] == 'rss')) require_once('pages/endora_settings_page2.html');
	elseif(($_GET['page'] == 'endora/endora.php') && ($_GET['tab'] == 'info')) require_once('pages/endora_settings_page3.html');
	else require_once('pages/endora_settings_page1.html');
	if(($_GET['page'] == 'endora/endora.php') && ($_GET['zobraz'] == 'ne')) {
		$data['upraveno'] = 'nezobrazovat';
		update_option('endora_reklama', $data);
	}
}

/** @version 12.02.2012 */
function endora_widget_init() {
	// unique widget id | widget name | callback function | array option
	wp_register_sidebar_widget(ENDORA_WIDGET_ID1, 'Endora reklama', 'endora_widget_reklama', array('description'=>'Umístí reklamu freehostingu Endora na správné místo.'));
	wp_register_widget_control(ENDORA_WIDGET_ID1, '', 'endora_widget_reklama_control');
	wp_register_sidebar_widget(ENDORA_WIDGET_ID2, 'Endora informace', 'endora_widget_info', array('description'=>'Umístí na web zajímavé informace jako je například obsazené místo, traffic webu, nebo jeho program.'));
	wp_register_widget_control(ENDORA_WIDGET_ID2, '', 'endora_widget_info_control');
}
/** @version 12.02.2012 */
function endora_widget_reklama($args) {
	extract($args, EXTR_SKIP);
	echo $before_widget;
	echo $before_title;
	$title = get_option(ENDORA_WIDGET_ID1);
	echo $title['title'];
	echo $after_title;
	echo '<script>window.onload = scroll;</script><div id="reklama-wplugin"><endora></div>';
	echo $after_widget;
}
/** @version 12.02.2012 */
function endora_widget_info($args) {
	extract($args, EXTR_SKIP);
	echo $before_widget;
	echo $before_title;
	$title = get_option(ENDORA_WIDGET_ID2);
	echo $title['title'];
	echo $after_title;
	echo '<div id="info-wplugin">';
		$data = get_option('endora_api');
		if($data['api']==NULL) {
			echo 'Administrátor nezadal API klíč.';
		} elseif($data['graf']==NULL&&$data['disk']==NULL&&$data['traff']==NULL&&$data['prog']==NULL) {
			echo 'Administrátor nepovolil zobrazení žádné informace.';
		}
		//curl($api, $graf, $disk, $traf, $prog)
		curl($data['api'], $data['graf'], $data['disk'], $data['traff'], $data['prog']);
	echo '</div>';
	echo $after_widget;
}
/** @version 12.02.2012 */
function endora_widget_info_control() {
	endora_widget_reklama_control(true);
}

/** @version 12.02.2012 */
function endora_widget_reklama_control($informace=false) {
	$informace==false ? $options = get_option(ENDORA_WIDGET_ID1) : $options = get_option(ENDORA_WIDGET_ID2);
	if(!is_array($options)) { $informace==false ? $options = array('title'=>'Reklama') : $options = array('title'=>'Informace'); }
	if($_POST['endora_widget_reklama_control_submit']) {
		$options['title'] = htmlspecialchars($_POST['endora_widget_reklama_control_nadpis']);
		$informace==false ? update_option(ENDORA_WIDGET_ID1, $options) : update_option(ENDORA_WIDGET_ID2, $options);
	}?>
	<p>
		<label for="endora_widget_reklama_control_nadpis">Nadpis: </label>
		<input type="text" id="endora_widget_reklama_control_nadpis" name="endora_widget_reklama_control_nadpis" value="<?php echo $options['title'];?>" />
		<input type="hidden" id="endora_widget_reklama_control_submit" name="endora_widget_reklama_control_submit" value="x" />
	</p><?php
}

/** @version 14.02.2012 */
function endora_dash_rss() {
	wp_add_dashboard_widget('endora-rss', 'Endora novinky', /*fce1*/'endora_dash_rss_output', /*fce1*/'endora_dash_rss_setting'); seradDash('endora-rss');
}

/** @version 14.02.2012 */
function endora_dash_rss_output() {
	$data = get_option('endora_dash_rss');
	if(!is_array($data)) {
		$data = array('items'=>2, 'summary'=>1, 'author'=>0, 'date'=>1);
		update_option('endora_dash_rss', $data);
	}
	wp_widget_rss_output('http://endora.cz/rss/index2', array('items'=>$data['items'], 'show_summary'=>$data['summary'], 'show_author'=>$data['author'], 'show_date'=>$data['date']));
}

/** @version 14.02.2012 */
function endora_dash_rss_setting() {
	$options = get_option('endora_dash_rss');
	if($_POST['endora_dash_rss_send']) {
		$options['items'] = $_POST['endora_dash_rss_count'];
		$options['summary'] = $_POST['endora_dash_rss_nahled'];
		$options['date'] = $_POST['endora_dash_rss_datum'];
		update_option('endora_dash_rss', $options);
	}?>
	<p>
		Počet zde zobrazovaných novinek <select name="endora_dash_rss_count" style="width: 50px">
		<?php for ( $i = 1; $i <= 10; $i++ ) echo "<option value='$i' " . ( $options['items'] == $i ? "selected='selected'" : '' ) . ">$i</option>"; ?>
		</select><br />
		Zobrazovat náhled <input type="checkbox" name="endora_dash_rss_nahled" value="1"<?php if($options['summary']==1) echo ' checked'; ?> /><br />
		Zobrazovat datum <input type="checkbox" name="endora_dash_rss_datum" value="1"<?php if($options['date']==1) echo ' checked'; ?> /><br />
		<input type="hidden" id="endora_dash_rss_send" name="endora_dash_rss_send" value="x" />
	</p><?php
}

/** @version 15.02.2012 */
function endora_dash_api() {
	wp_add_dashboard_widget('endora-api', 'Endora informace', /*fce1*/'endora_dash_api_output', /*fce1*/'endora_dash_api_setting'); seradDash('endora-api');
}

/** @version 15.02.2012 */
function endora_dash_api_output() {
	$data = get_option('endora_api');
	$api = $data['api'];
	$data = get_option('endora_dash_api');
	if(!is_array($data)) {
		$data = array('api'=>$api, 'graf'=>1, 'disk'=>1, 'traf'=>1, 'prog'=>1);
		update_option('endora_dash_api', $data);
	}
	//curl($api, $graf, $disk, $traf, $prog)
	if($api!=NULL) {
		curl($api, $data['graf'], $data['disk'], $data['traf'], $data['prog']);
	} else {
		echo '<div style="color: red;">Nebyl zadán API klíč!</div>';
	}
	echo '<div style="float: right;">( <a href="?page=endora/endora.php&tab=info">administrace</a> )</div><br />';
}

/** @version 15.02.2012 */
function endora_dash_api_setting() {
	$options = get_option('endora_dash_api');
	if($_POST['endora_dash_api_send']) {
		$options['graf'] = $_POST['endora_dash_api_graf'];
		$options['disk'] = $_POST['endora_dash_api_disk'];
		$options['traf'] = $_POST['endora_dash_api_traf'];
		$options['prog'] = $_POST['endora_dash_api_prog'];
		update_option('endora_dash_api', $options);
	}?>
	<p>
		Zobrazovat informace o disku <input type="checkbox" name="endora_dash_api_disk" value="1"<?php if($options['disk']==1) echo ' checked'; ?> /><br />
		Zobrazovat informace o trafficu <input type="checkbox" name="endora_dash_api_traf" value="1"<?php if($options['traf']==1) echo ' checked'; ?> /><br />
		Zobrazovat informace o programu <input type="checkbox" name="endora_dash_api_prog" value="1"<?php if($options['prog']==1) echo ' checked'; ?> /><br />
		Zobrazovat grafické znázornění <input type="checkbox" name="endora_dash_api_graf" value="1"<?php if($options['graf']==1) echo ' checked'; ?> /><br />
		<input type="hidden" id="endora_dash_api_send" name="endora_dash_api_send" value="x" />
	</p><?php
}

/** @version 15.03.2013 */
function curl($api, $graf=1, $disk, $traf, $prog) {
	if (!function_exists('curl_init')) { wp_die('Sorry cURL is not installed!'); }
	$c = curl_init();
	curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($c, CURLOPT_URL, 'https://webadmin.endora.cz/api/xml/key/'.$api);
	$json = curl_exec($c);
	curl_close($c);
	$json = json_decode($json, true);
	$data = get_option('endora_reklama');
	if($data['upraveno']!='nezobrazovat') {
		if($json['ad']=='default') { $data['upraveno']='ne'; update_option('endora_reklama', $data); }
		elseif($json['ad']=='modified') { $data['upraveno']='ano'; update_option('endora_reklama', $data); }
	}
	if($disk) {
		echo 'Obsazené místo: ' . $json['diskspace']['current'] . ' / ' . $json['diskspace']['limit'] . '<br />';
		if($graf) {
			graf($json['diskspace']['current'], $json['diskspace']['limit']);
		}
	} if($traf) {
		echo 'Traffic: ' . $json['traffic']['current'] . ' / ' . $json['traffic']['limit'] . '<br />';
		if($graf) {
			graf($json['traffic']['current'], $json['traffic']['limit']);
		}
	} if($prog) {
		$program = $json['variant']['program'];
		if($program == 'Free' OR $program == 'Lite') {
			echo 'Program: ' . $program . '<br />';
		} else {
			echo 'Program: ' . $program . ' (platný do ' . MyDateToDate($json['variant']['expire']) . ')<br />';
		}
	}
}

/** @version 15.03.2013 */
function MyDateToDate ($datum = "") {
	if ($datum == "") return "-";
	$mydatum = explode("-", $datum);
	return (int)$mydatum['2'] . ".&nbsp;" . (int)$mydatum['1'] . ".&nbsp;" . (int)$mydatum['0'];  // vrati datum ve forme dd. mm. rrrr (j. n. Y)
}

/** @version 10.02.2012 */
function graf($str1, $str2) {
	$mb = str_replace('MB', '', $str1);
	$mb = str_replace(',', '.', $mb);
	$gb = str_replace('GB', '', $str2);
	$mb = $mb * 1024;
	$gb = $gb * 1048576;
	if($gb == 0) $gb = 1;
	$proc = ($mb / $gb) * 100;
	if($proc <= 60) $col = 'green';
	elseif($proc <= 80) $col = 'orange';
	else $col = 'red';
	echo '<div class="meter '.$col.'"><span style="width: '.$proc.'%"></span></div>';
}

/** @version 15.02.2012 */
function seradDash($id) {
	global $wp_meta_boxes;
	$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
	$widget_backup = array($id=>$normal_dashboard[$id]);
	unset($normal_dashboard[$id]);
	$sorted_dashboard = array_merge($widget_backup, $normal_dashboard);
	$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
}
?>