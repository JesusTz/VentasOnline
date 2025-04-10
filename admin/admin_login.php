<?php

include '../components/connect.php';

session_start();

if(isset($_POST['submit'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

   $select_admin = $conn->prepare("SELECT * FROM `admins` WHERE name = ? AND password = ?");
   $select_admin->execute([$name, $pass]);
   $row = $select_admin->fetch(PDO::FETCH_ASSOC);

   if($select_admin->rowCount() > 0){
      $_SESSION['admin_id'] = $row['id'];
      header('location:dashboard.php');
   }else{
      $message[] = '¡Nombre de usuario o contraseña incorrecta!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Acceso ADM</title>
   <!-- Bootstrap CSS -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
   <!-- Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <!-- Custom CSS -->
   <style>
      body {
         background-color: #f8f9fa;
         background-image: url('https://source.unsplash.com/1600x900/?office');
         background-size: cover;
         background-repeat: no-repeat;
         background-position: center;
         height: 100vh;
         margin: 0;
         display: flex;
         align-items: center;
         justify-content: center;
      }
      .form-container {
         background-color: rgba(255, 255, 255, 0.9); /* Ajustando la opacidad */
         border-radius: 10px;
         padding: 30px;
         box-shadow: 0px 0px 20px 0px rgba(0, 0, 0, 0.1);
         max-width: 400px;
         width: 100%;
      }
      .form-container h3 {
         text-align: center;
         margin-bottom: 20px;
      }
      .form-container p {
         text-align: center;
         margin-bottom: 20px;
         font-size: 14px;
         color: #393d40;
      }
      .message {
         background-color: #dc3545;
         color: #fff;
         padding: 10px;
         border-radius: 5px;
         margin-bottom: 10px;
         display: flex;
         align-items: center;
      }
      .message span {
         flex: 1;
         color: #ffc107;
      }
      .message i {
         cursor: pointer;
      }
      .btn-login {
         margin-top: 20px;
         text-align: center;
      }
   </style>
</head>
<body>

<?php
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
?>

<section class="form-container">
   <form action="" method="post">
      <h3>Inicia sesión ahora</h3>
      <p>Administrador= <span>admin</span> & Contraseña = <span>1809</span></p>
      <div class="mb-3">
         <input type="text" name="name" required placeholder="Ingresa el nombre de Usuario" maxlength="20" class="form-control" oninput="this.value = this.value.replace(/\s/g, '')">
      </div>
      <div class="mb-3">
         <input type="password" name="pass" required placeholder="Ingresa Contraseña" maxlength="20" class="form-control" oninput="this.value = this.value.replace(/\s/g, '')">
      </div>
      <div class="btn-login">
         <input type="submit" value="Iniciar sesión" class="btn btn-primary" name="submit">
      </div>
   </form>
</section>
   
<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
