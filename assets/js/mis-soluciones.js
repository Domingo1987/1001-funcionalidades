document.addEventListener('DOMContentLoaded', () => {
    if (typeof misSolucionesData === 'undefined') return;

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
        nodes: JSON.parse("[" + misSolucionesData.nodos + "]"),
        links: JSON.parse("[" + misSolucionesData.enlaces + "]")
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
        .style("stroke", "#000");

    const node = svg.append("g")
        .selectAll("circle")
        .data(data.nodes)
        .enter().append("circle")
        .attr("class", "node")
        .attr("r", d => 10 + d.count * 2)
        .attr("fill", d => categoryColors[d.category] || "#ccc")
        .on("mouseover", function (event, d) {
            const circle = d3.select(this);
            const parentG = d3.select(this.parentNode);
            parentG.append("text")
                .attr("x", d.x)
                .attr("y", d.y)
                .attr("dy", ".35em")
                .attr("text-anchor", "middle")
                .attr("fill", "#FFF")
                .text(d.id.replace('Problema ', ''))
                .attr("class", "hover-text");

            circle.on("mouseout", function () {
                parentG.select(".hover-text")
                    .transition()
                    .duration(200)
                    .style("opacity", 0)
                    .remove();
            });
        })
        .on("click", (event, d) => {
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

    node.append("title").text(d => d.id);

    simulation.on("tick", () => {
        link.attr("x1", d => d.source.x)
            .attr("y1", d => d.source.y)
            .attr("x2", d => d.target.x)
            .attr("y2", d => d.target.y);

        node.attr("cx", d => d.x).attr("cy", d => d.y);
    });
});
