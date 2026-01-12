repositorio de github en caso de emergerncia

https://github.com/axDraws/ARUMA

Proyecto ARUMA 🌿

Este proyecto es una aplicación web desarrollada con PHP Vanilla y MySQL. Para garantizar que el sistema funcione correctamente en cualquier equipo (Windows, Linux o Mac) sin necesidad de instalar XAMPP o configurar bases de datos manualmente, se ha incluido una configuración de Docker.
🚀 Instrucciones de Ejecución

Sigue estos pasos para levantar el proyecto en menos de 5 minutos:
1. Requisitos Previos

    Tener instalado Docker Desktop. Si no lo tiene, puede descargarlo en docker.com.

2. Puesta en marcha

    Copie la carpeta de este proyecto desde la memoria USB a su equipo (Escritorio o Documentos).

    Abra una terminal (CMD, PowerShell o Terminal de Linux) dentro de la carpeta del proyecto.

    Ejecute el siguiente comando:
    Bash

    docker-compose up

    Espere a que la terminal indique que los servicios están listos.

        Nota: En la primera ejecución, Docker descargará las imágenes e importará automáticamente el archivo aruma_schema.sql a la base de datos.

3. Acceso al Sistema

Una vez que el contenedor esté corriendo, abra su navegador y acceda a: 👉 http://localhost:8080
