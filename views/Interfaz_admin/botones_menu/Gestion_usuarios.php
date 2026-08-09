<!--
/*
 * Archivo: views/Interfaz_admin/botones_menu/Gestion_usuarios.php
 * Propósito: Módulo de administración de usuarios.
 * Qué muestra: Tabla con el listado de usuarios, botón para agregar y modales de edición.
 */
-->
<div class="usuarios-container">
    <div class="card-usuarios">
        <!-- Encabezado del Módulo -->
        <div class="d-flex justify-content-between align-items-center border-bottom border-secondary pb-3 mb-4">
            <div>
                <h3 class="titulo-modulo mb-1">
                    <span class="material-symbols-outlined text-success">group</span>
                    Gestión de Usuarios
                </h3>
                <p class="subtitulo-modulo text-muted">Administra los usuarios, contraseñas y accesos a las distintas interfaces del sistema.</p>
            </div>
            <button class="btn btn-primary" onclick="mostrarModalCrear()">
                <span class="material-symbols-outlined">person_add</span>
                Agregar Usuario
            </button>
        </div>

        <!-- Tabla de Usuarios -->
        <div class="table-responsive">
            <table class="tabla-sif">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Nombre de Usuario</th>
                        <th>Rol / Permisos</th>
                        <th>Fecha de Registro</th>
                        <th style="width: 120px; text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-usuarios-body">
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>
                            Cargando usuarios...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para Crear / Editar Usuario -->
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-sif-content">
            <div class="modal-header modal-sif-header">
                <h5 class="modal-title font-bold" id="modalUsuarioLabel">Registrar Usuario</h5>
                <button type="button" class="modal-sif-close" data-bs-dismiss="modal" aria-label="Close">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form id="form-usuario" onsubmit="guardarUsuario(event)">
                <div class="modal-body p-4">
                    <!-- ID Oculto -->
                    <input type="hidden" id="id_usuario" name="id_usuario" value="0">

                    <!-- Nombre de Usuario -->
                    <div class="mb-3">
                        <label for="nombre_usuario" class="form-label form-label-sif">Nombre de Usuario</label>
                        <input type="text" class="form-control form-control-sif w-100" id="nombre_usuario" name="nombre_usuario" required autocomplete="off" placeholder="Ej: josue_rivera">
                    </div>

                    <!-- Rol -->
                    <div class="mb-3">
                        <label for="rol" class="form-label form-label-sif">Rol / Nivel de Acceso</label>
                        <select class="form-select form-control-sif form-select-sif w-100" id="rol" name="rol" required>
                            <option value="" disabled selected>Selecciona un rol</option>
                            <option value="Administrador">Administrador</option>
                            <option value="Cajero">Cajero</option>
                            <option value="Vendedor">Vendedor</option>
                            <option value="Bodega">Bodega</option>
                        </select>
                    </div>

                    <!-- Permisos Extra -->
                    <div class="mb-3">
                        <label class="form-label form-label-sif">Permisos Extra (Opcional)</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permisos_extra[]" value="caja" id="permiso_caja">
                            <label class="form-check-label" for="permiso_caja">Acceso a Caja</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permisos_extra[]" value="ventas" id="permiso_ventas">
                            <label class="form-check-label" for="permiso_ventas">Acceso a Ventas</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permisos_extra[]" value="bodega" id="permiso_bodega">
                            <label class="form-check-label" for="permiso_bodega">Acceso a Bodega</label>
                        </div>
                    </div>

                    <!-- Contraseña -->
                    <div class="mb-3">
                        <label for="clave_acceso" class="form-label form-label-sif" id="lbl-clave">Contraseña</label>
                        <input type="password" class="form-control form-control-sif w-100" id="clave_acceso" name="clave_acceso" placeholder="Mínimo 6 caracteres">
                        <small class="text-muted mt-1 d-block" id="helper-clave" style="display: none; font-size: 11px;">Deja este campo en blanco si no deseas cambiar la contraseña actual.</small>
                    </div>
                </div>
                <div class="modal-footer modal-sif-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit-modal">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
{
    // Inicializar modal con Bootstrap
    let bootstrapModalUsuario = null;

    function initModal() {
        const modalEl = document.getElementById('modalUsuario');
        if (modalEl) {
            bootstrapModalUsuario = new bootstrap.Modal(modalEl);
        }
    }

    // Cargar la lista de usuarios
    function cargarUsuarios() {
        const tbody = document.getElementById('tabla-usuarios-body');
        if (!tbody) return;

        fetch('../../controllers/admin/UsuarioController.php?action=listar')
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    const usuarios = response.data;
                    if (usuarios.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">No hay usuarios registrados.</td></tr>';
                        return;
                    }

                    let html = '';
                    usuarios.forEach(u => {
                        let badgeClass = 'badge-rol-vendedor';
                        if (u.rol === 'Administrador') badgeClass = 'badge-rol-admin';
                        else if (u.rol === 'Cajero') badgeClass = 'badge-rol-cajero';

                        html += `
                            <tr>
                                <td><code class="text-secondary">${u.id_usuario}</code></td>
                                <td class="font-bold text-light">${u.nombre_usuario}</td>
                                <td>
                                    <span class="badge-rol ${badgeClass}">
                                        <span class="material-symbols-outlined" style="font-size: 14px;">
                                            ${u.rol === 'Administrador' ? 'shield_person' : (u.rol === 'Cajero' ? 'point_of_sale' : 'sell')}
                                        </span>
                                        ${u.rol}
                                    </span>
                                </td>
                                <td>${u.fecha_creacion}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-outline-primary" onclick="editarUsuario(${u.id_usuario})" title="Editar Usuario">
                                            <span class="material-symbols-outlined" style="font-size: 18px;">edit</span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="confirmarEliminarUsuario(${u.id_usuario}, '${u.nombre_usuario}')" title="Eliminar Usuario">
                                            <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML = html;
                } else {
                    tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-danger">Error: ${response.message}</td></tr>`;
                }
            })
            .catch(err => {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-danger">Error al conectar con el servidor.</td></tr>`;
            });
    }

    // Mostrar modal para crear nuevo
    window.mostrarModalCrear = function() {
        document.getElementById('form-usuario').reset();
        document.getElementById('id_usuario').value = '0';
        document.getElementById('modalUsuarioLabel').innerText = 'Registrar Nuevo Usuario';
        document.getElementById('lbl-clave').innerText = 'Contraseña';
        document.getElementById('clave_acceso').required = true;
        document.getElementById('helper-clave').style.setProperty('display', 'none', 'important');
        document.getElementById('btn-submit-modal').innerText = 'Registrar Usuario';
        
        document.getElementById('permiso_caja').checked = false;
        document.getElementById('permiso_ventas').checked = false;
        document.getElementById('permiso_bodega').checked = false;

        if (!bootstrapModalUsuario) initModal();
        bootstrapModalUsuario.show();
    };

    // Editar usuario existente
    window.editarUsuario = function(id) {
        fetch(`../../controllers/admin/UsuarioController.php?action=obtener&id=${id}`)
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    const u = response.data;
                    document.getElementById('id_usuario').value = u.id_usuario;
                    document.getElementById('nombre_usuario').value = u.nombre_usuario;
                    document.getElementById('rol').value = u.rol;
                    document.getElementById('clave_acceso').value = '';
                    
                    document.getElementById('permiso_caja').checked = u.permisos_extra && u.permisos_extra.includes('caja');
                    document.getElementById('permiso_ventas').checked = u.permisos_extra && u.permisos_extra.includes('ventas');
                    document.getElementById('permiso_bodega').checked = u.permisos_extra && u.permisos_extra.includes('bodega');

                    document.getElementById('modalUsuarioLabel').innerText = 'Actualizar Datos de Usuario';
                    document.getElementById('lbl-clave').innerText = 'Nueva Contraseña';
                    document.getElementById('clave_acceso').required = false;
                    document.getElementById('helper-clave').style.setProperty('display', 'block', 'important');
                    document.getElementById('btn-submit-modal').innerText = 'Guardar Cambios';

                    if (!bootstrapModalUsuario) initModal();
                    bootstrapModalUsuario.show();
                } else {
                    alert('Error: ' + response.message);
                }
            })
            .catch(err => alert('Error al obtener datos del usuario.'));
    };

    // Guardar (crear o actualizar)
    window.guardarUsuario = function(event) {
        event.preventDefault();
        const form = document.getElementById('form-usuario');
        const formData = new FormData(form);

        fetch('../../controllers/admin/UsuarioController.php?action=guardar', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    bootstrapModalUsuario.hide();
                    cargarUsuarios();
                } else {
                    alert('Error: ' + response.message);
                }
            })
            .catch(err => alert('Error al guardar el usuario.'));
    };

    // Eliminar usuario
    window.confirmarEliminarUsuario = function(id, nombre) {
        SIFDialog.confirm(`¿Estás completamente seguro de que deseas eliminar al usuario "${nombre}"?\nEsta acción no se puede deshacer.`, 'Confirmar Eliminación de Usuario').then(confirmado => {
            if (!confirmado) return;

            const formData = new FormData();
            formData.append('id', id);

            fetch('../../controllers/admin/UsuarioController.php?action=eliminar', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(response => {
                    if (response.status === 'success') {
                        SIFDialog.alert('Usuario eliminado correctamente.', 'Operación Exitosa');
                        cargarUsuarios();
                    } else {
                        SIFDialog.alert('Error: ' + response.message, 'Atención');
                    }
                })
                .catch(err => SIFDialog.alert('Error al procesar la eliminación.', 'Error'));
        });
    };

    // Iniciar carga de datos al montar la vista
    initModal();
    cargarUsuarios();
}
</script>
