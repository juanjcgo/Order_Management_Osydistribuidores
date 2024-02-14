<?php

/**
 * Obtiene un token de sesión de la API de SalesUp.
 *
 * @return mixed|null Devuelve un objeto decodificado desde la respuesta JSON de la API o null si hay un error.
 */
function osy_get_token_sesion_upnify()
{
    $url = 'https://api.salesup.com/integraciones/sesion';
    $tokenIntegracion = 'P2594465029-5A08-4640-815D-05A5B733A120';

    $request = curl_init();
    curl_setopt($request, CURLOPT_POST, 1);
    curl_setopt($request, CURLOPT_URL, $url);
    curl_setopt($request, CURLOPT_HTTPHEADER, array('token:' . $tokenIntegracion));
    curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($request, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($request, CURLOPT_TIMEOUT, 60);
    $result = curl_exec($request);
    curl_close($request);

    $array_result = json_decode($result);

    return $array_result;
}

/**
 * Crea una oportunidad con los datos del nuevo pedido
 *
 * @return mixed|null Devuelve un array con la respuesta res = true si hay un error res = false.
 */
function osy_create_opportunity($tkContacto, $order_id, $concepto)
{
    $res_token    = osy_get_token_sesion_upnify();
    $ruta_archivo = ORD_PATH . 'info/logs.txt';
    $current_time = current_time('mysql');

    if (!isset($res_token[0]->code) || empty($res_token[0]->code)) {
        file_put_contents($ruta_archivo, $current_time . ' - ' . 'Error al generar el Token de sesión: ' . $res_token . "\n", FILE_APPEND);
    }

    $code          = $res_token[0]->code;
    $comision      = 0.15;
    $subtotal      = '';
    $total         = '';
    $total_items   = '';
    $shipping_cost = 0;
    $billing_city  = '';
    $tkFase        = 'OFAS-F09A3A84-5351-4D80-9489-5E69A751A4AA'; // Confirmación telefonica

    switch ($code) {
        case 0:
            // Crear un objeto DateTime
            $object_date = new DateTime(date('Y-m-d'));

            // Sumar 5 días
            $object_date->add(new DateInterval('P5D'));

            // Obtener la nueva fecha - Cierre estimado
            $cierreEstimado = $object_date->format('Y-m-d');

            // Asegúrate de tener acceso a las funciones de WooCommerce
            if (class_exists('WooCommerce')) {

                // Obtén el objeto de pedido de WooCommerce (puedes obtener el ID del pedido según tus necesidades)
                $order = wc_get_order($order_id);

                // Obtén el subtotal del pedido
                $subtotal = $order->get_subtotal();

                // Obtén el total del pedido (incluyendo impuestos y envío)
                $total = $order->get_total();

                // Obtén la cantidad total de productos en el pedido
                $total_items = $order->get_item_count();

                // Conto de envio
                $shipping_cost = $order->get_shipping_total();

                // Obtener los datos de facturación del pedido
                $billing_city = strtolower($order->get_billing_city());
                /* $billing_state = $order->get_billing_state(); */
            }

            //  Validar la fase
            if ($billing_city == 'bogotá') {

                // Cambiar Fase a 'En ruta Bogota'
                $tkFase = 'OFAS-E49B9D65-03CE-4356-95E2-82DB321AFDB2';

                // Crear un objeto DateTime con la fecha actual
                $object_date = new DateTime(date('Y-m-d'));

                // Sumar un día
                $object_date->add(new DateInterval('P1D'));

                // Obtener la nueva fecha - Cierre estimado
                $cierreEstimado = $object_date->format('Y-m-d');
            }

            // Token de sesión
            $tokenSesion = $res_token[0]->token;

            $url = 'https://api.salesup.com/oportunidades';
            $params = '{
                "tkProspecto":    "' . $tkContacto . '",
                "tkFase":         "' . $tkFase . '",
                "tkLinea":        "LINP-CAFD0FB9-A9C5-455C-B804-8837C4C1295B",
                "tkMoneda":       "MON-41B50D55-201D-4611-A11B-C9A68B39D1BC",
                "tkCerteza":      "CER-8751F9C5-E0A0-4DC7-B2F3-1D35CD939369",
                "concepto":       "' . $concepto . '",
                "cierreEstimado": "' . $cierreEstimado . '",
                "monto":          '  . $total . ',
                "cantidad":       '  . $total_items . ',
                "subtotal":       '  . $subtotal . ',
                "comision":       '  . $comision . ',
                "comisionMonto":  '  . $subtotal * $comision . ',
                "cp13o":          "' . $shipping_cost . '",
                "cp14o":          "' . $concepto . '",
                "cp18o":          "' . $order_id . '"
            }';

            $base64 = base64_encode($params);
            $request = curl_init();
            curl_setopt($request, CURLOPT_POST, 1);
            curl_setopt($request, CURLOPT_URL, $url . '?p=' . $base64);
            curl_setopt($request, CURLOPT_HTTPHEADER, array('token:' . $tokenSesion));
            curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($request, CURLOPT_CONNECTTIMEOUT, 60);
            curl_setopt($request, CURLOPT_TIMEOUT, 60);
            $result = curl_exec($request);
            curl_close($request);

            return [
                'res' => true,
                'data' => $result
            ];

            break;
        case 1:
            return [
                'res' => false,
                'msg' => 'Falló la inserción'
            ];
            break;
        case 2:
            return [
                'res' => false,
                'msg' => 'Fáltan parámetros'
            ];
            break;
        case 3:
            return [
                'res' => false,
                'msg' => 'Error al eliminar el registro'
            ];
            break;
        case 4:
            return [
                'res' => false,
                'msg' => 'Token de sesión vencido'
            ];
            break;
        case 5:
            return [
                'res' => false,
                'msg' => 'Fallo de procedimiento en base de datos'
            ];
            break;
        case 6:
            return [
                'res' => false,
                'msg' => 'Permisos insuficientes'
            ];
            break;
        case 7:
            return [
                'res' => false,
                'msg' => 'Mensajes en la lógica de negocios'
            ];
            break;
        default:
            return [
                'res' => false,
                'msg' => 'Error desconocido'
            ];
    }
}


// Registrar prospecto y oportunidad en el CRM Upnify 
add_action('woocommerce_thankyou', 'send_new_order_data_to_crm');
function send_new_order_data_to_crm($order_id)
{
    $order           = wc_get_order($order_id);
    $billing_address = $order->get_address('billing');
    $line_items      = $order->get_items();
    $user_id         = get_current_user_id();
    $first_name      = get_user_meta($user_id, 'first_name', true);
    $last_name       = get_user_meta($user_id, 'last_name', true);
    $customer_note   = $order->get_customer_note();
    $customer_city   = '';
    $data_product    = '';
    $info_product    = '';
    $current_time    = current_time('mysql');
    $metadata        = $order->get_meta_data();

    foreach ($metadata as $meta) {
        if ($meta->key == 'ciudad') {
            $customer_city = ucfirst($meta->value);
        }
    }

    foreach ($line_items as $item) {

        $product_name = $item->get_name();
        $quantity     = $item->get_quantity();
        $variation_id = $item->get_variation_id();

        if ($variation_id) {

            $variation  = new WC_Product_Variation($variation_id);
            $attributes = $variation->get_attributes();

            // Si el producto tienen variaciones 
            if (!empty($attributes)) {
                foreach ($attributes as $key => $value) {
                    $data_product .= '
                        <strong>Producto:</strong> ' . $product_name . '
                        <ul>
                            <li><strong>Cantidad: </strong>' . $quantity . '</li>
                            <li><strong>' . $key . '</strong>: ' . $value . ' </li>
                        </ul>
                        <br>
                    ';
                    $info_product .= $product_name . ' * ' . $quantity . ' - ' . $key . ' = ' . $value . ' | ';
                }
            } else {
                $data_product .= 'No hay variaciones para ' . $product_name;
                $info_product .= 'No hay variaciones para ' . $product_name;
            }
        } else {
            // Si el producto no tiene variaciones 
            $data_product .= '
                        <strong>Producto:</strong> ' . $product_name . '
                            <ul>
                                <li><strong>Cantidad: </strong>' . $quantity . '</li>
                            </ul>
                        <br>
                    ';
            $info_product .= $product_name . ' * ' . $quantity . ' | ';
        }
    }

    /********************************** Data of Order **********************************/
    $params = array(
        'nombre'     => $billing_address['first_name'],       /* Nombre del cliente */
        'apellidos'  => $billing_address['last_name'],        /* Apellido del cliente */
        'empresa'    => $billing_address['company'],          /* Tienda OSY */
        'movil'      => $billing_address['phone'],            /* Telefono 1 del cliente */
        'CP15'       => $billing_address['address_1'],        /* Direccion del cliente */
        'CP19'       => $billing_address['address_2'],        /* Barrio */
        'CIUDAD'     => $customer_city,                       /* Ubicacion del cliente*/
        'idEstado'   => substr($billing_address['state'], 3), /* Ubicacion del cliente*/
        'CP20'       => $first_name . ' ' . $last_name,       /* Nombre del asesor */
        'CP13'       => $data_product,                        /* Nombre del producto */
        'COMENTARIOS' => $customer_note                       /* Nota de pedido */
    );

    /* ********************************* Request Upnify ********************************* */
    $url = 'https://api.salesup.com/integraciones/P25APF1D94E74-F7C5-4A02-94FE-EFCBB2A6629C';
    $request = curl_init();
    curl_setopt($request, CURLOPT_URL, $url);
    curl_setopt($request, CURLOPT_POST, 1);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($request, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($request, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($request, CURLOPT_TIMEOUT, 60);
    $result = curl_exec($request);
    curl_close($request);

    /********************************** Validate request **********************************/
    $array_result   = json_decode($result);
    $code           = $array_result[0]->code;
    $object_status  = $array_result[0]->details[0];
    $ruta_archivo   = ORD_PATH . 'info/logs.txt';

    // Registra el resultado de la petición API
    file_put_contents($ruta_archivo, $current_time . ' - ' . $result . "\n", FILE_APPEND);

    if ($code == 0) {
        // El prospecto no existe, lo creamos y creamos la oportunidad
        if (!property_exists($object_status, 'action')) {
            // Guadamos el tkContacto como un metadato del pedido
            update_post_meta($order_id, 'tkContacto', $object_status->tkContacto);
        }

        // Registrar nueva oportunidad  
        $res = osy_create_opportunity($object_status->tkContacto, $order_id, $info_product);

        if ($res['res']) {
            $array_res = json_decode($res['data']);
            // Guardar el token de la oportunidad como metadato de producto
            update_post_meta($order_id, 'tkOportunidad', $array_res[0]->details[0]->tkOportunidad);
            file_put_contents($ruta_archivo, $current_time . ' - ' . $res['data'] . "\n", FILE_APPEND);
        } else {
            // Registrar el error al crear la nueva oporrtunidad
            file_put_contents($ruta_archivo, $current_time . ' - ' . $res['msg'] . "\n", FILE_APPEND);
        }
    }
}

add_shortcode('show_data_order', 'show_data_order');
function show_data_order()
{
    $pedido = wc_get_order(5371);
    $output = '';

    // Verificar si el pedido existe
    if (!$pedido) {
        return 'Pedido no encontrado.';
    }

    // Obtener los datos de facturación del pedido
    $billing_city = $pedido->get_billing_city();
    $billing_state = $pedido->get_billing_state();

    // Construir el HTML para mostrar los datos
    $output = '<p>Ciudad de facturación: ' . strtolower($billing_city) . '</p>';
    $output .= '<p>Estado de facturación: ' . $billing_state . '</p>';

    return $output;
}
