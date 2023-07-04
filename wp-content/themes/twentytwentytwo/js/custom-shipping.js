// ACTUALIZACION DE COSTO DE ENVIO 
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

// DATEPICKER
jQuery(document).ready(function($) {
    $('#custom_shipping_cost').on('change', function(){
        var selectSeleccionado = $(this).val();
        var datePickerElement = $('#custom_shipping_date_field')
        console.log('desde datepicker', selectSeleccionado)
        if (selectSeleccionado === 'hood_palermo') {
            datePickerElement.addClass('show');
            console.log('desde datepicker', datePickerElement)
            jQuery( '#custom_shipping_date' ).datepicker({
              dateFormat: 'dd MM yy',
              beforeShowDay: function(date) {
                var day = date.getDay();
                return [(day != 0)];
              }
          });
        }
    });
});

// CAPOS SELECT CON CONDICION
jQuery(function($) {
    // Escucha el evento de cambio de los radio buttons
    $(document.body).on('change', 'input[name="radio_region"]', function(){
        var radioSeleccionado = $(this).val();
        var caba_hoods = $('.caba_hoods');
        var select = $('#custom_shipping_cost');
        // Obtener la opción con el valor "select_opt"
        var optionToDisable = select.find('option[value="select_opt"]');
        // Deshabilitar la opción
        optionToDisable.prop('disabled', true);
        
        if (radioSeleccionado == 'gba') {
            caba_hoods.removeClass('show');
            $('#country_zones').remove();
            var gbaSelect = $('<select>', {id: 'gba_zones'});
            gbaSelect.append($('<option>', {
                id: 'select_opt',
                text: 'Selecicone una opcion',
                disabled: true,
                selected: true
            }));
            gbaSelect.append($('<option>', {
                id: 'caba',
                value: 'caba',
                text: 'Ciudad Autónoma de Buenos Aires'
            }));
            gbaSelect.append($('<option>', {
                id: 'north_zone',
                value: 'north_zone',
                text: 'Zona Norte'
            }));
            gbaSelect.append($('<option>', {
                id: 'west_zone',
                value: 'west_zone',
                text: 'Zona Oeste'
            }));
            gbaSelect.append($('<option>', {
                id: 'south_zone',
                value: 'south_zone',
                text: 'Zona Sur'
            }));


            // Agrega el nuevo campo select al contenedor
            $('#radio_region_field').append(gbaSelect);

            // Escucha el evento de cambio del campo select
            gbaSelect.on('change', function() {
                var opcionSeleccionada = $(this).val();
                
                if (opcionSeleccionada === 'caba') {
                    // Realiza acciones específicas para la opción 'caba'
                    caba_hoods.addClass('show');
                    console.log('Seleccionaste Ciudad Autónoma de Buenos Aires');
                } else {
                    caba_hoods.removeClass('show');
                }

                if (opcionSeleccionada === 'sur_zone') {
                    // Realiza acciones específicas para la opción 'sur_zone'
                    console.log('Seleccionaste Zona Sur');
                }
            });


            console.log('gba seleccionado')
        }
        else if (radioSeleccionado == 'country') {
            caba_hoods.removeClass('show');
            $('#gba_zones').remove();
            var countrySelect = $('<select>', {id: 'country_zones'});
            countrySelect.append($('<option>', {
                id: 'select_opt',
                text: 'Selecicone una opcion',
                disabled: true,
                selected: true
            }));
            countrySelect.append($('<option>', {
                id: 'country_south_zone',
                value: 'country_south_zone',
                text: 'Zona Sur'
            }));
            countrySelect.append($('<option>', {
                id: 'country_north_zone',
                value: 'country_north_zone',
                text: 'Zona Norte'
            }));
            // Agrega el nuevo campo select al contenedor
            $('#radio_region_field').append(countrySelect);
            console.log('country seleccionado')
        }
        
    });
});
