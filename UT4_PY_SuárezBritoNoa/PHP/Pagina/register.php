<?php
session_start(); // Inicia la sesión para acceder a las variables de sesión.

include 'conexion.php'; // Incluye el archivo de conexión a la base de datos para poder interactuar con la base de datos.


// Inicializar errores
$errores = []; // Inicializa un array vacío para almacenar los posibles errores del formulario.


$idioma = isset($_COOKIE['idioma']) ? $_COOKIE['idioma'] : 'ES'; // Verifica si existe una cookie de idioma. Si no, se asigna 'ES' como valor predeterminado.


// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dni = trim($_POST['dni']); // Obtiene el valor del campo 'dni' y elimina los espacios en blanco al principio y al final.
    $nombre = trim($_POST['nombre']); // Obtiene el valor del campo 'nombre'.
    $apellido = trim($_POST['apellido']); // Obtiene el valor del campo 'apellido'.
    $telefono = trim($_POST['telefono']); // Obtiene el valor del campo 'telefono'.
    $fechaNacimiento = trim($_POST['fecha_nacimiento']); // Obtiene el valor del campo 'fecha_nacimiento'.
    $seguro = trim($_POST['seguro']); // Obtiene el valor del campo 'seguro'.


    // Validaciones de los campos del formulario
    if (empty($dni)) { // Si el campo 'dni' está vacío, agrega un error.
        $errores[] = $idioma == 'EN' ? 'The DNI field is required.' : 'El campo DNI es obligatorio.';
    } elseif (!preg_match('/^\d{8}[A-Za-z]$/', $dni)) { // Verifica que el DNI tenga 8 dígitos seguidos de una letra.
        $errores[] = $idioma == 'EN' ? 'The DNI must have 8 digits followed by a letter.' : 'El DNI debe tener 8 números seguidos de una letra.';
    }

    if (empty($nombre)) { // Si el campo 'nombre' está vacío, agrega un error.
        $errores[] = $idioma == 'EN' ? 'The Name field is required.' : 'El campo Nombre es obligatorio.';
    }

    if (empty($apellido)) { // Si el campo 'apellido' está vacío, agrega un error.
        $errores[] = $idioma == 'EN' ? 'The Surname field is required.' : 'El campo Apellido es obligatorio.';
    }

    if (empty($telefono)) { // Si el campo 'telefono' está vacío, agrega un error.
        $errores[] = $idioma == 'EN' ? 'The Phone field is required.' : 'El campo Teléfono es obligatorio.';
    } elseif (!preg_match('/^\d{9}$/', $telefono)) { // Verifica que el teléfono tenga 9 dígitos.
        $errores[] = $idioma == 'EN' ? 'The Phone must be 9 digits.' : 'El Teléfono debe contener 9 dígitos.';
    }

    if (empty($fechaNacimiento)) { // Si el campo 'fechaNacimiento' está vacío, agrega un error.
        $errores[] = $idioma == 'EN' ? 'The Date of Birth field is required.' : 'El campo Fecha de nacimiento es obligatorio.';
    }

    if (empty($seguro)) { // Si el campo 'seguro' está vacío, agrega un error.
        $errores[] = $idioma == 'EN' ? 'The Insurance field is required.' : 'El campo Seguro es obligatorio.';
    }

    // Si no hay errores, procesar el registro
    if (empty($errores)) { // Si no se encontraron errores en las validaciones...
        try {
            // Consulta para insertar un nuevo alumno en la base de datos
            $query = "INSERT INTO Alumno (dni, nombre, apellido, telefono, fechaNacimiento, seguro) 
                      VALUES (:dni, :nombre, :apellido, :telefono, :fechaNacimiento, :seguro)";
            $result = $miPDO->prepare($query); // Prepara la consulta SQL.
            $result->execute([ // Ejecuta la consulta con los valores del formulario.
                ':dni' => $dni,
                ':nombre' => $nombre,
                ':apellido' => $apellido,
                ':telefono' => $telefono,
                ':fechaNacimiento' => $fechaNacimiento,
                ':seguro' => $seguro,
            ]);

            // Redirigir al login después del registro exitoso
            $mensaje_exito = $idioma == 'EN' ? 'Student registered successfully' : 'Alumno registrado con éxito';
            echo "<script>alert('$mensaje_exito');</script>"; // Muestra un mensaje de éxito.
            header('Location: login.php'); // Redirige al usuario a la página de login después de registrar al alumno.
            exit; // Detiene la ejecución del script.
        } catch (PDOException $e) { // Si ocurre un error al ejecutar la consulta, muestra el error.
            $errores[] = $idioma == 'EN' ? 'Error registering the student: ' . $e->getMessage() : 'Error al registrar el alumno: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $idioma == 'EN' ? 'en' : 'es'; ?>">
<!-- Establece el idioma de la página dependiendo de la cookie 'idioma', 'en' para inglés y 'es' para español. -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $idioma == 'EN' ? 'Register Student - Equestrian Center' : 'Registrar Alumno - Hípica'; ?></title>
    <!-- Título de la página cambia según el idioma de la cookie. -->
    <link rel="stylesheet" href="../Estilos/register.css">
</head>
<body>
<header>
    <div class="logo">
        <img src="../Imagenes/logo.png" alt="Logo Hípica">
    </div>
    <h1><?php echo $idioma == 'EN' ? 'Horse Riding - Student Registration' : 'Hípica - Registro de Alumnos'; ?></h1>
</header>

<main class="register-container">
    <h2><?php echo $idioma == 'EN' ? 'Register New Student' : 'Registrar Nuevo Alumno'; ?></h2>
    <hr>
    
    <!-- Mostrar errores si los hay -->
    <?php if (!empty($errores)): ?> <!-- Si el array de errores no está vacío, muestra los errores -->
        <div class="error-container">
            <ul>
                <?php foreach ($errores as $error): ?> <!-- Recorre todos los errores y los muestra -->
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form action="" method="POST">
        <!-- Formulario para registrar a un nuevo alumno. -->
        <label for="dni"><?php echo $idioma == 'EN' ? 'DNI' : 'DNI:'; ?></label>
        <input type="text" id="dni" name="dni" value="<?php echo htmlspecialchars($dni ?? ''); ?>">

        <label for="nombre"><?php echo $idioma == 'EN' ? 'Name' : 'Nombre:'; ?></label>
        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre ?? ''); ?>">

        <label for="apellido"><?php echo $idioma == 'EN' ? 'Surname' : 'Apellido:'; ?></label>
        <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($apellido ?? ''); ?>">

        <label for="telefono"><?php echo $idioma == 'EN' ? 'Phone' : 'Teléfono:'; ?></label>
        <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefono ?? ''); ?>">

        <label for="fecha_nacimiento"><?php echo $idioma == 'EN' ? 'Date of Birth' : 'Fecha de Nacimiento:'; ?></label>
        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($fechaNacimiento ?? ''); ?>">

        <label for="seguro"><?php echo $idioma == 'EN' ? 'Insurance' : 'Seguro:'; ?></label>
        <input type="text" id="seguro" name="seguro" value="<?php echo htmlspecialchars($seguro ?? ''); ?>">
        
        <div class="buttons-container">
            <button type="submit"><?php echo $idioma == 'EN' ? 'Register Student' : 'Registrar Alumno'; ?></button>
            <!-- Botón para enviar el formulario y registrar al alumno. El texto del botón depende del idioma. -->
            <a href="usuario.php"><button type="button"><?php echo $idioma == 'EN' ? 'Go Back' : 'Volver Atrás'; ?></button></a>
            <!-- Botón para regresar a la página anterior. -->
        </div>
    </form>
</main>

 <!-- Footer -->
 <footer>
        <p>
            <?php
                if (isset($_COOKIE['idioma']) && $_COOKIE['idioma'] == 'EN') {
                    echo "&copy; 2024 Horse riding. All rights reserved.";
                    echo "<br><br>Follow us on: <a href='#' target='_blank'>Facebook</a> | <a href='#' target='_blank'>Instagram</a> | <a href='#' target='_blank'>Twitter</a>";
                } else {
                    echo "&copy; 2024 Hípica. Todos los derechos reservados.";
                    echo "<br><br>Síguenos en: <a href='#' target='_blank'>Facebook</a> | <a href='#' target='_blank'>Instagram</a> | <a href='#' target='_blank'>Twitter</a>";
                }
            ?>
        </p>
    </footer>
</body>
</html>
