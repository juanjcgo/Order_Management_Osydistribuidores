<?php

function encrypt_user_id($user_id)
{
    return base64_encode($user_id);
}

function decrypt_user_id($id_decrypt)
{
    return base64_decode($id_decrypt);
}
