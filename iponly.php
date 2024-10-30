<?php
/*
Plugin Name: IP Only
Plugin URI: http://home.wangjianshuo.com/archives/20120903_ip_only.htm
Description: Restrict access to certain entries, and comments to specific IP address. The others will display a message.
Version: 0.1
Author: Jian Shuo Wang
Author URI: http://home.wangjianshuo.com
License: GPL 2
*/
/*  Copyright 2012 Jian Shuo Wang (email: jianshuo@hotmail.com)

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

add_filter( 'the_content', 'ip_only_content' );
add_filter( 'the_content_feed', 'ip_only_content');
add_filter( 'get_comment_text', 'ip_only_content');


add_action('admin_init', 'iponly_admin_init' );
add_action('admin_menu', 'iponly_admin_add_page');

function ip_only_content($content) {
	if(in_category(47))
		if(iponly_access_granted())
			$content = get_option("iponly_access_granted_msg") . $content;
		else
			$content = get_option('iponly_access_denied_msg') . "#" . $_SERVER['REMOTE_ADDR'];
	return $content;
}

function iponly_access_granted() {
	$allowed = preg_split("/[\s,]/", get_option('iponly_allowed_ips'));
	return in_array($_SERVER['REMOTE_ADDR'], $allowed);
}



function iponly_admin_add_page() {
	add_options_page('IP Only Settings', 'IP Only Settings', 'manage_options', 'iponly', 'iponly_options_page');
}

function iponly_options_page() {
?>
	<div class="wrap">
		<h2>IP Only Settings</h2>
		<form method="post" action="options.php">
			<?php settings_fields('iponly_options'); ?>
			<table class="form-table">
			<?php echo iponly_option_row("Access Granted Message", "iponly_access_granted_msg");?>
			<?php echo iponly_option_row("Access Denied Message", "iponly_access_denied_msg");?>
			<?php echo iponly_option_row("Allowed IPs (one IP per line)", "iponly_allowed_ips");?>
			<?php echo iponly_option_row("Restricted Category (Posts in this category will be restricted to the IPs listed above only)", "iponly_restricted_category");?>
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
<?php
}

function iponly_option_row($label, $text) {
?>
	<tr valign="top"><th scope="row"><?php echo $label;?> </th>
					<td><textarea name="<?php echo $text;?>" class="large-text code"><?php echo get_option($text); ?></textarea></td>
				</tr>
<?php
}

function iponly_admin_init(){
	register_setting('iponly_options', 'iponly_access_granted_msg');
	register_setting('iponly_options', 'iponly_access_denied_msg');
	register_setting('iponly_options', 'iponly_allowed_ips');
	register_setting('iponly_options', 'iponly_restricted_category');
}
?>