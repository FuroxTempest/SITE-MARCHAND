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
            if($_POST['id_produit'] != "")
            {
                $id_produit = $_POST['id_produit'];
            }
            else if($_GET['id_produit'] != "")
            {
                $id_produit = $_GET['id_produit'];
            }
            $req = $bdd->query('SELECT id_produit, nomprod, prix_ttc, descriptif, quantite, poids, volume, stock, id_categorie FROM _produit WHERE id_produit = '.$id_produit.';');
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

<?php

    if($_POST['suppr'] == true) // Suppresion d'une réduction
    {
        $stmt = $bdd->prepare("DELETE FROM _reduc_cat WHERE id_promotion = ?;"); // On supprimer la réduction de la réduction_categorie
        $stmt->execute([$_POST['id_promotion']]);

        $stmtSuppr = $bdd->prepare('DELETE FROM _promotion WHERE id_promotion=?;');
        $stmtSuppr->execute([$_POST['id_promotion']]);
        unset($_POST['id_promotion']);
    }
    else if($_POST['modif'] == true) // Modification d'une réduction
    {
        $new_date_deb = $_POST['date_deb'];
        $new_date_fin = $_POST['date_fin'];
        $new_reduc = $_POST['reduc'];
        $id_promotion = $_POST['id_promotion'];

        $stmt = $bdd->prepare("DELETE FROM _reduc_cat WHERE id_promotion = ?;"); // On supprimer la réduction de la réduction_categorie
        $stmt->execute([$_POST['id_promotion']]);

        $stmtModif = $bdd->prepare('UPDATE _promotion SET date_debut = ?, date_fin = ?, reduction = ? WHERE id_promotion = ?;');
        $stmtModif->execute([$new_date_deb, $new_date_fin, $new_reduc, $id_promotion]);

        unset($_POST['date_deb']);
        unset($_POST['date_fin']);
        unset($_POST['id_promotion']);
    }
    else if(isset($_POST['date_du_jour']))
    {
        if($_POST['date_du_jour'] != "") // si formulaire rempli, insertion des données dans la base
        {
            $tab_values = array($_POST['id_produit'], $_POST['reduc'], $_POST['date_deb'], $_POST['date_fin']);
            $stmt = $bdd->prepare("INSERT INTO _promotion(id_produit, reduction, date_debut, date_fin) VALUES (?,?,?,?);");
            $stmt->execute($tab_values);
            echo '<div class="notification_ok">Réduction bien prise en compte!</div>';
            header('Location: ./solder_produit.php?id_produit='.$produit['id_produit']);
        }
    }

?>

<body>
    <?php include('head_vendeur.php'); ?>
    <?php 
    if($_POST['suppr'] == true)
    {
        echo '<div class="notification_ok">Réduction supprimée avec succès!</div>"';
        unset($_POST['suppr']);
    }
    else if($_POST['modif'] == true)
    {
        echo '<div class="notification_ok">Réduction modifiée avec succès!</div>"';
        unset($_POST['reduc']);
    }
    ?>

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

                <!-- Liste des promotions déjà saisies -->
                <?php
                    // Affichage de la liste des réductions
                    $stmtProm = $bdd->prepare("SELECT id_promotion, date_debut, date_fin, reduction FROM _promotion WHERE id_produit=? ORDER BY(date_debut);");
                    $stmtProm->execute([$produit['id_produit']]);
                    $listeProm = $stmtProm->fetchAll();

                    if(!empty($listeProm)) // Vérification de si la liste est vide
                    {
                        echo '
                        <h2>Récapitulatif des périodes de réductions</h2>
                        <table>
                            <thead>
                                <tr>
                                    <th>Date de début</th>
                                    <th>Date de fin</th>
                                    <th style="width: 8em;">Réduction</th>
                                    <th class="bouton-solde">Modifier</th>
                                    <th class="bouton-solde">Supprimer</th>
                                </tr>
                            </thead>
                            <tbody>
                        ';
                        foreach($listeProm as $cle=>$line)
                        {
                            echo '<tr>';
                            foreach($line as $key=>$elt)
                            {
                                if($key == 'reduction')
                                {
                                    echo '<td>'.$elt.' %</td>';
                                }
                                else if($key == 'date_debut' || $key == 'date_fin')
                                {
                                    $date = new DateTime($elt);
                                    echo '<td>'.date_format($date,"d.m.Y").'</td>';
                                }
                            }
                            // Bouton de modification
                            echo '
                            <td class="bouton-solde">
                                <form action="solder_produit.php" method="POST">
                                    <input type="hidden" name="modif" value="true"/>
                                    <input type="hidden" name="id_promotion" value="'.$line['id_promotion'].'"/>
                                    <input type="hidden" name="id_produit" value="'.$produit['id_produit'].'"/>

                                    <button type="button" class="bouton_recap_solde" data-bs-toggle="modal" data-bs-target="#modif_reduc_'.$line['id_promotion'].'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                        </svg>
                                    </button>     

                                    <div class="modal fade" id="modif_reduc_'.$line['id_promotion'].'" tabindex="-1" aria-labelledby="videLabel" aria-hidden="true">
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
                                                        <input type="date" name="date_deb" min="'.date("Y-m-d").'" value="'.$line['date_debut'].'" required id="date_deb_'.$cle.'"/>
                                                    </div>
                                                    <div class="col-12 p-3 text-start">
                                                        <label for="date_fin">Date de fin</label>
                                                        <input type="date" name="date_fin" min="'.date("Y-m-d").'" value="'.$line['date_fin'].'" required id="date_fin_'.$cle.'"/>
                                                    </div>
                                                    <div class="col-12 p-3 text-start">
                                                        <label for="reduc">Pourcentage de réduction</label>
                                                        <input type="number" min="1" max="100" step="1" name="reduc" value="'.$line['reduction'].'" placeholder="Ex : 20%" required />
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
                            // Bouton de suppression
                            echo '
                            <td class="bouton-solde">
                                <form action="solder_produit.php" method="POST">
                                    <input type="hidden" name="suppr" value="true"/>
                                    <input type="hidden" name="id_promotion" value="'.$line['id_promotion'].'"/>
                                    <input type="hidden" name="id_produit" value="'.$produit['id_produit'].'"/>

                                    <button type="button" class="bouton_recap_solde" data-bs-toggle="modal" data-bs-target="#suppr_reduc_'.$line['id_promotion'].'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                            <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                        </svg>
                                    </button>     

                                    <div class="modal fade" id="suppr_reduc_'.$line['id_promotion'].'" tabindex="-1" aria-labelledby="videLabel" aria-hidden="true">
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
                            </td>';
                            echo '</tr>';
                        }
                        echo '
                            </tbody>
                        </table>
                        <hr/>
                        ';
                    }
                ?>

                <!-- Formulaire d'ajout d'une promotion -->
                <h2>Ajouter une promotion</h2>
                <form action="solder_produit.php" method="POST">
                    <?php
                        echo '
                        <input type="hidden" name="date_du_jour" value="'.date("Y-m-d").'" id="date_du_jour"/>
                        <input type="hidden" name="id_produit" value="'.$produit['id_produit'].'" />
                        
                        <div class="row">
                            <div class="col">
                                <label for="date_deb">Date de début</label>
                                <input type="date" name="date_deb" min="'.date("Y-m-d").'" value="'.date("Y-m-d").'" required id="date_deb"/>
                            </div>
                            <div class="col">
                                <label for="date_fin">Date de fin</label>
                                <input type="date" name="date_fin" min="'.date("Y-m-d").'" required id="date_fin"/>
                            </div>
                            <div class="col">
                                <label for="reduc">Pourcentage de réduction</label>
                                <input type="number" min="1" max="100" step="1" name="reduc" placeholder="Ex : 20%" required />
                            </div>
                        </div>
                        ';
                    ?>
                    <input type="submit" name='Valider' value="Valider"/>
                </form>
            </article>
        </main>
    </body>
    <script>

        var div = document.createElement("div");
        div.classList.add("notification_non");
        div.appendChild(document.createTextNode("La date de fin doit être après la date de début!"));

        var date_deb = document.getElementById("date_deb");
        var date_fin = document.getElementById("date_fin");
        var date_du_jour = document.getElementById("date_du_jour");

        date_deb.addEventListener("blur",ajoutMinDateFin);
        date_deb.addEventListener("blur",verifDate);
        date_fin.addEventListener("blur",verifDate);

        function verifDate()
        {
            if(date_deb.value > date_fin.value && date_fin.value != "")
            {
                // cas d'erreur si date_fin < date_deb
                
                div.remove();
                date_deb.parentNode.insertBefore(div, date_deb);

                date_fin.value = "";
            }
            if(date_deb.value < date_du_jour.value)
            {
                // cas d'erreur si la date de début < date du jour
                
                div.remove();
                date_deb.parentNode.insertBefore(div, date_deb);

                date_deb.value = date_du_jour.value;
                ajoutMinDateFin();
            }
        }

        function ajoutMinDateFin() // ajout de la date_deb en minimum pour date_fin 
        {
            date_fin.min = date_deb.value;
        }

        <?php 
        foreach($listeProm as $cle=>$line) // MODIFIER SOLDE
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
                    date_deb_'.$cle.'.parentNode.insertBefore(div, date_deb_'.$cle.');

                    date_fin_'.$cle.'.value = "";
                }
                if(date_deb_'.$cle.'.value < date_du_jour.value)
                {
                    // cas erreur si la date de début < date du jour
                    
                    div.remove();
                    date_deb_'.$cle.'.parentNode.insertBefore(div, date_deb_'.$cle.');

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
        ?>

    </script>
</html>