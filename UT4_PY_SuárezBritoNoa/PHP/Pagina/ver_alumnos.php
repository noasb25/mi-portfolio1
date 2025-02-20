<?php
session_start();

// Verificar si la cookie de idioma existe, si no, asignar 'ES' como valor predeterminado
$idioma = isset($_COOKIE['idioma']) ? $_COOKIE['idioma'] : 'ES';
// Esta línea verifica si la cookie 'idioma' está presente. Si existe, se asigna su valor a la variable $idioma. Si no existe, se asigna 'ES' por defecto, es decir, español.

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php'); // Si no hay sesión de usuario, redirigir al login
    exit;
}
// Aquí se verifica si el usuario está autenticado al comprobar si la sesión tiene un valor para 'usuario'. Si no está autenticado, se redirige a la página de login.

// Conexión a la base de datos
include 'conexion.php';
// Aquí se incluye el archivo de conexión a la base de datos para poder ejecutar consultas.

// Inicializar los errores
$errores = [];
// Se crea un array vacío para almacenar errores que puedan surgir durante el procesamiento del formulario.

// Procesar el formulario para borrar un alumno
if (isset($_POST['borrar_usuario'])) {
    // Verifica si se ha enviado el formulario de borrado de un alumno.

    $dni_a_borrar = trim($_POST['dni_alumno']);
    // Se recoge el DNI del alumno a borrar desde el formulario. 'trim()' elimina espacios adicionales.

    // Validación: Verificar si el DNI está vacío
    if (empty($dni_a_borrar)) {
        // Si el DNI está vacío, se agrega un error al array $errores.
        $errores[] = $idioma == 'ES' ? "Por favor, ingrese un DNI para borrar el alumno." : "Please enter a DNI to delete the student.";
    } else {
        try {
            // Eliminar las dependencias y el alumno
            $query_nivelespecialidad = "DELETE FROM nivelespecialidad WHERE id_alumno IN (SELECT id_alumno FROM Alumno WHERE dni = :dni)";
            // Se elimina primero la relación del alumno con otras tablas dependientes. En este caso, elimina registros relacionados con 'nivelespecialidad'.
            
            $stmt_nivelespecialidad = $miPDO->prepare($query_nivelespecialidad);
            $stmt_nivelespecialidad->bindParam(':dni', $dni_a_borrar, PDO::PARAM_STR);
            $stmt_nivelespecialidad->execute();
            // Se ejecuta la consulta para eliminar registros dependientes de la tabla 'nivelespecialidad'.

            $query_clasealumno = "DELETE FROM clasealumno WHERE id_alumno IN (SELECT id_alumno FROM Alumno WHERE dni = :dni)";
            // Luego se elimina la relación entre el alumno y las clases que ha tomado.

            $stmt_clasealumno = $miPDO->prepare($query_clasealumno);
            $stmt_clasealumno->bindParam(':dni', $dni_a_borrar, PDO::PARAM_STR);
            $stmt_clasealumno->execute();
            // Se ejecuta la consulta para eliminar los registros dependientes de la tabla 'clasealumno'.

            $query = "DELETE FROM Alumno WHERE dni = :dni";
            // Finalmente, se elimina el alumno de la tabla 'Alumno'.

            $stmt = $miPDO->prepare($query);
            $stmt->bindParam(':dni', $dni_a_borrar, PDO::PARAM_STR);
            $stmt->execute();
            // Se ejecuta la consulta para eliminar el alumno de la base de datos.

            $mensaje_borrado = $idioma == 'ES' ? "Alumno con DNI '$dni_a_borrar' eliminado con éxito." : "Student with DNI '$dni_a_borrar' deleted successfully.";
            // Si todo va bien, se guarda un mensaje de éxito para mostrar al usuario.

        } catch (PDOException $e) {
            // Si ocurre un error al ejecutar alguna consulta, se captura la excepción y se muestra el error.
            $errores[] = $idioma == 'ES' ? "Error al borrar el alumno: " . $e->getMessage() : "Error deleting the student: " . $e->getMessage();
        }
    }
}

// Consulta para obtener todos los alumnos
$query = "SELECT dni, nombre, apellido, telefono, fechaNacimiento, seguro FROM Alumno ORDER BY apellido, nombre";
$stmt = $miPDO->prepare($query);
$stmt->execute();
// Se prepara y ejecuta la consulta para obtener todos los alumnos de la base de datos ordenados por apellido y nombre.

$alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Se obtienen todos los registros de alumnos como un array asociativo para su posterior uso en la vista.
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $idioma == 'ES' ? 'Ver Todos los Alumnos' : 'View All Students'; ?></title>
    <!-- El título de la página cambia según el idioma de la cookie. -->
    <link rel="stylesheet" href="../Estilos/ver_alumnos.css?v=1.0">
</head>
<body>
    <header>
        <div class="logo">
            <img src="../Imagenes/logo.png" alt="Logo Hípica">
        </div>
        <h1><?php echo $idioma == 'ES' ? 'Hípica - Ver Todos los Alumnos' : 'Horse Riding - View All Students'; ?></h1>
    </header>

    <main>
        <h2><?php echo $idioma == 'ES' ? 'Lista de Alumnos' : 'List of Students'; ?></h2>

        <!-- Tabla de alumnos -->
        <table border="1">
            <thead>
                <tr>
                    <!-- Cabecera de la tabla, los nombres de las columnas también cambian según el idioma -->
                    <th><?php echo $idioma == 'ES' ? 'DNI' : 'DNI'; ?></th>
                    <th><?php echo $idioma == 'ES' ? 'Nombre' : 'Name'; ?></th>
                    <th><?php echo $idioma == 'ES' ? 'Apellido' : 'Last Name'; ?></th>
                    <th><?php echo $idioma == 'ES' ? 'Teléfono' : 'Phone'; ?></th>
                    <th><?php echo $idioma == 'ES' ? 'Fecha de Nacimiento' : 'Date of Birth'; ?></th>
                    <th><?php echo $idioma == 'ES' ? 'Seguro' : 'Insurance'; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Mostrar los datos de los alumnos en la tabla
                foreach ($alumnos as $alumno) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($alumno['dni']) . "</td>";
                    echo "<td>" . htmlspecialchars($alumno['nombre']) . "</td>";
                    echo "<td>" . htmlspecialchars($alumno['apellido']) . "</td>";
                    echo "<td>" . htmlspecialchars($alumno['telefono']) . "</td>";
                    echo "<td>" . htmlspecialchars($alumno['fechaNacimiento']) . "</td>";
                    echo "<td>" . htmlspecialchars($alumno['seguro']) . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Formulario para borrar un alumno -->
        <div class="borrar-alumno">
            <h3><?php echo $idioma == 'ES' ? '¿Te gustaría borrar algún alumno?' : 'Would you like to delete a student?'; ?></h3>
            <form action="ver_alumnos.php" method="POST">
                <label for="dni_alumno"><?php echo $idioma == 'ES' ? 'DNI del alumno a borrar:' : 'DNI of the student to delete:'; ?></label>
                <input type="text" id="dni_alumno" name="dni_alumno">

                <!-- Mostrar errores si hay -->
                <?php if (!empty($errores)): ?>
                    <div class="error-container">
                        <ul>
                            <?php foreach ($errores as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <button type="submit" name="borrar_usuario"><?php echo $idioma == 'ES' ? 'Borrar Alumno' : 'Delete Student'; ?></button>
            </form>

            <?php
            // Mostrar mensaje de confirmación si el alumno fue borrado
            if (isset($mensaje_borrado)) {
                echo "<p>$mensaje_borrado</p>";
            }
            ?>
        </div>
    </main>
    <a href="profesores.php"><button type="button"><?php echo $idioma == 'ES' ? 'Volver Atrás' : 'Go Back'; ?></button></a>

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
