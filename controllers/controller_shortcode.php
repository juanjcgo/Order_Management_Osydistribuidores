<?php

function ord_management_panel()
{
    if (is_user_logged_in()) {

        $current_user = wp_get_current_user();
        $user_roles = $current_user->roles;

       /*  if (in_array('administrator', $user_roles)) { */

            ob_start();
            include ORD_PATH . '/public/app/shortcodes/ord_management_panel.php';
            $content = ob_get_clean();

            return $content;
            
        /* }else {
            echo "No tienes permisos para acceder a esta pagina";
        } */

    } else {
        echo "No tienes accesos a esta area, por favor inicia sesión";
    }
}
add_shortcode("ord_management_panel", "ord_management_panel");