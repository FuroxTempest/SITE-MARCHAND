<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="./style_vendeur.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400&display=swap" rel="stylesheet">
        <title>Catalogue de produit</title>
    </head>
    <body>
            <?php
                include("./head_vendeur.php");
                include("../php/connect_params.php");
                $bdd = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass,
                [PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
                );
                $bdd->exec("SET SCHEMA 'alizonbdd';");
            ?>

        <main>
            <h1>Détail commande</h1>
            <article>
                <?php
                    $commandes= array();
                    foreach($bdd->query("SELECT nom,id_commande,adresse FROM _commande natural join _client WHERE id_commande = $_GET[id_com];") as $row)
                    {
                        echo "<h2>client : ".$row['nom']."</h2>";
                        echo "<p>numéro de commande : ".$row['id_commande']."</p>";
                        echo "<p>adresse : ".$row['adresse']."</p>";
                        $prix=0;
                        echo "<ul>";
                        foreach($bdd->query("SELECT nomprod,nb_article,prix_total FROM _commande natural join _panier natural join _produit WHERE id_commande = $_GET[id_com];") as $panier)
                        {
                            echo "<li>produit : ".$panier['nomprod']."</li><ul>";
                            echo "<li>quantité : ".$panier['nb_article']."</li>";
                            echo "<li> prix : ".$panier['prix_total']."</li></ul>";
                            $prix+=$panier['prix_total'];
                        }
                        echo "</ul>";
                        echo "<p>prix total : $prix</p>";
                    }
                ?>
            </article>
        </main>
    </body>
</html>