<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:user_login.php');
};

if(isset($_POST['order'])){

   // Datos del usuario
   $name = filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $number = filter_var($_POST['number'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $method = filter_var($_POST['method'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $address = 'flat no. '. $_POST['flat'] .', '. $_POST['street'] .', '. $_POST['city'] .', '. $_POST['state'] .', '. $_POST['country'] .' - '. $_POST['pin_code'];
   $address = filter_var($address, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $total_products = $_POST['total_products'];
   $total_price = $_POST['total_price'];

   // Verificar el carrito
   $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $check_cart->execute([$user_id]);

   if($check_cart->rowCount() > 0){
      // Insertar el pedido en la base de datos (opcional si no necesitas esta tabla)
      $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price) VALUES(?,?,?,?,?,?,?,?)");
      $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $total_price]);

      // Obtener los productos del carrito
      $cart_items = $check_cart->fetchAll(PDO::FETCH_ASSOC);
      foreach ($cart_items as $item) {
         // Actualizar el stock del producto
         $update_stock = $conn->prepare("UPDATE `products` SET Stock = Stock - ? WHERE id = ?");
         $update_stock->execute([$item['quantity'], $item['pid']]);

         // Insertar detalles de venta en la tabla de ventas
         $insert_sale = $conn->prepare("INSERT INTO `ventas`(id_producto, cantidad, precio_total) VALUES(?,?,?)");
         $insert_sale->execute([$item['pid'], $item['quantity'], $item['price'] * $item['quantity']]);
      }

      // Eliminar el carrito del usuario
      $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
      $delete_cart->execute([$user_id]);

      $message[] = 'Pedido Realizado con éxito';
   } else {
      $message[] = 'Tu carrito está vacío';
   }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Pagar</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="checkout-orders">

   <form action="" method="POST">

   <h3>Tus pedidos</h3>

      <div class="display-orders">
      <?php
         $grand_total = 0;
         $cart_items[] = '';
         $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $select_cart->execute([$user_id]);
         if($select_cart->rowCount() > 0){
            while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
               $cart_items[] = $fetch_cart['name'].' ('.$fetch_cart['price'].' x '. $fetch_cart['quantity'].') - ';
               $total_products = implode($cart_items);
               $grand_total += ($fetch_cart['price'] * $fetch_cart['quantity']);
      ?>
         <p> <?= $fetch_cart['name']; ?> <span>(<?= '$'.$fetch_cart['price'].' mxn x '. $fetch_cart['quantity']; ?>)</span> </p>
      <?php
            }
         }else{
            echo '<p class="empty">¡Tu carrito esta vacío!</p>';
         }
      ?>
         <input type="hidden" name="total_products" value="<?= $total_products; ?>">
         <input type="hidden" name="total_price" value="<?= $grand_total; ?>" value="">
         <div class="grand-total">Total de todo: <span>$<?= $grand_total; ?> mxn</span></div>
      </div>

      <h3>Realiza tu Pedido</h3>

      <div class="flex">
         <div class="inputBox">
            <span>Nombre:</span>
            <input type="text" name="name" placeholder="Agrega tu nombre" class="box" maxlength="20" required>
         </div>
         <div class="inputBox">
            <span>Numero:</span>
            <input type="number" name="number" placeholder="Agrega tu numero telefonico" class="box" min="0" max="9999999999" onkeypress="if(this.value.length == 10) return false;" required>
         </div>
         <div class="inputBox">
            <span>Tu correo:</span>
            <input type="email" name="email" placeholder="Agrega tu correo" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Elije metodo de pago :</span>
            <select name="method" class="box" required>
               <option value="cash on delivery">Tarjeta de credito</option>
               <option value="credit card">Paypal</option>
            </select>
         </div>
         <div class="inputBox">
            <span>Direccion de calle:</span>
            <input type="text" name="flat" placeholder="Calle 27 entre 20 y 18" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Numero de Casa:</span>
            <input type="text" name="street" placeholder="No-51" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Ciudad :</span>
            <input type="text" name="city" placeholder="Valladolid" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Colonia:</span>
            <input type="text" name="state" placeholder="San juan" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Pais:</span>
            <input type="text" name="country" placeholder="Mexico" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Codigo Postal :</span>
            <input type="number" min="0" name="pin_code" placeholder="97780" min="0" max="999999" onkeypress="if(this.value.length == 6) return false;" class="box" required>
         </div>
      </div>

      <input type="submit" name="order" class="btn <?= ($grand_total > 1)?'':'disabled'; ?>" value="place order">

   </form>

</section>


<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>