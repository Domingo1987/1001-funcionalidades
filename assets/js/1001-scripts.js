function sweetSorteoProblema(url) {
    Swal.fire({
        title: "⚙️ Compilando el dado...",
        html: "Por favor espera...",
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();

            setTimeout(() => {
                Swal.update({
                    title: "🧠 Iniciando algoritmo de selección...",
                    html: `<div id="contador-problema" style="font-size: 1.5rem;">...</div>`
                });

                const display = Swal.getPopup().querySelector("#contador-problema");
                let count = 0;
                const fakeInterval = setInterval(() => {
                    const random = Math.floor(Math.random() * 1000) + 1;
                    if (display) display.textContent = `Posible problema #${random}`;
                    count++;
                    if (count > 15) clearInterval(fakeInterval);
                }, 40);

                setTimeout(() => {
                    if (display) display.textContent = `Problema desbloqueado 🎉`;
                    Swal.update({
                        title: `🔓 Problema listo`,
                        html: "¡A resolverlo!",
                        icon: "success"
                    });
                }, 1200);
            }, 1400);

            setTimeout(() => {
                window.location.href = url;
            }, 3500);
        }
    });
}


