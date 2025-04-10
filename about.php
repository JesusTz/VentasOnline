<?php
include 'components/connect.php';
session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
}

$select_categories = $conn->prepare("SELECT * FROM `categorias`");
$select_categories->execute();
$categories = $select_categories->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Categorias</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="category">
   <h1 class="heading">Compra por Categoría</h1>

   <div class="swiper category-slider">
      <div class="swiper-wrapper">
         <?php foreach ($categories as $category) { ?>
            <a href="category.php?category=<?= urlencode($category['categoria']); ?>" class="swiper-slide slide">
               <!-- Aquí puedes agregar el icono relacionado con la categoría -->
               <!-- Por ejemplo, puedes tener una tabla de correspondencia entre categorías y iconos -->
               <!-- Luego, puedes recuperar el icono correspondiente para cada categoría -->
               <!-- Aquí solo se muestra el nombre de la categoría, deberás ajustar el código según tus necesidades -->
               <h3><?= $category['categoria']; ?></h3>
            </a>
         <?php } ?>
      </div>
      <div class="swiper-pagination"></div>
   </div>
</section>

<?php include 'components/footer.php'; ?>

<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
<script src="js/script.js"></script>
<script>
var swiper = new Swiper(".category-slider", {
   loop: true,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable: true,
   },
   breakpoints: {
      0: {
         slidesPerView: 2,
      },
      650: {
         slidesPerView: 3,
      },
      768: {
         slidesPerView: 4,
      },
      1024: {
         slidesPerView: 5,
      },
   },
});
</script>
</body>
</html>
