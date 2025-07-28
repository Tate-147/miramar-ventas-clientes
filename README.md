# Microservicio: Miramar-Ventas-Clientes 👤🛒

Microservicio encargado de la gestión de clientes y el registro de ventas para el sistema de la agencia de viajes **MiraMar**.

## Descripción del Servicio

Este es el segundo microservicio de negocio del sistema. Sus responsabilidades principales son gestionar toda la información de los clientes y procesar las transacciones de venta. Una de sus características clave es que, para registrar una venta, debe comunicarse directamente con el microservicio **`miramar-productos`** para validar los productos y obtener sus costos actualizados.

### Responsabilidades Principales
* **Gestión de Clientes**: Se encarga del ABML (Alta, Baja, Modificación y Lectura) completo de los clientes de la agencia.
* **Registro de Ventas**: Procesa y registra las ventas de cualquier producto (servicios o paquetes) a un cliente específico.
* **Comunicación entre servicios**: Interactúa con el API de `miramar-productos` para obtener la información necesaria sin conocer los detalles de su composición interna.

---

## Tecnologías Utilizadas ⚙️

* **Framework**: Lumen (PHP)
* **Base de Datos**: Configurado para MySQL/PostgreSQL (configurable en `.env`)
* **Cliente HTTP**: Guzzle (integrado en Lumen/Laravel) para la comunicación entre servicios.
* **Gestor de Dependencias**: Composer

---

## Instalación y Configuración Local

1.  **Clonar el repositorio**
    ```bash
    git clone https://github.com/Tate-147/miramar-ventas-clientes.git
    cd miramar-ventas-clientes
    ```

2.  **Instalar dependencias**
    ```bash
    composer install
    ```

3.  **Crear el archivo de entorno**
    ```bash
    cp .env.example .env
    ```

4.  **Configurar la base de datos**
    Abre el archivo `.env` y configura las variables para la base de datos de este servicio:
    ```ini
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=miramar_ventas_clientes
    DB_USERNAME=root
    DB_PASSWORD=tu_contraseña
    ```

5.  **Ejecutar las migraciones**
    ```bash
    php artisan migrate
    ```

6. **(Opcional) Poblar la base de datos**
   Para cargar datos de prueba (clientes y ventas de ejemplo) ejecuta:
   ```bash
   php artisan db:seed
   ```

   **Requisitos Previos**
   El microservicio miramar-productos debe estar corriendo en http://localhost:8001 y su base de datos ya debe tener productos cargados.

---

## Dependencia Importante ⚠️

Para que la funcionalidad de **registro de ventas** funcione, el microservicio **`miramar-productos` debe estar corriendo y accesible**. El `VentaController` está configurado para apuntar a la URL de este servicio.

---

## Ejecución 🚀

Para iniciar el servidor de desarrollo, ejecuta el siguiente comando. Se recomienda usar un puerto que no esté en uso por otros servicios.

```bash
php -S localhost:8002 -t public