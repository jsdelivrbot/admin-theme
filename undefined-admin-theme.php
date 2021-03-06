<?php

/*
Plugin Name: Undefined Admin Theme
Plugin URI: http://weareundefined.be
Description: A clean, simplified WordPress Admin theme
Author: Ryan Sommers & Undefined
Version: 1.1.9
Author URI: http://weareundefined.be
*/

function undefined_files()
{
    wp_enqueue_style('undefined-admin-theme', plugins_url('css/admin-theme.css', __FILE__), array(), '1.1.7');
    wp_enqueue_script('slate', plugins_url("js/slate.js", __FILE__), array('jquery'), '1.1.7');
}

add_action('admin_enqueue_scripts', 'undefined_files');
add_action('login_enqueue_scripts', 'undefined_files');

function undefined_add_editor_styles()
{
    add_editor_style(plugins_url('css/editor-style.css', __FILE__));
}

add_action('after_setup_theme', 'undefined_add_editor_styles');

add_action('admin_head', 'undefined_colors');
add_action('login_head', 'undefined_colors');

function undefined_colors()
{
    include('css/dynamic.php');
}

function undefined_get_user_admin_color()
{
    $user_id = get_current_user_id();
    $user_info = get_userdata($user_id);
    if (!($user_info instanceof WP_User)) {
        return;
    }
    $user_admin_color = $user_info->admin_color;

    return $user_admin_color;
}

// Remove the hyphen before the post state
add_filter('display_post_states', 'undefined_post_state');

function undefined_post_state($post_states)
{
    if (!empty($post_states)) {
        $state_count = count($post_states);
        $i = 0;
        foreach ($post_states as $state) {
            ++$i;
            ($i == $state_count) ? $sep = '' : $sep = '';
            echo "<span class='post-state'>$state$sep</span>";
        }
    }
}

function login_redirect($redirect_to, $request, $user)
{
    return admin_url('edit.php?post_type=page');
}

add_filter('login_redirect', __NAMESPACE__ . '\\login_redirect', 10, 3);

function remove_menu()
{
    remove_menu_page('index.php'); //dashboard
    remove_menu_page('edit-comments.php'); //comments
}

add_action('admin_menu', __NAMESPACE__ . '\\remove_menu', 99);

function remove_wp_logo($wp_admin_bar)
{
    $wp_admin_bar->remove_node('wp-logo');
}

add_action('admin_bar_menu', __NAMESPACE__ . '\\remove_wp_logo', 999);

function change_footer_admin()
{
    return '&nbsp;';
}

add_filter('admin_footer_text', __NAMESPACE__ . '\\change_footer_admin', 9999);

function change_footer_version()
{
    return ' ';
}

add_filter('update_footer', __NAMESPACE__ . '\\change_footer_version', 9999);

/*
 * Change the opacity of WordPress Admin Bar
 */
function adminbar_opacity()
{
    $adminbar_opacity = '<style type="text/css">#wpadminbar { filter:alpha(opacity=50); opacity:0.5; }</style>';
    echo $adminbar_opacity;
}

if (!is_admin()) {
    add_action('wp_head', __NAMESPACE__ . '\\adminbar_opacity');
}

/*
 * Redirect Dashboard to pages
 */
function redirect_from_dashboard()
{
    wp_redirect(admin_url('edit.php?post_type=page'));
    exit;
}

add_action('wp_dashboard_setup', __NAMESPACE__ . '\\redirect_from_dashboard');

function admin_menu()
{
    global $menu;
    $url = '/';
    $menu[0] = array(__('STASH'), 'read', '#', 'undefined-logo', 'undefined-logo');
}

add_action('admin_menu', __NAMESPACE__ . '\\admin_menu');