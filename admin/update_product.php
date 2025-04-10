<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

function uploadImage($image, $folder, $old_image) {
    if(!empty($image['name'])){
        $image_name = filter_var($image['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $image_size = $image['size'];
        $image_tmp_name = $image['tmp_name'];
        $image_folder = $folder . $image_name;

        if($image_size > 2000000){
            $message[] = 'El tamaño de la imagen es demasiado grande.';
            return false;
        }

        if(move_uploaded_file($image_tmp_name, $image_folder)){
            if($old_image) unlink($folder . $old_image);
            return $image_name;
        }else{
            $message[] = 'Hubo un problema al subir la imagen.';
            return false;
        }
    } else {
        return $old_image;
    }
}

if(isset($_POST['update'])){

   $pid = $_POST['pid'];
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $price = $_POST['price'];
   $price = filter_var($price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
   $details = $_POST['details'];
   $details = filter_var($details, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $category = $_POST['category'];
   $category = filter_var($category, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $preciocompra = $_POST['preciocompra'];
   $preciocompra = filter_var($preciocompra, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
   $stock = $_POST['Stock'];
   $stock = filter_var($stock, FILTER_SANITIZE_NUMBER_INT);

   $update_product = $conn->prepare("UPDATE `products` SET name = ?, price = ?, details = ?, Id_Cat = ?, preciocompra = ?, Stock = ? WHERE id = ?");
   $update_product->execute([$name, $price, $details, $category, $preciocompra, $stock, $pid]);

   $image_folder = '../uploaded_img/';
   $image_01 = uploadImage($_FILES['image_01'], $image_folder, $_POST['old_image_01']);
   $image_02 = uploadImage($_FILES['image_02'], $image_folder, $_POST['old_image_02']);
   $image_03 = uploadImage($_FILES['image_03'], $image_folder, $_POST['old_image_03']);

   if($image_01){
      $update_image_01 = $conn->prepare("UPDATE `products` SET image_01 = ? WHERE id = ?");
      $update_image_01->execute([$image_01, $pid]);
   }

   if($image_02){
      $update_image_02 = $conn->prepare("UPDATE `products` SET image_02 = ? WHERE id = ?");
      $update_image_02->execute([$image_02, $pid]);
   }

   if($image_03){
      $update_image_03 = $conn->prepare("UPDATE `products` SET image_03 = ? WHERE id = ?");
      $update_image_03->execute([$image_03, $pid]);
   }

   $message[] = 'Producto actualizado con éxito!';
}

$select_categories = $conn->query("SELECT * FROM `categorias`");
$categories = $select_categories->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Actualizar producto</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">

   <style>
      body {
         background-image: url('../images/mont.jpg');
         background-size: cover;
         background-repeat: no-repeat;
      }
      .update-product {
         max-width: 800px;
         margin: 20px auto;
         background-color: rgba(255, 255, 255, 0.9);
         padding: 20px;
         border-radius: 8px;
      }
      .update-product .inputBox {
         margin-bottom: 20px;
      }
      .update-product .inputBox span {
         display: block;
         margin-bottom: 5px;
         color: #333;
         font-size: 20px;
      }
      .update-product .box {
         width: 100%;
         padding: 8px;
         border-radius: 5px;
         border: 1px solid #ccc;
      }
      .update-product .btn {
         padding: 8px 20px;
         border-radius: 5px;
         background-color: #007bff;
         color: #fff;
         border: none;
         cursor: pointer;
      }
      .update-product .btn:hover {
         background-color: #0056b3;
      }
   </style>
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="update-product">

   <h1 class="heading">Actualizar producto</h1>

   <?php
      $update_id = $_GET['update'];
      $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
      $select_products->execute([$update_id]);
      if($select_products->rowCount() > 0){
         while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
   ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
      <input type="hidden" name="old_image_01" value="<?= $fetch_products['image_01']; ?>">
      <input type="hidden" name="old_image_02" value="<?= $fetch_products['image_02']; ?>">
      <input type="hidden" name="old_image_03" value="<?= $fetch_products['image_03']; ?>">
      <div class="image-container">
         <div class="main-image">
            <img src="../uploaded_img/<?= $fetch_products['image_01']; ?>" alt="">
         </div>
         <div class="sub-image">
            <img src="../uploaded_img/<?= $fetch_products['image_01']; ?>" alt="">
            <img src="../uploaded_img/<?= $fetch_products['image_02']; ?>" alt="">
            <img src="../uploaded_img/<?= $fetch_products['image_03']; ?>" alt="">
         </div>
      </div>
      <div class="inputBox">
         <span>Actualizar Nombre</span>
         <input type="text" name="name" required class="box" maxlength="100" placeholder="Ingresa el nombre del producto" value="<?= $fetch_products['name']; ?>">
      </div>
      <div class="inputBox">
         <span>Actualizar Precio de Venta</span>
         <input type="number" name="price" required class="box" min="0" step="0.01" placeholder="Ingresa el precio del producto" value="<?= $fetch_products['price']; ?>">
      </div>
      <div class="inputBox">
         <span>Actualizar Precio de Compra</span>
         <input type="number" name="preciocompra" required class="box" min="0" step="0.01" placeholder="Ingresa el precio de compra" value="<?= $fetch_products['preciocompra']; ?>">
      </div>
      <div class="inputBox">
         <span>Actualizar Stock</span>
         <input type="number" name="Stock" required class="box" min="0" step="1" placeholder="Ingresa la cantidad en stock" value="<?= $fetch_products['Stock']; ?>">
      </div>
      <div class="inputBox">
         <span>Actualizar Descripción</span>
         <textarea name="details" class="box" required cols="30" rows="10"><?= $fetch_products['details']; ?></textarea>
      </div>
      <div class="inputBox">
         <span>Actualizar Categoría</span>
         <select name="category" class="box" required>
            <?php foreach ($categories as $category): ?>
            <option value="<?= $category['id_cat']; ?>" <?= $category['id_cat'] == $fetch_products['Id_Cat'] ? 'selected' : '' ?>><?= $category['categoria']; ?></option>
            <?php endforeach; ?>
         </select>
      </div>
      <div class="inputBox">
         <span>Actualizar Imagen 01</span>
         <input type="file" name="image_01" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
      </div>
      <div class="inputBox">
         <span>Actualizar Imagen 02</span>
         <input type="file" name="image_02" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
      </div>
      <div class="inputBox">
         <span>Actualizar Imagen 03</span>
         <input type="file" name="image_03" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
      </div>
      <div class="flex-btn">
         <input type="submit" name="update" class="btn" value="Actualizar">
         <a href="products.php" class="option-btn">Cancelar</a>
      </div>
   </form>

   <?php
         }
      } else {
         echo '<p class="empty">No se encontró el producto!</p>';
      }
   ?>

</section>

<script src="../js/admin_script.js"></script>

</body>
</html>
