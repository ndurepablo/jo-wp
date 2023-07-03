
<?php

/**
 * Add custom field to the checkout page
 */
add_action( 'woocommerce_after_order_notes', function ( $checkout ) {
    echo '<div id="custom_checkout_field"><h2>' . __( 'Order Meta' ) . '</h2>';

    // Obtener el subtotal del pedido
    $subtotal = WC()->cart->subtotal;
    // Mostrar el valor del subtotal en un encabezado h1
    echo '<h1>Subtotal: ' . $subtotal . '</h1>';

    $gba_shipping_cost = ($subtotal > 100) ? 0 : 300; 
    $country_shipping_cost = ($subtotal > 500) ? 0 : 500; 
    
    // Opción 1: Domicilio en Capital Federal y GBA
    woocommerce_form_field( 'custom_shipping_cost1', array(
        'type'    => 'radio',
        'class'   => array('form-row-radio'),
        'options' => array(
            $gba_shipping_cost => __('Domicilio en Capital Federal y GBA', 'woocommerce'),
            $country_shipping_cost => __('Barrio privado / Country', 'woocommerce'),
        ),
        
    ), $checkout->get_value( 'custom_shipping_cost1' ) );

    $hoodsJsonFile = get_template_directory_uri() . '/js/hoods.json';
    $gba_region_list = json_reader($hoodsJsonFile);

    $countriesJsonFile = get_template_directory_uri() . '/js/countries.json';
    $countries_region_list = json_reader($countriesJsonFile);

    $countries = json_reader_hoods($countriesJsonFile, 'zona_norte', 'countries');
    woocommerce_form_field( 'countries_region', array(
        'type'    => 'select',
        'class'   => array('form-row-radio'),
        'options' => $countries,
        
    ), $checkout->get_value( 'countries_region' ) );

    $hoods = json_reader_hoods($hoodsJsonFile, 'caba', 'hoods');
    $options = array();
    foreach ($hoods as $region) {
        $name = $region['name'];
        $shippingCost = $region['shippingCost'];
        $options[$shippingCost] = $name;
    }
    woocommerce_form_field('custom_shipping_cost', array(
        'type'    => 'select',
        'class'   => array('form-row-radio'),
        'options' => $options,
    ), $checkout->get_value('custom_shipping_cost'));

    echo '</div>';
} );

/**
 * Checkout Process
 */
add_action( 'woocommerce_checkout_process', 'customised_checkout_field_process' );
function customised_checkout_field_process() {
    if ( ! $_POST['custom_shipping_cost'] ) {
        wc_add_notice( __( 'Please enter cost!' ), 'error' );
    }
}

/**
 * Update the value given in custom field
 */
add_action( 'woocommerce_checkout_update_order_meta', 'custom_checkout_field_update_order_meta' );
function custom_checkout_field_update_order_meta( $order_id ) {
    if ( ! empty( $_POST['custom_shipping_cost'] ) ) {
        update_post_meta( $order_id, 'custom_shipping_cost', sanitize_text_field( $_POST['custom_shipping_cost'] ) );
    }
}

// Agregar campo de costo de envío al resumen de la orden
add_action( 'wp', 'reset_custom_shipping_cost' );
function reset_custom_shipping_cost() {
    if ( ! is_admin() && is_cart() ) {
        WC()->session->set( 'custom_shipping_cost', 0 ); // Establecer el valor en cero al cargar la página del carrito
    }
    elseif ( ! is_admin() && is_checkout() ) {
        WC()->session->set( 'custom_shipping_cost', 0 ); // Establecer el valor en cero al cargar la página del carrito
    }
}

add_action( 'woocommerce_cart_calculate_fees', 'agregar_costo_envio' );
function agregar_costo_envio() {
    $custom_shipping_cost = WC()->session->get( 'custom_shipping_cost' ); // Obtener el valor actual de custom_shipping_cost
    
    if ( ! is_numeric( $custom_shipping_cost ) ) {
        $custom_shipping_cost = 0; // Establecer el valor en cero si no es un número válido
    }
    
    $costo_envio = (float) $custom_shipping_cost; // Convertir el valor a flotante (o ajusta el tipo de dato según sea necesario)
    WC()->cart->add_fee( 'Costo de envío', $costo_envio );
}


// Actualizar el costo de envío al cambiar el campo custom_shipping_cost
add_action( 'wp_ajax_actualizar_costo_envio', 'actualizar_costo_envio' );
add_action( 'wp_ajax_nopriv_actualizar_costo_envio', 'actualizar_costo_envio' );
function actualizar_costo_envio() {
    if ( isset( $_POST['custom_shipping_cost'] ) ) {
        $customShippingCost = sanitize_text_field( $_POST['custom_shipping_cost'] );
        WC()->session->set( 'custom_shipping_cost', $customShippingCost );
        WC()->cart->calculate_shipping();
        WC()->cart->calculate_totals();
    }
    wp_die();
}



