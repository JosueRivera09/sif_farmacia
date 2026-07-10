# Sistema SIF - Farmacia

Sistema de Gestión para Farmacia desarrollado en PHP, MySQL, Apache (XAMPP).

## Requisitos
- **XAMPP** (con Apache y MySQL activados).
- PHP 8.0 o superior.

## Instalación de la Base de Datos
Para configurar la base de datos y las tablas correspondientes:

1. Importar el archivo [schema.sql](file:///C:/xampp/htdocs/sistema_sif/database/schema.sql) en el servidor MySQL desde phpMyAdmin
2. Configura los parámetros de conexión correspondientes en el directorio `config/`.

### Usuarios de Prueba Creados:
- **Administrador**:
  - Usuario: `admin`
  - Contraseña: `admin123`
- **Vendedor**:
  - Usuario: `vendedor`
  - Contraseña: `vendedor123`

## Estructura del Proyecto

- `assets/`: Recursos estáticos (CSS, JS, Imágenes).
- `config/`: Archivos de configuración y conexión a la base de datos.
- `controllers/`: Controladores de la arquitectura MVC.
- `models/`: Modelos de la arquitectura MVC (consultas SQL).
- `views/`: Vistas de la arquitectura MVC (interfaces de usuario).
- `reportes/`: Módulo de generación de reportes.
- `database/`: Scripts de base de datos SQL y semillas.

