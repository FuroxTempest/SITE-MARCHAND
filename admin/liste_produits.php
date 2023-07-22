<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="style_admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400&display=swap" rel="stylesheet">
    <link rel="icon" href="../images/favicon.ico" />
    <title> Liste des produits | Admin</title>
</head>
<body>
<?php include("head_admin.php"); ?>
    <?php
       include('../php/connect_params.php');
       try{
           $bdd = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass,
           [PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
           PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
           );
       }catch (PDOException $e){
           print "Erreur ! : " . $e->getMessage() . "<br/>";
           die();
       }
   

        $produits = $bdd->prepare("SELECT id_produit, id_categorie, nomprod as nom, prix_ttc as prix_art, descriptif, stock, id_vendeur from alizonbdd._produit ORDER BY id_produit;");
        $produits->setFetchMode(PDO::FETCH_ASSOC);
        $produits->execute();
        $tab_produit = array();

        foreach ($produits as $prod){
            array_push($tab_produit, $prod);
        }
        ?>

        <main class="main_produit">
        <h1 class="text-center mb-5 fw-bold">Catalogue de produit</h1>

            <!-- Affichage des produits -->
            <div class="liste_produit row justify-content-around bg-white">
            <?php
                foreach($tab_produit as $key)
                {
                    ?>
                    <div class="card mt-5">
                        <?php
                        foreach(glob("../images/".$key['id_produit']."_*")as $image){
    
                        }
                        ?>
                        <img src="<?php echo $image ?>" class="card-img-top"  alt="<?php echo $key['nomprod'] ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo (ucfirst($key['nomprod']))?></h5>
                            <p class="card-text"><?php echo $key["descriptif"] ?></p>
                            <form action="./detail_produit.php" method="GET">
                                <input type="hidden" name="id_produit" value="<?php echo $key['id_produit'] ?>"/>
                                <input type="submit" class="btn btn-primary" value="Détails"/>
                            </form>
                        </div>
                    </div>
                    <?php
                }  
                ?>
            </div>
        </main>
</body>
</html>