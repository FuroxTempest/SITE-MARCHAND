    <!-- Modal -->
    <div class="modal fade" id="verif" tabindex="-1" aria-labelledby="verif" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">PAYEMENT</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body modal_body_fin">

            

            <section class="resume_panier_verif">

                <h3>Résumé de la commande : </h3>
                <ul>
                <?php
                $total = 0;
                foreach ($panier as $key ) {
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

                    if ($reduction){
                        $pourcentage = (100-$reductions['reduction'])*0.01;
                        $mintot = ($key['prix_art']*$key['quantite'])*$pourcentage;
                        $total += $mintot;
                    }else {
                        $mintot = $key["prix_art"]*$key["quantite"];
                        $total += $mintot;

                    }
                    echo "<li style=\"display:flex; justify-content:space-between;\" \"><p> &#x2022 $key[nom]&nbsp;x&nbsp$key[quantite]&nbsp:&nbsp</p> <p> ". number_format($mintot, 2, ",", " ")."&nbsp;€</p></li>";
                }
                ?>
                </ul>
                <?php

                    echo "<h4 style=text-align:right;>Total : $total €</h4>";
                ?>




            </section>


            <section class="client_payement">

                <article > 
                    <h3>Mode de payement : </h3>
                    <?php
                    try{
                        foreach($bdd->query("select nom,prenom,rue,code,ville,telephone from alizonbdd._client where _client.email='".$_SESSION['adresseEmail']."';") as $row) {
                    ?>
                    <form action="confirmation_cb.php" id="form_connex" class="col-10 col-md-7 needs-validation">
                        <div class="form-floating">
                            <input type="text" name="carte" class="form-control" placeholder="placeholder" required>
                            <label for=carte>numero de carte</label>
                        </div>
                        <div class="cb_crypto">
                            <div class="form-floating">
                                <input type="text" id="date_exp" class="form-control" placeholder="placeholder" required>
                                <label  for=date_exp>date d'expiration</label>
                            </div>  
                            <div class="form-floating">
                                <input type="text" id="crypto" class="form-control" placeholder="placeholder" required>
                                <label for=crypto>cryptogramme visuel</label>
                            </div>
                        </div>
                        <div class="form-floating">
                            <?php
                                echo "<input type=\"text\" id=\"prenom\" value = \"$row[prenom]\" class=\"form-control\" placeholder=\"placeholder\" required>";
                            ?>
                            <label for=prenom>Prenom</label>
                        </div>
                        <div class="form-floating">
                            <?php
                                echo "<input type=\"text\" id=\"nom\" value=\"$row[nom]\" class=\"form-control\" placeholder=\"placeholder\" required>";
                            ?>
                            <label for=nom>Nom</label>
                        </div>
                        <div class="form-floating">
                            <?php
                                echo "<input type=\"text\" id=\"adr\" value=\"$row[rue]\" class=\"form-control\" placeholder=\"placeholder\" required>";
                            ?>
                            <label for=adr>adresse</label>
                        </div>
                        <div class="form-floating">
                            <?php
                                echo "<input type=\"text\" id=\"codePost\" value=\"$row[code]\" class=\"form-control\" placeholder=\"placeholder\" required>";
                            ?>
                            <label for=codePost>Code postal</label>
                        </div>
                        <div class="form-floating">
                            <?php
                                echo "<input type=\"text\" id=\"ville\" value =\"$row[ville]\" class=\"form-control\" placeholder=\"placeholder\" required>";
                            ?>
                            <label for=ville>Ville</label>
                        </div>
                        <div class="form-floating">
                            <?php
                                echo "<input type=\"text\" id=\"tel\" value=\"$row[telephone]\" class=\"form-control\" placeholder=\"placeholder\" required>";
                            ?>
                            <label for=tel>n° de telephonne</label>
                        </div>
                        <div class="form-floating">
                            <?php
                                echo "<input type=\"text\" id=\"mail\" value=\"".$_SESSION['adresseEmail']."\" class=\"form-control\" placeholder=\"placeholder\" >";
                            ?>
                            <label for=mail>Mail</label>
                        </div>
                        <button  type="submit" class="btn btn-primary">Payer</button>
                    </form>
                    <?php
                        }
                    }catch (PDOException $e){
                        echo "Erreur ! : " . $e->getMessage() . "<br/>";
                        die();
                    }
                    ?>
                </article>



            
                

            </section>

            
        </div> 

        <div class="modal-footer modal_footer_fin">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
    </div>
