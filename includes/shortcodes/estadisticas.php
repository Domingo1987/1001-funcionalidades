<?php
// Shortcode [estadisticas_usuario] - Muestra estadísticas del usuario en tarjetas
function estadisticas_usuario_shortcode() {
    $user_id = get_current_user_id();
    $user_name = get_user_meta($user_id, 'first_name', true);
    $user_email = get_userdata($user_id)->user_email;

    global $wpdb;
    $num_comentarios = $wpdb->get_var(
        $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->comments WHERE user_id = %d AND comment_approved = '1'", $user_id)
    );
    $num_problemas_comentados = $wpdb->get_var(
        $wpdb->prepare("SELECT COUNT(DISTINCT comment_post_ID) FROM $wpdb->comments WHERE user_id = %d AND comment_approved = '1'", $user_id)
    );
    $num_respuestas = $wpdb->get_var(
        $wpdb->prepare("
            SELECT COUNT(*) 
            FROM $wpdb->comments c1
            INNER JOIN $wpdb->comments c2 ON c1.comment_ID = c2.comment_parent
            WHERE c1.user_id = %d AND c2.comment_approved = '1'",
        $user_id)
    );
    $num_problemas_con_respuesta = $wpdb->get_var(
        $wpdb->prepare("
            SELECT COUNT(DISTINCT c1.comment_post_ID)
            FROM $wpdb->comments c1
            INNER JOIN $wpdb->comments c2 ON c1.comment_ID = c2.comment_parent
            WHERE c1.user_id = %d AND c2.comment_approved = '1'",
        $user_id)
    );
    $fecha_primer_acceso = $wpdb->get_var(
        $wpdb->prepare("SELECT user_registered FROM $wpdb->users WHERE ID = %d", $user_id)
    );

    $output = '
    <div class="estadisticas-usuario">
        <h2 class="estadisticas-titulo">Estadísticas de ' . esc_html($user_name) . '</h2>
        <div class="estadisticas-grid">
            <div class="stat-card"><strong>Nombre:</strong> ' . esc_html($user_name) . '</div>
            <div class="stat-card"><strong>Email:</strong> ' . esc_html($user_email) . '</div>
            <div class="stat-card"><strong>Comentarios:</strong> ' . $num_comentarios . '</div>
            <div class="stat-card"><strong>Problemas comentados:</strong> ' . $num_problemas_comentados . '</div>
            <div class="stat-card"><strong>Respuestas recibidas:</strong> ' . $num_respuestas . '</div>
            <div class="stat-card"><strong>Problemas con respuesta:</strong> ' . $num_problemas_con_respuesta . '</div>
            <div class="stat-card"><strong>Primer acceso:</strong> ' . $fecha_primer_acceso . '</div>
        </div>
    </div>';
    
    return $output;
}
add_shortcode('estadisticas_usuario', 'estadisticas_usuario_shortcode');

// Shortcode [mis_soluciones] - Grafo visual con los problemas comentados y sus categorías
function mis_soluciones_shortcode() {
    if (!is_user_logged_in()) {
        return '<p>Debes estar logueado para ver tus soluciones.</p>';
    }

    $user_problems_with_categories = get_user_problems_with_categories();
    
    ob_start();
    ?>
    <div>
        <p>Problemas comentados y sus categorías:</p>
        <ul>
            <?php foreach ($user_problems_with_categories as $problem): ?>
                <li>Problema: <?php echo $problem['problem_number']; ?> - Categoría: <?php echo $problem['category']; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div id="graph"></div>
    <script src="https://d3js.org/d3.v6.min.js"></script>
    <script>
		
		const categoryColors = {
			'Introducción a la programación de computadores': '#1f77b4',
			'Conceptos generales de los lenguajes de programación': '#ff7f0e',
			'Presentación del lenguaje C': '#2ca02c',
			'Procedimientos y Funciones': '#d62728',
			'Tipos de datos definidos por el programador': '#9467bd',
			'Tipos de datos estructurados': '#8c564b',
			'Punteros': '#e377c2',
			'Definición de tipos de datos dinámicos': '#7f7f7f',
			'Archivos': '#bcbd22'
		};


        const data = {
            nodes: [
                <?php foreach ($user_problems_with_categories as $problem): ?>
                    { id: 'Problema <?php echo $problem['problem_number']; ?>', category: '<?php echo $problem['category']; ?>', count: 1 },
                <?php endforeach; ?>
            ],
            links: [
                <?php
                $category_groups = [];
                foreach ($user_problems_with_categories as $problem) {
                    if (!isset($category_groups[$problem['category']])) {
                        $category_groups[$problem['category']] = [];
                    }
                    $category_groups[$problem['category']][] = $problem['problem_number'];
                }
                
                foreach ($category_groups as $category => $problems) {
                    for ($i = 0; $i < count($problems); $i++) {
                        for ($j = $i + 1; $j < count($problems); $j++) {
                            echo "{ source: 'Problema $problems[$i]', target: 'Problema $problems[$j]' },";
                        }
                    }
                }
                ?>
            ]
        };

        const width = 800, height = 600;

        const svg = d3.select("#graph")
                      .append("svg")
                      .attr("width", width)
                      .attr("height", height);

        const simulation = d3.forceSimulation(data.nodes)
                             .force("link", d3.forceLink(data.links).id(d => d.id))
                             .force("charge", d3.forceManyBody().strength(-400))
                             .force("center", d3.forceCenter(width / 2, height / 2));

        const link = svg.append("g")
                        .selectAll("line")
                        .data(data.links)
                        .enter().append("line")
						.attr("class", "link")
		                .style("stroke", "#000"); // Establecer el color de la línea a negro


        const node = svg.append("g")
                        .selectAll("circle")
                        .data(data.nodes)
                        .enter().append("circle")
                        .attr("class", "node")
                        .attr("r", d => 10 + d.count * 2) // Ajustar el tamaño del círculo según el número de comentarios
						.attr("fill", d => categoryColors[d.category] || "#ccc") // Establecer el color del círculo según la categoría
                        .on("mouseover", function(event, d) {
                            const circle = d3.select(this);
                            const parentG = d3.select(this.parentNode);
                            parentG.append("text")
                                   .attr("x", d.x)
                                   .attr("y", d.y)
                                   .attr("dy", ".35em")
                                   .attr("text-anchor", "middle")
                                   .attr("fill", "#FFF") // Establecer color del texto a blanco
                                   .text(d.id.replace('Problema ', ''))
                                   .attr("class", "hover-text");

                            circle.on("mouseout", function() {
                                parentG.select(".hover-text")
                                       .transition()
                                       .duration(200) // Duración de 1 segundo para la transición
                                       .style("opacity", 0)
                                       .remove();
                            });
                        })
                        .on("click", (event, d) => { // Añadir evento click
                            alert('Problema resuelto: ' + d.id);
                        })
                        .call(d3.drag()
                            .on("start", (event, d) => {
                                if (!event.active) simulation.alphaTarget(0.3).restart();
                                d.fx = d.x;
                                d.fy = d.y;
                            })
                            .on("drag", (event, d) => {
                                d.fx = event.x;
                                d.fy = event.y;
                            })
                            .on("end", (event, d) => {
                                if (!event.active) simulation.alphaTarget(0);
                                d.fx = null;
                                d.fy = null;
                            })
                        );

        node.append("title")
            .text(d => d.id);

        simulation.on("tick", () => {
            link.attr("x1", d => d.source.x)
                .attr("y1", d => d.source.y)
                .attr("x2", d => d.target.x)
                .attr("y2", d => d.target.y);

            node.attr("cx", d => d.x)
                .attr("cy", d => d.y);
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('mis_soluciones', 'mis_soluciones_shortcode');

// Shortcode [cantidad_comentarios] - Cantidad total de comentarios realizados por usuarios registrados
function mostrar_cant_comentarios_en_respuestas() {
    $cantidad = cant_comentarios_de_usuarios();
    return $cantidad;
}
add_shortcode('cantidad_comentarios', 'mostrar_cant_comentarios_en_respuestas');

// Shortcode [cantidad_codigo] - Cantidad de comentarios con bloques de código
function mostrar_cant_codigo_en_respuestas() {
    $cantidad = cant_codigo_en_respuestas();
    return $cantidad;
}
add_shortcode('cantidad_codigo', 'mostrar_cant_codigo_en_respuestas');

// Alias directo para acceder a la función de cantidad de usuarios
add_shortcode('comentarios_de_usuarios_totales_sh', 'cant_comentarios_de_usuarios');

// Alias directo para acceder a la función de cantidad de códigos
add_shortcode('actualizar_cant_codigo_sh', 'cant_codigo_en_respuestas');
