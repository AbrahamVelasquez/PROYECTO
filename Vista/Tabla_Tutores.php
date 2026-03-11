<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ciudad Escolar</title>
</head>
<body>
    <h1>Tutores Registrados</h1>
    
    <form action='index.php' method='POST'>
        <input type='submit' name='btnLogOut' value='Cerrar sesión' onclick="return confirm('¿Está seguro que quiere cerrar sesión?')"> 
    </form>

    <br>

    <!-- Nos lleva al formulario para agregar -->
    <a href="#">
        Agregar Usuario
    </a>
    
    <br><br>
    
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>ID Tutor</th>
                <th>DNI</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Editar</th>
                <th>Eliminar</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($tutores)): ?>
                <tr>
                    <td colspan="6" align="center">No hay usuarios registrados</td>
                </tr>
            <?php else: ?>
                <?php foreach ($tutores as $fila): ?>
                <tr>
                    <td><?php echo $fila['id_tutor']; ?></td>
                    <td><?php echo $fila['dni']; ?></td>
                    <td><?php echo $fila['nombre']; ?></td>
                    <td><?php echo $fila['apellidos']; ?></td>
                    <td><?php echo $fila['email']; ?></td>
                    <td><?php echo $fila['telefono']; ?></td>
                             <td>
                        <!-- BOTÓN PARA Editar -->
                        <a href="#">
                            Editar
                        </a>
                    </td>
                    <td>
                        <!-- BOTÓN PARA ELIMINAR -->
                        <a href="#" 
                            onclick="return confirm('¿Eliminar este Tutor?')">Eliminar</a>
                        </a>
                    </td>
                </tr>

                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <br>
    <!-- <a href="index.php"> Volver al inicio</a> -->
</body>
</html>