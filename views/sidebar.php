<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_role = isset($_SESSION['rol']) ? $_SESSION['rol'] : '';
$permisos_extra = isset($_SESSION['permisos_extra']) ? $_SESSION['permisos_extra'] : [];

// Determinar el archivo y la carpeta actual
$current_file = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

// Determinar el contexto del panel actual
if ($current_dir === 'Interfaz_admin' || $current_file === 'admin_dashboard.php') {
    $panel_context = 'admin';
    $sidebar_subtitle = 'Panel Admin';
} elseif ($current_dir === 'Interfaz_caja' || $current_file === 'cajero_dashboard.php') {
    $panel_context = 'caja';
    $sidebar_subtitle = 'Panel Caja';
} elseif ($current_dir === 'Interfaz_vendedor' || $current_file === 'vendedor_dashboard.php') {
    $panel_context = 'ventas';
    $sidebar_subtitle = 'Panel Ventas';
} elseif ($current_dir === 'bodega' || $current_file === 'bodega_lotes.php') {
    $panel_context = 'bodega';
    $sidebar_subtitle = 'Panel Bodega';
} else {
    $panel_context = 'admin';
    $sidebar_subtitle = 'Panel SIF';
}

// Función auxiliar para obtener el enlace de regreso según el rol
function obtenerLinkVolver(string $rol)
{
    switch ($rol) {
        case 'Administrador':
            return '../Interfaz_admin/admin_dashboard.php';
        case 'Cajero':
            return '../Interfaz_caja/cajero_dashboard.php';
        case 'Vendedor':
            return '../Interfaz_vendedor/vendedor_dashboard.php';
        case 'Bodega':
            return '../bodega/bodega_lotes.php';
        default:
            return '#';
    }
}
?>

<aside class="sidebar">
    <div class="sidebar-header">
        <div>
            <img src="../../assets/img/logo.png" alt="Logo SISTEMA SIF" style="width: 50px; height: 50px; object-fit: contain;">
        </div>
        <div>
            <h1 class="sidebar-brand">SISTEMA SIF</h1>
            <p class="sidebar-subtitle mb-0"><?php echo htmlspecialchars($sidebar_subtitle); ?></p>
        </div>
    </div>

    <nav class="sidebar-menu custom-scrollbar">
        <?php
        // Caso 1: Administrador (acceso total a todos los módulos y sub-botones del sistema)
        if ($user_role === 'Administrador'):
            $page = isset($_GET['page']) ? $_GET['page'] : '';
            $subParam = isset($_GET['sub']) ? $_GET['sub'] : '';
            $in_admin_page = ($panel_context === 'admin');

            $href_admin = ($current_dir === 'Interfaz_admin') ? 'admin_dashboard.php' : '../Interfaz_admin/admin_dashboard.php';
            $href_caja = ($current_dir === 'Interfaz_caja') ? 'cajero_dashboard.php' : '../Interfaz_caja/cajero_dashboard.php';
            $href_ventas = ($current_dir === 'Interfaz_vendedor') ? 'vendedor_dashboard.php' : '../Interfaz_vendedor/vendedor_dashboard.php';
            $href_bodega = ($current_dir === 'bodega') ? 'bodega_lotes.php' : '../bodega/bodega_lotes.php';
        ?>
            <!-- BLOQUE ADMINISTRACIÓN -->
            <div class="mb-2">
                <span class="sidebar-subtitle px-3" style="font-size: 9px; color: #a855f7 !important; font-weight: 700; text-transform: uppercase;">Administración</span>
                <a class="nav-link-custom <?php echo ($in_admin_page && empty($page)) ? 'active' : ''; ?>"
                    id="btn-inicio"
                    href="<?php echo ($in_admin_page && empty($page)) ? '#' : $href_admin; ?>">
                    <span class="material-symbols-outlined">dashboard</span>
                    <span>Dashboard</span>
                </a>
                <a class="nav-link-custom <?php echo ($in_admin_page && $page === 'usuarios') ? 'active' : ''; ?>"
                    id="btn-usuarios"
                    href="<?php echo ($in_admin_page && $page === 'usuarios') ? '#' : $href_admin . '?page=usuarios'; ?>">
                    <span class="material-symbols-outlined">group</span>
                    <span>Gestión Usuarios</span>
                </a>
                <a class="nav-link-custom <?php echo ($in_admin_page && $page === 'productos') ? 'active' : ''; ?>"
                    id="btn-productos"
                    href="<?php echo ($in_admin_page && $page === 'productos') ? '#' : $href_admin . '?page=productos'; ?>">
                    <span class="material-symbols-outlined">inventory_2</span>
                    <span>Catálogo Productos</span>
                </a>
                <a class="nav-link-custom <?php echo ($in_admin_page && $page === 'reportes') ? 'active' : ''; ?>"
                    id="btn-reportes"
                    href="<?php echo ($in_admin_page && $page === 'reportes') ? '#' : $href_admin . '?page=reportes'; ?>">
                    <span class="material-symbols-outlined">analytics</span>
                    <span>Reportes</span>
                </a>
            </div>

            <!-- BLOQUE MÓDULO DE CAJA -->
            <div class="border-top border-secondary pt-2 mb-2">
                <span class="sidebar-subtitle px-3" style="font-size: 9px; color: #60a5fa !important; font-weight: 700; text-transform: uppercase;">Módulo de Caja</span>
                <a class="nav-link-custom <?php echo ($panel_context === 'caja' && (empty($subParam) || $subParam === 'cobrar')) ? 'active' : ''; ?>"
                   id="btn-modulo-caja"
                   href="<?php echo ($panel_context === 'caja' && (empty($subParam) || $subParam === 'cobrar')) ? '#' : $href_caja . '?sub=cobrar'; ?>">
                    <span class="material-symbols-outlined">payments</span>
                    <span>Pedidos por Cobrar</span>
                </a>
                <a class="nav-link-custom <?php echo ($panel_context === 'caja' && $subParam === 'historial') ? 'active' : ''; ?>"
                   id="btn-historial"
                   href="<?php echo ($panel_context === 'caja' && $subParam === 'historial') ? '#' : $href_caja . '?sub=historial'; ?>">
                    <span class="material-symbols-outlined">receipt_long</span>
                    <span>Historial Cobros</span>
                </a>
                <a class="nav-link-custom <?php echo ($panel_context === 'caja' && $subParam === 'arqueo') ? 'active' : ''; ?>"
                   id="btn-arqueo"
                   href="<?php echo ($panel_context === 'caja' && $subParam === 'arqueo') ? '#' : $href_caja . '?sub=arqueo'; ?>">
                    <span class="material-symbols-outlined">account_balance_wallet</span>
                    <span>Arqueo de Caja</span>
                </a>
            </div>

            <!-- BLOQUE MÓDULO DE VENTAS -->
            <div class="border-top border-secondary pt-2 mb-2">
                <span class="sidebar-subtitle px-3" style="font-size: 9px; color: #fca5a5 !important; font-weight: 700; text-transform: uppercase;">Módulo de Ventas</span>
                <a class="nav-link-custom <?php echo ($panel_context === 'ventas' && (empty($subParam) || $subParam === 'resumen')) ? 'active' : ''; ?>"
                   id="btn-inicio-ventas"
                   href="<?php echo ($panel_context === 'ventas' && (empty($subParam) || $subParam === 'resumen')) ? '#' : $href_ventas . '?sub=resumen'; ?>">
                    <span class="material-symbols-outlined">dashboard</span>
                    <span>Resumen Ventas</span>
                </a>
                <a class="nav-link-custom <?php echo ($panel_context === 'ventas' && $subParam === 'nueva_venta') ? 'active' : ''; ?>"
                   id="btn-nueva-venta"
                   href="<?php echo ($panel_context === 'ventas' && $subParam === 'nueva_venta') ? '#' : $href_ventas . '?sub=nueva_venta'; ?>">
                    <span class="material-symbols-outlined">point_of_sale</span>
                    <span>Nueva Venta</span>
                </a>
                <a class="nav-link-custom <?php echo ($panel_context === 'ventas' && $subParam === 'inventario') ? 'active' : ''; ?>"
                   id="btn-inventario"
                   href="<?php echo ($panel_context === 'ventas' && $subParam === 'inventario') ? '#' : $href_ventas . '?sub=inventario'; ?>">
                    <span class="material-symbols-outlined">search</span>
                    <span>Catálogo Inventario</span>
                </a>
            </div>

            <!-- BLOQUE MÓDULO DE BODEGA -->
            <div class="border-top border-secondary pt-2 mb-2">
                <span class="sidebar-subtitle px-3" style="font-size: 9px; color: #34d399 !important; font-weight: 700; text-transform: uppercase;">Módulo de Bodega</span>
                <a class="nav-link-custom <?php echo ($panel_context === 'bodega') ? 'active' : ''; ?>"
                   id="btn-bodega"
                   href="<?php echo ($panel_context === 'bodega') ? '#' : $href_bodega; ?>">
                    <span class="material-symbols-outlined">warehouse</span>
                    <span>Bodega y Lotes</span>
                </a>
            </div>

        <?php
        // Caso 2: Usuarios no Administradores (Cajero, Vendedor, Bodega con o sin permisos extra)
        // Se renderizan los módulos con un ORDEN ESTÁTICO FIJO para evitar desplazamientos al hacer clic
        else:
            $has_caja = ($user_role === 'Cajero' || in_array('caja', $permisos_extra) || in_array('cajero', $permisos_extra));
            $has_ventas = ($user_role === 'Vendedor' || in_array('ventas', $permisos_extra) || in_array('vendedor', $permisos_extra));
            $has_bodega = ($user_role === 'Bodega' || in_array('bodega', $permisos_extra));

            $subParam = isset($_GET['sub']) ? $_GET['sub'] : '';

            // 1. BLOQUE MÓDULO DE CAJA
            if ($has_caja):
                $is_caja_active = ($panel_context === 'caja');
                $href_caja_base = ($current_dir === 'Interfaz_caja') ? 'cajero_dashboard.php' : '../Interfaz_caja/cajero_dashboard.php';
        ?>
                <div class="mb-2">
                    <span class="sidebar-subtitle px-3" style="font-size: 9px; color: #60a5fa !important; font-weight: 700; text-transform: uppercase;">Módulo de Caja</span>
                    
                    <a class="nav-link-custom <?php echo ($is_caja_active && (empty($subParam) || $subParam === 'cobrar')) ? 'active' : ''; ?>"
                       id="btn-modulo-caja"
                       href="<?php echo $is_caja_active ? '#' : $href_caja_base . '?sub=cobrar'; ?>">
                        <span class="material-symbols-outlined">payments</span>
                        <span>Pedidos por Cobrar</span>
                    </a>
                    
                    <a class="nav-link-custom <?php echo ($is_caja_active && $subParam === 'historial') ? 'active' : ''; ?>"
                       id="btn-historial"
                       href="<?php echo $is_caja_active ? '#' : $href_caja_base . '?sub=historial'; ?>">
                        <span class="material-symbols-outlined">receipt_long</span>
                        <span>Historial Cobros</span>
                    </a>
                    
                    <?php if ($user_role === 'Cajero' || $user_role === 'Administrador' || in_array('caja', $permisos_extra)): ?>
                        <a class="nav-link-custom <?php echo ($is_caja_active && $subParam === 'arqueo') ? 'active' : ''; ?>"
                           id="btn-arqueo"
                           href="<?php echo $is_caja_active ? '#' : $href_caja_base . '?sub=arqueo'; ?>">
                            <span class="material-symbols-outlined">account_balance_wallet</span>
                            <span>Arqueo de Caja</span>
                        </a>
                    <?php endif; ?>
                </div>
        <?php
            endif;

            // 2. BLOQUE MÓDULO DE VENTAS
            if ($has_ventas):
                $is_ventas_active = ($panel_context === 'ventas');
                $href_ventas_base = ($current_dir === 'Interfaz_vendedor') ? 'vendedor_dashboard.php' : '../Interfaz_vendedor/vendedor_dashboard.php';
        ?>
                <div class="border-top border-secondary pt-2 mb-2">
                    <span class="sidebar-subtitle px-3" style="font-size: 9px; color: #fca5a5 !important; font-weight: 700; text-transform: uppercase;">Módulo de Ventas</span>
                    
                    <a class="nav-link-custom <?php echo ($is_ventas_active && (empty($subParam) || $subParam === 'resumen')) ? 'active' : ''; ?>"
                       id="btn-inicio"
                       href="<?php echo $is_ventas_active ? '#' : $href_ventas_base . '?sub=resumen'; ?>">
                        <span class="material-symbols-outlined">dashboard</span>
                        <span>Resumen Ventas</span>
                    </a>
                    
                    <a class="nav-link-custom <?php echo ($is_ventas_active && $subParam === 'nueva_venta') ? 'active' : ''; ?>"
                       id="btn-nueva-venta"
                       href="<?php echo $is_ventas_active ? '#' : $href_ventas_base . '?sub=nueva_venta'; ?>">
                        <span class="material-symbols-outlined">point_of_sale</span>
                        <span>Nueva Venta</span>
                    </a>
                    
                    <a class="nav-link-custom <?php echo ($is_ventas_active && $subParam === 'inventario') ? 'active' : ''; ?>"
                       id="btn-inventario"
                       href="<?php echo $is_ventas_active ? '#' : $href_ventas_base . '?sub=inventario'; ?>">
                        <span class="material-symbols-outlined">search</span>
                        <span>Catálogo Inventario</span>
                    </a>
                </div>
        <?php
            endif;

            // 3. BLOQUE MÓDULO DE BODEGA
            if ($has_bodega):
                $is_bodega_active = ($panel_context === 'bodega');
                $href_bodega_base = ($current_dir === 'bodega') ? 'bodega_lotes.php' : '../bodega/bodega_lotes.php';
        ?>
                <div class="border-top border-secondary pt-2 mb-2">
                    <span class="sidebar-subtitle px-3" style="font-size: 9px; color: #34d399 !important; font-weight: 700; text-transform: uppercase;">Módulo de Bodega</span>
                    
                    <a class="nav-link-custom <?php echo $is_bodega_active ? 'active' : ''; ?>"
                       id="btn-sidebar-lotes"
                       href="<?php echo $href_bodega_base; ?>">
                        <span class="material-symbols-outlined">warehouse</span>
                        <span>Bodega y Lotes</span>
                    </a>
                </div>
        <?php
            endif;
        endif;
        ?>
    </nav>

    <div class="sidebar-footer">
        <style>
            .text-support-custom {
                color: #38bdf8 !important;
            }

            .text-support-custom:hover {
                background-color: rgba(56, 189, 248, 0.1) !important;
            }
        </style>
        <a class="nav-link-custom text-support-custom" href="#" data-bs-toggle="modal" data-bs-target="#modalSoporte">
            <span class="material-symbols-outlined">support_agent</span>
            <span>Soporte Técnico</span>
        </a>
        <a class="nav-link-custom text-error-custom" href="../../controllers/auth/logout.php">
            <span class="material-symbols-outlined">logout</span>
            <span>Cerrar Sesión</span>
        </a>
    </div>
</aside>

<!-- Modal de Soporte Técnico -->
<div class="modal fade" id="modalSoporte" tabindex="-1" aria-labelledby="modalSoporteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: #1e293b; color: #f8fafc; border: 1px solid #334155; border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.3);">
            <div class="modal-header border-0 pb-0" style="padding: 24px 24px 10px 24px;">
                <div class="d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined text-info" style="font-size: 28px;">support_agent</span>
                    <h5 class="modal-title fw-bold" id="modalSoporteLabel" style="font-family: 'Inter', sans-serif;">Soporte Técnico</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" style="padding: 10px 24px 24px 24px;">
                <p class="text-secondary-light mb-4" style="color: #cbd5e1; font-size: 14px; font-family: 'Inter', sans-serif;">
                    ¿Tienes dudas, problemas o sugerencias sobre el <strong>Sistema SIF</strong>?
                    Estamos aquí para ayudarte. Ponte en contacto con nosotros a través de cualquiera de los siguientes medios:
                </p>

                <div class="d-flex flex-column gap-3">
                    <!-- WhatsApp -->
                    <!-- CONFIGURACIÓN DE WHATSAPP: 
                         Cambia el número de teléfono en href (ejemplo: '50499999999' por tu número con código de área) 
                         y personaliza el texto después de '?text=' -->
                    <a href="https://api.whatsapp.com/send/?phone=%2B50584961780&text&type=phone_number&app_absent=0" target="_blank" class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none transition-all" style="background-color: #0f172a; border: 1px solid #1e293b; color: #f8fafc; transition: all 0.2s ease-in-out;" onmouseover="this.style.borderColor='#22c55e'; this.style.backgroundColor='#14532d'" onmouseout="this.style.borderColor='#1e293b'; this.style.backgroundColor='#0f172a'">
                        <span class="fa-brands fa-whatsapp text-success fs-3"></span>
                        <div>
                            <h6 class="mb-0 fw-bold" style="font-size: 14px; font-family: 'Inter', sans-serif;">WhatsApp Soporte</h6>
                            <small style="color: #94a3b8; font-size: 12px;">Respuesta rápida • Lun-Vie 8am - 5pm</small>
                        </div>
                    </a>

                    <!-- Correo -->
                    <!-- CONFIGURACIÓN DE CORREO: 
                         Cambia 'soporte@sistemasif.com' en href y en el texto del correo por la dirección deseada -->
                    <a href="mailto:soporte@sistemasif.com?subject=Soporte%20Sistema%20SIF" class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none transition-all" style="background-color: #0f172a; border: 1px solid #1e293b; color: #f8fafc; transition: all 0.2s ease-in-out;" onmouseover="this.style.borderColor='#0ea5e9'; this.style.backgroundColor='#0c4a6e'" onmouseout="this.style.borderColor='#1e293b'; this.style.backgroundColor='#0f172a'">
                        <span class="material-symbols-outlined text-info fs-3">mail</span>
                        <div>
                            <h6 class="mb-0 fw-bold" style="font-size: 14px; font-family: 'Inter', sans-serif;">Correo Electrónico</h6>
                            <small style="color: #94a3b8; font-size: 12px;">soporte@sistemasif.com</small>
                        </div>
                    </a>

                    <!-- Manual de Usuario -->
                    <!-- CONFIGURACIÓN DEL MANUAL: 
                         Cambia el href '#' por la ruta del archivo PDF o URL del manual de usuario en línea -->
                    <a href="#" class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none transition-all" style="background-color: #0f172a; border: 1px solid #1e293b; color: #f8fafc; transition: all 0.2s ease-in-out;" onmouseover="this.style.borderColor='#a855f7'; this.style.backgroundColor='#3b0764'" onmouseout="this.style.borderColor='#1e293b'; this.style.backgroundColor='#0f172a'">
                        <span class="material-symbols-outlined fs-3" style="color: #c084fc;">menu_book</span>
                        <div>
                            <h6 class="mb-0 fw-bold" style="font-size: 14px; font-family: 'Inter', sans-serif;">Manual de Usuario</h6>
                            <small style="color: #94a3b8; font-size: 12px;">Ver documentación de la aplicación</small>
                        </div>
                    </a>

                    <!-- Llamada / Extensión -->
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background-color: #0f172a; border: 1px solid #1e293b; color: #f8fafc;">
                        <span class="material-symbols-outlined text-warning fs-3">phone_in_talk</span>
                        <div>
                            <h6 class="mb-0 fw-bold" style="font-size: 14px; font-family: 'Inter', sans-serif;">Línea Telefónica Interna</h6>
                            <small style="color: #94a3b8; font-size: 12px;">+505 8496-1780 (Administración de TI)</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 d-flex justify-content-end" style="padding: 10px 24px 24px 24px;">
                <button type="button" class="btn btn-secondary px-4 py-2" data-bs-dismiss="modal" style="background-color: #334155; border: none; font-size: 14px; border-radius: 6px;">Cerrar</button>
            </div>
        </div>
    </div>
</div>