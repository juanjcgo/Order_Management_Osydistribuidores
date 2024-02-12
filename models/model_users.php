<?php

class ORD_USERS
{

    /*********************************Get Token Rest API*********************************/
    public function ord_get_token_api($user_id)
    {
        try {
            $token = get_user_meta($user_id, 'token_access_rest', true);

            if ($token) {
                return [
                    'res'   => true,
                    'token' => $token
                ];
            } else {
                return [
                    'res'  => false,
                    'msg' => 'No existe un token de acceso'
                ];
            }
        } catch (Exception $e) {
            return [
                'res' => false,
                'msg' => ('Excepción capturada' . $e->getMessage() . "\n")
            ];
        }
    }

    /**********************************Get Data One User*********************************/
    public function ord_get_data_user($user_id)
    {
        try {
            $meta_data_user = ['advisor_phone', 'advisor_city', 'advisor_address', 'advisor_birthday', 'advisor_children'];

            foreach ($meta_data_user as $meta_key) {
                if (get_user_meta($user_id, $meta_key, true)) {
                    $meta_value[$meta_key] = get_user_meta($user_id, $meta_key, true);
                }
            }

            return [
                'res'  => true,
                'data' => $meta_value
            ];
        } catch (Exception $e) {
            return [
                'res' => false,
                'msg' => ('Excepción capturada' . $e->getMessage() . "\n")
            ];
        }
    }

    /**********************************Set Data One User*********************************/
    public function ord_set_data_user($user_id, $dataForm)
    {
        try {

            parse_str($dataForm, $variables);

            $meta_data_user = ['advisor_phone', 'advisor_city', 'advisor_address', 'advisor_birthday', 'advisor_children'];

            foreach ($meta_data_user as $meta_key) {
                $meta_value = isset($variables[$meta_key]) ? $variables[$meta_key] : '';
                update_user_meta($user_id, $meta_key, $meta_value);
            }

            return [
                'res' => true,
                'msg' => 'Se actualizó la información exitosamente!'
            ];

        } catch (Exception $e) {
            return [
                'res' => false,
                'msg' => ('Excepción capturada' . $e->getMessage() . "\n")
            ];
        }
    }
}
