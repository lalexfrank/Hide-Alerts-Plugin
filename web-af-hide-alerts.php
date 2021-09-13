<?php

/**

 * @package Web AF Alert Hider

 */

/*

Plugin Name: Web AF Alert Hider

Plugin URI: https://webaf.com/

Description: Hide your pesky admin panel alerts. Disable the plugin to display all alerts.

Version: 1.0

Author: L. Alex Frank

Author URI: https://webaf.com/

License: GPLv2 or later

Text Domain: webaf

*/


function hide_alerts_add_settings_page() {
    add_options_page( 'Hide Alerts page', 'Hide Alerts Settings', 'manage_options', 'hide_alerts_plugin', 'hide_alerts_render_plugin_settings_page' );
}
add_action( 'admin_menu', 'hide_alerts_add_settings_page' );

function hide_alerts_render_plugin_settings_page() {
    ?>
    <h2>Hide Alerts Settings</h2>
    <form action="options.php" method="post">
        <?php
        settings_fields( 'hide_alerts_plugin_options' );
        do_settings_sections( 'hide_alerts_plugin' ); ?>
        <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
    </form>
    <?php
}

function hide_alerts_register_settings() {
    register_setting( 'hide_alerts_plugin_options', 'hide_alerts_plugin_options', 'hide_alerts_plugin_options_validate' );
    add_settings_section( 'user_id_settings', 'User ID Settings', 'hide_alerts_plugin_section_text', 'hide_alerts_plugin' );

    add_settings_field( 'hide_alerts_plugin_setting_user_id', 'User ID', 'hide_alerts_plugin_setting_user_id', 'hide_alerts_plugin', 'user_id_settings' );
}
add_action( 'admin_init', 'hide_alerts_register_settings' );

function hide_alerts_plugin_section_text() {
    echo '<p>Here you can set the user ID(s) for the user(s) that can see alerts. If entering multiple IDs separate values by a comma, ex. 3,21</p>';
}

function hide_alerts_plugin_setting_user_id() {
    $options = get_option( 'hide_alerts_plugin_options' );
    echo "<input id='hide_alerts_plugin_setting_user_id' name='hide_alerts_plugin_options[user_id]' type='text' value='" . esc_attr( $options['user_id'] ) . "' />";
}

 // What actually hides the alerts
add_action('admin_head', 'awd_hide_alerts');

function awd_hide_alerts() {
  // hides alerts for all users except specified user id

$userIDOption = get_option('hide_alerts_plugin_options');

    if ( isset( $userIDOption['user_id'] ) ) {
      $stringID = $userIDOption['user_id'];
      $str_arr = explode (",", $stringID);
      $userIDExclude = $str_arr;

    } else {
        exit;
    }

  $user_ID = get_current_user_id();
  if (!in_array($user_ID, $userIDExclude)) {
    // Jquery Migrate and general pop-up alerts
    echo '<style>
    .notice.notice-error, li#wp-admin-bar-enable-jquery-migrate-helper, .media-upload-form .notice, .media-upload-form div.error, .wrap .notice, .wrap div.error, .wrap div.updated, .error, .notice {
        display: none!important;
    }
    </style>';
    // plugin update badges
    echo '<style>
    li#wp-admin-bar-updates, span.update-plugins {
        display: none!important;
    }
    </style>';
  }
}

// creates an alert that displays only for users that have their alerts hidden.
function hide_alerts_notice() {
  if (in_array($user_ID, $userIDExclude)) {
    ?>
    <style>
    .error.notice.alert-plugin-alert {
        display: block!important;
    }
    </style>
    <div class="error notice alert-plugin-alert">
        <p><?php _e( "Your site's updates and alerts are being monitored by Web AF Design. To unhide notifications, <a href='/wp-admin/options-general.php?page=hide_alerts_plugin'>go here</a>.", 'my_plugin_textdomain' ); ?></p>
    </div>
    <?php
  }
}
add_action( 'admin_notices', 'hide_alerts_notice' );
