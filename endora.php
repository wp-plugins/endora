<?php
/*
Plugin Name: Endora
Plugin URI: http://www.zeminem.cz/endora-plugin/
Description: Plugin freehostingu Endora umožňující snadnou editaci a umístění reklamy
Version: 1.1.beta
Author: Martin Zlámal
Author URI: http://www.zeminem.cz/
License: GPLv2
*/
register_activation_hook(__FILE__, 'pluginInstall'); /* v2.0 */
register_deactivation_hook(__FILE__, 'pluginUninstall'); /* v2.0 */
//wp_register_script('jscolor', plugins_url('js/jscolor.js', __FILE__)); /* v2.6 */
wp_enqueue_style('wplugin-style', plugins_url('css/reklama-wplugin-style.php', __FILE__)); /* v2.1 */
add_action('admin_menu', 'endora_create_menu'); /* v1.2.0 */
add_action('widgets_init', create_function('', 'register_widget("Widg01End");'));
add_action('contextual_help', 'contextual_help');

/**
 * Akce provedené při aktivování pluginu. Zapisuje do databáze defaultní hodnoty.
 */
function pluginInstall() {
	add_option('endora-barva-1', '#FF0000'); /* text | v? */
	add_option('endora-barva-2', '#00FF00'); /* odkazy */
	add_option('endora-barva-3', '#0000FF'); /* pozadi */
	add_option('endora-velikost', '14'); /* velikost pisma */
}

/**
 * Akce provedené při deaktivování pluginu. Maže veškeré hodnoty zapsané do databáze.
 * Pro zachování zpětné kompatibility odstraňuje i hodnoty používané beta verzích pluginu.
 */
function pluginUninstall() {
	delete_option("endora-barva"); /* zpetna vazba z beta | v? */
	delete_option("widget_endora_widget"); /* zpetna vazba z beta */
	delete_option("endora-barva-1");
	delete_option("endora-barva-2");
	delete_option("endora-barva-3");
	delete_option("endora-velikost");
	delete_option("endora-zarovnani");
	delete_option("endora-pismo");
	/* delete_option("widget_widg01end"); */
}

/**
 * Vytvoří položky hlavního menu a následně aktivuje registraci hodnot do databáze.
 */
function endora_create_menu() {
	add_menu_page('Endora Settings', 'Endora Settings', 'administrator', __FILE__, /*fce*/'endora_settings_page', plugins_url('endora/img/endora.png', dirname(__FILE__))); /* v? */
	add_action('admin_init', 'register_mysettings'); /* v1.2.0 */
}

/**
 * Registruje hodnoty do jedné skupiny. Připraveno k použití a zapsání do databáze.
 */
function register_mysettings() {
	register_setting('endora-settings-group', 'endora-barva-1'); /* v2.7.0 */
	register_setting('endora-settings-group', 'endora-barva-2');
	register_setting('endora-settings-group', 'endora-barva-3');
	register_setting('endora-settings-group', 'endora-velikost');
	register_setting('endora-settings-group', 'endora-zarovnani');
	register_setting('endora-settings-group', 'endora-pismo');
}

/**
 * Html samotné stránky v administraci ("Endora Settings")
 */
function endora_settings_page() {
	wp_enqueue_script('jscolor', plugins_url('js/jscolor.js', __FILE__)); /* v2.6 */ 
	include_once(ABSPATH . 'wp-content/plugins/endora/pages/endora_settings_page.html');
}

/**
 * Endora_Widget Class
 */
class Widg01End extends WP_Widget {
	/** constructor */
	function __construct() { parent::WP_Widget( /* Base ID */'widg01end', /* Name */'Endora reklama', array( 'description' => 'Umístí reklamu freehostingu Endora na správné místo' ) ); }
	
	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; }
		echo '<div id="reklama-wplugin"><endora>blabla<a>blabla</a>blabla</div>';
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
	
}// class Endora_Widget

/**
 * Kontextová nabídka "HELP" na stránce Endora Settings
 */
function contextual_help() {
	$screen = $_GET['page'];
	if ($screen == 'endora/endora.php') {
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
?>