<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <form action="index.php" method="POST">

        <h2>Inicio de sesión</h2>
        Usuario (ID): <input type="text" name = "usuario" required><br/><br/>
        Contraseña: <input type="text" name = "contrasena" required><br/><br/>

        <input type="submit" name="btnLogIn" value="Inicia sesion" style = 'padding: 3px; font-size: 0.8em'>

        <p><?php echo $_GET['mensaje'] ?? '' ?></p>
        <!-- Para que diga si el usuario o la contraseña son erroneos -->

    </form>
</body>
</html>