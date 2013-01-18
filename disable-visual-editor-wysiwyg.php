<?php
/*
Plugin Name: Disable Visual Editor WYSIWYG
Version: 1.4.1
License: GPL2
Plugin URI: http://discordiadesign.com
Author: Stanislav Mandulov
Author URI: http://stanxp.com/
Description: This plugin will disable the visual editor for selected page(s)/post(s). The idea behind this came after i had to keep the html intact by the tinymce editor whenever i switched back to Visual tab in the editor.
* 
    Copyright 2010  DiscordiaDesign.com  (email : office@discordiadesign.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action( 'admin_init', 'dvew_add_custom_box', 1 );
add_action( 'save_post', 'dvew_save_post' );

add_filter( 'wp_default_editor', 'dvew_switch_editor' );
add_filter( 'admin_footer', 'dvew_admin_edit_page_js', 99);

function dvew_switch_editor($content){
	if(isset($_GET['post']) && get_post_meta($_GET['post'], 'dvew_checkbox') != false){
		return 'html';
	}
	return $content;
}


function dvew_admin_edit_page_js(){
	if(isset($_GET['post']) && get_post_meta($_GET['post'], 'dvew_checkbox') != false){
		echo '  <style type="text/css">
				a#content-tmce, a#content-tmce:hover{
					display:none;
				}
				</style>';
		echo '	<script type="text/javascript">
			 	jQuery(document).ready(function(){
					document.getElementById("content-tmce").onclick = \'none\';
			 	});
			 	</script>';
	}
}

function dvew_add_custom_box() {
    add_meta_box( 
        'dvew_sectionid',
        __( 'Visual Editor', 'dvew_textbox' ),
        'dvew_custom_box',
        'post',
		'side',
		'default'
    );
    add_meta_box(
        'dvew_sectionid',
        __( 'Visual Editor', 'dvew_textbox' ), 
        'dvew_custom_box',
        'page',
		'side',
		'default'
    );
}


function dvew_custom_box() {

  wp_nonce_field( plugin_basename( __FILE__ ), 'dvew_noncename' );

  $checked = "";
  if(isset($_GET['post']) && get_post_meta($_GET['post'], 'dvew_checkbox') != false) $checked = ' checked="checked" ';

  echo '<input type="checkbox" id="dvew_checkbox" name="dvew_checkbox" '.$checked.'/>';
  echo '<label for="dvew_checkbox">';
       _e(" Disable", 'dvew_textbox' );
  echo '</label> ';
}

function dvew_save_post( $post_id ) {
	if(defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

	if(!isset($_POST['dvew_noncename']) || !wp_verify_nonce( $_POST['dvew_noncename'], plugin_basename( __FILE__ ) ) )  return;
  
	if(!isset($_POST['post_type'])) return;
		
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) ) return;
	}else{
		if ( !current_user_can( 'edit_post', $post_id ) ) return;
	}
	
	if(isset($_POST['dvew_checkbox'])){
		if(!get_post_meta($post_id, 'dvew_checkbox')) add_post_meta($post_id, 'dvew_checkbox', 1);
	}else{
		delete_post_meta($post_id, 'dvew_checkbox');
	}
}

?>