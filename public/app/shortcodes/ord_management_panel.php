<?php
wp_enqueue_style("ord_bootstrap_css");
wp_enqueue_style("ord_bootstrap_icons");
wp_enqueue_style("ord_datatables_css");
wp_enqueue_style("ord_order_management_css");
wp_enqueue_style("ord_loader_css");
/* wp_enqueue_script("ord_jquey_js"); */
wp_enqueue_script("ord_bootstrap_1_js");
wp_enqueue_script("ord_bootstrap_2_js");
wp_enqueue_script("ord_service_order_js");
wp_enqueue_script("ord_order_management_js");
wp_enqueue_script("ord_datatables_js");


$user_id = encrypt_user_id(get_current_user_id());

$args = array(
    'post_type' => 'novedades',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC'
);
$novedades = get_posts($args);

?>


<div class="row">

    <div class="col-12 col-md-12 col-lg-3 mt-3 ord-sidebar-categories">

        <h3 class="ord-novedades-title"><span>(<?php echo count($novedades); ?>) </span>Novedades</h3>
        <div class="ord-box-novedades">

            <?php if ($novedades) { ?>
                <div class="accordion" id="ord_accordion_novedades">
                    <?php foreach ($novedades as $post) { ?>
                        <?php
                        setup_postdata($post);

                        (get_the_post_thumbnail_url($post->ID, 'large')) ? $thumb_url = get_the_post_thumbnail_url($post->ID, 'large') : $thumb_url = plugins_url('../../../public/assets/img/new.jpg', __FILE__);

                        ?>

                        <div class="accordion-item">
                            <h2 class="accordion-header">

                                <div class="row accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#flush-collapse-<?php echo $post->ID; ?>" aria-expanded="false" aria-controls="flush-collapse-<?php echo $post->ID; ?>">
                                    <div class="col-3">
                                        <img class="ord-img-novedad" src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                                    </div>
                                    <div class="col-9">
                                        <h5 class="ord-novedad-title"><?php echo $post->post_title; ?></h5>
                                        <p class="ord-novedad-date"><?php echo $post->post_modified; ?></p>
                                    </div>
                                </div>


                            </h2>
                            <div id="flush-collapse-<?php echo $post->ID; ?>" class="accordion-collapse collapse" data-bs-parent="#ord_accordion_novedades">
                                <div class="accordion-body">
                                    <p class="ord-novedad-content"><?php echo $post->post_content; ?></p>
                                </div>
                            </div>
                        </div>


                    <?php } ?>
                </div>
                <?php wp_reset_postdata(); ?>
            <?php } else { ?>
                <h6 class="ord-no-found-novedades">No hay novedades</h6>
            <?php } ?>

        </div>

        <div class="ord-nav-sidebar">
            <ul class="list-group list-group-flush">
                <li class="list-group-item"> <i class="bi bi-house"></i><a href="/">Home</a></li>
                <li class="list-group-item"><i class="bi bi-cart3"></i><a href="/tienda">Productos</a></li>
                <?php
                $menu_items = wp_get_nav_menu_items('menu');
                if ($menu_items) {
                    foreach ($menu_items as $menu_item) {
                        echo '<li class="list-group-item"><i class="bi bi-bag-check"></i><a href="' . $menu_item->url . '">' . $menu_item->title . '</a></li>';
                    }
                }
                ?>
            </ul>
        </div>


    </div>
    <div class="col-xs-12 col-md-12 col-lg-9 mt-3 ord-profit-box">

        <h5 class="mb-3">Comisión por estado de pedido</h5>

        <div class="row mb-5">
            <div class="col-6 col-sm-3 mb-3">
                <div class="card ord-order-status" data-status-order="wc-paid">
                    <div class="card-body">
                        <h5 class="card-title">Pagos</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Ganancia (15%)</h6>
                        <p class="card-text ord-profit-paid"></p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-3">
                <div class="card ord-order-status" data-status-order="wc-delivered">
                    <div class="card-body">
                        <h5 class="card-title">Entregados</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Ganancia (15%)</h6>
                        <p class="card-text ord-profit-delivered"></p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-3">
                <div class="card ord-order-status" data-status-order="wc-completed">
                    <div class="card-body">
                        <h5 class="card-title">Despachados</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Ganancia (15%)</h6>
                        <p class="card-text ord-profit-completed"></p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-3">
                <div class="card ord-order-status" data-status-order="wc-processing">
                    <div class="card-body">
                        <h5 class="card-title">Registrados</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Ganancia (15%)</h6>
                        <p class="card-text ord-profit-processing"></p>
                    </div>
                </div>
            </div>
        </div>



        <h5 class="center mb-3">Pedidos recientes</h5>
        <div class="card p-md-4 pb-5 ord-box-table" style="width: 100%;">

            <nav class="navbar navbar-expand-lg bg-body-tertiary mt-2 mb-3 ord-filter-web">
                <div class="container-fluid">

                    <div class="collapse navbar-collapse" id="navbarNav">

                        <ul class="navbar-nav">
                            <li class="nav-item ord-order-status" data-status-order="wc-all">
                                <a class="nav-link" href="#">Todos</a>
                            </li>
                            <li class="nav-item ord-order-status" data-status-order="wc-paid">
                                <a class="nav-link" href="#">Pagados</a>
                            </li>
                            <li class="nav-item ord-order-status" data-status-order="wc-delivered">
                                <a class="nav-link " aria-current="page" href="#">Entregados</a>
                            </li>
                            <li class="nav-item ord-order-status" data-status-order="wc-completed">
                                <a class="nav-link " aria-current="page" href="#">Despachados</a>
                            </li>
                            <li class="nav-item ord-order-status" data-status-order="wc-processing">
                                <a class="nav-link" href="#">Registrados</a>
                            </li>
                            <li class="nav-item ord-order-status" data-status-order="wc-pending">
                                <a class="nav-link" href="#">Pendientes</a>
                            </li>
                            <li class="nav-item ord-order-status" data-status-order="wc-failed">
                                <a class="nav-link" href="#">Fallidos</a>
                            </li>
                            <li class="nav-item ord-order-status" data-status-order="wc-cancelled">
                                <a class="nav-link" href="#">Cancelado</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>


            <div class="mb-3 pt-3 ord-filter-box-mov">
                <label class="mb-2" for="ord_filter_mov">Filtrar por estado</label>
                <select class="form-select" aria-label="Filtrar pedidos" id="ord_filter_mov">
                    <option value="wc-all">Todos</option>
                    <option value="wc-paid">Pagados</option>
                    <option value="wc-delivered">Entregados</option>
                    <option value="wc-completed">Despachados</option>
                    <option value="wc-processing">Registrados</option>
                    <option value="wc-pending">Pendientes</option>
                    <option value="wc-failed">Fallidos</option>
                    <option value="wc-cancelled">Cancelado</option>
                </select>
            </div>



            <table id="ord_data_orders" class="table table-hover table-light table-striped" width="100%" data-info-order="<?php echo $user_id ?>">
                <thead class="table-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Asesor</th>
                        <th scope="col">Total</th>
                        <th scope="col">Comisión</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Celular</th>
                        <th scope="col">Fecha</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="ord_modal_details" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Detalles del pedido</span></h1>
                <button type="button" class="btn-close" id="ord_btn_close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="container">
                    <ul class="nav nav-tabs">
                        <li class="nav-item ord-close-btn">
                            <a class="nav-link active" data-toggle="tab" href="#tab1">Detalles del pedido</a>
                        </li>
                        <li class="nav-item ord-close-btn">
                            <a class="nav-link" data-toggle="tab" href="#tab2">Detalles del cliente</a>
                        </li>
                        <li class="nav-item ord-note-form" id="ord_view">
                            <a class="nav-link" data-toggle="tab" href="#tab3">Notas</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div id="tab1" class="tab-pane fade show active">

                            <div class="ord_lists_box">

                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">ID de pedido: <span id="ord_id_order"></span></li>
                                    <li class="list-group-item">Fecha de pedido: <span id="ord_date"></span></li>
                                    <li class="list-group-item">Estado del pedido: <span id="ord_status"></span></li>
                                    <li class="list-group-item">Costo de envio: <span id="shipping_total"></span></li>
                                    <li class="list-group-item">Sub Total: <span id="order_subtotal"></span></li>
                                    <li class="list-group-item">Impuesto: <span id="order_total_tax"></span></li>
                                    <li class="list-group-item">Monto de pago: <span id="order_total"></span></li>
                                    <li class="list-group-item">Comisión: <span id="ord_commission"></span></li>
                                </ul>
                                <br>
                                <table id="ord_products" class="table" width="100%">
                                    <thead class="">
                                        <tr>
                                            <th scope="col">Producto</th>
                                            <th scope="col">Cantidad</th>
                                            <th scope="col">Precio</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>

                            </div>

                        </div>
                        <div id="tab2" class="tab-pane fade">
                            <div class="ord_lists_box">

                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">Nombre: <span id="ord_name_customer"></span></li>
                                    <li class="list-group-item">Apellido: <span id="ord_lastname_customer"></span></li>
                                    <li class="list-group-item">Correo electrónico: <span id="ord_email_customer"></span></li>
                                    <li class="list-group-item">Teléfono: <span id="ord_phone_customer"></span></li>
                                    <li class="list-group-item">Dirección Línea 1: <span id="ord_address1_customer"></span></li>
                                    <li class="list-group-item">Dirección Línea 2: <span id="ord_address2_customer"></span></li>
                                    <li class="list-group-item">Ciudad: <span id="ord_city_customer"></span></li>
                                    <li class="list-group-item">Estado / Provincia / Región: <span id="ord_company_customer"></span></li>
                                    <li class="list-group-item">Código ZIP / Código postal: <span id="ord_zipcode_customer"></span></li>
                                    <li class="list-group-item">Pais: <span id="ord_country_customer"></span></li>
                                </ul>
                            </div>
                        </div>

                        <div id="tab3" class="tab-pane fade">
                            <div class="ord_lists_box mt-4 ord-lists-chats">
                                <!-- <h2>Notas del pedido</h2> -->
                                <div class="ord-box-chats"></div>
                            </div>
                        </div>

                    </div>
                </div>



                <div class="modal-footer ord-box-footer">

                    <form id='ord_form_note'>
                        <textarea name="ord_note" placeholder="Escribe tu nota aquí"></textarea>
                        <input type="hidden" name="id_order" id="id_order_note">
                        <button type="submit"><i class="bi bi-send-plus-fill"></i></button>
                    </form>

                    <button type="button" class="btn btn-secondary ord-dismiss-modal" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>