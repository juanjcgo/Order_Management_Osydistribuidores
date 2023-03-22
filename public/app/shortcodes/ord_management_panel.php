<?php
wp_enqueue_style("ord_bootstrap_css");
wp_enqueue_style("ord_bootstrap_icons");
wp_enqueue_style("ord_datatables_css");
wp_enqueue_style("ord_order_management_css");
wp_enqueue_script("ord_jquey_js");
wp_enqueue_script("ord_bootstrap_1_js");
wp_enqueue_script("ord_bootstrap_2_js");
wp_enqueue_script("ord_order_management_js");
wp_enqueue_script("ord_datatables_js");

?>

<nav class="navbar navbar-dark ord-bg-navbar ">
    <div class="container-fluid">

        <a class="navbar-brand" href="#">Panel de gestion de pedidos</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="offcanvas offcanvas-end text-bg-dark mt-4" tabindex="-1" id="offcanvasDarkNavbar" aria-labelledby="offcanvasDarkNavbarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasDarkNavbarLabel"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <form class="d-flex mt-3" role="search">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Buscar</button>
                </form>
            </div>
        </div>
    </div>
</nav>




<div class="row">

    <div class="col-3 mt-3">
        <ul class="list-group list-group-flush">
            <li class="list-group-item"> <i class="bi bi-house"></i>Home</li>
            <li class="list-group-item"><i class="bi bi-cart3"></i>Productos</li>
            <li class="list-group-item"><i class="bi bi-0-circle"></i>A third item</li>
            <li class="list-group-item"><i class="bi bi-0-circle"></i>A fourth item</li>
            <li class="list-group-item"><i class="bi bi-0-circle"></i>And a fifth one</li>
        </ul>
    </div>
    <div class="col-xs-12 col-md-9 mt-3">
        <h2 class="center">Pedidos recientes</h2>
        <div class="card p-4 ord-box-table" style="width: 100%;">

            <nav class="navbar navbar-expand-lg bg-body-tertiary mt-2 mb-3">
                <div class="container-fluid">

                    <div class="collapse navbar-collapse" id="navbarNav">

                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <i class="fa fa-home"></i>
                                <a class="nav-link active " aria-current="page" href="#">Pedido completado</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Pedido pendiente</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Pedido Pagado</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link disabled">Pedido fallido</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link disabled">Pedido procesando</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>


            <table id="ord_data_orders" class="table table-hover table-light table-striped" width="100%">
                <thead class="table-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Pedido</th>
                        <th scope="col">Total del pedido</th>
                        <th scope="col">Comisión</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Fecha del pedido</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

        </div>
    </div>
</div>



<!-- Button trigger modal -->
<!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ord_modal_details">
  Launch demo modal
</button> -->

<!-- Modal -->
<div class="modal fade" id="ord_modal_details" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Detalles del pedido</span></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

            <div class="container">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tab1">Detalles del pedido</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab2">Detalles del cliente</a>
                    </li>
                </ul>
                    <div class="tab-content">
                        <div id="tab1" class="tab-pane fade show active">
                    
                            <div class="ord_lists_box">

                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">ID de pedido: <span id="ord_id_order"></span></li>
                                    <li class="list-group-item">Fecha de pedido: <span id="ord_date"></span></li>
                                    <li class="list-group-item">Estado del pedido: <span id="ord_status"></span></li>
                                    <li class="list-group-item">Comisión: <span id="ord_commission"></span></li>
                                </ul>



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


                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">Sub Total: <span id="order_price"></span></li>
                                    <li class="list-group-item">Impuesto: <span id="order_tax"> COP $ 0</span></li>
                                    <li class="list-group-item">Monto de pago: <span id="order_payment_amount"> COP $ 0</span></li>
                                </ul>

                            </div>

                        </div>
                        <div id="tab2" class="tab-pane fade">
                            <p>Contenido de la pestaña 2.</p>
                        </div>
    
                    </div>
                </div>

         
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>