<?php
// Agrega roles personalizados al activar el plugin
function cienuno_agregar_roles_personalizados() {
    add_role('creador_ia', 'Creador-IA', ['read' => true, 'upload_files' => true]);
    add_role('estudiante', 'Estudiante', ['read' => true]);
}
register_activation_hook(__FILE__, 'cienuno_agregar_roles_personalizados');
