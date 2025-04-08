<?php
// Archivo: integraciones/seguridad.php

// Asignar capacidades personalizadas para el tipo de contenido 'inteligen_artificial'
// Se aplica a roles: administrator, editor y author
function add_inteligen_artificial_capabilities() {
    $roles = ['administrator', 'editor', 'author'];

    foreach ($roles as $role_name) {
        $role = get_role($role_name);
        if ($role) {
            $caps = [
                'edit_inteligen_artificial', 'read_inteligen_artificial', 'delete_inteligen_artificial',
                'edit_inteligen_artificials', 'edit_others_inteligen_artificials', 'publish_inteligen_artificials',
                'read_private_inteligen_artificials', 'delete_inteligen_artificials',
                'delete_private_inteligen_artificials', 'delete_published_inteligen_artificials',
                'delete_others_inteligen_artificials', 'edit_private_inteligen_artificials',
                'edit_published_inteligen_artificials', 'create_inteligen_artificials'
            ];
            foreach ($caps as $cap) {
                $role->add_cap($cap);
            }
        }
    }
}
add_action('init', 'add_inteligen_artificial_capabilities');
