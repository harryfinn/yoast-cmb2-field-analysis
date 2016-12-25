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
    if(!class_exists('CMB2', false) && !defined('CMB2_LOADED')) {
      add_action('admin_notices', [$this, 'require_cmb2_message']);

      return true;
    }

    return false;
  }

  public function check_for_yoast_seo() {
    if(!is_plugin_active('wordpress-seo/wp-seo.php') &&
      !is_plugin_active('wordpress-seo-premium/wp-seo-premium.php')) {
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
    if($page_hook !== 'post.php' && $page_hook !== 'post-new.php') return;

    $current_screen = get_current_screen();
    $current_post_type = get_post_type_object($current_screen->post_type);

    if($this->check_if_yoast_seo_is_hidden($current_post_type->name) === true) return;

    wp_register_script(
      'yoast-cmb2-plugin-js',
      plugins_url('js/yoast-cmb2-field-analysis.js', __FILE__),
      ['jquery', 'yoast-seo-post-scraper'],
      $this->plugin_data['Version']
    );
    wp_enqueue_script('yoast-cmb2-plugin-js');
  }


  /**
   * Test whether Yoast SEO is hidden either by choice of the admin or because
   * the post type is not a public post type
   *
   *
   * @param  string $post_type (optional) The post type to test, defaults to the current post post_type.
   *
   * @return  bool        Whether or not the yoast seo metabox should be hidden
   */
  private function check_if_yoast_seo_is_hidden($post_type = null) {
    $global_post = $GLOBALS['post'];

    if(!isset($post_type) && (isset($global_post) && (is_object($global_post)
      && isset($global_post->post_type)))) {
      $post_type = $global_post->post_type;
    }

    if(isset($post_type)) {
      // Don't make static as post_types may still be added during the run.
      $cpts    = get_post_types(array('public' => true),'names');
      $options = get_option('wpseo_titles');
      return ((isset($options['hideeditbox-' . $post_type]) && $options['hideeditbox-' . $post_type] === true)
          || in_array($post_type, $cpts) === false);
    }
    return false;
  }
}

new YoastCMB2Analysis();
