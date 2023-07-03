

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

    
    woocommerce_form_field( 'custom_shipping_cost',
        array(
            'type'  => 'number',
            'required'  => true,
            'label' => __( 'Custom Shipping Cost' ),
        ),
        $checkout->get_value( 'custom_shipping_cost' )
    );

    // echo '</div>';
    $hoodsJsonFile = get_template_directory_uri() . '/js/hoods.json';
    $gba_region_list = json_reader($hoodsJsonFile);
    // woocommerce_form_field( 'gba_region', array(
    //     'type'    => 'select',
    //     'class'   => array('form-row-radio'),
    //     'options' => $gba_region_list,
        
    // ), $checkout->get_value( 'gba_region' ) );


    $countriesJsonFile = get_template_directory_uri() . '/js/countries.json';
    $countries_region_list = json_reader($countriesJsonFile);
    // woocommerce_form_field( 'countries_region', array(
    //     'type'    => 'select',
    //     'class'   => array('form-row-radio'),
    //     'options' => $countries_region_list,
        
    // ), $checkout->get_value( 'countries_region' ) );

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




    // woocommerce_form_field( 'custom_shipping_date',
    //     array(
    //         'type'  => 'date',
    //         'required'  => true,
    //         'label' => __( 'Custom Shipping Date' ),
    //     ),
    //     $checkout->get_value( 'custom_shipping_date' )
    // );
    // echo '</div>';
} );

/**
 * Checkout Process
 */
add_action( 'woocommerce_checkout_process', 'customised_checkout_field_process' );
function customised_checkout_field_process() {
    if ( ! $_POST['custom_shipping_cost'] ) {
        wc_add_notice( __( 'Please enter cost!' ), 'error' );
    }
    
    // if ( ! $_POST['custom_shipping_date'] ) {
    //     wc_add_notice( __( 'Please enter date!' ), 'error' );
    // }
}

/**
 * Update the value given in custom field
 */
add_action( 'woocommerce_checkout_update_order_meta', 'custom_checkout_field_update_order_meta' );
function custom_checkout_field_update_order_meta( $order_id ) {
    if ( ! empty( $_POST['custom_shipping_cost'] ) ) {
        update_post_meta( $order_id, 'custom_shipping_cost', sanitize_text_field( $_POST['custom_shipping_cost'] ) );
    }
    
    // if ( ! empty( $_POST['custom_shipping_date'] ) ) {
    //     update_post_meta( $order_id, 'custom_shipping_date', sanitize_text_field( $_POST['custom_shipping_date'] ) );
    // }
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




/**
 * Add custom field to the checkout page
 */
// add_action( 'woocommerce_after_order_notes', function ( $checkout ) {
//     echo '<div id="custom_checkout_field"><h2>' . __( 'Order Meta' ) . '</h2>';

//     echo '<div class="form-row form-row-wide">';
    
//     // Opción 1: Domicilio en Capital Federal y GBA
//     woocommerce_form_field( 'custom_shipping_location', array(
//         'type'    => 'radio',
//         'class'   => array('form-row-radio'),
//         'options' => array(
//             'capital_gba' => __('Domicilio en Capital Federal y GBA', 'woocommerce'),
//             'barrio_privado' => __('Barrio privado / Country', 'woocommerce'),
//         ),
//     ), $checkout->get_value( 'custom_shipping_location' ) );

//     echo '</div>';

//     echo '</div>';
	
// } );


// add_action( 'woocommerce_after_order_notes', function ( $checkout ) {
//     echo '<div id="custom_checkout_field"><h2>' . __( 'Order Meta' ) . '</h2>';

//     echo '<div class="form-row form-row-wide">';
    
//     // Opción 1: Domicilio en Capital Federal y GBA
//     woocommerce_form_field( 'custom_shipping_location', array(
//         'type'    => 'radio',
//         'class'   => array('form-row-radio'),
//         'options' => array(
//             '10' => __('Domicilio en Capital Federal y GBA', 'woocommerce'),
//             '20' => __('Barrio privado / Country', 'woocommerce'),
//         ),
//     ), $checkout->get_value( 'custom_shipping_location' ) );

//     echo '</div>';

//     echo '<div class="form-row form-row-wide custom-shipping-zone">';
//     // Opción 2: Zona de envío
//     woocommerce_form_field( 'custom_shipping_zone', array(
//         'type'    => 'select',
//         'class'   => array('form-row-select'),
//         'label'   => __('Zona de envío', 'woocommerce'),
//         'options' => array(
//             ''          => __('Seleccionar zona', 'woocommerce'),
//             'zona_sur'  => __('Zona Sur', 'woocommerce'),
//             'zona_norte' => __('Zona Norte', 'woocommerce'),
//         ),
//     ), $checkout->get_value( 'custom_shipping_zone' ) );
//     echo '</div>';

//     echo '<div class="form-row form-row-wide custom-shipping-zone">';
//     // Opción 2: Zona de envío del país
//     woocommerce_form_field( 'custom_shipping_zone_country', array(
//         'type'    => 'select',
//         'class'   => array('form-row-select'),
//         'label'   => __('Zona de envío del país', 'woocommerce'),
//         'options' => array(
//             ''          => __('Seleccionar zona', 'woocommerce'),
//             'zona_sur'  => __('Country Zona Sur', 'woocommerce'),
//             'zona_norte' => __('Country Zona Norte', 'woocommerce'),
//         ),
//     ), $checkout->get_value( 'custom_shipping_zone_country' ) );
//     echo '</div>';

//     echo '<div class="form-row form-row-wide custom-shipping-zone-hoods">';
//     // Opción 3: Zona de envío de los barrios
//     woocommerce_form_field( 'custom_shipping_zone_hoods', array(
//         'type'    => 'select',
//         'class'   => array('form-row-select'),
//         'label'   => __('Zona de envío de los barrios', 'woocommerce'),
//         'options' => array(
//             ''            => __('Seleccionar zona', 'woocommerce'),
//             'caballito'   => __('Caballito', 'woocommerce'),
//             'villa_urquiza' => __('Villa Urquiza', 'woocommerce'),
//         ),
//     ), $checkout->get_value( 'custom_shipping_zone_hoods' ) );
//     echo '</div>';

//     echo '</div>';

//     // Agregar el script JavaScript
//     echo '<script>
//         jQuery(function($){
//             var $customShippingLocation = $(\'input[name="custom_shipping_location"]\'),
//                 $customShippingZone = $(\'#custom_shipping_zone_field\'),
//                 $customShippingZoneCountry = $(\'#custom_shipping_zone_country_field\'),
//                 $customShippingZoneHoods = $(\'#custom_shipping_zone_hoods_field\');
            
//             // Ocultar los campos de zona de envío al cargar la página
//             $customShippingZone.hide();
//             $customShippingZoneCountry.hide();
//             $customShippingZoneHoods.hide();
            
//             // Mostrar u ocultar los campos de zona de envío según la selección del radio button
//             $customShippingLocation.change(function(){
//                 if ($customShippingLocation.filter(\':checked\').val() === \'capital_gba\') {
//                     $customShippingZone.slideDown();
//                     $customShippingZoneCountry.slideUp();
//                 } else if ($customShippingLocation.filter(\':checked\').val() === \'barrio_privado\') {
//                     $customShippingZone.slideUp();
//                     $customShippingZoneCountry.slideDown();
//                 } else {
//                     $customShippingZone.slideUp();
//                     $customShippingZoneCountry.slideUp();
//                 }
//             });
            
//             // Mostrar u ocultar el campo de zona de envío de los barrios según la selección de "Zona Norte" en la opción de zona de envío
//             $(\'#custom_shipping_zone\').change(function(){
//                 if ($(this).val() === \'zona_norte\' && $customShippingLocation.filter(\':checked\').val() === \'capital_gba\') {
//                     $customShippingZoneHoods.slideDown();
//                 } else {
//                     $customShippingZoneHoods.slideUp();
//                 }
//             });
//         });
//     </script>';
// } );



