<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
};

// Verificar si se está pasando el ID de la categoría a actualizar
if (isset($_GET['update'])) {
    $category_id = $_GET['update'];

    // Obtener datos de la categoría
    $select_category = $conn->prepare("SELECT * FROM `categorias` WHERE id_cat = ?");
    $select_category->execute([$category_id]);

    if ($select_category->rowCount() > 0) {
        $category_data = $select_category->fetch(PDO::FETCH_ASSOC);
    } else {
        header('location:categorias.php');
    }
}

// Procesar el formulario cuando se envíe
if (isset($_POST['update_category'])) {

    $category_name = $_POST['category_name'];
    $category_name = filter_var($category_name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Verificar si el nombre de categoría ya existe
    $check_category = $conn->prepare("SELECT * FROM `categorias` WHERE categoria = ? AND id_cat != ?");
    $check_category->execute([$category_name, $category_id]);

    if ($check_category->rowCount() > 0) {
        $message = 'Ya existe una categoría con ese nombre.';
    } else {
        // Actualizar la categoría en la base de datos
        $update_category = $conn->prepare("UPDATE `categorias` SET categoria = ? WHERE id_cat = ?");
        $update_category->execute([$category_name, $category_id]);

        if ($update_category) {
            $message = 'Categoría actualizada correctamente!';
        } else {
            $message = 'Error al actualizar la categoría';
        }

        // Redirigir a la página de categorías después de guardar los cambios
        header('location:categorias.php');
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Categoría</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
    <style>
        body {
            background-image: url('../images/mont.jpg');
            /* Reemplaza 'ruta/al/fondo.jpg' con la ruta de tu imagen de fondo */
            background-size: cover;
            background-repeat: no-repeat;
        }
    </style>
</head>

<body>

    <?php include '../components/admin_header.php'; ?>

    <section class="add-categories">

        <h1 class="heading">Actualizar Categoría</h1>

        <!-- Formulario para actualizar la categoría -->
        <form action="" method="post">
            <div class="flex">
                <div class="inputBox">
                    <span>Nombre de la Categoría</span>
                    <input type="text" class="box" required maxlength="100" placeholder="Escribe el nombre" name="category_name" value="<?php echo isset($category_data['categoria']) ? $category_data['categoria'] : ''; ?>">
                </div>
            </div>

            <input type="submit" value="Guardar Cambios" class="btn" name="update_category">
        </form>

        <!-- Mensaje de error si la categoría ya existe -->
        <?php if (isset($message)): ?>
        <?php endif; ?>

    </section>

    <style>
        /* Estilos para el formulario */
        .add-categories {
            max-width: 800px;
            margin: 20px auto;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 8px;
        }

        .add-categories .heading {
            color: black;
            text-align: center;
        }

        .add-categories .inputBox {
            margin-bottom: 20px;
        }

        .add-categories .inputBox span {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-size: 20px;
        }

        .add-categories .box {
            width: 100%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .add-categories .btn {
            padding: 8px 20px;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        .add-categories .btn:hover {
            background-color: #0056b3;
        }

        /* Estilos para el mensaje de error */
        .message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
            background-color: #dc3545;
            color: #fff;
            text-align: center;
        }
    </style>

    <script src="../js/admin_script.js"></script>

</body>

</html>
