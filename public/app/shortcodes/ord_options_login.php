<?php
wp_enqueue_style("ord_bootstrap_css");
wp_enqueue_style("ord_bootstrap_icons");
wp_enqueue_script("ord_popper_js");
wp_enqueue_script("ord_bootstrap_1_js");
wp_enqueue_script("ord_bootstrap_2_js");

$current_user = wp_get_current_user();
$user_roles = $current_user->roles;
$logout_url = wp_logout_url(home_url());
?>

<div class="btn-group">
    <span class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <?php echo '¡Hola, ' . esc_html($current_user->display_name) . '!'; ?>
    </span>
    <div class="dropdown-menu">
        <?php if (in_array('administrator', $user_roles) || in_array('advisor', $user_roles) || in_array('leader', $user_roles)) { ?>
            <a class="dropdown-item" href="/panel">Panel de Pedidos</a>
            <a class="dropdown-item" href="/perfil-asesor">Editar mi Perfil</a>
        <?php } else { ?>
            <a class="dropdown-item" href="/mi-cuenta">Mi Cuenta</a>
        <?php } ?>
        <a class="dropdown-item" href="<?php echo esc_url($logout_url) ?>">Cerrar Sesión</a>
    </div>
</div>