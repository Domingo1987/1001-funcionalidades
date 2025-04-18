<?php
if (!defined('ABSPATH')) {
    exit;
}

// Obtener todos los cursos
$admin = new Admin();
$cursos = $admin->get_all_cursos();
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="users1001-cursos-container">
        <div class="users1001-form-container">
            <h2>Agregar Nuevo Curso</h2>
            <div id="mensaje-curso" class="notice" style="display: none;"></div>
            
            <form id="agregar-curso-form">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="nuevo-curso">Nombre del curso</label></th>
                        <td>
                            <input type="text" id="nuevo-curso" name="nuevo-curso" class="regular-text" required>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button id="submit-curso" class="btn btn-success">Agregar Curso</button>
                </p>
            </form>
        </div>
        
        <div class="users1001-lista-container">
            <h2>Cursos Disponibles</h2>
            
            <?php if (!empty($cursos)) : ?>
                <table class="table table-striped table-hover table-scroll" style="width: 100%;">
                    <thead style="background-color: #32b643; color: #ffffff;">
                        <tr>
                            <th class="text-left">📚 Nombre del Curso</th>
                            <th class="text-center">🛠️ Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="lista-cursos">
                        <?php foreach ($cursos as $curso) : ?>
                            <tr>
                                <td><?php echo esc_html($curso); ?></td>
                                <td class="text-center">
                                    <button class="btn btn-error btn-sm eliminar-curso" data-curso="<?php echo esc_attr($curso); ?>">
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php else : ?>
                <p>No hay cursos disponibles.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
