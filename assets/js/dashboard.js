document.addEventListener('DOMContentLoaded', () => {
    animarContadores();

    // Aplicar estrellas visuales
    document.querySelectorAll('.estrellas').forEach(el => {
        const promedio = parseFloat(el.dataset.promedio || 0);
        el.style.setProperty('--estrella-promedio', promedio);
    });
});

function animarContadores() {
    const contadores = document.querySelectorAll('.contador-animado');

    contadores.forEach(contador => {
        const final = parseInt(contador.dataset.valor, 10);
        let actual = 0;
        const duracion = 1000; // milisegundos
        const incremento = Math.max(1, Math.ceil(final / (duracion / 16)));

        const timer = setInterval(() => {
            actual += incremento;
            if (actual >= final) {
                actual = final;
                clearInterval(timer);
            }
            contador.textContent = actual;
        }, 16);
    });
}
