<?php
/*
Plugin Name: Endora
Plugin URI: http://www.zeminem.cz/endora-plugin/
Description: Plugin freehostingu Endora umožňující snadnou editaci a umístění reklamy
Version: 0b5
Author: Martin Zlámal
Author URI: http://www.zeminem.cz/
License: GPLv2
*/
register_activation_hook(__FILE__, 'pluginInstall'); /* v2.0 */
register_deactivation_hook(__FILE__, 'pluginUninstall'); /* v2.0 */
wp_register_script(jscolor, plugins_url('jscolor.js', __FILE__)); /* v2.6 */
wp_register_style('styleSheet', plugins_url('endora-style.php', __FILE__)); /* v2.1 */
	wp_enqueue_style('styleSheet'); /* v2.1 */
add_action('admin_menu', 'endora_create_menu'); /* v1.2.0 */
add_action('widgets_init', create_function('', 'register_widget("Endora_Widget");'));
add_action('contextual_help', 'contextual_help');
/*
Nelíbí se ti jak je plugin naprogramovaný? Pamatuj, že důležité ja aby fungoval, ne aby měl hezká střeva... ;) 
*/
function pluginInstall() {
	add_option('endora-barva-1', '#000000'); /* text | v? */
	add_option('endora-barva-2', '#FFFFFF'); /* pozadi */
	add_option('endora-velikost', '14'); /* velikost pisma */
}

function pluginUninstall() {
	delete_option("endora-barva"); /* zpětná vazba z beta | v? */
	delete_option("widget_endora_widget");
	delete_option("endora-barva-1");
	delete_option("endora-barva-2");
	delete_option("endora-velikost");
	delete_option("endora-zarovnani");
	delete_option("endora-pismo");
}

/* Vytvoří položky hlavního menu */
function endora_create_menu() {
	add_menu_page('Endora Settings', 'Endora Settings', 'administrator', __FILE__, 'endora_settings_page', plugins_url('endora/endora.png', dirname(__FILE__))); /* v? */
	add_action('admin_init', 'register_mysettings'); /* v1.2.0 */
}

function register_mysettings() {
	register_setting('endora-settings-group', 'endora-barva-1'); /* v2.7.0 */
	register_setting('endora-settings-group', 'endora-barva-2');
	register_setting('endora-settings-group', 'endora-velikost');
	register_setting('endora-settings-group', 'endora-zarovnani');
	register_setting('endora-settings-group', 'endora-pismo');
}

function endora_settings_page() {
wp_enqueue_script(jscolor, plugins_url('jscolor.js', __FILE__)); ?>
<div class="wrap">
<h2>Nastavení reklamy</h2>
<div id="message" class="updated"><p>Nezapomeňte, že toto nastavení má smysl pouze pokud je Endora widget <a href="<?php echo get_bloginfo('siteurl').'/wp-admin/widgets.php'; ?>">umístěn</a>.</p></div>
<form method="post" action="options.php" name="nastaveni">
	<?php settings_fields('endora-settings-group' );
	do_settings_fields('endora-settings-group', 'endora-settings-group' ); ?>
	<table width="500">
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
        <td><select name="endora-velikost" style="width: 188px" OnChange="document.nastaveni.submit()">
			<?php
			for($i=0;$i<=10;$i=$i+2){
				$j = $i + 10;
				echo '<option value="'.$j.'" ';
				if($j == get_option('endora-velikost')) echo 'selected="yes"';
				echo '>'.$j.' px</option>';
			} ?>
		</select></td>
        </tr>
		<tr valign="top">
        <th scope="row">Zarovnání reklamy</th>
        <td><select name="endora-zarovnani" style="width: 188px" OnChange="document.nastaveni.submit()">
			<option value="left" <?php if(get_option('endora-zarovnani')=='left')echo'selected="yes"'; ?>>Left</option>
			<option value="right" <?php if(get_option('endora-zarovnani')=='right')echo'selected="yes"'; ?>>Right</option>
			<option value="center" <?php if(get_option('endora-zarovnani')=='center')echo'selected="yes"'; ?>>Center</option>
		</select></td>
        </tr>
		<tr valign="top">
        <th scope="row">Písmo reklamy</th>
        <td><select name="endora-pismo" style="width: 188px" OnChange="document.nastaveni.submit()">
			<option value="sans-serif" <?php if(get_option('endora-pismo')=='sans-serif')echo'selected="yes"'; ?>>Arial – Helvetica</option>
			<option value="Palatino, 'palatino linotype', serif" <?php if(get_option('endora-pismo')=='Palatino, \'palatino linotype\', serif')echo'selected="yes"'; ?>>Palatino</option>
			<option value="'Comic Sans MS', 'Sand CE', fantasy" <?php if(get_option('endora-pismo')=='\'Comic Sans MS\', \'Sand CE\', fantasy')echo'selected="yes"'; ?>>Comic Sans</option>
			<option value="monospace" <?php if(get_option('endora-pismo')=='monospace')echo'selected="yes"'; ?>>Courier</option>
			<option value="Verdana, 'Geneva CE', lucida, sans-serif" <?php if(get_option('endora-pismo')=='Verdana, \'Geneva CE\', lucida, sans-serif')echo'selected="yes"'; ?>>Verdana</option>
		</select></td>
		<td>
		<?php
		$pismo=get_option('endora-pismo');
		switch($pismo){
			case'sans-serif':echo'základní bezpatkové písmo';break;
			case'Palatino, \'palatino linotype\', serif':echo'elegantní patkové písmo';break;
			case'\'Comic Sans MS\', \'Sand CE\', fantasy':echo'veselé oblé znaky';break;
			case'monospace':echo'neproporciální písmo';break;
			case'Verdana, \'Geneva CE\', lucida, sans-serif':echo'o 10% širší obdoba Tahomy';break;
		}
		?>
		</td>
        </tr>
	</table>
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
</form>
<div id="message" class="updated"><p>Pokud je reklama nastavena v administračním rozhraní Endory, nastavení na této stránce <strong>nebude</strong> fungovat!</p><p><a class="button-secondary" href="https://webadmin.endora.cz/" title="Administrace">Ručně deaktivovat reklamu v administraci Endora!</a></p><p>Deaktivování znamená v administraci nastavit:<br />1) používat styl webu<br />2) <b>necentrovat</b> reklamu</p></div>
<h2>Reálný náhled reklamy</h2>
<iframe src="<?php echo get_bloginfo('siteurl'); ?>/#endora-wp" width="100%" height="200"></iframe>
<div id="message" class="updated"><p>Nereaguje reklama na změny? Přesvědčte se, že máte v administraci Endora vypnuté nastavení reklamy a nastavujete ji pouze pomocí toho pluginu.<br />Našli jste chybu? <b>Nebojte se</b> to oznámit na email <b>mrtnzlml@gmail.com</b>. Na opravě se pak bude rychle pracovat, ale musím o ní vědět.</p></div>
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
		<div id="endora-wp"><endora></div>
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
function contextual_help($text) {
$screen = $_GET['page'];
if ($screen == 'endora/endora.php') {
	$text = "<h5>Potřebujete více informací, nebo rad ohledně tohoto pluginu?</h5>";
	$text .= "<p>Obraťte se ne fórum podpory freehostingu <a href=\"http://www.endora.cz/\"Endora</p>";
	$text .= "<a href=\"http://podpora.endora.cz/\">Fórum podpory</a>";
	$text .= "<h5>Další užitečné odkazy</h5>";
	$text .= "<a href=\"http://www.endora.cz/\">freehosting Endora</a><br /><a href=\"http://webadmin.endora.cz/\">administrace Endory</a>";
}return $text;}
?>