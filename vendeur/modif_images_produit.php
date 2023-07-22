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
    <title>Vendeur | Modification Image</title>
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
            $req = $bdd->query('SELECT id_produit, nomprod, prix_ttc, descriptif, quantite, poids, volume, stock, id_categorie FROM _produit WHERE id_produit = '.$_POST['id_produit'].';');
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
    <p>
    <?php
        if($_POST['nouvelleModif'] == '') // Verif qu'on ne vient pas juste de cliquer sur modifier image
        {
            $nbr_img = 0;
            foreach(glob("../images/$produit[id_produit]_*.*",GLOB_NOESCAPE) as $key=>$img) // compte le nombre d'images
            {
                $nbr_img++;
            }
            if($_POST['newImage'] != '') // Verif que l'on veut ajouter une nouvelle image
            {
                if($_FILES['imagesP']['error'][0] == 4) // Cas ou il n'y a pas d'images
                {
                    echo '
                    <div class="notification_non">
                        Il n\'y a pas d\'image sélectionnée!
                    </div>
                    ';
                }
                else // cas ou il y a des images de selectionner
                {
                    $upload_path = "../images/";
                    $id_produit = $produit['id_produit'];
                    foreach($_FILES['imagesP']['name'] as $key=>$img)
                    {
                        // Calcul d udernier numéro d'image
                        $max = 0;
                        foreach(glob("../images/".$produit['id_produit']."_*") as $image)
                        {
                            $sortir = false;
                            $mot = "";
                            for($i=0;$i<strlen($image);$i++)
                            {
                                if($i > 9 && !$sortir)
                                {
                                    if($image[$i] == '.')
                                    {
                                        $sortir = true;
                                    }
                                    else
                                    {
                                        $mot = $mot.$image[$i];
                                    }
                                }
                            }
                            $nbr = explode('_', $mot);
                            if($nbr[1] > $max)
                            {
                                $max = $nbr[1];
                            }
                        }

                        $newKey = $max + 1;
                        $extension = pathinfo($img, PATHINFO_EXTENSION);
                        $_FILES['imagesP']['name'][$key] = "${id_produit}_${newKey}.${extension}";
                        $path_new_file = $upload_path.$_FILES['imagesP']['name'][$key];
                        move_uploaded_file($_FILES['imagesP']['tmp_name'][$key], $path_new_file);
                    }
                    echo '
                    <div class="notification_ok">
                        Image(s) ajoutée(s) avec succès!
                    </div>
                    ';
                }
            }
            else // Si on veut supprimer une image
            {
                $img_restant = $nbr_img;
                $err = false;
                $select = false;
                foreach(glob("../images/$produit[id_produit]_*.*",GLOB_NOESCAPE) as $key=>$img)
                {
                    if($_POST['images'.$key] == 'on')
                    {
                        if($img_restant == 1)
                        {
                            echo '
                            <div class="notification_non">
                                Attention! Il faut forcément une image pour un produit. Une image n\'a donc pas été supprimée!
                            </div>
                            ';
                            $err = true;
                        }
                        else
                        {
                            $select = true;
                            unlink($img);
                            $img_restant--;
                        }
                    }
                }
                if(!$err)
                {
                    if($select)
                    {
                        echo '
                        <div class="notification_ok">
                            Image(s) supprimée(s) avec succès!
                        </div>
                        ';
                    }
                    else
                    {
                        echo '
                        <div class="notification_non">
                            Aucune image n\'a été sélectionnée!
                        </div>
                        ';
                    }
                }
            }
        }
    ?>
    </p>

    <?php include('head_vendeur.php'); ?>
        <main>
            <h1>
                <?php echo '<p>'.ucfirst($produit['nomprod']).'</p>'; ?>
                <a href="./detail_produit_vendeur.php?id_produit=<?php echo $produit['id_produit'];?>" class="return_back">
                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-arrow-return-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M14.5 1.5a.5.5 0 0 1 .5.5v4.8a2.5 2.5 0 0 1-2.5 2.5H2.707l3.347 3.346a.5.5 0 0 1-.708.708l-4.2-4.2a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 8.3H12.5A1.5 1.5 0 0 0 14 6.8V2a.5.5 0 0 1 .5-.5z"/>
                    </svg>
                </a>
            </h1>
            <article>
                <form action="modif_images_produit.php" method="POST" enctype="multipart/form-data">

                    <!-- Ajouter des images -->

                    <h2>Ajouter une image</h2>
                    <input type="file" multiple name="imagesP[]" accept="image/*">
                    <input type="submit" name="newImage" value="Ajouter l'image"/>
                    <hr>

                    <!-- Supprimer des images -->

                    <h2>Supprimer une image</h2>
                    <p>Sélctionnez le(s) image(s) à supprimer.</p>
                    <div class="container row justify-content-center">
                        <?php
                            $nbr_img = 0;
                            foreach(glob("../images/$produit[id_produit]_*.*",GLOB_NOESCAPE) as $key=>$img) // compte le nombre d'images
                            {
                                $nbr_img++;
                            }
                            echo '<input type="hidden" name="id_produit" value="'.$produit['id_produit'].'"/>';
                            echo '<div class="col-6 row">';
                            foreach(glob("../images/$produit[id_produit]_*.*",GLOB_NOESCAPE) as $key=>$img) // affiche la moitié des images dans une colonne a droite
                            {
                                if($key < ($nbr_img / 2))
                                {
                                    echo '<div class="col-12 row">';
                                    echo '<img src="'.$img.'" class="col-9" id="image_produit"/>';
                                    echo '<input type="checkbox" name="images'.$key.'" class="col-3"/>';
                                    echo '</div>';
                                }
                            }
                            echo '</div>';
                            echo '<div class="col-6 row align-self-start">';
                            foreach(glob("../images/$produit[id_produit]_*.*",GLOB_NOESCAPE) as $key=>$img) // affiche l'autre moitié des images dans une colonne a gauche
                            {
                                if($key >= ($nbr_img / 2))
                                {
                                    echo '<div class="col-12 row">';
                                    echo '<input type="checkbox" name="images'.$key.'" class="col-3"/>';
                                    echo '<img src="'.$img.'" class="col-9" id="image_produit"/>';
                                    echo '</div>';
                                }
                            }
                            echo '</div>';
                        ?>
                        <button type="button" class="bouton-principal" data-bs-toggle="modal" data-bs-target="#videModal">Suppimer les images sélectionnées</button>            

                        <!--modal de confirmation-->
                        <div class="modal fade" id="videModal" tabindex="-1" aria-labelledby="videLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="videLabel">Confirmation</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Êtes-vous certain de vouloir supprimer les images sélectionnées?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-primary">Confirmer</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </article>
        </main>
    </body>

<script>

    

</script>

</html>