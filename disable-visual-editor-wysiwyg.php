<?php
/*
Plugin Name: Disable Visual Editor WYSIWYG
Version: 1.1
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

add_filter( 'admin_init', 'dvew_admin_pre_edit_page');
add_filter( 'format_to_edit', 'dvew_admin_edit_page');

add_action( 'admin_init', 'dvew_add_custom_box', 1 );
add_action( 'save_post', 'dvew_save_post' );


function dvew_admin_pre_edit_page(){
	if(strpos($_SERVER['PHP_SELF'],'/wp-admin/post.php') !== FALSE){
		if(!isset($_COOKIE['wp-settings-1'])){
			$cookie_path = str_replace('/wp-admin/post.php','/',$_SERVER['PHP_SELF']);
			setcookie('wp-settings-1','m5=3&editor=html&m9=o&m1=o',0,$cookie_path);
		}
	}

}

function dvew_admin_edit_page($content){
	if(isset($_GET['post']) && get_post_meta($_GET['post'], 'dvew_checkbox') != false){
		if(strpos($_SERVER['PHP_SELF'],'/wp-admin/post.php') !== FALSE){
			$cookie_path = str_replace('/wp-admin/post.php','/',$_SERVER['PHP_SELF']);
			setcookie('wp-settings-1','m5=3&editor=html&m9=o&m1=o',0,$cookie_path);
		}
	
		add_filter('admin_footer', 'dvew_admin_edit_page_js');
	}
	
	return $content;
}
function dvew_admin_edit_page_js(){
	echo '<script type="text/javascript">
		 jQuery(document).ready(function(){
			  switchEditors.go(\'content\', \'html\');
			  document.getElementById("edButtonPreview").onclick = \'none\';
			  document.getElementById("edButtonPreview").innerHTML = \'<span style="text-decoration:line-through">\'+document.getElementById("edButtonPreview").innerHTML+\'</em>\';
		 });
		  </script>';
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
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return;

  if ( !wp_verify_nonce( $_POST['dvew_noncename'], plugin_basename( __FILE__ ) ) )
      return;

  if ( 'page' == $_POST['post_type'] ) 
  {
    if ( !current_user_can( 'edit_page', $post_id ) )
        return;
  }
  else
  {
    if ( !current_user_can( 'edit_post', $post_id ) )
        return;
  }

  if(isset($_POST['dvew_checkbox'])){
	  if(!get_post_meta($post_id, 'dvew_checkbox')) add_post_meta($post_id, 'dvew_checkbox', 1);
  }else{
	  delete_post_meta($post_id, 'dvew_checkbox');
  }
}

?>