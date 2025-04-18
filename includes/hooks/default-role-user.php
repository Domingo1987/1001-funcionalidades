<?php
// Preselecciona el rol 'estudiante' al añadir un nuevo usuario desde el admin
add_action('admin_footer-user-new.php', function () {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rol = document.querySelector('select#role');
            if (rol) {
                rol.value = 'estudiante';
            }
        });
    </script>
    <?php
});
