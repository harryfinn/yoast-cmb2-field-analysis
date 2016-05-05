<?php
/**
* Plugin Name: Yoast CMB2 Field Analysis
* Plugin URI: https://harryfinn.co.uk
* Description: Adds the content of all CMB2 prefixed fields to the Yoast SEO score analysis.
* Version: 1.0.1
* Author: Harry Finn
* Author URI: https://harryfinn.co.uk
* License: GPL v3
*/

if(!defined('ABSPATH')) exit;

class YoastCMB2Analysis {
  private $plugin_data = null;

  public function __construct() {
    add_action('admin_init', [$this, 'plugin_admin_setup']);
    add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
  }

  public function plugin_admin_setup() {
    $this->plugin_data = get_plugin_data(dirname(__FILE__));

    if(current_user_can('activate_plugins')) {
      $cmb2_active = $this->check_for_cmb2();
      $yoast_seo_active = $this->check_for_yoast_seo();
      $deactivate = $cmb2_active || $yoast_seo_active;

      if($deactivate) {
        deactivate_plugins(plugin_basename(__FILE__));

        if(!empty($_GET['activate'])) unset($_GET['activate']);
      }
    }
  }

  public function check_for_cmb2() {
    if(!is_plugin_active('cmb2/init.php') && !has_action('cmb2_init')) {
      add_action('admin_notices', [$this, 'require_cmb2_message']);

      return true;
    }

    return false;
  }

  public function check_for_yoast_seo() {
    if(!is_plugin_active('wordpress-seo/wp-seo.php')) {
      add_action('admin_notices', [$this, 'require_yoast_message']);

      return true;
    }

    return false;
  }

  public function require_cmb2_message() {
    ?>

    <div class="error">
      <p>Yoast CMB2 Field Analysis requires CMB2 (plugin or library) to be installed and initialized.</p>
    </div>

    <?php
  }

  public function require_yoast_message() {
    ?>

    <div class="error">
      <p>Yoast CMB2 Field Analysis requires Yoast SEO 3.0+ to be installed and activated.</p>
    </div>

    <?php
  }

  public function enqueue_scripts($page_hook) {
    if($page_hook !== 'post.php') return;

    $current_screen = get_current_screen();
    $current_post_type = get_post_type_object($current_screen->post_type);

    if($current_post_type->public === false) return;

    wp_register_script(
      'yoast-cmb2-plugin-js',
      plugins_url('js/yoast-cmb2-field-analysis.js', __FILE__),
      ['jquery', 'yoast-seo-post-scraper'],
      $this->plugin_data['Version']
    );
    wp_enqueue_script('yoast-cmb2-plugin-js');
  }
}

new YoastCMB2Analysis();
