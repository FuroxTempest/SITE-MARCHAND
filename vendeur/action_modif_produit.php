<?php
    include('../php/connect_params.php');
    try
    {
        $bdd = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass,
        [PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
        );
        $bdd->exec("set schema 'alizonbdd';");
        // selection produit
        $req = $bdd->query('SELECT id_produit, nomprod, prix_ttc, descriptif, quantite, poids, volume, stock, id_categorie FROM _produit WHERE id_produit = '.$_POST['id_produit'].';');
        $req->execute();
        $produit = $req->fetch();
    }
    catch (PDOException $e)
    {
        print "Erreur ! : " . $e->getMessage() . "<br/>";
        die();
    }

    foreach($_POST as $key=>$post_produit)
    {
        if($post_produit != $produit[$key] && $key != 'id_categorie' && $key != 'nomNewCat' ) // Update si changement pour tout (sauf categorie)
        {
            if( ($key == 'poids' && $post_produit == '') || ($key == 'volume' && $post_produit == '') ) // Verif si poids ou volume est vide
            {
                $bdd->exec('UPDATE _produit SET '.$key.'= NULL WHERE id_produit = '.$produit['id_produit'].';');
            }
            else
            {
                $bdd->exec('UPDATE _produit SET '.$key.'=\''.$post_produit.'\' WHERE id_produit = '.$produit['id_produit'].';');
            }
        }
        else if($key == 'id_categorie')
        {
            if($post_produit == 'autre') // Update categorie si nouvelle categorie
            {
                $req = $bdd->query('SELECT nom from _categorie where nom = \''.$_POST['nomNewCat'].'\';');
                $req->execute();
                $nom = $req->fetch();
                $nom = $nom['nom'];
                if($nom == '') // Vraiment inexistante? ajout si oui
                {
                    $bdd->exec('INSERT INTO _import_categorie(nom) VALUES (\''.$_POST['nomNewCat'].'\');');
                    $nom = $_POST['nomNewCat'];
                }
                $req = $bdd->query("SELECT id_cat, nom from _categorie WHERE nom = '$nom';"); // recup id_cat
                $req->execute();
                $cat = $req->fetch();
                $bdd->exec('UPDATE _produit SET id_categorie='.$cat['id_cat'].' WHERE id_produit = '.$produit['id_produit'].';'); // update cat

            }
            else // si categorie existante
            {
                $req = $bdd->query("SELECT id_cat, nom from _categorie WHERE nom = '$post_produit';"); // recup id_cat
                $req->execute();
                $cat = $req->fetch();
                $bdd->exec('UPDATE _produit SET id_categorie='.$cat['id_cat'].' WHERE id_produit = '.$produit['id_produit'].';'); // update cat
            }
        }
    }

    header('Location: ./detail_produit_vendeur.php?id_produit='.$_POST['id_produit'].'&modif=true');
    die();
?>