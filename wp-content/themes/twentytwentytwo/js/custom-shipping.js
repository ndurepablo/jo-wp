// jQuery(document).ready(function($) {
//     $('body').on('change', 'input[name=custom_shipping_cost]', function() {
//         console.log('first')
//         var customShippingCost = parseFloat($(this).val());
//         $.ajax({
//             type: 'POST',
//             url: wc_checkout_params.ajax_url,
//             data: {
//                 action: 'actualizar_costo_envio',
//                 custom_shipping_cost: customShippingCost,
//             },
//             success: function(response) {
//                 $('body').trigger('update_checkout');
//             }
//         });
//     });
// });

jQuery(document).ready(function($) {
    $('body').on('change', 'select[id="custom_shipping_cost"]', function() {
        console.log('Cambio de seleccionado')
        var customShippingCost = $(this).val();
        console.log(customShippingCost)
        $.ajax({
            type: 'POST',
            url: wc_checkout_params.ajax_url,
            data: {
                action: 'actualizar_costo_envio',
                custom_shipping_cost: customShippingCost,
            },
            success: function(response) {
                $('body').trigger('update_checkout');
            }
        });
    });
});

jQuery(document).ready(function($) {

  jQuery( '#custom_shipping_date' ).datepicker({
    dateFormat: 'dd MM yy',
    beforeShowDay: function(date) {
      var day = date.getDay();
      return [(day != 3)];
    }
});
});


// jQuery(document).ready(function($){
//   console.log('cargo')
//   // $("#custom_shipping_date").datepicker({
//   //   dateFormat: "yy-mm-dd" // Formato de la fecha (opcional)
// });
// });
// jQuery(document).ready(function($){

//   $("#custom_shipping_date").datepicker({
//     beforeShowDay: function(date) {
//         var day = date.getDay();
//         return [(day != 1 && day != 2)];
//     }
//   });
// })

  

// jQuery('#custom_shipping_cost').change(function() {
//     var customShippingCost = jQuery(this).val();


//     jQuery.ajax({
//         type: 'POST',
//         url: customShippingAjax.ajaxurl,
//         data: {
//             action: 'actualizar_costo_envio',
//             custom_shipping_cost: customShippingCost
//         },
//         success: function() {
//             // Actualizar la página o realizar otras acciones necesarias
//         }
//     });
// });

