<?php
session_start(); // Inicia la sesión para acceder a las variables de sesión, como el usuario y el rol.


// Verificar si el usuario ha iniciado sesión y tiene el rol de alumno
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'alumno') {
    header('Location: login.php'); // Si no está autenticado o no es un alumno, redirige a la página de login.
    exit; // Detiene la ejecución del script si el usuario no tiene acceso.
}

// Conectar con la base de datos
include 'conexion.php'; // Incluye el archivo de conexión a la base de datos.


// Verificar que el DNI esté disponible en la sesión
if (!isset($_SESSION['dni'])) {
    echo "Error: No se ha encontrado el DNI en la sesión."; // Si no existe el DNI en la sesión, muestra un error.
    exit; // Detiene la ejecución si no se encuentra el DNI.
}

$dniAlumno = $_SESSION['dni']; // Obtiene el DNI del alumno desde la sesión.


// Obtener los datos actuales del alumno
$query = "SELECT dni, nombre, apellido, telefono, fechaNacimiento, seguro FROM Alumno WHERE dni = :dniAlumno";
$stmt = $miPDO->prepare($query); 
// Prepara la consulta SQL para obtener los datos del alumno, utilizando el DNI como parámetro.
$stmt->bindParam(':dniAlumno', $dniAlumno, PDO::PARAM_STR); 
// Vincula el valor de $dniAlumno a la consulta SQL.
$stmt->execute(); // Ejecuta la consulta para obtener los datos del alumno.


$datosAlumno = $stmt->fetch(PDO::FETCH_ASSOC); 
// Almacena los resultados de la consulta en un arreglo asociativo.


if (!$datosAlumno) { 
    echo "<p>No se encontraron datos para el alumno con DNI: $dniAlumno</p>"; 
    // Si no se encontraron datos en la base de datos, muestra un mensaje de error.
    exit; 
    // Detiene la ejecución si no se encuentran los datos del alumno.
}


// Verificar idioma desde la cookie
$idioma = isset($_COOKIE['idioma']) ? $_COOKIE['idioma'] : 'ES'; 
// Verifica si existe la cookie 'idioma' y toma su valor (si existe), si no, predeterminado a 'ES' (español).


// Inicializar un array de errores
$errores = []; 
// Crea un arreglo vacío para almacenar los errores de validación.


if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
    // Verifica si el formulario ha sido enviado mediante el método POST.

    // Recibe los datos del formulario, con un valor predeterminado vacío si no se proporcionan.
    $dni = isset($_POST['dni']) ? $_POST['dni'] : ''; 
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $apellido = isset($_POST['apellido']) ? $_POST['apellido'] : '';
    $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : '';
    $fechaNacimiento = isset($_POST['fechaNacimiento']) ? $_POST['fechaNacimiento'] : '';
    $seguro = isset($_POST['seguro']) ? $_POST['seguro'] : '';


    // Validación en PHP
    $erroresTraduccion = [
        'dni' => $idioma == 'EN' ? "The DNI is required and must be valid (8 digits and a letter)." : "El DNI es obligatorio y debe ser válido (8 números seguidos de una letra).",
        'nombre' => $idioma == 'EN' ? "Name is required." : "El nombre es obligatorio.",
        'apellido' => $idioma == 'EN' ? "Last name is required." : "El apellido es obligatorio.",
        'telefono' => $idioma == 'EN' ? "Phone must be 9 digits." : "El teléfono debe tener 9 dígitos.",
        'fechaNacimiento' => $idioma == 'EN' ? "Date of birth is required." : "La fecha de nacimiento es obligatoria.",
        'seguro' => $idioma == 'EN' ? "Insurance is required." : "El seguro es obligatorio.",
        'dniExistente' => $idioma == 'EN' ? "The DNI is already registered for another student." : "El DNI ya está registrado en otro alumno."
    ];
    // Traduce los mensajes de error según el idioma elegido, en español o inglés.


    // Validaciones de los datos ingresados
    if (empty($dni) || !preg_match("/^\d{8}[A-Za-z]$/", $dni)) {
        $errores[] = $erroresTraduccion['dni']; 
        // Verifica que el DNI no esté vacío y sea válido (8 dígitos seguidos de una letra).
    } else {
        // Si el DNI es válido, verifica si ya está registrado en la base de datos.
        $query = "SELECT COUNT(*) FROM Alumno WHERE dni = :dni";
        $stmt = $miPDO->prepare($query); 
        // Prepara la consulta para contar el número de registros con el mismo DNI.
        $stmt->bindParam(':dni', $dni, PDO::PARAM_STR); 
        $stmt->execute(); 
        // Ejecuta la consulta.
        $count = $stmt->fetchColumn(); 

        if ($count > 0 && $dni != $datosAlumno['dni']) { 
            $errores[] = $erroresTraduccion['dniExistente']; 
            // Si el DNI ya existe en la base de datos (y no es el mismo DNI que el que ya tiene el alumno), agrega un error.
        }
    }

    // Validaciones adicionales
    if (empty($nombre)) {
        $errores[] = $erroresTraduccion['nombre']; // Si el nombre está vacío, agrega el error correspondiente.
    }
    if (empty($apellido)) {
        $errores[] = $erroresTraduccion['apellido']; // Si el apellido está vacío, agrega el error correspondiente.
    }
    if (empty($telefono) || !preg_match("/^\d{9}$/", $telefono)) {
        $errores[] = $erroresTraduccion['telefono']; // Si el teléfono está vacío o no tiene 9 dígitos, agrega el error correspondiente.
    }
    if (empty($fechaNacimiento)) {
        $errores[] = $erroresTraduccion['fechaNacimiento']; // Si la fecha de nacimiento está vacía, agrega el error correspondiente.
    }
    if (empty($seguro)) {
        $errores[] = $erroresTraduccion['seguro']; // Si el seguro está vacío, agrega el error correspondiente.
    }


    // Si no hay errores, actualiza los datos en la base de datos
    if (empty($errores)) {
        // Si no se han encontrado errores, se procede a actualizar los datos del alumno.
        $query = "UPDATE Alumno SET dni = :dni, nombre = :nombre, apellido = :apellido, telefono = :telefono, fechaNacimiento = :fechaNacimiento, seguro = :seguro WHERE dni = :dniAlumno";
        // Prepara la consulta de actualización de los datos del alumno.
        $stmt = $miPDO->prepare($query); 
        $stmt->bindParam(':dniAlumno', $dniAlumno, PDO::PARAM_STR); // Asocia el DNI del alumno actual.
        $stmt->bindParam(':dni', $dni, PDO::PARAM_STR); // Asocia el nuevo DNI.
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR); // Asocia el nuevo nombre.
        $stmt->bindParam(':apellido', $apellido, PDO::PARAM_STR); // Asocia el nuevo apellido.
        $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR); // Asocia el nuevo teléfono.
        $stmt->bindParam(':fechaNacimiento', $fechaNacimiento, PDO::PARAM_STR); // Asocia la nueva fecha de nacimiento.
        $stmt->bindParam(':seguro', $seguro, PDO::PARAM_STR); // Asocia el nuevo seguro.

        try {
            $stmt->execute(); // Ejecuta la consulta de actualización.
            // Confirmación de modificación
            $mensaje_exito = $idioma == 'EN' ? 'Data successfully updated' : 'Datos modificados con éxito';
            echo "<script>alert('$mensaje_exito');</script>"; // Muestra un mensaje de éxito si los datos fueron actualizados correctamente.
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage(); // Si ocurre un error durante la ejecución de la consulta, muestra el mensaje de error.
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $idioma == 'EN' ? 'Modify Data - Student' : 'Modificar Datos - Alumno'; ?></title>
    <link rel="stylesheet" href="../Estilos/modificar_datos.css?v=1.0">
</head>
<body>
    <header>
        <div class="logo">
            <img src="../Imagenes/logo.png" alt="Logo Hípica">
        </div>
        <h1><?php echo $idioma == 'EN' ? 'Horse Riding - Modify Student Data' : 'Hípica - Modificar Datos del Alumno'; ?></h1>
    </header>

    <main class="modificar-container">
        <h2><?php echo $idioma == 'EN' ? 'Modify your data, ' . htmlspecialchars($datosAlumno['nombre']) : 'Modifica tus datos, ' . htmlspecialchars($datosAlumno['nombre']); ?></h2>

        <!-- Mostrar errores de validación -->
        <?php if (!empty($errores)): ?>
            <div class="errores">
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li style="color: red;"><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="dni"><?php echo $idioma == 'EN' ? 'DNI' : 'DNI:'; ?></label>
            <input type="text" name="dni" value="<?php echo htmlspecialchars($datosAlumno['dni']); ?>">

            <label for="nombre"><?php echo $idioma == 'EN' ? 'Name' : 'Nombre:'; ?></label>
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($datosAlumno['nombre']); ?>">

            <label for="apellido"><?php echo $idioma == 'EN' ? 'Last Name' : 'Apellido:'; ?></label>
            <input type="text" name="apellido" value="<?php echo htmlspecialchars($datosAlumno['apellido']); ?>">

            <label for="telefono"><?php echo $idioma == 'EN' ? 'Phone' : 'Teléfono:'; ?></label>
            <input type="text" name="telefono" value="<?php echo htmlspecialchars($datosAlumno['telefono']); ?>">

            <label for="fechaNacimiento"><?php echo $idioma == 'EN' ? 'Date of Birth' : 'Fecha de Nacimiento:'; ?></label>
            <input type="date" name="fechaNacimiento" value="<?php echo htmlspecialchars($datosAlumno['fechaNacimiento']); ?>">

            <label for="seguro"><?php echo $idioma == 'EN' ? 'Insurance' : 'Seguro:'; ?></label>
            <input type="text" name="seguro" value="<?php echo htmlspecialchars($datosAlumno['seguro']); ?>">

            <div class="buttons-container">
                <button type="submit"><?php echo $idioma == 'EN' ? 'Modify' : 'Modificar'; ?></button>
                <a href="bienvenida.php"><button type="button"><?php echo $idioma == 'ES' ? 'Volver Atrás' : 'Go Back'; ?></button></a>
            </div>
        </form>
        <form action="logout.php" method="POST">
            <button type="submit"><?php echo $idioma == 'EN' ? 'Log Out' : 'Cerrar Sesión'; ?></button>
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
