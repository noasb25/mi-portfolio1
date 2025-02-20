# 🏇 Plataforma de Gestión de Hípica

📌 **Descripción**  
Este proyecto es una plataforma web diseñada para la gestión eficiente de una hípica, permitiendo administrar alumnos, profesores, caballos y clases. Incluye un sistema de autenticación y herramientas para gestionar horarios, inscripciones y modificaciones de datos.

---

## 🚀 Tecnologías Utilizadas
- 🖥 **Frontend:** HTML, CSS, JavaScript  
- ⚙️ **Backend:** PHP  
- 🗄 **Base de Datos:** MySQL  
- 📦 **Servidor Local:** XAMPP, Apache  

---

## 🛠 Instalación y Ejecución
Sigue estos pasos para instalar y ejecutar el proyecto en tu entorno local:

1️⃣ **Descargar y configurar el servidor**  
   - Instala [XAMPP](https://www.apachefriends.org/es/index.html) si aún no lo tienes.  
   - Asegúrate de que los módulos **Apache** y **MySQL** estén activos.  

2️⃣ **Clonar o copiar el proyecto**  
   - Coloca los archivos en el directorio del servidor web, por ejemplo:  
     ```
     C:\xampp\htdocs\UT4_PY_SuárezBritoNoa
     ```  

3️⃣ **Configurar la base de datos**  
   - Abre **phpMyAdmin** y crea una base de datos ejecutando el siguiente comando SQL:  
     ```sql
     CREATE DATABASE IF NOT EXISTS bdhipica;
     USE bdhipica;
     ```
   - Importa el archivo `.sql` incluido en el proyecto.  

4️⃣ **Configurar la conexión a la base de datos**  
   - Edita los archivos de configuración en PHP (`config.php` o similar).  
   - Asegúrate de que los datos coincidan con tu entorno:  
     ```php
     $hostDB = '127.0.0.1';  // Dirección del servidor MySQL
     $nombreDB = 'bdhipica';  // Nombre de la base de datos
     $usuarioDB = 'root';     // Usuario de la base de datos
     $contraDB = '';          // Contraseña (vacío en XAMPP por defecto)
     ```

5️⃣ **Ejecutar el proyecto**  
   - Abre tu navegador y accede a:  
     ```
     http://localhost/UT4_PY_SuárezBritoNoa
     ```

---

## 📌 Funcionalidades Principales
✅ Gestión de alumnos y profesores  
✅ Administración de caballos y clases  
✅ Sistema de autenticación seguro  
✅ Interfaz intuitiva y fácil de usar  

---

🚀 _¡Gracias por visitar este proyecto! Si tienes dudas o sugerencias, no dudes en compartirlas._  
