<?php
session_start(); // Inicia la sesión para poder acceder a las variables de sesión.


// Si el formulario es enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica si se ha enviado el formulario mediante POST.

    // Guardar el idioma en una cookie
    if (isset($_POST['idioma'])) {
        $idioma = $_POST['idioma']; // Recupera el valor del idioma seleccionado en el formulario.
        
        setcookie('idioma', $idioma, time() + 3600 * 24 * 30, '/'); 
        // Establece una cookie llamada 'idioma' con el valor seleccionado. 
        // La cookie expira en 30 días (3600 segundos * 24 horas * 30 días).
        
        header('Location: inicio.php'); 
        // Después de guardar la cookie, redirige al usuario a la página de inicio para aplicar el idioma seleccionado.

        exit; // Detiene la ejecución para evitar que se ejecute más código después de la redirección.
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($_COOKIE['idioma']) && $_COOKIE['idioma'] == 'EN' ? 'Select Your Language' : 'Selecciona tu idioma'; ?></title>
    <!-- Establece el título de la página según el idioma de la cookie. Si el idioma es inglés, se mostrará "Select Your Language", 
    y si es español, se mostrará "Selecciona tu idioma". -->
    <link rel="stylesheet" href="../Estilos/preferencias.css?v=1.0">
</head>
<body>
    <header>
        <div class="logo">
            <img src="../Imagenes/logo.png" alt="Logo Hípica" style="width: 50px; height: auto; margin-right: 10px;">
        </div>
        <h1><?php echo isset($_COOKIE['idioma']) && $_COOKIE['idioma'] == 'EN' ? 'Horse riding - Select Your Language' : 'Hípica - Selecciona tu idioma'; ?></h1>
        <!-- Muestra el encabezado con un título que varía dependiendo del idioma. -->
    </header>
    
    <main>
        <div class="preferencias-container">
            <form action="" method="POST">
                <h2><?php echo isset($_COOKIE['idioma']) && $_COOKIE['idioma'] == 'EN' ? 'Select Language:' : 'Selecciona el idioma:'; ?></h2>
                <!-- Título del formulario para seleccionar el idioma. Cambia según el idioma de la cookie. -->

                <hr> <!-- Línea horizontal separadora. -->
                
                <select name="idioma" id="idioma">
                    <!-- El selector para elegir el idioma. Se pueden elegir entre español (ES) e inglés (EN). -->
                    <option value="ES" <?php echo isset($_COOKIE['idioma']) && $_COOKIE['idioma'] == 'ES' ? 'selected' : ''; ?>>
                        <?php echo isset($_COOKIE['idioma']) && $_COOKIE['idioma'] == 'EN' ? 'Spanish' : 'Español'; ?>
                    </option>
                    <!-- Opción para Español: Si la cookie 'idioma' es 'ES', esta opción se marcará como seleccionada. -->
                    
                    <option value="EN" <?php echo isset($_COOKIE['idioma']) && $_COOKIE['idioma'] == 'EN' ? 'selected' : ''; ?>>
                        <?php echo isset($_COOKIE['idioma']) && $_COOKIE['idioma'] == 'EN' ? 'English' : 'Inglés'; ?>
                    </option>
                    <!-- Opción para Inglés: Si la cookie 'idioma' es 'EN', esta opción se marcará como seleccionada. -->
                </select>

                <br> <!-- Salto de línea para separar el selector de los botones. -->

                <button type="submit"><?php echo isset($_COOKIE['idioma']) && $_COOKIE['idioma'] == 'EN' ? 'Save Preference' : 'Guardar Preferencia'; ?></button>
                <!-- Botón para guardar la preferencia de idioma. El texto cambia según el idioma de la cookie. -->
                
                <a href="inicio.php"><button type="button"><?php echo isset($_COOKIE['idioma']) && $_COOKIE['idioma'] == 'EN' ? 'Go Back' : 'Volver Atrás'; ?></button></a>
                <!-- Botón para regresar a la página de inicio. El texto cambia según el idioma de la cookie. -->
            </form>
        </div>
    </main>
    
    <!-- Footer -->
    <footer>
        <p>
            <?php
                if (isset($_COOKIE['idioma']) && $_COOKIE['idioma'] == 'EN') {
                    echo "&copy; 2024 Horse riding. All rights reserved.";
                    // Muestra el mensaje de derechos de autor en inglés si la cookie 'idioma' está configurada a 'EN'.
                    echo "<br><br>Follow us on: <a href='#' target='_blank'>Facebook</a> | <a href='#' target='_blank'>Instagram</a> | <a href='#' target='_blank'>Twitter</a>";
                    // Enlaces a redes sociales en inglés.
                } else {
                    echo "&copy; 2024 Hípica. Todos los derechos reservados.";
                    // Muestra el mensaje de derechos de autor en español si la cookie 'idioma' está configurada a 'ES'.
                    echo "<br><br>Síguenos en: <a href='#' target='_blank'>Facebook</a> | <a href='#' target='_blank'>Instagram</a> | <a href='#' target='_blank'>Twitter</a>";
                    // Enlaces a redes sociales en español.
                }
            ?>
        </p>
    </footer>
</body>
</html>
