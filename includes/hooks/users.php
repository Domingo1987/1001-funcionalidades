<?php
// Interviene el correo de "nuevo usuario creado"
add_filter('wp_new_user_notification_email', 'usuarios1001_modificar_correo_bienvenida', 10, 3);

function usuarios1001_modificar_correo_bienvenida($email_data, $user, $blogname) {
    // Solo intervenir si es un "suscriptor"
    if (in_array('subscriber', $user->roles)) {
        $email_data['subject'] = '¡Bienvenido a 1001Problemas!';
        $email_data['headers'] = ['Content-Type: text/html; charset=UTF-8'];
        $email_data['message'] = '
            <p>Hola <strong>' . esc_html($user->display_name) . '</strong>,</p>
            <p>Tu cuenta ha sido creada exitosamente en <strong>' . esc_html($blogname) . '</strong>.</p>
            <p>Ahora podés iniciar sesión con tu nombre de usuario: <strong>' . esc_html($user->user_login) . '</strong>.</p>
            <p>Accedé aquí: <a href="' . esc_url(wp_login_url()) . '">' . esc_url(wp_login_url()) . '</a></p>
            <hr>
            <p style="font-size: 12px; color: #666;">Este mensaje fue generado automáticamente. Si no solicitaste esta cuenta, podés ignorarlo.</p>
        ';
    }

    return $email_data;
}
