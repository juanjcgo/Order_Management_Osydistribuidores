<?php
/* API USERS*/
function ord_manage_request_users()
{
    register_rest_route(
        "ord",
        "ord_get_data_user",
        array(
            'methods'  => 'POST',
            'callback' => 'ord_get_data_user_callback'
        )
    );

    register_rest_route(
        "ord",
        "ord_get_token",
        array(
            'methods'  => 'POST',
            'callback' => 'ord_get_token_callback'
        )
    );

    register_rest_route(
        "ord",
        "ord_set_data_user",
        array(
            'methods'  => 'POST',
            'callback' => 'ord_set_data_user_callback'
        )
    );
}


/****************************** Update View Note *******************************/
function ord_get_token_callback($data_request)
{
    try {

        require_once ORD_PATH . "models/model_users.php";
        if (!class_exists('ORD_USERS')) {
            return [
                'res' => false,
                'msg' => 'Hubo un error interno, por favor notificar al desarrollador',
            ];
        }

        $user_id    = decrypt_user_id($data_request['user_id']);
        $class_user = new ORD_USERS();
        return $class_user->ord_get_token_api($user_id);
        
    } catch (Exception $e) {
        return [
            'res' => false,
            'msg' => ('Excepción capturada' . $e->getMessage() . "\n")
        ];
    }
}

/****************************** Get Meta Data User *******************************/
function ord_get_data_user_callback($data_request)
{
    try {
        require_once ORD_PATH . "models/model_users.php";
        if (!class_exists('ORD_USERS')) {
            return [
                'res' => false,
                'msg' => 'Hubo un error interno, por favor notificar al desarrollador',
            ];
        }

        $user_id    = decrypt_user_id($data_request['user_id']);
        $class_user = new ORD_USERS();
        $real_token = $class_user->ord_get_token_api($user_id);
        $token_header = $data_request->get_header('Authorization');
        if (preg_match('/Bearer\s(\S+)/', $token_header, $matches)) {
            $token = $matches[1];
        }

        if ($real_token['res'] && $real_token['token'] === $token) {
            return $class_user->ord_get_data_user($user_id);
        } else {
            return $real_token;
        }
    } catch (Exception $e) {
        return [
            'res' => false,
            'msg' => ('Excepción capturada' . $e->getMessage() . "\n")
        ];
    }
}

/****************************** Get Meta Data User *******************************/
function ord_set_data_user_callback($data_request)
{
    try {
        require_once ORD_PATH . "models/model_users.php";
        if (!class_exists('ORD_USERS')) {
            return [
                'res' => false,
                'msg' => 'Hubo un error interno, por favor notificar al desarrollador',
            ];
        }

        $user_id      = decrypt_user_id($data_request['user_id']);
        $class_user   = new ORD_USERS();
        $real_token   = $class_user->ord_get_token_api($user_id);
        $token_header = $data_request->get_header('Authorization');
        $dataForm     = $data_request['dataForm'];

        if (preg_match('/Bearer\s(\S+)/', $token_header, $matches)) {
            $token = $matches[1];
        }

        if ($real_token['res'] && $real_token['token'] === $token) {
            return $class_user->ord_set_data_user($user_id, $dataForm);
        } else {
            return $real_token;
        }
    } catch (Exception $e) {
        return [
            'res' => false,
            'msg' => ('Excepción capturada' . $e->getMessage() . "\n")
        ];
    }
}

add_action("rest_api_init", "ord_manage_request_users");
