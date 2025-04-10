<?php
if(isset($message)) {
    if(is_array($message)) {
        foreach($message as $msg) {
            echo '
            <div class="message">
                <span>'.$msg.'</span>
                <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
            </div>
            ';
        }
    } else {
        // Si $message no es un array, lo tratamos como un único mensaje
        echo '
        <div class="message">
            <span>'.$message.'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>
        ';
    }
}

/*<?php
   if(isset($message)){
      foreach($message as $message){
         echo '
         <div class="message">
            <span>'.$message.'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
         ';
      }
   }
?>*/

?>


<header class="header">

   <section class="flex">

      <a href="../admin/dashboard.php" class="logo">Admin<span>PanelSP</span></a>

      <nav class="navbar">
         <a href="../admin/dashboard.php">Inicio</a>
         <a href="../admin/products.php">Productos</a>
         <a href="../admin/categorias.php">Categorias</a>
         <a href="../admin/placed_orders.php">Pedidos</a>
         <a href="../admin/admin_accounts.php">Facturas</a>
         <a href="../admin/users_accounts.php">Usuarios</a>
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <div class="profile">
         <?php
            $select_profile = $conn->prepare("SELECT * FROM `admins` WHERE id = ?");
            $select_profile->execute([$admin_id]);
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <p><?= $fetch_profile['name']; ?></p>
         <a href="../admin/update_profile.php" class="btn">Actualizar perfil</a>
         <a href="../components/admin_logout.php" class="delete-btn" onclick="return confirm('logout from the website?');">Cerrar sesión</a> 
      </div>

   </section>

</header>