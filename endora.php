<?php
/*
Plugin Name: Endora
Plugin URI:
Description: Plugin freehostingu Endora umožňující snadnou editaci a umístění reklamy.
Version: 11.02.2012
Author: Martin Zlámal
Author URI: http://www.zeminem.cz/
License: GPLv2
*/
// REGISTER HOOKS
register_activation_hook(__FILE__, 'endora_pluginInstall'); /* v2.0 */
register_deactivation_hook(__FILE__, 'endora_pluginUninstall'); /* v2.0 */
// STYLES & SCRIPTS
wp_enqueue_style('wplugin-style', plugins_url('css/reklama-wplugin-style.php', __FILE__)); /* v2.1 */
wp_enqueue_script('wplugin-script', plugins_url('js/reklama-js.js', __FILE__)); /* v2.6 */
// ACTIONS
add_action('admin_menu', 'endora_create_menu'); /* v1.2.0 */
add_action('widgets_init', create_function('', 'register_widget("Widg01End");'));
add_action('widgets_init', create_function('', 'register_widget("Widg02End");'));
add_action('contextual_help', 'endora_contextual_help');
add_action('wp_dashboard_setup', 'endora_rss');
// FILTERS
add_filter('plugin_action_links', /*fce*/'endora_setting_link', 'administrator', 2);

/*************
 *           *
 * FUNCTIONS *
 *           *
 *************/

/**
 * Akce pri instalaci resp. aktivování pluginu.
 * Uloží defaultní hodnoty do databáze.
 * @version 11.02.2012
 */
function endora_pluginInstall() {
	add_option('endora-barva-1', '#FF0000'); /* text | v? */
	add_option('endora-barva-2', '#00FF00'); /* odkazy */
	add_option('endora-barva-3', '#0000FF'); /* pozadi */
	add_option('endora-api-barva-1', '#000000'); /* text */
	add_option('endora-api-barva-2', '#DDDDDD'); /* pozadi */
	add_option('endora-api-barva-3', '#BBBBBB'); /* progress */
	add_option('endora-api-barva-4', '#FF0000'); /* progress-red */
	add_option('endora-api-barva-5', '#FFA500'); /* progress-orange */
	add_option('endora-api-barva-6', '#008000'); /* progress-green */
	add_option('endora-velikost', '14'); /* velikost pisma */
	add_option('endora-api-disk', 'true');
	add_option('endora-api-traf', 'true');
	add_option('endora-api-graf', 'true');
	add_option('endora-api-radius', 'true');
	add_option('endora-api-mod', 'true');
	add_option('endora-rss-count', '5');
}

/**
 * Akce provedné pri odinstalování resp. deaktivovování pluginu
 * @version 11.02.2012
 */
function endora_pluginUninstall() {
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
	/* if(get_option('???volba-smazat-uplne???') == true) delete_option('widget_widg01end'); */
	/* delete_option('widget_widg02end'); */
}

/**
 * Registrace skupiny hodnot do databáze
 * @version 11.02.2012
 */
function endora_register_settings() {
	register_setting('endora-settings-group1', 'endora-barva-1'); /* v2.7.0 */
	register_setting('endora-settings-group1', 'endora-barva-2');
	register_setting('endora-settings-group1', 'endora-barva-3');
	register_setting('endora-settings-group1', 'endora-velikost');
	register_setting('endora-settings-group1', 'endora-zarovnani');
	register_setting('endora-settings-group1', 'endora-pismo');
	/* endora-settings-group2 */
	register_setting('endora-settings-group2', 'endora-api');
	register_setting('endora-settings-group2', 'endora-api-disk');
	register_setting('endora-settings-group2', 'endora-api-traf');
	register_setting('endora-settings-group2', 'endora-api-prog');
	register_setting('endora-settings-group2', 'endora-api-graf');
	//register_setting('endora-settings-group2', 'endora-api-mod');
	register_setting('endora-settings-group2', 'endora-api-radius');
	register_setting('endora-settings-group2', 'endora-api-padding');
	register_setting('endora-settings-group2', 'endora-api-barva-1'); /* text */
	register_setting('endora-settings-group2', 'endora-api-barva-2'); /* pozadi */
	register_setting('endora-settings-group2', 'endora-api-barva-3'); /* progress */
	register_setting('endora-settings-group2', 'endora-api-barva-4'); /* progress-red */
	register_setting('endora-settings-group2', 'endora-api-barva-5'); /* progress-orange */
	register_setting('endora-settings-group2', 'endora-api-barva-6'); /* progress-green */
	/* endora-settings-group3-rss */
	register_setting('endora-settings-group3', 'endora-rss-count');
}

/**
 * Vytvoří menu v administraci Wordpress
 * @version 01.01.2012
 */
function endora_create_menu() {
	$endora_settings = __('Endora Nastavení');
	add_menu_page($endora_settings, $endora_settings, 'administrator', __FILE__, /*fce*/'endora_settings_page', plugins_url('endora/img/endora.png', dirname(__FILE__)));
	add_action('admin_init', /*fce*/'endora_register_settings'); /* v1.2.0 */
}

/**
 * Stránka Endora Settings (admin)
 * @version 10.02.2012
 */
function endora_settings_page() {
	wp_enqueue_script('jscolor', plugins_url('js/jscolor.js', __FILE__)); /* v2.6 */
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
}

/**
 * Odkaz na nastavení pluginu z přehledu (admin)
 * @version 11.02.2012
 */
function endora_setting_link($links, $file) {
	static $this_plugin;
    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    } if ($file == $this_plugin) {
		$settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=endora/endora.php">Nastavení</a>';
		array_unshift($links, $settings_link);
	}
	return $links;
}

/**
 * Funkce přidávající RSS widget na dashboard. RSS je funkce pro zobrazení kanálu a RSS_SETTING je nastavení kanálu.
 * @version 11.02.2012
 */
function endora_rss() {
	wp_add_dashboard_widget('endora-rss', 'Endora novinky', /*fce1*/'rss');
}

/**
 * Zobrazuje RSS kanál.
 * @version 01.01.2012
 */
function rss() {
	$items = get_option('endora-rss-count');
	wp_widget_rss_output('http://www.endora.cz/rss', array('items'=>$items, 'show_summary'=>1, 'show_author'=>0, 'show_date'=>1));
}

/**
 * Vrací informace získané pomocí API klíče.
 * @version 10.02.2012
 */
function curl($api, $graf=1, $disk, $traf, $prog) {
	if (!function_exists('curl_init')) { wp_die('Sorry cURL is not installed!'); }
	$c = curl_init();
	curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($c, CURLOPT_URL, 'https://webadmin.endora.cz/api/xml/key/'.$api);
	$json = curl_exec($c);
	curl_close($c);
	$json = json_decode($json, true);
	if($json['ad'] == 'default') update_option('endora-api-mod', 'false'); else update_option('endora-api-mod', 'true');
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
		if($json['variant']=='default') {
			$program = 'Free';
			echo 'Program: ' . $program . '<br />';
		} else {
			echo 'Program: ' . $program . ' (platný do ' . $json['variant']['expire'] . ')<br />';
		}
	}
}

/**
 * Vykresluje progress_bar naplnění limitů.
 * @version 10.02.2012
 */
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

/**
 * Kontextová nabídka HELP
 * 1) admin - Endora Settings
 * 2) admin - RSS
 * @version 01.01.2012
 */
function endora_contextual_help() {
	$screen = $_GET['page'];
	if($screen == 'endora/endora.php') {
		$informace = '<p>'.__('Tento plugin byl vytvořen pro lepší ovládání reklamy vkládané freehostingem <a href="http://www.endora.cz/" target="_blank">Endora</a>.').'</p>';
		$informace .= '<p>'.__('Hlavní výhodou je, že reklama zapadne přesně do designu webu a nijak nenarušuje jeho vzhled. K tomu slouží jedinečný systém widgetů, které WordPress nabízí.').'</p>';
		$nastaveni = '<p>'.__('Nastavení pluginu je velmi proté. Využívá se několika vstupních polí pro barvy, velikosti a například písmo.').'</p>';
		$nastaveni .= '<p>'.__('Toto nastavení má smysl pouze tehdy, je-li umístě widget. Stejně tak reklama nebude reagovat na nastavení ve WordPressu, pokud bude reklama nastavena prostřednictvím administračního rozhraní na Endoře.').'</p>';
		$kontakt = '<p>'.__('V případě problémů s pluginem mě neváhejte kontaktovat na email <b>mrtnzlml@gmail.com</b> a já se problém pokusím co nejdříve vyřešit.').'</p>';
		get_current_screen()->add_help_tab( array('id' => 'endora-help-informace', 'title' => __( 'Informace' ), 'content' => $informace,) );
		get_current_screen()->add_help_tab( array('id' => 'endora-help-nastaveni', 'title' => __( 'Nastavení' ), 'content' => $nastaveni,) );
		get_current_screen()->add_help_tab( array('id' => 'endora-help-kontakt', 'title' => __( 'Kontakt' ), 'content' => $kontakt,) );
	}
}

/*************
 *           *
 *  WIDGETS  *
 *           *
 *************/
class Widg01End extends WP_Widget {
	function __construct() { parent::WP_Widget( /* Base ID */'widg01end', /* Name */'Endora reklama', array( 'description' => __('Umístí reklamu freehostingu Endora na správné místo'))); }
	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; }
		echo '<script>window.onload = scroll;</script>';
		echo '<div id="reklama-wplugin"><endora></div>';
		echo $after_widget;
	}
	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}
	/** @see WP_Widget::form */
	function form( $instance ) {
		if ( $instance ) {
			$title = esc_attr( $instance[ 'title' ] );
		}
		else {
			$title = __( 'Reklama', 'text_domain' );
		}
		?><p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<?php 
	}	
}
class Widg02End extends WP_Widget {
	function __construct() { parent::WP_Widget( /* Base ID */'widg02end', /* Name */'Endora informace', array( 'description' => __('Umístí na web zajímavé informace jako je například obsazené místo, traffic webu, nebo jeho program.'))); }
	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; }
		echo '<div id="info-wplugin">';
			$api = get_option('endora-api');
			$graf = get_option('endora-api-graf');
			$disk = get_option('endora-api-disk');
			$traf = get_option('endora-api-traf');
			$prog = get_option('endora-api-prog');
			//curl($api, $graf, $disk, $traf, $prog)
			curl($api, $graf, $disk, $traf, $prog);
		echo '</div>';
		echo $after_widget;
	}
	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}
	/** @see WP_Widget::form */
	function form( $instance ) {
		if ( $instance ) {
			$title = esc_attr( $instance[ 'title' ] );
		}
		else {
			$title = __( 'Informace', 'text_domain' );
		}
		?><p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<?php 
	}	
}
?>