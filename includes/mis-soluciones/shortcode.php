<?php
// Shortcode [soluciones] - Muestra iframe con la solución en Replit si el número es válido
function soluciones_replit_shortcode() {
    if (!isset($_GET['num'])) {
        wp_redirect(home_url());
        exit;
    }

    $num_code = intval($_GET['num']);
    $num = ($num_code - 1987) / 1001;

    if (is_numeric($num) && intval($num) == $num && $num >= 1 && $num <= 1000) {
        $replit_url = 'https://replit.com/@DomingoPerez/problema-' . intval($num) . '?embed=true';
        return '<p style="text-align: center;"><iframe src="' . esc_url($replit_url) . '" width="900" height="600" frameborder="0" loading="lazy"></iframe></p>';
    } else {
        wp_redirect(home_url());
        exit;
    }
}
add_shortcode('soluciones', 'soluciones_replit_shortcode');


// Shortcode [solucion] - Muestra botón de solución si el usuario ha comentado el problema actual y al menos 4 problemas
function solucion_shortcode() {
    if (!is_singular('problema')) return ''; // Asegura que estamos en un CPT de tipo problema

    $num_problemas = get_num_problemas_comentados(); // ← ya deberías tener esta función
    $problema_resuelto = post_comentado_por_usuario(); // ← ya deberías tener esta función
    $num_prob = get_num_problema(); // ← ya deberías tener esta función personalizada
    $num_prob_code = $num_prob * 1001 + 1987;

    $url = ($num_problemas > 3 && $problema_resuelto)
        ? home_url('/soluciones/?num=' . $num_prob_code)
        : home_url('/denegado');

    return '<div style="text-align:center;margin:2rem 0;">
        <a href="' . esc_url($url) . '" class="button medium rounded grey">Ver Solución</a>
    </div>';
}
add_shortcode('solucion', 'solucion_shortcode');

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
