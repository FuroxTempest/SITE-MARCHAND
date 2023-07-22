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

        <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
        <link rel="icon" href="../images/favicon.ico" />
        <title>Vendeur | Liste commande</title>
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
            <h1>
                <p>
                    Liste des commandes
                </p>
            </h1>
            <article>
                <!-- Affichage des commandes -->
                <div class="row row-cols-1 row-cols-md-3 g-4 range">
                    <?php
                        $nb_com=0;
                        //On récupère chaque commande de la bdd
                        $commandes= array();
                        foreach($bdd->query("SELECT nom,prenom,statut_commande,telephone,id_commande,rue,code,ville FROM alizonbdd._commande natural join alizonbdd._client ORDER BY _commande.id_commande ;") as $row)
                        {
                            array_push($commandes,$row);
                            $nb_com++;
                        }
                    ?>
                    <!--Le tableau d'affichage des commandes-->
                    <div class="SELECT date_commande,statut_commande from alizonbdd._commande WHERE id_commande=?");table-responsive" style="width:100%;">
                        <table class="table table-striped table-hover" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>
                                        ID
                                    </th>
                                    <th>
                                        adresse de livraison
                                    </th>
                                    <th>
                                        numéro de télephone
                                    </th>
                                    <th>
                                        Nom
                                    </th>
                                    <th>
                                        Status
                                    </th>
                                    <th>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    //On affiche ici les commandes
                                    foreach($commandes as $key)
                                    {
                                        $same=false;
                                        $id_vendeur=$_SESSION['id_vendeur'];
                                        $id=$key['id_commande'];

                                        $req_panier=$bdd->prepare("SELECT id_produit FROM  alizonbdd._panier Where id_commande = ?");
                                        $req_panier->execute([$id]);
                                        $id_prod=$req_panier->fetchAll();

                                        foreach($id_prod as $cle){
                                            $id_produit=$cle['id_produit'];
                                            $req_rech=$bdd->prepare("SELECT id_vendeur from alizonbdd._produit WHERE id_produit= ?");
                                            $req_rech->execute([$id_produit]);
                                            $resu=$req_rech->fetch();
                                            if($resu['id_vendeur'] ==  $id_vendeur){
                                                $same=true;
                                                break;
                                            }
                                        }

                                        if($same==true){
                                            echo"<tr>";

                                    
                                                echo"<td> ".$key['id_commande']." </td>";
                                                echo"<td style=\"white-space: nowrap;\"> ".$key['rue']." ".$key['code'].", ".$key['ville']." </td>";
                                                echo"<td> ".$key['telephone']." </td>";
                                                echo"<td> ".$key['prenom']." ".$key['nom']." </td>";

                                                if($key['statut_commande']=='en cours'){
                                                    echo "<td id=".$id." style=\"color: red;\">". $key['statut_commande']." </td>";
                                                }else if($key['statut_commande']=='préparation'){
                                                    echo "<td id=".$id." style=\"color: brown;\">". $key['statut_commande']." </td>";
                                                }else if($key['statut_commande']=='livraison'){
                                                    echo "<td id=".$id." style=\"color: yellow;\">". $key['statut_commande']." </td>";
                                                }else if($key['statut_commande']=='colis livrée'){
                                                    echo "<td id=".$id." style=\"color: blue;\">". $key['statut_commande']." </td>";
                                                }
                                                //Le bouton d'ouverture du modal de détails
                                                echo '<td> 
                                                    <a data-bs-toggle="modal" data-bs-target="#DetailsCommande'.$key['id_commande'].'"><u>Détails</u></a> 
                                                </td>';

                                            echo"</tr>";
                                        }
                                    }
                                    ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php
                    //si il n'y a aucune commande, on affiche le message correspondant
                    if(sizeof($commandes)==0){
                        echo"<p style=\"margin:10%; text-align:center;\">Il n'y a actuellement aucune commande</p>";
                    }
                ?>
            </article>
            <?php
                //On créer pour chaque commande son modal 
                foreach($commandes as $key)
                {
                    //l'id du modal doit porter l'id de la commande dans son nom pour éviter un problème de duplication
                    echo'<div class="modal fade" id="DetailsCommande'.$key['id_commande'].'" tabindex="-1" aria-labelledby="Details_commande" aria-hidden="true">'.PHP_EOL;
            ?>
                    <div class="modal-dialog">
                        <div class="modal-content" style="width:30em; font-size:1.5em;">
                            <div class="modal-header">
                                <?php
                                    echo'<h5 class="modal-title" id="Details_commande">Détails commande n°'.$key['id_commande'].'</h5>'.PHP_EOL;
                                ?>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body ">
                                <?php
                                    //On affiche les informations importantes
                                    echo'   <p>Nom du client : '.$key['nom'] . $key['prenom'].'</p>'.PHP_EOL;
                                    echo "<p>Adresse : ".$key['rue']." ".$key['code'].", ".$key['ville']."</p>".PHP_EOL;
                                    $prix=0;
                                    //On récupère les produit de la commande
                                    foreach($bdd->query("SELECT nomprod,nb_article,prix_total FROM _commande natural join _panier natural join _produit WHERE id_commande = $key[id_commande];") as $panier)
                                    {
                                        $prix+=$panier['prix_total'];
                                    }
                                    //Pour calculer le prix
                                    echo "<p>Prix total : ".$prix." €</p>".PHP_EOL;
                                    ?>
                                    <!--Le tableau contenant les produits-->
                                    <div class="table-responsive" style="width:100%; height: 10em;">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        produit
                                                    </th>
                                                    <th>
                                                        quantité
                                                    </th>
                                                    <th>
                                                        prix
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                //On récupère encore les produits
                                                foreach($bdd->query("SELECT nomprod,nb_article,prix_total FROM _commande natural join _panier natural join _produit WHERE id_commande = $key[id_commande];") as $panier)
                                                {
                                                    //On en affiche les détails
                                                    echo "<tr>
                                                        <td>
                                                            ".$panier['nomprod']."
                                                        </td>".PHP_EOL;
                                                    echo "<td>
                                                            ".$panier['nb_article']."
                                                        </td>".PHP_EOL;
                                                    echo "<td>
                                                            ".$panier['prix_total']." €
                                                        </td>
                                                    </tr>".PHP_EOL;
                                                }?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!--bouton de fermeture du modal-->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Sortir</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
                }
            ?>
        </main>
    </body>

    <script>
        //Script java pour faire évoluer le statut de la commande en direct
        $(document).ready(function() {
            var nb_com = <?php echo json_encode($nb_com); ?>; // on récupère le nombre de commande
            var id = <?php echo json_encode($id); ?>; // on récupère l'id de la commande
            var com_passé=0; 
            id=id-nb_com;
            console.log(id);
            console.log(nb_com);
            if(nb_com!=0){
                //Rafraîchissement de la partie spécifique toutes les 10 secondes
                setInterval(function() {
                //  script utilisé pour se déplacer dans les commandes
                    if(com_passé>=nb_com){
                        com_passé=1;
                        id=id-nb_com+1;
                    }else{
                        com_passé=com_passé+1;
                        id=id+1;
                    }
                    $.ajax({
                        // on envoie a la page ajax_vendeur.php l'id de la commande
                        async: true,
                        type: 'POST',
                        url: './ajax_vendeur.php',
                        data: {'id' : id },
                        success: function(data) { // en cas de succès
                            console.log(id);
                            document.getElementById(id).innerHTML = data; // on ajoute la valeur retourné dans le code avec l'id indiqué
                        }
                    });
                }, 700);
            }else{
                console.log("Pas de commande à afficher");
            }
        });
    </script>
</html>
