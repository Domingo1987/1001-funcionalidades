# Changelog - Plugin 1001 Funcionalidades

## [7.0.0] - 2025-04-14

### Añadido
- Sistema de carga por AJAX en el dashboard: las secciones ahora se cargan **al hacer clic**, mejorando el rendimiento inicial.
- Uso de **transients** para cachear los resultados de cada sección del dashboard por usuario, evitando múltiples consultas SQL repetidas.
- Shortcode `[dashboard]` reorganizado con etiquetas `<details>` y `<summary>`, compatible con carga condicional y visual collapsible.
- Sección `📊 Progreso por categoría` ahora se carga mediante `fetch()` y visualiza los datos con ApexCharts radial bar.
- Sección `📈 Evolución temporal` integrada con gráfico tipo heatmap categorizado por mes y categoría.
- Sección `📌 Resumen general` separada y cargada dinámicamente.
- Nuevo sistema de selección de problemas precargados en el evaluador, con selector de prácticas asociados al curso del usuario.
- Mejora en el shortcode `[evaluador_problemas]` para autocompletar el campo "Problema" si se selecciona uno desde la base.
- Archivos `evaluador.js` y `shortcode.php` refactorizados con lógica modular y control de errores con `console.log()` y `error_log()`.

### Mejorado
- Separación de funciones `renderizar_*` del dashboard por secciones en archivos individuales dentro de `includes/partes`.
- Mejora del rendimiento general al evitar cargar todos los datos del dashboard en la carga inicial de la página.
- `functions.php` y `ajax.php` del dashboard ahora centralizan mejor la lógica de selección y respuesta.
- Visualización de datos solo cuando es necesario, respetando el contexto y reduciendo la carga inicial.
- Soporte completo para usuarios con múltiples años, cursos y centros desde `historico_academico`.

### Corregido
- Problemas con tildes mal codificadas (`Programaciu00f3n`) en `historico_academico`: ahora se usa `JSON_UNESCAPED_UNICODE` para guardar correctamente los caracteres UTF-8.
- Prevención de acceso directo a archivos PHP con `if (!defined('ABSPATH')) exit;` aplicado globalmente.

### Eliminado
- Código obsoleto para precarga masiva de estadísticas en `shortcode.php` que ya no era necesario tras modularización con AJAX.
- Acción AJAX `get_num_problema` no utilizada fue desactivada y comentada tras verificación de uso.

### Ejemplo de uso
```plaintext
[dashboard] → carga el panel completo con secciones colapsadas por defecto y carga bajo demanda.

## [5.0.0] - 2025-04-07
### Añadido
- Shortcode `[problema_azar]` ahora admite un atributo opcional `capitulo` y se adapta automáticamente según si el usuario está logueado o no:
  - Si el usuario **no está logueado**, redirige a un problema publicado aleatorio (con o sin capítulo).
  - Si el usuario **está logueado**, redirige a un problema que **aún no ha comentado**, ya sea general o del capítulo indicado.
- Hook `redirecciones.php` que detecta la URL `/?problema_azar=1&capitulo=X` y ejecuta internamente el shortcode, permitiendo redirecciones limpias desde enlaces o botones sin JS.
- Mejora en el shortcode `[barra_problemas]` para generar enlaces directos con `?problema_azar=1&capitulo=X` y compatibilidad con el nuevo comportamiento unificado.

### Ejemplo de uso
```plaintext
[problema_azar] → problema aleatorio no comentado (si está logueado) o cualquiera (si no).
[problema_azar capitulo="3"] → problema aleatorio del Capítulo 3 (term_id = 56).
```

### Eliminado
- Shortcodes redundantes `[problemas_usuario]` y `[problema_azar_cap]` fueron eliminados y sus funcionalidades integradas completamente en `[problema_azar]`.

### Mejorado
- Lógica centralizada y mantenible para selección de problemas aleatorios.
- Mejor experiencia para usuarios no logueados y rutas de aprendizaje más claras.

## [4.9.0] - 2025-04-07
### Añadido
- Nuevo shortcode `[barra_problemas]` para navegación visual.
- Integración con PicoCSS para estilos modernos en páginas con `[listar_problemas]` o `[barra_problemas]`.
- Integración con SweetAlert2 para alertas personalizadas.
- Hook `page-intros.php` para mostrar introducciones visuales en páginas específicas.
- Hook `language-selector.php` para mejorar la experiencia multilenguaje.
- Organización modular con nuevas carpetas: `ajax`, `hooks`, `shortcodes`, `utils`, `integraciones`.

### Mejorado
- Scripts y estilos solo se cargan si los shortcodes relevantes están presentes.
- `wp_localize_script` para pasar la URL de `admin-ajax.php` a JS.
- Código más limpio y mantenible.
- Función `mostrar_capitulos_o_categoria_ia()` ahora detecta explícitamente el tipo de contenido (`problema` o `intelig_en_artificial`) y selecciona su taxonomía correspondiente (`categorias_problemas` o `ia_categoria`), mejorando la claridad y mantenibilidad del código.

## [4.8.0] - 2025-04-06
### Añadido
- Selector de lenguaje con archivos `language-selector.css` y `language-selector.js`.
- Estilos personalizados en `1001-estilos.css` y scripts en `1001-scripts.js`.
- Carga condicional de PicoCSS si se detecta shortcode relevante.

## [4.7.0] - 2025-04-04
### Añadido
- Migración de funciones desde `functions.php` al plugin `1001-funcionalidades`.
- Preparación para desacoplar completamente el tema y plugin.

## [4.6.0] - 2025-04-02
### Añadido
- Integración inicial con ChatGPT (`chatgpt.php`).
- Hook `comentarios.php` para manejar comentarios anidados y polimórficos.

## [4.5.0] - 2025-03-30
### Añadido
- Estructura de AJAX en `includes/ajax/` con handlers `openai.php` y `problema-num.php`.


