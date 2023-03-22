<?php
function ord_script_register()
{

    /* Globals scripts */
    wp_register_script("ord_jquey_js", 'https://code.jquery.com/jquery-3.6.3.min.js');
    wp_register_script("ord_bootstrap_1_js", 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js');
    wp_register_script("ord_bootstrap_2_js", 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js');
    wp_register_script('ord_datatables_js', 'https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.js', array('ord_jquey_js'));

    


    /* Globals styles */
    wp_register_style('ord_bootstrap_css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css');
    wp_register_style('ord_datatables_css', 'https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.css');
   

    //Globals Icons bootstrap
    wp_register_style('ord_bootstrap_icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css');

    
    /* Locals styles */
    wp_register_style("ord_order_management_css", plugins_url("../public/assets/css/order_management.css", __FILE__));


    
    wp_register_script("ord_order_management_js", plugins_url("../public/assets/js/order_management.js", __FILE__), array('ord_jquey_js'), '1.0', true);
    wp_localize_script(
        'ord_order_management_js',
        'ord',
        array(
            'url'      => admin_url('admin-ajax.php'),
            "rest_url" => rest_url("ord")
        )
    ); 

}
add_action("wp_enqueue_scripts", "ord_script_register");