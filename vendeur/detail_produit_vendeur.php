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
    <?php
        include('../php/connect_params.php');
        try
        {
            $bdd = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass,
            [PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
            );
            $bdd->exec("set schema 'alizonbdd';");
            //selection des catgorie
            $req = $bdd->query('SELECT id_produit, nomprod, prix_ttc, descriptif, quantite, poids, volume, stock, id_categorie FROM _produit WHERE id_produit = '.$_GET['id_produit'].';');
            $req->execute();
            $produit = $req->fetch();
            echo '<title>'.ucfirst($produit['nomprod']).' | Détails</title>';
        }
        catch (PDOException $e)
        {
            print "Erreur ! : " . $e->getMessage() . "<br/>";
            die();
        }
    ?>    
</head>
<body>
    <?php include('head_vendeur.php'); ?>
        <main>
            <h1>
                <?php echo '<p>'.ucfirst($produit['nomprod']).'</p>'; ?>
                <a href="./catalogue_produit.php" class="return_back">
                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-arrow-return-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M14.5 1.5a.5.5 0 0 1 .5.5v4.8a2.5 2.5 0 0 1-2.5 2.5H2.707l3.347 3.346a.5.5 0 0 1-.708.708l-4.2-4.2a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 8.3H12.5A1.5 1.5 0 0 0 14 6.8V2a.5.5 0 0 1 .5-.5z"/>
                    </svg>
                </a>

                <!-- Suppression d'un produit -->

                <form action="./catalogue_produit.php" method="POST">
                    <input type="hidden" name="delete" value="true"/>
                    <input type="hidden" name="id_prod" value="<?php echo $produit['id_produit']; ?>" />
                    <!-- button vers modal -->
                    <button type="button" class="delete_product" data-bs-toggle="modal" data-bs-target="#supprimer_prod">
                        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                            <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                        </svg>
                    </button>
                    <!-- modal -->
                    <div class="modal fade" id="supprimer_prod" tabindex="-1" aria-labelledby="videLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="videLabel">Confirmation</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p style="font-size: 0.5em;font-weight: normal">Êtes-vous sûr de vouloir supprimer le produit?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-primary">Confirmer</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- ============================================================================== -->
            </h1>
            <article>
                <p>
                    <div class="container row p-0 m-0">
                        <?php
                            foreach(glob("../images/$produit[id_produit]_*.*",GLOB_NOESCAPE) as $img)
                            {
                                echo '<img src="'.$img.'" class="col-4" id="image_produit"/>';
                            }
                        ?>
                    </div>
                    <form action="modif_images_produit.php" method="POST">
                        <input type="submit" name="nouvelleModif" value="Ajouter/Supprimer des images"/>
                        <?php echo '<input type="hidden" name="id_produit" value="'.$produit['id_produit'].'"/>'; ?>
                    </form>
                </p>
                <form action='modif_produit.php' method='POST' class='detail_prod'>
                    <table>
                        <tbody>

                            <?php
                                // Modifier un produit

                                echo '<input type="hidden" name="id_produit" value="'.$produit['id_produit'].'"/>';
                                $string_produit = array(
                                'id_produit'=>'ID du produit',
                                'nomprod'=>'Nom du produit',
                                'prix_ht'=>'Prix HT (en €)',
                                'prix_ttc'=>'Prix TTC (en €)',
                                'descriptif'=>'Description',
                                'quantite'=>'Quantité de conditionnement',
                                'poids'=>'Poids (en g)',
                                'volume'=>'Volume (en mL)',
                                'stock'=>'Stock',
                                'id_categorie'=>'Catégorie'
                                );

                                $req = $bdd->query("SELECT nom, tva from _categorie WHERE id_cat = ".$produit['id_categorie'].";");
                                $req->execute();
                                $cat_produit = $req->fetch();

                                foreach($produit as $key=>$elt)
                                {
                                    if($key == 'volume' || $key == 'poids')
                                    {
                                        if($key == 'volume' && $produit[$key] != null)
                                        {
                                            echo '
                                            <tr>
                                                <td>'.$string_produit[$key].'</td>
                                                <td>'.$produit[$key].'</td>
                                            </tr>';
                                        }
                                        else if($key =='poids' && $produit[$key] != null)
                                        {
                                            echo '
                                            <tr>
                                                <td>'.$string_produit[$key].'</td>
                                                <td>'.$produit[$key].'</td>
                                            </tr>';
                                        }
                                    }
                                    else if($key == 'id_categorie')
                                    {
                                        echo '
                                        <tr>
                                            <td>Catégorie</td>
                                            <td>'.$cat_produit['nom'].'</td>
                                        </tr>
                                        ';
                                    }
                                    else if($key == 'prix_ttc')
                                    {
                                        echo '
                                        <tr>
                                            <td>'.$string_produit[$key].'</td>
                                            <td>'.number_format($produit[$key], 2, ",", " ").'</td>
                                        </tr>';
                                        echo '
                                        <tr>
                                            <td>'.$string_produit['prix_ht'].'</td>
                                            <td>'.number_format($produit[$key] / $cat_produit['tva'], 2, ",", " ").'</td>
                                        </tr>';
                                    }
                                    else
                                    {
                                        echo '
                                        <tr>
                                            <td>'.$string_produit[$key].'</td>
                                            <td>'.$produit[$key].'</td>
                                        </tr>';
                                    }
                                }
                            ?>

                           
                        </tbody>
                    </table>
                    <div class="row mt-3 justify-content-between">
                        <input type="submit" value="Modifier les attributs" class="col-5 m-3 mb-0"/>
                        <?php echo '<a href="solder_produit.php?id_produit='.$produit['id_produit'].'" class="col-5 m-3 mb-0">Gérer les réductions</a>'; ?>
                    </div>
                </form>
                <?php
                    if(isset($_GET['modif']))
                    {
                        echo '
                        <div class="notification_ok">
                            Produit modifié avec succès!
                        </div>
                        ';
                    }
                ?>
        </article>
    </main>

</body>
</html>