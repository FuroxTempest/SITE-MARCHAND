<html>
<?php
session_start();
include('./connect_params.php');

try{ 
    $bdd = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass,
    [PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
    );
}catch (PDOException $e){
    print "Erreur ! : " . $e->getMessage() . "<br/>";
    die();
}
$idvendeur = $_GET['id_vendeur'];
foreach($bdd->query("SELECT nom,descriptif,adresse_postale,mail,logo,note from alizonbdd._vendeur where _vendeur.id_vendeur = ".$idvendeur.";") as $row) {
    $vendeur = $row;
}

$page= "..$_SERVER[PHP_SELF]";


?>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php echo"<title> ".$vendeur['nom']." | ALIZON</title>";?>
    <link rel="icon" href="../images/favicon.ico" />

    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400&display=swap" rel="stylesheet"> 
 
    
</head>
<body style="background-color:#E6E6E6;">
    <?php
        include("./header.php");
    ?>

    <main id="vendeur">
        <p class="titre_vendeur">
            VENDEUR
        </p>
        <section id="carte_vendeur">
            <div>
                <?php echo "<img src=\"".$vendeur['logo']."\" alt=\"logo vendeur\" class=\"icone_vendeur\" >";?>
            </div>
            <div class="contenue">
                <div>
                    
                    <?php
                    echo "<h1>$vendeur[nom]</h1>";
                    echo "<h2>$vendeur[note]/5</h2>";
                    ?> 
                                        
                </div>
                <div class="mail">
                    <div>
                        <?php
                        echo "<a href=\"mailto:".$vendeur['mail']."\">".$vendeur['mail']."</a> <br>";
                        echo "<p>".$vendeur['adresse_postale']."</p>";
                        ?>
                    </div>
                    <?php
                        echo "<h2>$vendeur[note]/5</h2>";
                    ?>

                    
                </div>
                <p>
                    <?php
                    echo $vendeur['descriptif'];
                    ?>
                </p>


            </div>

            

        </section>
        <hr style="width:80%; margin:45px auto ;">
        <section id="produits">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-4" >
                <?php
                foreach($bdd->query("SELECT _produit.stock,_produit.id_produit,_produit.id_categorie,_produit.nomprod as nom,_produit.prix_ttc as prix_art ,_produit.descriptif from alizonbdd._produit where  _produit.id_vendeur = ".$idvendeur .";") as $key) {
                    include("./carte_produit.php");
                }

                ?>
            </div>

        </section>

    </main>
    <?php
        include("./footer.php");
    ?>
    </body>
</html>
