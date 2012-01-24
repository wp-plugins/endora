<?php
/*
Plugin Name: Endora
Plugin URI:
Description: Plugin freehostingu Endora umožňující snadnou editaci a umístění reklamy
Version: 0b4
Author: Martin Zlámal
Author URI: http://www.zeminem.cz/
License: GPL2
*/
register_activation_hook(__FILE__, 'pluginInstall');
register_deactivation_hook(__FILE__, 'pluginUninstall');
wp_register_script(jscolor, plugins_url('jscolor.js', __FILE__));
add_action('admin_menu', 'endora_create_menu');
add_action('widgets_init', create_function('', 'register_widget("Endora_Widget");'));

function pluginInstall() {}

function pluginUninstall() {
	delete_option("endora-barva");/*zpětná vazba*/
	delete_option("widget_endora_widget");
	delete_option("endora-barva-1");
	delete_option("endora-barva-2");
	delete_option("endora-velikost");
}

/* Vytvoří položky hlavního menu */
function endora_create_menu() {
	add_menu_page('Endora Settings', 'Endora Settings', 'administrator', __FILE__, 'endora_settings_page', plugins_url('endora/endora.png', dirname(__FILE__)));
	add_action('admin_init', 'register_mysettings');
}

function register_mysettings() {
	register_setting('endora-settings-group', 'endora-barva-1');
	register_setting('endora-settings-group', 'endora-barva-2');
	register_setting('endora-settings-group', 'endora-velikost');
}

function endora_settings_page() {
wp_enqueue_script(jscolor, plugins_url('jscolor.js', __FILE__)); ?>
<div class="wrap">
<h2>Nastavení reklamy</h2>
<form method="post" action="options.php">
	<?php settings_fields('endora-settings-group' );
	do_settings_fields('endora-settings-group', 'endora-settings-group' ); ?>
	<table width="400">
        <tr valign="top">
        <th scope="row">Barva textu</th>
        <td><input type="text" name="endora-barva-1" value="<?php echo get_option('endora-barva-1'); ?>" class="color {pickerMode:'HVS',hash:true,pickerFaceColor:'transparent',pickerBorder:0,pickerInsetColor:'black'}" /></td>
		<td>Např. #000000</td>
        </tr>
        <tr valign="top">
        <th scope="row">Barva pozadí</th>
        <td><input type="text" name="endora-barva-2" value="<?php echo get_option('endora-barva-2'); ?>" class="color {pickerMode:'HSV',hash:true,pickerFaceColor:'transparent',pickerBorder:0,pickerInsetColor:'black'}" /></td>
		<td>Např. #FFFFFF</td>
        </tr>
        <tr valign="top">
        <th scope="row">Velikost písma</th>
        <td><select name="endora-velikost" style="width: 189px">
			<?php
			for($i=0;$i<=10;$i=$i+2){
				$j = $i + 10;
				echo '<option value="'.$j.'" ';
				if($j == get_option('endora-velikost')) echo 'selected="yes"';
				echo '>'.$j.' px</option>';
			} ?>
		</select></td>
		<td>Např. 15 px</td>
        </tr>
	</table>
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
</form>
<h2>Náhled reklamy</h2>
<div style="color:<?php echo get_option('endora-barva-1'); ?>; background-color:<?php echo get_option('endora-barva-2'); ?>; font-size:<?php echo get_option('endora-velikost'); ?>px;"><a>Trenérská licence</a> pro trenéry kulturistiky|Novinka: <a>Jan Kraus</a> má svůj magazín|Zdravotní <a>matrace</a>, které napomáhají od bolesti zad - matrace UNAR|<b>Oblékáme se stylově</b> - <a>oblečení</a> a hip hop oblečení|Nábytek, <a>židle</a> a nábytkové doplňky na lino.cz</div>
<h2>Reálný náhled reklamy</h2>
<iframe src="<?php echo get_bloginfo('siteurl'); ?>/#endora" width="100%" height="500"></iframe>
</div><!--wrap-->
<?php } //endora_settings_page

/**
 * Endora_Widget Class
 */
class Endora_Widget extends WP_Widget {
	/** constructor */
	function __construct() { parent::WP_Widget( /* Base ID */'endora_widget', /* Name */'Endora reklama', array( 'description' => 'Umístí reklamu freehostingu Endora na správné místo' ) ); }

	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; }?>
		<div id="endora" style="color:<?php echo get_option('endora-barva-1'); ?>; background-color:<?php echo get_option('endora-barva-2'); ?>; font-size:<?php echo get_option('endora-velikost'); ?>px;"><endora></div>
		<?php echo $after_widget;
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
			$title = __( 'Reklama freehostingu Endora', 'text_domain' );
		}
		?><p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<?php 
	}
}// class Endora_Widget
?>