<?php
if (!defined('ABSPATH')) {
    exit;
}


// Agrega campos al perfil de usuario
function cienuno_agregar_campos_usuario($user) { ?>
    <h3>Información Académica</h3>
    <table class="form-table">
        <tr>
            <th><label for="curso">Curso</label></th>
            <td><input type="text" name="curso" value="<?php echo esc_attr(get_user_meta($user->ID, 'curso', true)); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="centro">Centro</label></th>
            <td><input type="text" name="centro" value="<?php echo esc_attr(get_user_meta($user->ID, 'centro', true)); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="anio">Año</label></th>
            <td><input type="number" name="anio" value="<?php echo esc_attr(get_user_meta($user->ID, 'anio', true)); ?>" class="small-text" /></td>
        </tr>
    </table>
<?php }
add_action('show_user_profile', 'cienuno_agregar_campos_usuario');
add_action('edit_user_profile', 'cienuno_agregar_campos_usuario');
