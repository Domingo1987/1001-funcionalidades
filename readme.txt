=== 1001 Funcionalidades ===
Contributors: domingo1987
Donate link: https://1001problemas.com/
Tags: shortcodes, usuarios, estadísticas, personalización, frontend
Requires at least: 5.5
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Plugin personalizado para el sitio 1001problemas.com. Incluye shortcodes, estadísticas de usuario, scripts interactivos, mejoras visuales y más.

== Description ==

Este plugin agrupa todas las funcionalidades personalizadas desarrolladas para 1001problemas.com:

* Shortcodes útiles como [problema_azar], [estadisticas_usuario], [solucion] y más.
* Scripts y estilos visuales para enriquecer la experiencia del estudiante.
* Lógica de control de accesos y redirecciones.
* Estadísticas en vivo del usuario logueado.
* Separación modular del código en componentes reutilizables.
* Compatible con futuras integraciones AJAX o servicios externos.

Este desarrollo busca integrar herramientas simples de gamificación, seguimiento y personalización en entornos educativos.

== Installation ==

1. Subí la carpeta `1001-funcionalidades` al directorio `/wp-content/plugins/` o instalá el ZIP desde el panel de WordPress.
2. Activá el plugin desde la sección "Plugins" en WordPress.
3. Asegurate de haber eliminado (o comentado) cualquier función duplicada del archivo `functions.php` del tema.

== Frequently Asked Questions ==

= ¿Este plugin reemplaza todo el código que tenía en el tema? =
Sí, fue diseñado para centralizar toda la lógica personalizada, mantener limpio el tema y facilitar actualizaciones futuras.

= ¿Cómo puedo agregar nuevos shortcodes? =
Abrí el archivo `includes/shortcodes.php` y seguí el formato de los ya existentes.

== Screenshots ==

1. Estadísticas de usuario personalizadas.
2. Botones de acceso inteligente a problemas no comentados.
3. Integraciones con IDEs externos (Programiz, Replit).

== Changelog ==

= 1.0.0 =
* Primera versión estable con estructura modular.
* Migración de todos los shortcodes y funciones desde el tema BigBangWP.
* Inclusión de sistema de estadísticas y recursos visuales personalizados.

== Upgrade Notice ==

= 1.0.0 =
Migración completa del código personalizado a un entorno de plugin limpio. Se recomienda desactivar funciones duplicadas del tema.
