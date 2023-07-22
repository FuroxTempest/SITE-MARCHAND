<?php


// Test redution :
$reduction = false;
$reductions = array();
$date_ajd = new DateTime();



foreach($bdd->query("select _produit.id_produit,_promotion.reduction,_promotion.date_debut,_promotion.date_fin from alizonbdd._produit inner join alizonbdd._promotion on _promotion.id_produit=_produit.id_produit where  _produit.id_produit = ".$key['id_produit']." ;") as $reduc) {
    $date_debut = new DateTime($reduc['date_debut']);
    $date_fin = new DateTime($reduc['date_fin']);

    if (($date_ajd >= $date_debut) && ($date_ajd <= $date_fin)) {
        $reduction = true;
        if($reductions['reduction'] < $reduc['reduction']){
            $reductions = $reduc;
        }else {
            $reductions = $reductions;
        }
        
        
    }

}


?>


<div class="carte_panier">
    <div class="img">

        <?php 
            //on récupère la 1ère image du produit
            foreach(glob("../images/".$key['id_produit']."_*")as $image){
            }
            echo "<img class=\"carte_panier_img\" src=\"".$image."\" width=\"270\" alt=\"image produit\"> "; 
        ?>

            
    </div>
    <div class="main">

        <div class="nom_prix">
            <div >
                <!--Le bouton permettant d'aller sur le détail du produit-->
                <form action="detail_produit.php" method="get" class="">
                    <?php
                        echo "<input type=\"hidden\" name=\"idprod\" value=\"".$key['id_produit']."\">";
                    ?>
                    <?php
                    echo "<button class=\"nomprod\" type=\"submit\" >".ucfirst($key['nom'])."</button>";
                    ?>
                </form>

            </div>

            <?php 

            $pourcentage = (100-$reductions['reduction'])*0.01;
            if ($reduction == false) {
                echo "<h5> <span> </span> ".number_format($key['prix_art']*$key['quantite'], 2, ",", " ") ."€ <span class=\"ttc\">ttc</span></h5> "; 
            }else {
                echo "<h5> <span class=\"prix_barre\"> ".number_format($key['prix_art']*$key['quantite'], 2, ",", " ")."€  </span> ".number_format(($key['prix_art']*$key['quantite'])*$pourcentage, 2, ",", " ") ."€ <span class=\"ttc\">ttc</span></h5> "; 


            }



            ?>
        </div>

        <div class="nb_supr">
            
            <form action="panier.php" method="post" class="ps-2"  onchange="this.submit()">
                <!--zone et bouton de sélection de la quantité-->
                <input type="number" id="typeNumber" class="form-control" name="qtt_prod" value="<?php echo $key['quantite']; ?>" pattern="[0-9]+">
                <?php
                    echo "<input type=\"hidden\" name=\"idprod\" value=\"".$key['id_produit']."\">";
                ?>
            </form>

            <!--Le bouton de suppression de ce produit du panier-->
            <form action="panier.php" method="get">
                <?php
                    echo "<input type=\"hidden\" name=\"vid_uniq\" value=\"".$key['id_produit']."\">";
                
                echo "<button class=\"trash\" type=\"button\" data-bs-toggle=\"modal\" data-bs-target=\"#videSoloModal".$key['id_produit']."\"><img src=\"../images/poubelle.png\" alt=\"retirer ce produit du panier\" ></button>";

                echo "<div class=\"modal fade\" id=\"videSoloModal".$key['id_produit']."\" tabindex=\"-1\" aria-labelledby=\"videSoloLabel\" aria-hidden=\"true\">";
                ?>
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="videSoloLabel">Confirmation</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <?php 
                                    echo "Êtes-vous certain de vouloir supprimer \"".$key['nom']."\" de votre panier?";
                                    ?>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-primary" style="background-color:#F2CD5C; border:none; color:black;">Confirmer</button>
                                </div>
                            </div>
                        </div>
                    </div>
            </form>

        </div>

        

        

    </div>
    
</div>
<hr class="hr_panier">
