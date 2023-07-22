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
        <link rel="icon" href="../images/favicon.ico" />
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

                        
            if(isset($_POST['id_prod']) && isset($_POST['delete'])){

                if($_POST['delete'] == true)
                {
                
                    $idprod=$_POST['id_prod'];
                    //suppressions des promotions
                    $supProm = $bdd->prepare("DELETE FROM alizonbdd._promotion WHERE id_produit=? ;");
                    $supProm->execute([$idprod]);

                    //suppressions des produits dans les paniers
                    $supProdPanier=$bdd->prepare("DELETE FROM alizonbdd._panier WHERE id_produit=? ;");
                    $supProdPanier->execute([$idprod]);

                    //suppressions du produit dans la liste des produits
                    $supProdListe=$bdd->prepare("DELETE FROM alizonbdd._produit WHERE id_produit=? ;");
                    $supProdListe->execute([$idprod]);

                    unset($idprod);
                    unset($_POST['id_prod']);

                    echo '
                    <div class="notification_ok">
                        Le produit à bien été supprimé
                    </div>
                    ';
                }
                
            }
        ?>
        <main>
            <h1><p>Catalogue de produit</p></h1>
            <article>

                <!-- Filtrage
                <table class="filtre">
                    <tbody>
                        <form action="" method="POST">
                            <?php
                                $cats = $bdd->query("SELECT nom FROM _categorie;");
                                $i=0;
                                echo <<<html
                                <tr>
                                html;
                                foreach($cats as $cat)
                                {
                                    if($i==4)
                                    {
                                        $i=0;
                                        echo '</tr><tr>';
                                    }
                                    echo '
                                    <td>
                                        <input type="checkbox" name="'.$cat['nom'].'"/>
                                        <label for='.$cat['nom'].'>'.$cat['nom'].'</label>
                                    </td>';
                                    $i++;
                                }
                            ?>
                        </form>
                    </tbody>
                </table>
                -->

                <!-- Affichage des produits -->
                <div class="row">
                    <?php
                        //$prod = $bdd->query("SELECT * FROM _produit;");
                        $prod= array();
                        $stmt = $bdd->prepare("SELECT * FROM _produit WHERE id_vendeur = ? ORDER BY id_produit;");
                        $stmt->execute([$_SESSION['id_vendeur']]);
                        $elements = $stmt->fetchAll();
                        foreach($elements as $row)
                        {
                            array_push($prod,$row);
                        }
                        if(empty($prod))
                        {
                            echo '<p class="empty">Aucun produit actuellement...<br>Ajoutez-en en <a href="import_action_vendeur.php">important un catalogue</a><br>ou en <a href="ajout_action_produit">ajoutant un produit</a></p>';
                        }
                        else
                        {
                            echo '<div id="catalogue">';
                            foreach($prod as $key)
                            {
                                include("./carte_produit.php");
                            }
                            echo '</div>';
                        }
                    ?>
                </div>
            </article>
        </main>
    </body>
</html>
