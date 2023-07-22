<?php
    session_start();
    //1) paramètre de la bdd
    include("../php/connect_params.php");
    //2) ouvrir la bdd
    $bdd = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass,
    [PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
    );
    $bdd->exec("set schema 'alizonbdd';");

    if($_FILES['imagesP']['name'][0] != '')
    {

        //selection des catgorie
        $sth = $bdd->prepare("SELECT nom FROM _categorie;");
        $sth->execute();
        $listeCat= $sth->fetchAll();

        try
        {
            // Test : nouvelle cat?
            if($_POST['nomNewCat'] != '')
            {
                $nomCatP = $_POST['nomNewCat'];
            }
            else
            {
                $nomCatP=$_POST['catP'];
            }

            // Def les champ vide en NULL
            foreach($_POST as $key=>$elt)
            {
                if($elt == '')
                {
                    $_POST[$key] = NULL;
                }
            }

            // Def le tableau de param de la requete prep
            $array_param = array(
                $_POST['nomP'],
                $_POST['qteP'],
                $_POST['poidP'],
                $_POST['volumeP'],
                $_POST['prixP'],
                $_POST['descP'],
                $_POST['stockP'],
                $nomCatP,
                $_SESSION['id_vendeur']
            );

            // Reformatage nom et nom_cat avec MAJ au début
            $array_param[0] = strtolower($array_param[0]);
            $array_param[7] = strtolower($array_param[7]);

            // Upload des images
                // Verif taille
            $upload_path = "../images/";
            foreach($_FILES['imagesP']['size'] as $size)
            {
                if($size > 15000000)
                {
                    header('Location: ./ajout_action_produit.php?msg=3');
                    die();
                }
            }
                //nbr produit
            $last_id = $bdd->query("SELECT count(*) from _produit;")->fetch();
            $last_id = $last_id['count']+1;

                //uplaod file
            
            foreach($_FILES['imagesP']['name'] as $key=>$img)
            {
                $newKey = $key+1;
                $extension = pathinfo($img, PATHINFO_EXTENSION);
                $_FILES['imagesP']['name'][$key] = "${last_id}_${newKey}.${extension}";
                $path_new_file = $upload_path.$_FILES['imagesP']['name'][$key];
                move_uploaded_file($_FILES['imagesP']['tmp_name'][$key], $path_new_file);
            }

            // Insertion BDD
            $insertProd = $bdd -> prepare("INSERT into _import_produit(nomProd, quantite, poids, volume, prix_ttc, descriptif, stock, nom_cat, id_vendeur) Values (?,?,?,?,?,?,?,?,?);");
            $insertProd->execute($array_param) or die(print_r($insertProd->errorInfo(),true));
            $bdd = null;
        }
        catch (PDOException $e)
        {
            print "Erreur ! : " . $e->getMessage() . "<br/>";
            die();
        }
        header('Location: ./ajout_action_produit.php?msg=1');
        die();
    }
    else
    {
        header('Location: ./ajout_action_produit.php?msg=2');
    }
?>