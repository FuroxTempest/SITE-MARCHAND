<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil | ALIZON</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400&display=swap" rel="stylesheet">
    <link rel="icon" href="../images/favicon.ico" />

    

</head>
<body id="accueil" onload="restoreScrollPos()">
    <?php
        // On ajoute les header dans la page
        include("./header.php");

        // On ajoute la page contenant les notifications 
        include("./notification.php");

        // On récupere le nom des catégories depuis la base de donnée vers la variable $tabcategorie
        $tabcategorie = array();
        foreach($bdd->query("SELECT nom from alizonbdd._categorie order by id_cat;") as $row) {
            array_push($tabcategorie,$row['nom']);
        }
    ?>    
    <!-- Carousselle de réduction -->
    <section id="promotion" class="mx-auto" style="width: 100%; " >
        <div id="carouselExampleCaptions"  class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators" >
              <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
              <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
              <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
              <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="3" aria-label="Slide 4"></button>
              <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="4" aria-label="Slide 5"></button>
            </div>
            <div class="carousel-inner">
              <div class="carousel-item active ">
                <a href="./categorie.php?idcategorie=1"><img src="../images/promo_5.png" id="carousel1" class="d-block w-100" alt="..."></a>
              </div>
              <div class="carousel-item">
                <a href="./categorie.php?idcategorie=1"><img src="../images/promo_2.png" id="carousel1" class="d-block w-100" alt="..."></a>
              </div>
              <div class="carousel-item">
                <a href="./categorie.php?idcategorie=1"><img src="../images/promo_3.png" id="carousel1" class="d-block w-100"  alt="..."></a>
              </div>
              <div class="carousel-item">
                <a href="./categorie.php?idcategorie=1"><img src="../images/promo_4.png" id="carousel1" class="d-block w-100"  alt="..."></a>
              </div>
              <div class="carousel-item">
                <a href="./categorie.php?idcategorie=1"><img src="../images/promo_1.png" id="carousel1" class="d-block w-100"  alt="..."></a>
              </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Next</span>
            </button>
          </div>
    </section>



    <section id="accueil_categorie" class="mx-auto">
      <?php
      // Je recupere le lien de la pagea actuel (ici ../php/accueil.php)
      $page= "..$_SERVER[PHP_SELF]";

      // On repete n fois la page accueil_categorie.php (n=le nombre de categorie dansla base)
      foreach ($tabcategorie as $categorie) {
        include("./accueil_categorie.php");
      }

      ?>
    </section>

    <?php
        // On ajoute le footer à la page
        include("./footer.php")
    ?>
    <script>
        function setScroll() {
            let scroll = window.scrollY;
            let scrollString = scroll.toString();
            localStorage.setItem("scrollPosition", scrollString);
        }

        function restoreScrollPos() {
            let posYString = localStorage.getItem("scrollPosition");
            let posY = parseInt(posYString);
            window.scroll(0, posY);
            localStorage.clear();

            return true;
        }
    </script>
</body>
</html>

