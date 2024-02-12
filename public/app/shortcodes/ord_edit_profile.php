<?php
wp_enqueue_style("ord_bootstrap_css");
wp_enqueue_style("ord_profile_advisor_css");
wp_enqueue_style("ord_loader_css");
wp_enqueue_script("ord_bootstrap_1_js");
wp_enqueue_script("ord_bootstrap_2_js");
wp_enqueue_script("ord_edit_profile_advisor_js");
wp_enqueue_script("ord_service_users_js");

if (shortcode_exists('ultimatemember_account')) {
    echo do_shortcode('[ultimatemember_account]');
} else {
    echo 'Se requiere la ultima versión de Ultimate Member';
}

$user_id = encrypt_user_id(get_current_user_id());
?>

<div class="card p-3 mb-5">
    <h3 class="mb-3 ord-title-profile">Datos Personales</h3>

    <form id="ord_edit_profile" data-info-advisor="<?php echo $user_id ?>">
        <div class="row">

            <div class="col-12 col-md-6 mb-3">
                <label class="form-label">Numero de celular</label>
                <input type="number" name="advisor_phone" id="advisor_phone" class="form-control" placeholder="Numero de celular" aria-label="Numero de celular">
            </div>
            <div class="col-12 col-md-6 mb-3">
                <label class="form-label">Ciudad</label>
                <input type="text" name="advisor_city" id="advisor_city" class="form-control" placeholder="Ciudad" aria-label="Ciudad">
            </div>

            <div class="col-12 col-md-6 mb-3">
                <label class="form-label">Dirección</label>
                <input type="text" name="advisor_address" id="advisor_address" class="form-control" placeholder="Dirección" aria-label="Dirección">
            </div>
            <div class="col-12 col-md-6 mb-3">
                <label class="form-label">Fecha de nacimiento</label>
                <input type="date" name="advisor_birthday" id="advisor_birthday" class="form-control" placeholder="Fecha de nacimiento" aria-label="Fecha de nacimiento">
            </div>

            <div class="col-12 col-md-6 mb-3">
                <label class="form-label">Tienes hijos</label>
                <select class="form-select" name="advisor_children" id="advisor_children" aria-label="Tienes hijos?">
                    <option selected>Tienes hijos?</option>
                    <option value="1">SI</option>
                    <option value="2">NO</option>
                </select>
            </div>

            <div class="col-12 col-md-6 mb-3 pt-4">
                <button type="submit" class="btn">Actualizar</button>
            </div>

        </div>
    </form>
</div>