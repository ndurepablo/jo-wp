<?php
// function my_custom_code() {
//     // Ruta al archivo JSON
//     $hoodsjsonFile = 'wp-content/themes/twentytwentytwo/js/hoods.json';
//     // Leer el contenido del archivo JSON
//     $jsonString = file_get_contents($hoodsjsonFile);
//     // Decodificar el JSON en una estructura de datos de PHP
//     $data = json_decode($jsonString, true);

//     // Verificar si la decodificación del JSON fue exitosa
//     if ($data === null) {
//         echo 'Error al decodificar el JSON.';
//     } else {
//         // El JSON se ha importado correctamente, puedes trabajar con los datos aquí
//         // var_dump($data);
//         echo 'ok <br /><br />'; ;
//     }
//     foreach ($data as $regionName => $region) {
//         echo "Región: $regionName<br>";

//         foreach ($region as $id => $location) {
//             echo "ID: $id<br>";
//             echo "Nombre: " . $location['name'] . "<br>";
//             echo "ID del datepicker: " . $location['id'] . "<br>";
//             echo "Calcular fecha deshabilitada: " . $location['calculateDisabledDate'] . "<br>";
//             echo "Rango de días: " . $location['rangeDays'] . "<br>";
//             echo "Días: " . implode(', ', $location['days']) . "<br>";
//             echo "Costo de envío: " . $location['shippingCost'] . "<br>";
//             echo "Umbral de envío gratuito: " . $location['freeShippingThreshold'] . "<br>";
//             echo "<br>";
//         }
//     }
// }

// // Enganchar la función a un gancho adecuado en WordPress
// add_action('wp', 'my_custom_code');

/**
 * Función que lee un archivo JSON y retorna una lista.
 *
 * @param string $path La ruta al archivo JSON (requerido).
 * @return array La lista obtenida del archivo JSON.
 * @throws Exception Si no se proporciona un valor para $path.
 */
function json_reader($path) {
    if (empty($path)) {
        throw new Exception('El parámetro $path es requerido.');
    }
    
    // Ruta al archivo JSON
    $hoodsjsonFile = $path;
    // Leer el contenido del archivo JSON
    $jsonString = file_get_contents($hoodsjsonFile);
    // Decodificar el JSON en una estructura de datos de PHP
    $data = json_decode($jsonString, true);
    $region_list = array();
    foreach ($data as $regionName => $region) {
        $region_list[$regionName] = $region['label'];
    }
    // Retorna la lista obtenida del archivo JSON
    return $region_list;
};

/**
 * Función que lee un archivo JSON y retorna una lista.
 *
 * @param string $path La ruta al archivo JSON (requerido).
 * @return array La lista obtenida del archivo JSON.
 * @throws Exception Si no se proporciona un valor para $path.
 */
function json_reader_hoods($path, $region, $type) {
    if (empty($path)) {
        throw new Exception('El parámetro $path es requerido.');
    }
    
    // Ruta al archivo JSON
    $hoodsjsonFile = $path;
    // Leer el contenido del archivo JSON
    $jsonString = file_get_contents($hoodsjsonFile);
    // Decodificar el JSON en una estructura de datos de PHP
    $data = json_decode($jsonString, true);
    $region_list = array();
    if (isset($data[$region][$type])) {
        foreach ($data[$region][$type] as $country) {
            $region_list[] = array(
                'name' => $country['name'],
                'slug' => $country['slug']
            );
        }
    }
 
    return $region_list;
};

function json_reader_shipping_cost($path, $region, $type) {
    if (empty($path)) {
        throw new Exception('El parámetro $path es requerido.');
    }
    
    // Ruta al archivo JSON
    $hoodsjsonFile = $path;
    // Leer el contenido del archivo JSON
    $jsonString = file_get_contents($hoodsjsonFile);
    // Decodificar el JSON en una estructura de datos de PHP
    $data = json_decode($jsonString, true);
    $region_list = array();
    if (isset($data[$region][$type])) {
        foreach ($data[$region][$type] as $country) {
            $region_list += [$country['slug'] => $country['shippingCost']];
        
        }
    }
 
    return $region_list;
};