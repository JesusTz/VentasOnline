<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
};

if(isset($_POST['add_category'])){
   
   $category_name = $_POST['category_name'];
   $category_name = filter_var($category_name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   
   $select_categories = $conn->prepare("SELECT * FROM `categorias` WHERE categoria = ?");
   $select_categories->execute([$category_name]);

   if($select_categories->rowCount() > 0){
      $message[] = 'El nombre de la categoría ya existe!';
   }else{

      $insert_category = $conn->prepare("INSERT INTO `categorias`(categoria) VALUES(?)");
      $insert_category->execute([$category_name]);

      if($insert_category){
         $message[] = '¡Nueva categoría agregada!';
      }
   }  
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_category = $conn->prepare("DELETE FROM `categorias` WHERE id_cat = ?");
   $delete_category->execute([$delete_id]);
   header('location:categorias.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Categorías</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

   <style>
      body {
         background-image: url('../images/mont.jpg'); /* Reemplaza 'ruta/al/fondo.jpg' con la ruta de tu imagen de fondo */
         background-size: cover;
         background-repeat: no-repeat;
      }
   </style>
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="add-categories">

   <h1 class="heading">Agregar Categorías</h1>

   <form action="" method="post">
      <div class="flex">
         <div class="inputBox">
            <span>Nombre de la Categoría</span>
            <input type="text" class="box" required maxlength="100" placeholder="Escribe el nombre" name="category_name">
         </div>
      </div>
      
      <input type="submit" value="Agregar" class="btn" name="add_category">
   </form>

</section>

<section class="show-categories">
    <h1 style = "color: #fff"  class="heading">Categorías Agregadas</h1>

    <div class="category-table-container">
        <table class="category-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_categories = $conn->prepare("SELECT * FROM `categorias`");
                $select_categories->execute();
                if ($select_categories->rowCount() > 0) {
                    while ($fetch_category = $select_categories->fetch(PDO::FETCH_ASSOC)) {
                ?>
                        <tr>
                            <td><?= $fetch_category['categoria']; ?></td>
                            <td>
                                <div class="action-icons">
                                    <a href="update_category.php?update=<?= $fetch_category['id_cat']; ?>" class="btn btn-edit"><i class="fas fa-edit"></i></a>
                                    <a href="categorias.php?delete=<?= $fetch_category['id_cat']; ?>" class="btn btn-delete" onclick="return confirm('¿Eliminar esta categoría?');"><i class="fas fa-trash-alt"></i></a>
                                </div>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="2">Aún no se han añadido categorías!</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

<style>

    .category-table-container {
        max-width: 800px;
        margin: 0 auto;
        overflow-x: auto;
    }
    .category-table {
        width: 100%;
        border-collapse: collapse;
        border-radius: 8px;
    }
    .category-table th, .category-table td {
        padding: 10px;
        border: 2px solid #ddd;
        font-size: 18px;
        background-color: #fff; /* Añadido para eliminar la transparencia */
    }
    .category-table th {
        border: 2px solid #c7c5c5;
        background-color: #e0dede;
        font-weight: bold;
        text-align: center
        ;
    }
    .category-table td {
        text-align: left;
    }
    .category-table tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .btn {
        padding: 8px;
        border-radius: 5px;
        text-decoration: none;
        cursor: pointer;
        transition: background-color 0.3s;
        font-size: 14px;
    }
    .btn i {
        margin: 0;
    }
    .btn-edit {
        background-color: #007bff;
        color: #fff;
        margin-right: 5px;
    }
    .btn-delete {
        background-color: #dc3545;
        color: #fff;
    }
    .btn:hover {
        background-color: #0056b3;
    }
    .action-icons {
        display: flex;
    }

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


</style>

<script src="../js/admin_script.js"></script>
   
</body>
</html>
