<?php


/*******************************New Roles*******************************/
add_role(
    'advisor',
    __('Asesor', 'textdomain'),
    array(
        'read' => true,
    )
);

add_role(
    'leader',
    __('Lider de ventas', 'textdomain'),
    array(
        'read' => true,
    )
);

/**************************New Status Ordes*****************************/

add_action('init', 'register_call_order_status');
// Register new status request to cancel
function register_call_order_status()
{
    register_post_status('wc-paid', array(
        'label'                     => 'Pedido pagado',
        'public'                    => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list'    => true,
        'exclude_from_search'       => false,
        'label_count'               => _n_noop('Pedido pagado', 'Pedido pagado')
    ));

    register_post_status('wc-delivered', array(
        'label'                     => 'Pedido entregado',
        'public'                    => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list'    => true,
        'exclude_from_search'       => false,
        'label_count'               => _n_noop('Pedido entregado', 'Pedido entregado')
    ));
}

// Agregar nuevo estado de pedido personalizado
function add_call_to_order_status($order_statuses)
{
    $new_order_statuses = array();
    foreach ($order_statuses as $key => $status) {
        $new_order_statuses[$key] = $status;

        $new_order_statuses['wc-delivered'] = 'Pedido entregado';
        $new_order_statuses['wc-paid']      = 'Pedido pagado';
    }
    return $new_order_statuses;
}
add_filter('wc_order_statuses', 'add_call_to_order_status');


/************************New Option Menu Novedades**************************/
function add_submenu_woocommerce()
{
    add_submenu_page(
        'woocommerce',
        'Novedades',
        'Novedades',
        'manage_options',
        'edit.php?post_type=novedades',
    );
}
add_action('admin_menu', 'add_submenu_woocommerce');


/************************New Post Type Novedades**************************/
function create_new_content_type()
{
    $labels = array(
        'name'               => 'Novedades',
        'singular_name'      => 'Novedad',
        'menu_name'          => 'Novedades',
        'name_admin_bar'     => 'Novedad',
        'add_new'            => 'Añadir nueva',
        'add_new_item'       => 'Añadir nueva Novedad',
        'new_item'           => 'Nueva Novedad',
        'edit_item'          => 'Editar Novedad',
        'view_item'          => 'Ver Novedad',
        'all_items'          => 'Todas las Novedades',
        'search_items'       => 'Buscar Novedades',
        'parent_item_colon'  => 'Novedad superior:',
        'not_found'          => 'No se encontraron Novedades.',
        'not_found_in_trash' => 'No se encontraron Novedades en la papelera.'
    );

    $args = array(
        'labels'             => $labels,
        'description'        => 'Tipo de contenido para las Novedades.',
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'novedades'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-format-status',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'author')
    );

    register_post_type('novedades', $args);
}
add_action('init', 'create_new_content_type');


/************************ Register view note **************************/
add_action('woocommerce_order_note_added', 'ord_register_view', 10, 2);
function ord_register_view($comment_id, $order)
{
    add_comment_meta($comment_id, 'view', '0', true);
}


/********************** Form Edit Personal Data Advisor **********************/
function form_advisors_personal_data($user)
{
?>
    <h3><?php _e('Información personal del Asesor', 'text_domain'); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="advisor_phone"><?php _e('Numero de celular', 'text_domain'); ?></label></th>
            <td>
                <input type="text" name="advisor_phone" id="advisor_phone" value="<?php echo esc_attr(get_the_author_meta('advisor_phone', $user->ID)); ?>" class="regular-text" /><br />
            </td>
        </tr>
        <tr>
            <th><label for="advisor_city"><?php _e('Ciudad', 'text_domain'); ?></label></th>
            <td>
                <input type="text" name="advisor_city" id="advisor_city" value="<?php echo esc_attr(get_the_author_meta('advisor_city', $user->ID)); ?>" class="regular-text" /><br />
            </td>
        </tr>
        <tr>
            <th><label for="advisor_address"><?php _e('Dirección', 'text_domain'); ?></label></th>
            <td>
                <input type="text" name="advisor_address" id="advisor_address" value="<?php echo esc_attr(get_the_author_meta('advisor_address', $user->ID)); ?>" class="regular-text" /><br />
            </td>
        </tr>
        <tr>
            <th><label for="advisor_birthday"><?php _e('Fecha de nacimiento', 'text_domain'); ?></label></th>
            <td>
                <input type="date" name="advisor_birthday" id="advisor_birthday" value="<?php echo esc_attr(get_the_author_meta('advisor_birthday', $user->ID)); ?>" class="regular-text" /><br />
            </td>
        </tr>
        <tr>
            <th><label for="advisor_children"><?php _e('Tienes hijos', 'text_domain'); ?></label></th>
            <td>
                <select class="form-select" name="advisor_children" id="advisor_children" aria-label="Tienes hijos?">
                    <option>Tienes hijos?</option>
                    <option <?php echo $resultado = (esc_attr(get_the_author_meta('advisor_children', $user->ID)) == 1) ? 'selected' : ''; ?> value="1">SI</option>
                    <option <?php echo $resultado = (esc_attr(get_the_author_meta('advisor_children', $user->ID)) == 2) ? 'selected' : ''; ?> value="2">NO</option>
                </select>
            </td>
        </tr>
    </table>
<?php
}
add_action('show_user_profile', 'form_advisors_personal_data');
add_action('edit_user_profile', 'form_advisors_personal_data');

/********************* Save Personal Data Advisor *********************/
function save_advisors_personal_data($user_id)
{
    if (current_user_can('edit_user', $user_id)) {
        update_user_meta($user_id, 'advisor_phone', sanitize_text_field($_POST['advisor_phone']));
        update_user_meta($user_id, 'advisor_city', sanitize_text_field($_POST['advisor_city']));
        update_user_meta($user_id, 'advisor_address', sanitize_text_field($_POST['advisor_address']));
        update_user_meta($user_id, 'advisor_birthday', sanitize_text_field($_POST['advisor_birthday']));
        update_user_meta($user_id, 'advisor_children', sanitize_text_field($_POST['advisor_children']));
    }
}
add_action('personal_options_update', 'save_advisors_personal_data');
add_action('edit_user_profile_update', 'save_advisors_personal_data');


/* ******************** Generate Access Token API********************* */
function generate_access_token_rest($user_login, $user)
{
    if ($user) {
        $user_id = $user->ID;
        $token = wp_generate_password(32, false);
        update_user_meta($user_id, 'token_access_rest', $token);
    }
}
add_action('wp_login', 'generate_access_token_rest', 10, 2);


/* ******************** Add new status accion lote ********************* */
function add_new_status_accion_lote($actions) {
    $actions['wc-delivered'] = __('Cambiar estado a entregado', 'woocommerce');
    $actions['wc-paid'] = __('Cambiar estado a pagado', 'woocommerce');
    return $actions;
}
add_filter('bulk_actions-edit-shop_order', 'add_new_status_accion_lote');

/* ******************** Change status order acction lote ********************* */
function change_status_order_acction_lote($redirect_to, $action, $post_ids) {
    if ($action === 'wc-delivered') {
        foreach ($post_ids as $order_id) {
            $order = wc_get_order($order_id);
            if ($order) {
                $order->update_status('wc-delivered');
            }
        }
        $redirect_to = add_query_arg('changed_status', count($post_ids), $redirect_to);
    } else if ($action === 'wc-paid') {
        foreach ($post_ids as $order_id) {
            $order = wc_get_order($order_id);
            if ($order) {
                $order->update_status('wc-paid');
            }
        }
        $redirect_to = add_query_arg('changed_status', count($post_ids), $redirect_to);
    }
    return $redirect_to;
}
add_filter('handle_bulk_actions-edit-shop_order', 'change_status_order_acction_lote', 10, 3);


