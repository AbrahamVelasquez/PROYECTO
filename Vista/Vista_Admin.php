<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ciudad Escolar</title>
</head>
<body>
    Bienvenido a Vista de la Administración de Ciudad Escolar

    <br><br>

    <form action='index.php' method='POST'>
        <input type='submit' name='btnLogOut' value='Cerrar sesión' onclick="return confirm('¿Está seguro que quiere cerrar sesión?')"> 
    </form>

    <h2>Tablas disponibles para ver</h2>

    <form action='index.php' method='POST'>
        <input type='submit' name='btnVerTutores' value='Ver Tutores'>
        <input type="hidden" name="accion" value="mostrarTutores">
    </form>

</body>
</html>