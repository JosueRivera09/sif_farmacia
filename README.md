# Sistema SIF - Farmacia

Sistema de Gestión para Farmacia desarrollado en PHP, MySQL, Apache (XAMPP).

## Requisitos
- **XAMPP** (con Apache y MySQL activados).
- PHP 8.0 o superior.

## Instalación Rápida (1 Solo Paso)
Para configurar la base de datos, tablas, disparadores y datos de prueba automáticamente:

1. Clona el repositorio dentro de tu carpeta `C:\xampp\htdocs\sistema_sif`.
2. Abre tu navegador y accede a:
   [http://localhost/sistema_sif/instalar.php](http://localhost/sistema_sif/instalar.php)
3. Haz clic en **"Instalar y Configurar Ahora"**.

*También puedes realizarlo desde la consola/terminal ejecutando:*
```bash
php instalar.php
```

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

