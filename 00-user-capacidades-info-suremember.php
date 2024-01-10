<?php
/**
 * Plugin Name: 00 Informacion de usuario, SUREMEMBERS OFF IMPORTANTE NO BORRAR
 * Plugin URI: https://webyblog.es/
 * Description: Muestra información detallada del usuario actual, incluidas sus capacidades, membresia, suremembers_user_access_group, si está logueado.
 * Version: 10-01-2024
 * Author: Juan Luis Martel
 * Author URI: https://www.webyblog.es
 * License: GPLv3 or later
 */



if ( ! defined( 'ABSPATH' ) ) exit;


// El hook por defecto es 'generate_before_header'.
define('JLMR_HOOK_NAME_CAPABILITIES', 'generate_before_header');

// Función para mostrar la información detallada del usuario.
function jlmr_show_user_capabilities_info() {
    require_once(ABSPATH . "wp-load.php");

    // Verificar si el usuario está logueado
    if (is_user_logged_in()) {

        // Obtén el usuario actual
        $current_user = wp_get_current_user();

        echo '<h2>Datos del usuario y Capacidades</h2>';
        echo "ID del Usuario: " . $current_user->ID . "<br/>";
        echo "Nombre del Usuario: " . $current_user->user_login . "<br/>";
        echo "Correo electrónico del Usuario: " . $current_user->user_email . "<br/>";

        // Obtén los metadatos del usuario
        $access_groups = get_user_meta($current_user->ID, 'suremembers_user_access_group', false);
        if (!empty($access_groups)) {
            foreach ($access_groups as $group) {
                foreach ($group as $group_id) {
                    echo "Grupo al que puede acceder el usuario: " . $group_id . "<br/>";
                    $access_group_meta = get_user_meta($current_user->ID, 'suremembers_user_access_group_' . $group_id, false);
                    if (!empty($access_group_meta)) {
                        foreach ($access_group_meta as $meta) {
                            foreach ($meta as $key => $value) {
                                if ($key === 'created') {
                                    // Convertir la fecha de Unix timestamp a formato legible
                                    echo "Fecha de Creación: " . date("d-m-Y H:i:s", $value) . "<br/>";
                                } else {
                                    echo ucfirst($key) . ": " . $value . "<br/>";
                                }
                            }
                        }
                    }
                }
            }
        }
        // Obtener y mostrar todas las capacidades del usuario
        $capabilities = $current_user->get_role_caps();
        echo 'Capacidades del usuario:<br/>';
        foreach ($capabilities as $cap => $granted) {
            echo $cap . ' : ' . ($granted ? 'Sí' : 'No') . "<br/>";
        }

        // Mostrar todos los roles del usuario
        echo 'Roles del usuario:<br/>';
        foreach ($current_user->roles as $role) {
            echo $role . "<br/>";
        }

        // Obtener y mostrar la capacidad 'suremember-usuario-activo' del usuario
        if (isset($capabilities['suremember-usuario-activo'])) {
            echo 'Capacidad "suremember-usuario-activo": ' . ($capabilities['suremember-usuario-activo'] ? 'Sí' : 'No') . "<br/>";
        }

    } else {
        echo "El usuario no está logueado.";
    }
}

// Añadir la acción al hook especificado.
add_action(JLMR_HOOK_NAME_CAPABILITIES, 'jlmr_show_user_capabilities_info');

?>
