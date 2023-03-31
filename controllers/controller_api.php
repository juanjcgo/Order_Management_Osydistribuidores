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
}


function ord_get_orders_callback($data_request)
{
    try {

        $id_decrypt = $data_request['info_order'];
        $id_advisor = decrypt_user_id($id_decrypt);

        $customer_orders = wc_get_orders(array(
            'customer_id' => $id_advisor/* ,
            'status' => array('wc-processing', 'wc-completed') */
        ));

        $orders = [];
        $count = 0;

        // Recorre los resultados de la consulta
        foreach ($customer_orders as $order) {
            // Imprime algunos detalles del pedido

            $orders[] = [
                'order_id'           => $order->get_id(),
                'order_title'        => "# " . $order->get_id() . " " . $order->get_title(),
                'order_date_created' => $order->get_date_created()->format('Y-m-d H:i:s'),
                'order_price'        => $order->get_total(),
                'order_commission'   => $order->get_total() * 0.15,
                'order_status'       => $order->get_status(),
                'count'              => $count
            ];

            $count++;
        }


        /*         $orders_obj = wc_get_orders(array(-1));
        $orders = [];
        $count = 0;

        foreach ($orders_obj as $order) {

            $order_id = $order->get_id();

            $orders[] = [
                'order_id'           => $order_id,
                'order_title'        => "# " . $order_id . " " . $order->get_title(),
                'order_date_created' => $order->get_date_created()->format('d/m/Y H:i:s'),
                'order_price'        => wc_price($order->get_total()),
                'order_commission'   => wc_price($order->get_total() * 0.15),
                'order_status'       => $order->get_status(),
                'count'              => $count
            ];

            $count++;
        } */

        return [
            'res'  => true,
            'data' => $orders
        ];
    } catch (Exception $e) {
        return ('Excepción capturada' . $e->getMessage() . "\n");
    }
}


function ord_get_order_callback($data_request)
{
    try {

        $order_id = $data_request['order_id'];

        if (!empty($order_id)) {

            $order      = wc_get_order($order_id);
            $order_data = $order->get_data();
            $prods = [];
            $notes = [];

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

            $notes = wc_get_order_notes( array(
                'order_id' => $order_id,
            ) );
            
            foreach ( $notes as $note ) {
                $notes[] = [
                    'a'    =>  $note->added_by,
                    'note' => $note->content,
                    'date' => $note->date_created->format('d/m/Y H:i:s')
                ]; 
            }


            /* Order total */
            $orders = [
                'order_id'           => $order_id,
                'order_date_created' => $order->get_date_created()->format('d/m/Y H:i:s'),
                'order_price'        => $order->get_total(),
                'order_commission'   => $order->get_total() * 0.15,
                'order_status'       => $order->get_status(),
                'products'           => $prods,
                'billing'            => $order_data['billing'],
                'shipping'           => $order_data['shipping'],
                'notes'              => $notes
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


add_action("rest_api_init", "ord_manage_request");
