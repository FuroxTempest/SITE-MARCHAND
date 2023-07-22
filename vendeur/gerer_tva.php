<!DOCTYPE html>
<html lang="en">
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
    <title>Gérer la TVA</title>
    <?php
        include("../php/connect_params.php");
        $bdd = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass,
        [PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
        );
        $bdd->exec("SET SCHEMA 'alizonbdd';");
    ?>
</head>

<?php

/*
=============
Gerer les cat
=============
*/

    // Si modification

    if($_POST['modif_cat'] == true)
    {
        $tva = ($_POST['tva']/100)+1;
        $stmtModif = $bdd->prepare("UPDATE _categorie SET nom = ?, tva = ? WHERE id_cat = ?;");
        $stmtModif->execute([$_POST['nom'], $tva, $_POST['id_cat']]);

        echo '
        <div class="notification_ok">
            La catégorie a été modifié avec succès!
        </div>
        ';

        unset($_POST['nom']);
        unset($_POST['tva']);
        unset($_POST['id_cat']);
        unset($_POST['modif_cat']);
    }

    // Si réduction

    else if($_POST['solder_cat'] == true)
    {
        $stmtProduits = $bdd->prepare("SELECT id_produit FROM _produit WHERE id_categorie = ?;");
        $stmtProduits->execute([$_POST['id_cat']]);
        $listeProds = $stmtProduits->fetchAll();

        $stmtNbrReducCat = $bdd->prepare("SELECT count(*) from _reduc_cat;");
        $stmtNbrReducCat->execute();
        $nbrReducCat = $stmtNbrReducCat->fetch();
        
        foreach($listeProds as $prod)
        {
            $stmtReduc_1 = $bdd->prepare("INSERT INTO _promotion(date_debut, date_fin, reduction, id_produit) VALUES (?,?,?,?);");
            $stmtReduc_1->execute([$_POST['date_deb'], $_POST['date_fin'], $_POST['reduc'], $prod['id_produit']]);

            $stmtIdPromo = $bdd->prepare("SELECT id_promotion FROM _promotion WHERE date_debut = ? AND date_fin = ? AND reduction = ? AND id_produit = ?;");
            $stmtIdPromo->execute([$_POST['date_deb'], $_POST['date_fin'], $_POST['reduc'], $prod['id_produit']]);
            $idPromo = $stmtIdPromo->fetch();      

            $stmtReduc_2 = $bdd->prepare("INSERT INTO _reduc_cat(id_reduc_cat, id_promotion, id_cat) VALUES (?,?,?);");
            $stmtReduc_2->execute([($nbrReducCat['count']+1), $idPromo['id_promotion'],$_POST['id_cat']]);
        }

        echo '
        <div class="notification_ok">
            La réduction a été appliqué avec succès!
        </div>
        ';

        unset($_POST['date_deb']);
        unset($_POST['date_fin']);
        unset($_POST['solder_cat']);
        unset($_POST['id_cat']);
        unset($_POST['reduc']);
    }

    // Si suppression

    else if($_POST['supprimer_cat'] == true)
    {
        $stmtDelete_1 = $bdd->prepare("DELETE FROM _produit WHERE id_categorie = ?;");
        $stmtDelete_1->execute([$_POST['id_cat']]);

        $stmtDelete_2 = $bdd->prepare("DELETE FROM _categorie WHERE id_cat = ?;");
        $stmtDelete_2->execute([$_POST['id_cat']]);

        echo '
        <div class="notification_ok">
            La catégorie a été supprimé avec succès!
        </div>
        ';

        unset($_POST['supprimer_cat']);
        unset($_POST['id_cat']);
    }

/*
=============
Gerer les réduc
=============
*/

    // Si modification

    else if($_POST['modif_reduc'] == true)
    {
        $stmtModifId = $bdd->prepare("SELECT id_promotion FROM _reduc_cat WHERE id_reduc_cat = ?;");
        $stmtModifId->execute([$_POST['id_reduc']]);
        $listeModif = $stmtModifId->fetchAll();

        foreach($listeModif as $elt)
        {
            $stmtModif = $bdd->prepare("UPDATE _promotion SET reduction = ?, date_debut = ?, date_fin = ? WHERE id_promotion = ?;");
            $stmtModif->execute([$_POST['reduc'], $_POST['date_deb'], $_POST['date_fin'], $elt['id_promotion']]);
        }

        echo '
        <div class="notification_ok">
            La réduction a été modifié avec succès!
        </div>
        ';
    }

    // Si suppression

    else if($_POST['suppr_reduc'] == true)
    {
        $stmtSupprId = $bdd->prepare("SELECT id_promotion FROM _reduc_cat WHERE id_reduc_cat = ?;");
        $stmtSupprId->execute([$_POST['id_reduc']]);
        $listeSuppr = $stmtSupprId->fetchAll();

        $stmtSuppr = $bdd->prepare("DELETE FROM _reduc_cat WHERE id_reduc_cat = ?;");
        $stmtSuppr->execute([$_POST['id_reduc']]);

        foreach($listeSuppr as $elt)
        {
            $stmtSuppr = $bdd->prepare("DELETE FROM _promotion WHERE id_promotion = ?;");
            $stmtSuppr->execute([$elt['id_promotion']]);
        }

        echo '
        <div class="notification_ok">
            La réduction a été supprimé avec succès!
        </div>
        ';
    }

?>

<body>
    <?php
        include("./head_vendeur.php");
    ?>
    <main>
        <h1><p>Gérer les catégories</p></h1>
        <article>
            <?php

/*
========================
Affichage des catégories
========================
*/

                // Récuperation de la liste des categories
                $stmtCat = $bdd->prepare("SELECT id_cat, nom, nb_art_cat, tva FROM _categorie;");
                $stmtCat->execute();
                $listeCat = $stmtCat->fetchAll();

                // Si pas de categorie, afficher erreur
                if(empty($listeCat))
                {
                    echo '<p class="empty">Aucune catégorie actuellement...<br>Ajoutez-en en <a href="import_action_vendeur.php">important un catalogue de produit</a><br>ou en <a href="ajout_action_produit">ajoutant un produit</a></p>';
                }
                
                // Si il y a des categorie
                else
                {
                    echo '
                    <table>
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Nombre d\'articles</th>
                                <th>TVA</th>
                                <th>Modifier</th>
                                <th>Solder</th>
                                <th>Supprimer</th>
                            </tr>
                        </thead>
                        <tbody>
                    ';

                    foreach($listeCat as $cle=>$cat)
                    {
                        echo '<tr>';
                        foreach($cat as $cle_elt=>$elt)
                        {
                            if($cle_elt == 'nom')
                            {
                                echo '<td>'.ucfirst($elt).'</td>';
                            }
                            else if($cle_elt == 'tva')
                            {
                                echo '<td>'.(($elt-1)*100).' %</td>';
                            }
                            else if($cle_elt != 'id_cat')
                            {
                                echo '<td>'.$elt.'</td>';
                            }
                        }

                        // Modification de la cat

                        echo '
                        <td>
                            <form action="gerer_tva.php" method="POST">
                                <input type="hidden" name="modif_cat" value="true"/>
                                <input type="hidden" name="id_cat" value="'.$cat['id_cat'].'"/>

                                <button type="button" class="bouton_recap_solde" data-bs-toggle="modal" data-bs-target="#modif_cat_'.$cat['id_cat'].'">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                    </svg>
                                </button>     

                                <div class="modal fade" id="modif_cat_'.$cat['id_cat'].'" tabindex="-1" aria-labelledby="videLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="videLabel">Confirmation</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                <div class="row p-3">
                                                    <label for="nom">Nom de la catégorie</label>
                                                    <input type="text" name="nom" value="'.ucfirst($cat['nom']).'" required />
                                                </div>
                                                <div class="row p-3">
                                                    <label for="tva">TVA</label>
                                                    <input type="number" name="tva" value="'.(($cat['tva']-1)*100).'" min="0" max="100" step="1" required />
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                <button type="submit" class="btn btn-primary">Confirmer</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </td>
                        ';

                        // Solder la cat

                        echo '
                        <td>
                            <form action="gerer_tva.php" method="POST">
                                <input type="hidden" name="solder_cat" value="true"/>
                                <input type="hidden" name="id_cat" value="'.$cat['id_cat'].'"/>

                                <button type="button" class="bouton_recap_solde" data-bs-toggle="modal" data-bs-target="#solder_cat_'.$cat['id_cat'].'">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-bar-chart-line-fill" viewBox="0 0 16 16">
                                        <path d="M11 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h1V7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7h1V2z"/>
                                    </svg>
                                </button>     

                                <div class="modal fade" id="solder_cat_'.$cat['id_cat'].'" tabindex="-1" aria-labelledby="videLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="videLabel">Confirmation</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                
                                                <div class="row">
                                                    <div class="col-12 m-3">
                                                        <label for="date_deb">Date de début</label>
                                                        <input type="date" name="date_deb" min="'.date("Y-m-d").'" value="'.date("Y-m-d").'" required id="date_deb_'.$cle.'"/>
                                                    </div>
                                                    <div class="col-12 m-3">
                                                        <label for="date_fin">Date de fin</label>
                                                        <input type="date" name="date_fin" min="'.date("Y-m-d").'" required id="date_fin_'.$cle.'"/>
                                                    </div>
                                                    <div class="col-12 m-3">
                                                        <label for="reduc">Pourcentage de réduction</label>
                                                        <input type="number" min="1" max="100" step="1" name="reduc" placeholder="Ex : 20%" required />
                                                    </div>
                                                </div>
                                                
                                                <p class="text-center m-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
                                                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                                                    </svg>    
                                                <strong>IMPORTANT</strong>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
                                                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                                                    </svg>
                                                    Tous les produits de la catégorie seront alors soldé avec les paramètres suivants.
                                                </p>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                <button type="submit" class="btn btn-primary">Confirmer</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </td>
                        ';

                        // Supprimer la cat

                        echo '
                        <td>
                            <form action="gerer_tva.php" method="POST">
                                <input type="hidden" name="supprimer_cat" value="true"/>
                                <input type="hidden" name="id_cat" value="'.$cat['id_cat'].'"/>

                                <button type="button" class="bouton_recap_solde" data-bs-toggle="modal" data-bs-target="#supprimer_cat_'.$cat['id_cat'].'">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                    </svg>
                                </button>     

                                <div class="modal fade" id="supprimer_cat_'.$cat['id_cat'].'" tabindex="-1" aria-labelledby="videLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="videLabel">Confirmation</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                
                                                <p class="text-center">Êtes-vous sur de vouloir supprimer la catégorie?</p>
                                                
                                                <p class="text-center m-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
                                                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                                                    </svg>    
                                                <strong>IMPORTANT</strong>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
                                                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                                                    </svg>
                                                    Tous les produits de la catégorie seront donc supprimés <strong>défnitivement</strong>
                                                </p>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                <button type="submit" class="btn btn-primary">Confirmer</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </td>
                        ';

                        echo '</tr>';
                    }

                    echo '
                        </tbody>
                    </table>
                    ';
                }

/*
===================================
Affichage des périodes de réduction
===================================
*/

                $stmtPromo = $bdd->prepare("SELECT DISTINCT id_reduc_cat FROM _reduc_cat;");
                $stmtPromo->execute();
                $listeIdReducCat = $stmtPromo->fetchAll();

                if(!empty($listeIdReducCat)) // S'il y a des périodes de réduction
                {
                    foreach($listeIdReducCat as $id_reduc)
                    {
                        $stmtIdPromo = $bdd->prepare("SELECT id_promotion FROM _reduc_cat WHERE id_reduc_cat = ?;");
                        $stmtIdPromo->execute([$id_reduc['id_reduc_cat']]);
                        $listeIdPromo = $stmtIdPromo->fetchAll();

                        foreach($listeIdPromo as $id_prom)
                        {
                            $listeReducCat[$id_reduc['id_reduc_cat']][] = $id_prom['id_promotion']; 
                        }
                    }

                    echo '<hr/>';

                    echo '
                    <h2>Période de réduction sur les catégories</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Date de début</th>
                                <th>Date de fin</th>
                                <th>Réduction</th>
                                <th>Modifier</th>
                                <th>Supprimer</th>
                            </tr>
                        </thead>
                        <tbody>
                        ';

                    foreach($listeReducCat as $id_reduc=>$liste_reduc)
                    {
                        $stmtNomCat = $bdd->prepare("SELECT nom from _categorie INNER JOIN _reduc_cat ON _categorie.id_cat = _reduc_cat.id_cat WHERE _reduc_cat.id_reduc_cat = ?;");
                        $stmtNomCat->execute([$id_reduc]);
                        $nom_cat = $stmtNomCat->fetch();

                        $stmtInfoReduc = $bdd->prepare("SELECT date_debut, date_fin, reduction FROM _promotion WHERE id_promotion = ?;");
                        $stmtInfoReduc->execute([$liste_reduc[0]]);
                        $info_cat = $stmtInfoReduc->fetchAll();

                        echo '<tr>';
                            echo '<td>'.ucfirst($nom_cat['nom']).'</td>';

                            $date = new DateTime($info_cat[0]['date_debut']);
                            echo '<td>'.date_format($date,"d.m.Y").'</td>';

                            $date = new DateTime($info_cat[0]['date_fin']);
                            echo '<td>'.date_format($date,"d.m.Y").'</td>';

                            echo '<td>'.$info_cat[0]['reduction'].' %</td>';

                        // Modification d'une période de reduction

                            echo '
                            <td class="bouton-solde">
                                <form action="gerer_tva.php" method="POST">
                                    <input type="hidden" name="modif_reduc" value="true"/>
                                    <input type="hidden" name="id_reduc" value="'.$id_reduc.'"/>

                                    <button type="button" class="bouton_recap_solde" data-bs-toggle="modal" data-bs-target="#modif_reduc_'.$id_reduc.'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                        </svg>
                                    </button>     

                                    <div class="modal fade" id="modif_reduc_'.$id_reduc.'" tabindex="-1" aria-labelledby="videLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="videLabel">Confirmation</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    
                                                <div class="row">
                                                    <div class="col-12 p-3 text-start">
                                                        <label for="date_deb">Date de début</label>
                                                        <input type="date" name="date_deb" min="'.date("Y-m-d").'" value="'.$info_cat[0]['date_debut'].'" required id="date_deb_reduc_'.$id_reduc.'"/>
                                                    </div>
                                                    <div class="col-12 p-3 text-start">
                                                        <label for="date_fin">Date de fin</label>
                                                        <input type="date" name="date_fin" min="'.date("Y-m-d").'" value="'.$info_cat[0]['date_fin'].'" required id="date_fin_reduc_'.$id_reduc.'"/>
                                                    </div>
                                                    <div class="col-12 p-3 text-start">
                                                        <label for="reduc">Pourcentage de réduction</label>
                                                        <input type="number" min="1" max="100" step="1" name="reduc" value="'.$info_cat[0]['reduction'].'" placeholder="Ex : 20%" required />
                                                    </div>
                                                </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" class="btn btn-primary">Confirmer</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </td>
                            ';

                        // Suppression d'une période de rédution

                            echo '
                            <td class="bouton-solde">
                                <form action="gerer_tva.php" method="POST">
                                    <input type="hidden" name="suppr_reduc" value="true"/>
                                    <input type="hidden" name="id_reduc" value="'.$id_reduc.'"/>

                                    <button type="button" class="bouton_recap_solde" data-bs-toggle="modal" data-bs-target="#suppr_reduc_'.$id_reduc.'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                            <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                        </svg>
                                    </button>     

                                    <div class="modal fade" id="suppr_reduc_'.$id_reduc.'" tabindex="-1" aria-labelledby="videLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="videLabel">Confirmation</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Êtes-vous certain de vouloir supprimer cette réduction?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" class="btn btn-primary">Confirmer</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </td>
                            ';

                        
                        echo '</tr>';
                        echo '<tr>';
                        
                        echo '</tr>';
                    }

                    echo '
                        </tbody>
                    </table>
                    ';
                }

            ?>
        </article>
    </main>
</body>

<script>

var div = document.createElement("div");
div.classList.add("notification_non");
div.appendChild(document.createTextNode("La date de fin doit être après la date de début!"));

var ajout = document.querySelector("main");

<?php 

    foreach($listeCat as $cle=>$line)
    {
        echo '
        var date_deb_'.$cle.' = document.getElementById("date_deb_'.$cle.'");
        var date_fin_'.$cle.' = document.getElementById("date_fin_'.$cle.'");

        date_deb_'.$cle.'.addEventListener("blur",ajoutMinDateFin_'.$cle.');
        date_deb_'.$cle.'.addEventListener("blur",veriDate_'.$cle.');
        date_fin_'.$cle.'.addEventListener("blur",veriDate_'.$cle.');

        function veriDate_'.$cle.'()
        {
            if(date_deb_'.$cle.'.value > date_fin_'.$cle.'.value && date_fin_'.$cle.'.value != "")
            {
                // cas erreur si date_fin_'.$cle.' < date_deb_'.$cle.'

                div.remove();
                document.body.insertBefore(div, ajout);

                date_fin_'.$cle.'.value = "";
            }
            if(date_deb_'.$cle.'.value < date_du_jour.value)
            {
                // cas erreur si la date de début < date du jour
                
                div.remove();
                document.body.insertBefore(div, ajout);

                date_deb_'.$cle.'.value = date_du_jour.value;
                ajoutMinDateFin_'.$cle.'();
            }
        }

        function ajoutMinDateFin_'.$cle.'() // ajout de la date_deb_'.$cle.' en minimum pour date_fin_'.$cle.' 
        {
            date_fin_'.$cle.'.min = date_deb_'.$cle.'.value;
        }
        ';
    }

foreach($listeReducCat as $id_reduc=>$liste_reduc)
{
    echo '
    var date_deb_reduc_'.$id_reduc.' = document.getElementById("date_deb_reduc_'.$id_reduc.'");
    var date_fin_reduc_'.$id_reduc.' = document.getElementById("date_fin_reduc_'.$id_reduc.'");

    date_deb_reduc_'.$id_reduc.'.addEventListener("blur",ajoutMinDateFin_'.$id_reduc.');
    date_deb_reduc_'.$id_reduc.'.addEventListener("blur",veriDate_'.$id_reduc.');
    date_fin_reduc_'.$id_reduc.'.addEventListener("blur",veriDate_'.$id_reduc.');

    function veriDate_'.$id_reduc.'()
    {
        if(date_deb_reduc_'.$id_reduc.'.value > date_fin_reduc_'.$id_reduc.'.value && date_fin_reduc_'.$id_reduc.'.value != "")
        {
            // cas erreur si date_fin_reduc_'.$id_reduc.' < date_deb_reduc_'.$id_reduc.'

            div.remove();
            document.body.insertBefore(div, ajout);

            date_fin_reduc_'.$id_reduc.'.value = "";
        }
        if(date_deb_reduc_'.$id_reduc.'.value < date_du_jour.value)
        {
            // cas erreur si la date de début < date du jour

            div.remove();
            document.body.insertBefore(div, ajout);

            date_deb_reduc_'.$id_reduc.'.value = date_du_jour.value;
            ajoutMinDateFin_'.$id_reduc.'();
        }
    }

    function ajoutMinDateFin_'.$id_reduc.'() // ajout de la date_deb_reduc_'.$id_reduc.' en minimum pour date_fin_reduc_'.$id_reduc.' 
    {
        date_fin_reduc_'.$id_reduc.'.min = date_deb_reduc_'.$id_reduc.'.value;
    }
    ';
}

  
?>

</script>

</html>