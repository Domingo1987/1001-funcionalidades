<?php
/**
 * Proporciona una vista de administración para el plugin
 *
 * @since      1.0.0
 * @package    Users1001
 * @author     Domingo Pérez
 */
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="users1001-dashboard-container">
        <div class="users1001-dashboard-card">
            <h2>Información General</h2>
            <?php 
            $year = date('Y');
            $total_users = count_users();
            $users_with_curso = count(get_users(array(
                'meta_key' => 'curso_' . $year,
                'meta_compare' => 'EXISTS'
            )));
            ?>
            <div class="users1001-stats">
                <div class="stats-item">
                    <span class="stats-number"><?php echo $total_users['total_users']; ?></span>
                    <span class="stats-label">Total de usuarios</span>
                </div>
                <div class="stats-item">
                    <span class="stats-number"><?php echo $users_with_curso; ?></span>
                    <span class="stats-label">Usuarios con curso asignado (<?php echo $year; ?>)</span>
                </div>
            </div>
        </div>
        
        <div class="users1001-dashboard-card">
            <h2>Cursos Activos (<?php echo $year; ?>)</h2>
            <?php
            global $wpdb;
            $cursos_stats = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT meta_value as curso, COUNT(*) as total
                    FROM {$wpdb->usermeta}
                    WHERE meta_key = %s AND meta_value != ''
                    GROUP BY meta_value
                    ORDER BY total DESC",
                    'curso_' . $year
                )
            );
            
            if ($cursos_stats) :
            ?>
            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th>Curso</th>
                        <th>Estudiantes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cursos_stats as $curso) : ?>
                    <tr>
                        <td><?php echo esc_html($curso->curso); ?></td>
                        <td><?php echo esc_html($curso->total); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else : ?>
            <p>No hay cursos asignados para este año.</p>
            <?php endif; ?>
        </div>
        
        <div class="users1001-dashboard-card">
            <h2>Centros Activos (<?php echo $year; ?>)</h2>
            <?php
            $centros_stats = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT meta_value as centro, COUNT(*) as total
                    FROM {$wpdb->usermeta}
                    WHERE meta_key = %s AND meta_value != ''
                    GROUP BY meta_value
                    ORDER BY total DESC",
                    'centro_' . $year
                )
            );
            
            if ($centros_stats) :
            ?>
            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th>Centro</th>
                        <th>Estudiantes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($centros_stats as $centro) : ?>
                    <tr>
                        <td><?php echo esc_html($centro->centro); ?></td>
                        <td><?php echo esc_html($centro->total); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else : ?>
            <p>No hay centros asignados para este año.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="users1001-actions">
        <h2>Acciones Rápidas</h2>
        <div class="users1001-buttons">
            <a href="<?php echo admin_url('users.php'); ?>" class="button button-primary">Gestionar Usuarios</a>
            <a href="<?php echo admin_url('admin.php?page=users1001-cursos'); ?>" class="button">Gestionar Cursos</a>
            <a href="<?php echo admin_url('admin.php?page=users1001-centros'); ?>" class="button">Gestionar Centros</a>
        </div>
    </div>
</div>
