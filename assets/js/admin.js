document.addEventListener('DOMContentLoaded', () => {
    // 🔹 Detectar la sección activa
    const body = document.body.classList;
  
    if (body.contains('toplevel_page_users1001-cursos')) {
      initGestionCursos();
    }
  
    // 🔸 En el futuro: gestión de centros
    /*
    if (body.contains('toplevel_page_users1001-centros')) {
      initGestionCentros();
    }
    */
  
    // 🔸 En el futuro: gestión de usuarios
    /*
    if (body.contains('users1001_page_users1001-usuarios')) {
      initGestionUsuarios();
    }
    */
  });
  
  // ✅ Gestión de Cursos
  function initGestionCursos() {
    console.log('📘 JS activo: Gestión de Cursos');
  
    const form = document.querySelector('#agregar-curso-form');
    const mensaje = document.querySelector('#mensaje-curso');
  
    if (!form) return;
  
    form.addEventListener('submit', function (e) {
      e.preventDefault();
  
      const cursoProp = document.querySelector('#nuevo-curso').value;
      console.log('🟢 Enviando curso:', cursoProp);
  
      if (!cursoProp) return;
  
      fetch(ajaxurl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
          action: 'guardar_curso',
          nonce: users1001_vars.nonce,
          curso: cursoProp,
        }),
      })
        .then(res => res.json())
        .then(response => {
          console.log('✅ Respuesta guardar curso:', response);
          mostrarMensajeCurso(response);
  
          if (response.success) {
            document.querySelector('#nuevo-curso').value = '';
            actualizarTablaCursos(response.data.cursos);
          }
        })
        .catch(err => {
          console.error('🚨 Error AJAX guardar curso:', err);
          mostrarMensajeCurso({ success: false, data: 'Error al procesar la solicitud.' });
        });
    });
  
    document.addEventListener('click', function (e) {
      if (!e.target.matches('.eliminar-curso')) return;
  
      const curso = e.target.dataset.curso;
      console.log('🧨 Clic en eliminar curso:', curso);
  
      if (!confirm(`¿Está seguro de eliminar el curso "${curso}"?`)) return;
  
      fetch(ajaxurl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
          action: 'eliminar_curso',
          nonce: users1001_vars.nonce,
          curso: curso,
        }),
      })
        .then(res => res.json())
        .then(response => {
          console.log('✅ Respuesta eliminar curso:', response);
          mostrarMensajeCurso(response);
  
          if (response.success) {
            actualizarTablaCursos(response.data.cursos);
          }
        })
        .catch(err => {
          console.error('🚨 Error AJAX eliminar curso:', err);
          mostrarMensajeCurso({ success: false, data: 'Error al procesar la solicitud.' });
        });
    });
  
    function mostrarMensajeCurso(response) {
      mensaje.classList.remove('notice-success', 'notice-error');
  
      if (response.success) {
        mensaje.classList.add('notice-success');
      } else {
        mensaje.classList.add('notice-error');
      }
  
      mensaje.innerHTML = `<p>${response.data.message || response.data}</p>`;
      mensaje.style.display = 'block';
    }
  
    function actualizarTablaCursos(cursos) {
      const tbody = document.querySelector('#lista-cursos');
      let html = '';
  
      if (cursos.length > 0) {
        cursos.forEach(curso => {
          html += `
            <tr>
              <td>${curso}</td>
              <td>
                <button class="btn btn-error btn-sm eliminar-curso" data-curso="${curso}">
                  Eliminar
                </button>
              </td>
            </tr>`;
        });
      } else {
        html = '<tr><td colspan="2">No hay cursos disponibles.</td></tr>';
      }
  
      tbody.innerHTML = html;
    }
  }
  