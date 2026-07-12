# Sistema SIF - Farmacia

Sistema de Gestión para Farmacia desarrollado en PHP, MySQL, Apache (XAMPP).

## Requisitos
- **XAMPP** (con Apache y MySQL activados).
- PHP 8.0 o superior.

## Instalación de la Base de Datos
Para configurar la base de datos y las tablas correspondientes:

1. Importar el archivo [schema.sql](file:///C:/xampp/htdocs/sistema_sif/database/schema.sql) en el servidor MySQL desde phpMyAdmin
2. Configura los parámetros de conexión correspondientes en el directorio `config/`.

### Usuarios por Defecto (Ver `schema.sql` para detalles completos):
- **Administrador**:
  - Usuario: `joel_admin`
  - Contraseña: `J48_adm!`
- **Cajero**:
  - Usuario: `darllely_caja`
  - Contraseña: `D72_caj#`
- **Vendedor**:
  - Usuario: `cesar_ventas`
  - Contraseña: `C58_vnt$`

## Estructura del Proyecto

- `assets/`: Recursos estáticos (CSS, JS, Imágenes).
- `config/`: Archivos de configuración y conexión a la base de datos.
- `controllers/`: Controladores de la arquitectura MVC (agrupados por roles/módulos).
- `models/`: Modelos de la arquitectura MVC y consultas a la base de datos (organizados lógicamente en `admin`, `inventario`, `personas` y `ventas`).
- `views/`: Interfaces gráficas de usuario para los diferentes módulos.
- `database/`: Contiene `schema.sql` para la creación inicial de la base de datos.
- `migrar_db.php`: Script utilitario en la raíz para ejecutar actualizaciones estructurales rápidas a la base de datos de manera automática.
