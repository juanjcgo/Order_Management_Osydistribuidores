<?php
/* API */
function ord_manage_request()
{
    register_rest_route(
        "ord",
        "ord_get_orders",
        array(
            'methods'  => 'POST',
            'callback' => 'ord_get_orders_callback'
        )
    );

    register_rest_route(
        "ord",
        "ord_get_order",
        array(
            'methods'  => 'POST',
            'callback' => 'ord_get_order_callback'
        )
    );

    register_rest_route(
        "ord",
        "ord_create_note",
        array(
            'methods'  => 'POST',
            'callback' => 'ord_create_note_callback'
        )
    );

    register_rest_route(
        "ord",
        "ord_get_order_completed",
        array(
            'methods'  => 'POST',
            'callback' => 'ord_get_order_completed_callback'
        )
    );

    register_rest_route(
        "ord",
        "ord_update_view_notes",
        array(
            'methods'  => 'POST',
            'callback' => 'ord_update_view_notes_callback'
        )
    );
}

/****************************** Update View Note *******************************/
function ord_update_view_notes_callback($data_request)
{
    try {

        $order_id     = $data_request['order_id'];
        $id_decrypt   = $data_request['user_id'];
        $current_user = decrypt_user_id($id_decrypt);
        $order        = wc_get_order($order_id);
        $id_advisor   = $order->get_user_id();

        if (!empty($order_id) && $id_advisor == $current_user) {

            $obj_notes = wc_get_order_notes(array(
                'order_id' => $order_id,
            ));

            foreach ($obj_notes as $note) {
                update_comment_meta($note->id, 'view', "1");
            }

            return [
                'res' => true,
                'msg' => 'Nota actualizada'
            ];

        } else {
            return [
                'res' => false,
                'msg' => 'No fue posible actualizar la nota'
            ];
        }
    } catch (Exception $e) {
        return ('Excepción capturada' . $e->getMessage() . "\n");
    }
}


/****************************** Get order all *******************************/
function ord_get_order_completed_callback($data_request)
{
    try {
        $id_decrypt = $data_request['info_order'];
        $id_advisor = decrypt_user_id($id_decrypt);
        $order_status = $data_request['order_status'];
        $user_data = get_userdata($id_advisor);
        $user_role = $user_data->roles[0];
        $customer_orders = [];


        if ($user_role == 'administrator' || $user_role == 'leader') {
            if ($order_status != 'wc-all') {
                $customer_orders = wc_get_orders(array(
                    'status'      => array($order_status)
                ));
            } else {
                $customer_orders = wc_get_orders(array(
                    'status'      => array('wc-completed', 'wc-pending', 'wc-paid', 'wc-failed', 'wc-processing', 'wc-delivered', 'wc-cancelled'),
                    'limit'       => -1,
                ));
            }
        } else {
            if ($order_status != 'wc-all') {
                $customer_orders = wc_get_orders(array(
                    'customer_id' => $id_advisor,
                    'status'      => array($order_status)
                ));
            } else {
                $customer_orders = wc_get_orders(array(
                    'customer_id' => $id_advisor,
                    'status'      => array('wc-completed', 'wc-pending', 'wc-paid', 'wc-failed', 'wc-processing', 'wc-delivered', 'wc-cancelled'),
                    'limit'       => -1,
                ));
            }
        }

        $orders = [];
        $count = 1;

        if (!empty($customer_orders)) {
            foreach ($customer_orders as $order) {

                $order_data = $order->get_data();
                $order_id   = $order->get_id();
                $user_id    = get_post_meta($order_id, '_customer_user', true);
                $user_info  = get_userdata($user_id);
                $user_name  = $user_info->display_name;

                $orders[] = [
                    'order_id'           => $order->get_id(),
                    'customer_user'      => $user_name,
                    'order_title'        => "# " . $order->get_id() . " " . $order->get_title(),
                    'order_date_created' => $order->get_date_created()->format('Y-m-d H:i:s'),
                    'order_total'        => $order->get_total(),
                    'order_commission'   => $order->get_subtotal() * 0.15,
                    'order_status'       => $order->get_status(),
                    'order_status_translate' => ord_translate_status_orders($order->get_status()),
                    'customer_phone'     => $order_data['billing']['phone'],
                    'count'              => $count
                ];

                $count++;
            }
            return [
                'res'  => true,
                'data' => $orders
            ];
        } else {
            return [
                'res'  => false,
                'data' => 'No hay poedidos'
            ];
        }
    } catch (Exception $e) {
        return ('Excepción capturada' . $e->getMessage() . "\n");
    }
}

/****************************** Create note *******************************/
function ord_create_note_callback($data_request)
{
    try {
        $id_order = $data_request['id_order'];
        $ord_note = $data_request['ord_note'];
        $note_author = "advisor";

        $order = wc_get_order($id_order);
        $order->add_order_note($ord_note, array('added_by' => $note_author));
        $order->save();

        global $wpdb;
        $note_id = $wpdb->get_var($wpdb->prepare("SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = %s ORDER BY comment_ID DESC LIMIT 1", $id_order));
        update_comment_meta($note_id, 'view', "2");

        $obj_notes = wc_get_order_notes(array(
            'order_id' => $id_order,
        ));

        return $obj_notes;
    } catch (Exception $e) {
        return ('Excepción capturada' . $e->getMessage() . "\n");
    }
}

/****************************** Get orders *******************************/
function ord_get_orders_callback($data_request)
{
    try {

        $id_decrypt = $data_request['info_order'];
        $id_advisor = decrypt_user_id($id_decrypt);
        $customer_orders = [];
        $orders = [];
        $count = 1;
        $user_data = get_userdata($id_advisor);
        $user_role = $user_data->roles[0];

        if ($user_role == 'administrator' || $user_role == 'leader') {
            $args = array(
                'status' => array('wc-completed', 'wc-pending', 'wc-paid', 'wc-failed', 'wc-processing', 'wc-delivered'),
                'limit' => -1
            );
            $query = new WC_Order_Query($args);
            $customer_orders = $query->get_orders();
        } else {
            $customer_orders = wc_get_orders(array(
                'customer_id' => $id_advisor,
                'limit'       => -1,
            ));
        }

        if (!empty($customer_orders)) {
            foreach ($customer_orders as $order) {

                $order_data = $order->get_data();
                $order_id   = $order->get_id();
                $user_id    = get_post_meta($order_id, '_customer_user', true);
                $user_info  = get_userdata($user_id);
                $user_name  = $user_info->display_name;

                $orders[] = [
                    'order_id'           => $order_id,
                    'customer_user'      => $user_name,
                    'order_title'        => "# " . $order->get_id() . " " . $order->get_title(),
                    'order_date_created' => $order->get_date_created()->format('Y-m-d H:i:s'),
                    'order_total'        => $order->get_total(),
                    'order_subtotal'     => $order->get_subtotal(),
                    'order_commission'   => $order->get_subtotal() * 0.15,
                    'order_status'       => $order->get_status(),
                    'order_status_translate' => ord_translate_status_orders($order->get_status()),
                    'shipping_total'     => $order->get_shipping_total(),
                    'customer_phone'     => $order_data['billing']['phone'],
                    'number_note_views'  => ord_get_number_note_views($order_id),
                    'count'              => $count
                ];

                $count++;
            }
            return [
                'res'  => true,
                'data' => $orders
            ];
        } else {
            return [
                'res'  => false,
                'data' => 'No hay pedidos'
            ];
        }
    } catch (Exception $e) {
        return [
            'res' => 'error',
            'msg' => 'Excepción capturada' . $e->getMessage()
        ];
    }
}

/****************************** Get number of vitas per note  *******************************/
function ord_get_number_note_views($order_id)
{
    $obj_notes = wc_get_order_notes(array(
        'order_id' => $order_id,
    ));

    $count_view = 0;
    foreach ($obj_notes as $note) {
        $view = get_comment_meta($note->id, 'view', true);
        if ($view == "0") {
            $count_view++;
        }
    }
    return $count_view;
}

/****************************** Get une order  *******************************/
function ord_get_order_callback($data_request)
{
    try {

        $order_id = $data_request['order_id'];

        if (!empty($order_id)) {

            $order      = wc_get_order($order_id);
            $order_data = $order->get_data();
            $prods = [];

            if (!$order) {
                return [
                    'res' => false,
                    'msg' => 'Pedido no encontrado'
                ];
            }

            /* Products */
            foreach ($order->get_items() as $item) {
                $prods[] = [
                    'prod_name'     => $item->get_name(),
                    'prod_quantity' => $item->get_quantity(),
                    'prod_total'    => $item->get_total()
                ];
            }

            $obj_notes = wc_get_order_notes(array(
                'order_id' => $order_id,
            ));

            $data_notes = [];
            foreach ($obj_notes as $note) {
                $view = get_comment_meta($note->id, 'view', true);
                $data_notes[] = [
                    'note_id'  => $note->id,
                    'added_by' => $note->added_by,
                    'content'  => $note->content,
                    'content'  => $note->content,
                    'customer_note' => $note->customer_note,
                    'date_created'  => $note->date_created,
                    'view'          => $view
                ];
            }

            /* Order total */
            $orders = [
                'order_id'           => $order_id,
                'order_date_created' => $order->get_date_created()->format('d/m/Y H:i:s'),
                'order_total'        => floatval($order->get_total()),
                'order_subtotal'     => floatval($order->get_subtotal()),
                'shipping_total'     => floatval($order->get_shipping_total()),
                'order_commission'   => floatval($order->get_subtotal() * 0.15),
                'order_total_tax'    => floatval($order->get_total_tax()),
                'order_status'       => $order->get_status(),
                'order_status_translate' => ord_translate_status_orders($order->get_status()),
                'products'           => $prods,
                'billing'            => $order_data['billing'],
                'shipping'           => $order_data['shipping'],
                'notes'              => $data_notes
            ];

            return [
                'res'  => true,
                'data' => $orders
            ];
        } else {
            return [
                'res'  => false,
                'msg' => "Id no valido"
            ];
        }
    } catch (Exception $e) {
        return ('Excepción capturada' . $e->getMessage() . "\n");
    }
}


/****************************** Translate status ordes  *******************************/
function ord_translate_status_orders($status_code)
{
    switch ($status_code) {
        case "completed":
            return "Despachado";
            break;
        case "pending":
            return "Pendiente";
            break;
        case "paid":
            return "Pagado";
            break;
        case "failed":
            return "Fallido";
            break;
        case "processing":
            return "Registrado";
            break;
        case "delivered":
            return "Entregado";
            break;
        case "cancelled":
            return "Cancelado";
            break;
        default:
            return "Desconocido";
            break;
    }
}

add_action("rest_api_init", "ord_manage_request");
