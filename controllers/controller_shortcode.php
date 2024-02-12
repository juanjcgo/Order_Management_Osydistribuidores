<?php

function ord_management_panel()
{
    if (is_user_logged_in()) {

        $current_user = wp_get_current_user();
        $user_roles = $current_user->roles;

        if (in_array('administrator', $user_roles) || in_array('advisor', $user_roles) || in_array('leader', $user_roles)) {

            ob_start();
            include ORD_PATH . '/public/app/shortcodes/ord_management_panel.php';
            $content = ob_get_clean();

            return $content;
        } else {
            wp_redirect('/');
            exit;
        }
    } else {
        wp_enqueue_style("ord_bootstrap_css");
        echo "<h2 class='center'>No tienes accesos a esta area, por favor inicia sesión</h2>";
    }
}
add_shortcode("ord_management_panel", "ord_management_panel");


function ord_show_option_login()
{
    if (is_user_logged_in()) {

        ob_start();
        include ORD_PATH . '/public/app/shortcodes/ord_options_login.php';
        $content = ob_get_clean();

        return $content;
    } else {
        return "<a style='font-family: `Poppins`, sans-serif; font-weight: 600;' href='/acceso'>Acceder</a>";
    }
}
add_shortcode('ord_show_option_login', 'ord_show_option_login');

function ord_edit_profile()
{
    if (is_user_logged_in()) {

        $current_user = wp_get_current_user();
        $user_roles = $current_user->roles;

        if (in_array('administrator', $user_roles) || in_array('advisor', $user_roles) || in_array('leader', $user_roles)) {

            ob_start();
            include ORD_PATH . '/public/app/shortcodes/ord_edit_profile.php';
            $content = ob_get_clean();

            return $content;
        } else {
            wp_redirect('/');
            exit;
        }
    } else {
        wp_redirect('/acceso');
        exit;
    }
}
add_shortcode('ord_edit_profile', 'ord_edit_profile');
