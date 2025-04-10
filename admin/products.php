<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

function uploadImage($image, $folder)
{
    $image_name = $image['name'];
    $image_size = $image['size'];
    $image_tmp_name = $image['tmp_name'];
    $image_folder = $folder . $image_name;

    if ($image_size > 2000000) {
        $message[] = 'El tamaño de la imagen es demasiado grande.';
        return false;
    }

    if (move_uploaded_file($image_tmp_name, $image_folder)) {
        return $image_name;
    } else {
        $message[] = 'Hubo un problema al subir la imagen.';
        return false;
    }
}

// Manejo de eliminación de productos
if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];

    // Prepara y ejecuta la consulta para eliminar el producto
    $delete_product = $conn->prepare("DELETE FROM products WHERE id = ?");
    $delete_product->execute([$product_id]);

    // Redirecciona a la página de productos después de eliminar el producto
    header("Location: products.php");
    exit; // Asegura que el script se detenga después de la redirección
}

if (isset($_POST['add_product'])) {

    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $price = $_POST['price'];
    $price = filter_var($price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $preciocompra = $_POST['preciocompra'];
    $preciocompra = filter_var($preciocompra, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $Stock = $_POST['Stock'];
    $Stock = filter_var($Stock, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $details = $_POST['details'];
    $details = filter_var($details, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $image_folder = '../uploaded_img/';

    $image_01 = uploadImage($_FILES['image_01'], $image_folder);
    $image_02 = uploadImage($_FILES['image_02'], $image_folder);
    $image_03 = uploadImage($_FILES['image_03'], $image_folder);

    if (!$image_01 || !$image_02 || !$image_03) {
        $message[] = 'Hubo un problema al subir las imágenes.';
    } else {

        $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
        $select_products->execute([$name]);

        if ($select_products->rowCount() > 0) {
            $message[] = 'El nombre de este producto ya existe.';
        } else {

            $insert_products = $conn->prepare("INSERT INTO `products`(name, details, price, image_01, image_02, image_03, Id_Cat, preciocompra, Stock) VALUES(?,?,?,?,?,?,?,?,?)");
            $insert_products->execute([$name, $details, $price, $image_01, $image_02, $image_03, $_POST['category_id'], $preciocompra, $Stock]);

            if ($insert_products) {
                $message[] = 'Nuevo producto agregado.';
            } else {
                $message[] = 'Hubo un problema al agregar el producto.';
            }
        }
    }
}

$select_categories = $conn->query("SELECT * FROM `categorias`");
$categories = $select_categories->fetchAll(PDO::FETCH_ASSOC);

$select_products = $conn->query("SELECT products.*, categorias.categoria FROM products INNER JOIN categorias ON products.Id_Cat = categorias.id_cat");
$products = $select_products->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
    <style>
        body {
            background-image: url('../images/mont.jpg');
            background-size: cover;
            background-repeat: no-repeat;
        }
    </style>
</head>

<body>

    <?php include '../components/admin_header.php'; ?>

    <section>

        <h1 class="heading">Agregar Producto</h1>

        <!-- Formulario para agregar productos -->
        <form class="add-product" action="" method="post" enctype="multipart/form-data">
            <div class="flex">
                <div class="inputBox">
                    <span>Nombre del Producto</span>
                    <input type="text" class="box" required maxlength="100" placeholder="Escribe el nombre" name="name">
                </div>
                <div class="inputBox">
                    <span>Detalles</span>
                    <textarea class="box" required placeholder="Escribe los detalles" name="details"></textarea>
                </div>
                <table>
                    <tr> 
                        <td>
                            <div class="inputBox">
                                <span>Precio de Venta</span>
                                <input type="number" class="box" required min="0" step="0.01" placeholder="Precio" name="price">
                            </div>
                        </td>
                        <td>
                            <div class="inputBox">
                                <span>Precio de Compra</span>
                                <input type="number" class="box" required min="0" step="0.01" placeholder="Precio" name="preciocompra">
                            </div>
                        </td>
                        <td>
                            <div class="inputBox">
                                <span>Stock</span>
                                <input type="number" class="box" required min="0" step="0.01" placeholder="Cantidad en stock" name="Stock">
                            </div>
                        </td>
                        <td>
                            <div class="inputBox">
                                <span>Categoría</span>
                                <select name="category_id" class="box" required>
                                    <option value="">Selecciona una categoría</option>
                                    <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id_cat']; ?>"><?php echo $category['categoria']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </td>
                    </tr>
                </table>        



                <table>
                    <tr>
                        <td>
                            <div class="inputBox">
                                <span>Imagen 1</span>
                                <input type="file" class="box" required name="image_01">
                            </div>
                        </td>
                        <td>
                            <div class="inputBox">
                                <span>Imagen 2</span>
                                <input type="file" class="box" name="image_02">
                            </div>
                        </td>
                        <td>
                            <div class="inputBox">
                                <span>Imagen 3</span>
                                <input type="file" class="box" name="image_03">
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <input type="submit" value="Agregar Producto" class="btn" name="add_product">
        </form>

        <!-- Mensaje de éxito o error -->
        <?php if (isset($message)): ?>
            <div>
                <?php foreach ($message as $msg): ?>
                    
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </section>

    <section>
    <h1 style="color: #fff;" class="heading">Productos Añadidos</h1>

    <div class="show-products" class="product-table-container">
        <table class="product-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Precio Venta</th>
                    <th>Precio Compra</th>
                    <th>Stock Disponible</th>
                    <th>Detalles</th>
                    <th>Categoría</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_products = $conn->prepare("SELECT products.*, categorias.categoria FROM products INNER JOIN categorias ON products.Id_Cat = categorias.id_cat");
                $select_products->execute();
                if ($select_products->rowCount() > 0) {
                    while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                ?>
                        <tr>
                            <td><?= $fetch_products['name']; ?></td>
                            <td>$ <?= $fetch_products['price']; ?> mxn</td>
                            <td>$ <?= $fetch_products['preciocompra']; ?> mxn</td>
                            <td><?= $fetch_products['Stock']; ?></td>
                            <td><?= $fetch_products['details']; ?></td>
                            <td><?= $fetch_products['categoria']; ?></td>
                            <td>
                                <div class="action-icons">
                                    <a href="update_product.php?update=<?= $fetch_products['id']; ?>" class="btn btn-edit"><i class="fas fa-edit"></i></a>
                                    <a href="products.php?delete=<?= $fetch_products['id']; ?>" class="btn btn-delete" onclick="return confirm('¿Eliminar este producto?');"><i class="fas fa-trash-alt"></i></a>
                                </div>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="5">Aún no se han añadido productos!</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>


    <style>
        /* Estilos para la tabla de productos */
        .show-products {
            margin-top: 20px;
            max-width: 800px;
            margin: 20px auto;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 8px;
        }

        .show-products .heading {
            color: black;
            text-align: center;
        }

        .show-products .product-table-container {
            max-width: 800px;
            margin: 20px auto;
            overflow-x: auto;
        }

        .show-products .product-table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 8px;
        }

        .show-products .product-table th,
        .show-products .product-table td {
            padding: 10px;
            border: 2px solid #ddd;
            font-size: 18px;
            background-color: #fff;
            text-align: center;
        }

        .show-products .product-table th {
            border: 2px solid #c7c5c5;
            background-color: #e0dede;
            font-weight: bold;
        }

        .show-products .product-table td {
            text-align: center;
        }

        .show-products .product-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .show-products .btn {
            padding: 8px;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 14px;
        }

        .show-products .btn i {
            margin: 0;
        }

        .show-products .btn-edit {
            background-color: #007bff;
            color: #fff;
            margin-right: 5px;
        }

        .show-products .btn-delete {
            background-color: #dc3545;
            color: #fff;
        }

        .show-products .btn:hover {
            background-color: #0056b3;
        }

        .show-products .action-icons {
            display: flex;
        }

        /* Estilos para el formulario */
        .add-product {
            max-width: 770px;
            margin: 20px auto;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 8px;
        }

        .add-product .heading {
            color: black;
            text-align: center;
        }

        .add-product .inputBox {
            margin-bottom: 20px;
        }

        .add-product .inputBox span {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-size: 20px;
        }

        .add-product .box {
            width: 100%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .add-product .btn {
            padding: 8px 20px;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        .add-product .btn:hover {
            background-color: #0056b3;
        }
    </style>

    <script src="../js/admin_script.js"></script>

</body>

</html>
