<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

// Obtenemos el número de pedidos pendientes
$total_pendings = 0;
$select_pendings = $conn->prepare("SELECT COUNT(*) as total FROM `orders` WHERE payment_status = ?");
$select_pendings->execute(['Pendiente']);
if($select_pendings->rowCount() > 0){
   $total_pendings = $select_pendings->fetch(PDO::FETCH_ASSOC)['total'];
}

// Obtenemos el número de pedidos en proceso
$total_in_process = 0;
$select_in_process = $conn->prepare("SELECT COUNT(*) as total FROM `orders` WHERE payment_status = ?");
$select_in_process->execute(['Proceso']);
if($select_in_process->rowCount() > 0){
   $total_in_process = $select_in_process->fetch(PDO::FETCH_ASSOC)['total'];
}

// Obtenemos el número de pedidos completados
$total_completes = 0;
$select_completes = $conn->prepare("SELECT COUNT(*) as total FROM `orders` WHERE payment_status = ?");
$select_completes->execute(['completed']);
if($select_completes->rowCount() > 0){
   $total_completes = $select_completes->fetch(PDO::FETCH_ASSOC)['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>PanelADM</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
   <style>
      body {
         background-image: url('../images/mont.jpg'); /* Reemplaza 'ruta/al/fondo.jpg' con la ruta de tu imagen de fondo */
         background-size: cover;
         background-repeat: no-repeat;
      }
      .dashboard .box-container {
         flex-wrap: wrap;
         gap: 20px;
         justify-content: center;
      }
      .dashboard .box {
         background: #fff;
         border-radius: 5px;
         box-shadow: 0 2px 15px rgba(0,0,0,.1);
         padding: 20px;
         text-align: center;
         flex: 1 1 300px;
      }
      .dashboard .box canvas {
         margin-top: 20px;
      }
      hr {
         border: 0;
         border-top: 1px solid #ccc;
         margin: 20px 0;
      }
   </style>
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="dashboard">

   <h1 class="heading">Panel De Control</h1>

   <div class="box-container">

      <div class="box">
         <h3>Estado de Pedidos</h3>
         <canvas id="ordersChart"></canvas>
      </div>

      <div class="box">
         <h3>Productos más Vendidos</h3>
         <canvas id="salesChart"></canvas>
      </div>

      <div class="box">
         <h3>Ventas Totales</h3>
         <canvas id="totalSalesChart"></canvas>
      </div>
   </div>

   <hr>

</section>

<script>
   const ordersCtx = document.getElementById('ordersChart').getContext('2d');
   const ordersChart = new Chart(ordersCtx, {
      type: 'pie',
      data: {
         labels: ['Pendientes', 'Proceso', 'Finalizados'],
         datasets: [{
            data: [2, 5, 5],
            backgroundColor: ['#FF6384', '#36A2EB', '#4CAF50'],
         }]
      },
      options: {
         responsive: true,
         plugins: {
            legend: {
               position: 'top',
            }
         }
      }
   });

   const salesCtx = document.getElementById('salesChart').getContext('2d');
   const salesChart = new Chart(salesCtx, {
      type: 'pie',
      data: {
         labels: ['Smartwatch', 'AirPods', 'Paraguas'],
         datasets: [{
            data: [8, 5, 7], // Sustituir con datos reales
            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
         }]
      },
      options: {
         responsive: true,
         plugins: {
            legend: {
               position: 'top',
            }
         }
      }
   });

   const totalSalesCtx = document.getElementById('totalSalesChart').getContext('2d');
   const totalSalesChart = new Chart(totalSalesCtx, {
      type: 'pie',
      data: {
         labels: ['Productos vendidos', 'Producto No vendidos'], // Sustituir con datos reales
         datasets: [{
            data: [10, 15], // Sustituir con datos reales
            backgroundColor: ['#FF6384', '#36A2EB'],
         }]
      },
      options: {
         responsive: true,
         plugins: {
            legend: {
               position: 'top',
            }
         }
      }
   });
</script>

<script src="../js/admin_script.js"></script>

</body>
</html>
