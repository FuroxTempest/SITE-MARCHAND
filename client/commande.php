<?php
// connexion à la bdd
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

    // on sélectionne l'id du client connecté
    $client = array();
    foreach($bdd->query("select id_client from alizonbdd._client where _client.email='".$_SESSION['adresseEmail']."';") as $row) {
        $client[] = $row; // on les ajoute à un tableau
    }
    $commandebis = []; 
    // on selectionne les commandes du cliet ou le statut vaut en cours
    foreach($bdd->query(" select * from alizonbdd._commande where _commande.id_client=".$client[0]['id_client']." and _commande.statut_commande != 'en cours' order by _commande.id_commande desc; ") as $row) {
        $commandebis[] = $row;
    }
    $nbcommande = sizeof($commandebis);

?>

<!DOCTYPE html>

<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commandes | ALIZON </title>    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../images/favicon.ico" />
    <script>
        function toggleClassVisibility(classe) {
            var elements = document.getElementsByClassName(classe);
            for (var i = 0; i < elements.length; i++) {
                if(elements[i].style.display === "none") {
                    elements[i].style.display = "table-row";
                } else {
                    elements[i].style.display = "none";
                }
            }
        }
    </script>

</head>
<body>
    <?php
    include("header.php");
    ?>

<div id="petit_menu">   
        <a href="profil.php">Mon compte</a>
        <a href="commande.php"><b>Mes commandes</b></a>
        <a href="deco.php">Déconnexion</a>
</div>

<main id="vos_commandes">

    <h1>Vos commandes</h1>
    <section>
    <article>
        <table class="tftable" >
            
        <?php 
        //si pas de commande
        if ($nbcommande==0) {
            echo "<h2 class=\"pas_de_commande\"> Pas de commande en cours. </h2>";
        // sinon
        }else {   
            echo "<tr><th>Référence de la commande</th><th>Date</th><th>Prix total</th><th>Etat</th><th></th></tr>";     
        
            // on selctionne les commandes du client
            for ($i=0; $i < $nbcommande ; $i++) { 
                $commande = [];
                foreach($bdd->query("select _categorie.tva,_commande.statut_commande ,_produit.nomprod,_produit.prix_ttc, _produit.id_produit,_panier.id_commande,_panier.id_produit,_panier.nb_article,_panier.prix_total from alizonbdd._panier inner join alizonbdd._commande on _commande.id_commande = _panier.id_commande inner join alizonbdd._produit on _produit.id_produit = _panier.id_produit inner join alizonbdd._categorie on _produit.id_categorie = _categorie.id_cat  where _panier.id_commande=".$commandebis[$i]['id_commande'].";") as $row) {
                    $commande[] = $row;
                    $nb_com++;
                }
                ?>

            
                <?php
                    //on affiche tous les élements de la commandee
                    $date = new DateTime($commandebis[$i]['date_commande']);    
                    $prix_ttc = 0;
                    
                    foreach ($commande as $prx) {
                        $prix_ttc += $prx['prix_total'];

                    }

                    $id=$commandebis[$i]['id_commande'];
                    echo "<tr><td>".$commandebis[$i]['id_commande']."</td><td>".date_format($date,"d-m-Y")."</td><td>". number_format($prix_ttc, 2, ",", " ")." </td><td id=".$commandebis[$i]['id_commande'].">".$commandebis[$i]['statut_commande']."</td><td class=\"bton\"><button onclick=\"toggleClassVisibility('maClasse$i');\"\">Plus de details</button></td></tr>";
                    

                        $total = 0;
                        $prix_total_ht = 0;
                        
                        echo "<tr class=\"maClasse$i info_cache\" style=\"display:none; \"  ><th style=\" border-right: none; text-align:left;\" colspan=\"2\">Produit</th><th style=\"border-right: none; border-left: none;\">Quantité</th><th style=\"border-right: none; border-left: none;\">Prix Unitaire</th><th style=\" border-left: none;\" >Prix total</th></tr>";
                        foreach ($commande as $produit) {
                            $total += $produit['prix_total'];
                            $prix_total_ht += ($produit['prix_total'])*(2-$produit['tva']);
                            echo "<tr class=\"maClasse$i info_cache\" style=\"display:none;\" ><td colspan=\"2\" style=\" border-right: none; text-align:left;\">$produit[nomprod]</td><td style=\"border-right: none; border-left: none;\">$produit[nb_article]</td><td style=\" border-right: none; border-left: none;\">".number_format($produit['prix_ttc'], 2, ",", " ")."</td><td style=\"border-left: none;\">".number_format($produit['prix_total'], 2, ",", " ")." </td></tr>";
                        }

                        echo "<tr class=\"maClasse$i info_cache\" style=\"display:none;\" ><td style=\"border-right: none; \" colspan=\"3\"></td><td style=\"border-left: none;\">Total<span>ht</span></td><td>".number_format($prix_total_ht, 2, ",", " ")."</td></tr>";

                        echo "<tr class=\"maClasse$i info_cache\" style=\"display:none;\" ><td style=\"border-right: none; \" colspan=\"3\"></td><td style=\"border-left: none;\">Total<span>ttc</span></td><td>".number_format($total, 2, ",", " ")."</td></tr>";
                    
                ?>
                    
                
        <?php

            } 
        } 
        ?>
        </table>
        </article>


    </section>


</main> 

    <?php
        include("footer.php");
    ?>
</body>
<script>
        //Script java pour faire évoluer le statut de la commande en direct
        $(document).ready(function() {
            var nb_com = <?php echo json_encode($nb_com); ?>;// on récupère le nombre de commande
            var id = <?php echo json_encode($id); ?>; // on récupère l'id de la commande
            var com_passé=0;
            id=id+nb_com;
            console.log(id);
            console.log(nb_com);
            if(nb_com!=0){
                //Rafraîchissement de la partie spécifique toutes les 10 secondes
                setInterval(function() {
                //  script utilisé pour se déplacer dans les commandes
                    if(com_passé>=nb_com){
                        com_passé=1;
                        id=id+nb_com-1;
                    }else{
                        com_passé=com_passé+1;
                        id=id-1;
                    }
                    $.ajax({
                         // on envoie a la page ajax_vendeur.php l'id de la commande
                        async: true,
                        type: 'POST',
                        url: './ajax_client.php',
                        data: {'id' : id },
                        success: function(data) { // en cas de succès
                            console.log(id);
                            document.getElementById(id).innerHTML = data; // on ajoute la valeur retourné dans le code avec l'id indiqué
                        }
                    });
                }, 1000);
            }else{
                console.log("Pas de commande à afficher");
            }
        });
    </script>
</html>
