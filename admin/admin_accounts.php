<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}

// Consulta para obtener el total de productos, el precio de compra total y el stock total
$select_products = $conn->prepare("SELECT COUNT(*) as total_products, SUM(preciocompra * stock) as total_purchase_price, SUM(stock) as total_stock FROM products");
$select_products->execute();
$product_totals = $select_products->fetch(PDO::FETCH_ASSOC);

// Consulta para obtener la cantidad de productos vendidos y el precio total de ventas
$select_sales = $conn->prepare("SELECT SUM(cantidad) as total_sold_products, SUM(precio_total) as total_sales_price FROM ventas");
$select_sales->execute();
$sales_totals = $select_sales->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['generate_invoice'])) {
    $owner_name = $_POST['owner_name'];
    $total_products = $product_totals['total_products'] ?? 0;
    $total_purchase_price = $product_totals['total_purchase_price'] ?? 0;
    $total_stock = $product_totals['total_stock'] ?? 0;
    $total_sold_products = $sales_totals['total_sold_products'] ?? 0;
    $total_sales_price = $sales_totals['total_sales_price'] ?? 0;

    // Obtener la hora actual de la base de datos
    $current_time_query = $conn->query("SELECT CURRENT_TIMESTAMP");
    $current_time = $current_time_query->fetch(PDO::FETCH_ASSOC)['CURRENT_TIMESTAMP'];

    // Inserción en la tabla de facturas
    $insert_invoice = $conn->prepare("INSERT INTO facturas (nombre_propietario, total_productos, precio_compra_total, nombre_tienda, productos_vendidos, precio_total_ventas, total_stock, fecha) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $insert_invoice->execute([$owner_name, $total_products, $total_purchase_price, 'SuperNova Valladolid', $total_sold_products, $total_sales_price, $total_stock, $current_time]);

    // Mostrar la factura en una ventana emergente con un diseño mejorado
    echo "<script>
            var facturaVentana = window.open('', 'Factura', 'width=600,height=400');
            facturaVentana.document.write('<html><head><title>Factura</title></head><body>');
            facturaVentana.document.write('<div style=\"padding: 20px;\">');
            facturaVentana.document.write('<h2 style=\"text-align: center;\">Factura</h2>');
            facturaVentana.document.write('<p style=\"text-align: right;\">Fecha y Hora: " . $current_time . "</p>');
            facturaVentana.document.write('<h3 style=\"text-align: center;\">Nombre de la Tienda: SuperNova Valladolid</h3>');
            facturaVentana.document.write('<p><strong>Nombre del Propietario:</strong> " . $owner_name . "</p>');
            facturaVentana.document.write('<p><strong>Total de Productos:</strong> " . $total_products . "</p>');
            facturaVentana.document.write('<p><strong>Precio de Compra Total:</strong> $" . number_format($total_purchase_price, 2) . " MXN</p>');
            facturaVentana.document.write('<p><strong>Productos Vendidos:</strong> " . $total_sold_products . "</p>');
            facturaVentana.document.write('<p><strong>Precio Total de Ventas:</strong> $" . number_format($total_sales_price, 2) . " MXN</p>');
            facturaVentana.document.write('<p><strong>Stock Total:</strong> " . $total_stock . "</p>');
            facturaVentana.document.write('</div>');
            facturaVentana.document.write('</body></html>');
            facturaVentana.document.close();
          </script>";

    echo "<script>alert('Factura generada y guardada exitosamente');</script>";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Factura</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
    <style>
        body {
            background-image: url('../images/mont.jpg');
            background-size: cover;
            background-repeat: no-repeat;
        }

        .invoice-container {
            max-width: 600px;
            margin: 50px auto;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .invoice-container h1 {
            color: #333;
            margin-bottom: 20px;
        }

        .invoice-container .inputBox {
            margin-bottom: 20px;
        }

        .invoice-container .inputBox span {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-size: 20px;
        }

        .invoice-container .box {
            width: 100%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .invoice-container .btn {
            padding: 8px 20px;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            margin-top: 20px;
        }

        .invoice-container .btn:hover {
            background-color: #0056b3;
        }

        .invoice-details {
            margin-top: 20px;
            text-align: left;
            color: #333;
            font-size: 18px;
        }
    </style>
</head>



<body>

    <?php include '../components/admin_header.php'; ?>

    <section class="invoice-container">
        <h1 class="heading">Generar Factura</h1>
        <form action="" method="post">
            <div class="inputBox">
                <span>Nombre del Propietario</span>
                <input type="text" class="box" required maxlength="100" placeholder="Escribe el nombre" name="owner_name">
            </div>
            <div class="invoice-details">
                <p>Número de Productos diferentes: <strong><?= $product_totals['total_products']; ?></strong></p>
                <p>Precio de Compra Total: <strong>$<?= number_format($product_totals['total_purchase_price'] ?? 0, 2); ?> MXN</strong></p>
                <p>Stock Total de Productos: <strong><?= $product_totals['total_stock'] ?? 0; ?></strong></p>
                <p>Total de Productos Vendidos: <strong><?= $sales_totals['total_sold_products'] ?? 0; ?></strong></p>
                <p>Precio Total de Ventas: <strong>$<?= number_format($sales_totals['total_sales_price'] ?? 0, 2); ?> MXN</strong></p>
            </div>
            <input type="submit" value="Generar Factura" class="btn" name="generate_invoice">
        </form>
    </section>

</body>

</html>
