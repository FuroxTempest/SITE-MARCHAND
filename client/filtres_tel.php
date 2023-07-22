<div class="div_filtre_tel p-3 my-4 text-center">
    <button id="lien_filtre" data-bs-toggle="modal" data-bs-target="#modal_filtre">Filtres<img src="../images/fleche-vers-le-bas.png" id="img_fleche_filtre" alt="fleche filtre"></button>
    <div class="modal fade" id="modal_filtre" tabindex="-1" aria-labelledby="modal_filtre" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Filtres</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Div du filtre du prix -->
                    <div class="div_filtre_prix_tel">
                        <h4>Prix</h4>

                        <!-- Formulaire qui contient les barres et input que l'on va récupérer en JS -->
                        <form class="mt-3 bg-light p-2 rounded-2" method="get">
                            <label for="min-price" class="fs-5">Prix minimum :</label>
                            <br>
                            <input type="range" class="slider min-price-range-tel" id="" name="min-price" min="0" max="<?php echo $prix_max ?>" value="0" step="1">
                            <br>
                            <input type="number" class=" min-price-input-tel border border-0 text-center bg-light" id="" name="min-price" value="0" disabled="disabled">€</input>
                            <br>
                            <label for="max-price" class="mt-5 fs-5">Prix maximum :</label>
                            <br>
                            <input type="range" class="slider  max-price-range-tel" id="" name="max-price" min="0" max="<?php echo $prix_max ?>" value="<?php echo $prix_max ?>" step="1">
                            <br>
                            <input type="number" class=" max-price-input-tel border border-0 text-center bg-light" id="" name="max-price" value="<?php echo $prix_max ?>" disabled="disabled">€</input>
                        </form>
                    </div>

                   <!-- Div du filtre des vendeurs -->
                    <div class="div_filtre_vendeur mt-3">
                        <h4>Vendeur</h4>

                        <!-- Formulaire qui contient tout les vendeurs -->
                        <div class="mt-3 div_container_filtre">
                            <form method="get" class="py-2 rounded-2 form_filtre_vendeur">
                                <?php
                                    foreach($vend as $vendeur){
                                        ?>
                                            <div class="my-3 mx-2 fs-6 fs-xl-5 fs-xxl-4 text-start form-check">
                                                <input class="form-check-input" type="checkbox" name="<?php echo $vendeur['raison_sociale']; ?>" id="<?php echo $vendeur['raison_sociale']; ?>" value="<?php echo $vendeur['id_vendeur']; ?>">
                                                <label class="form-check-label" for="<?php echo $vendeur['raison_sociale']?>" ><?php echo $vendeur['raison_sociale'] ?></label>
                                            </div>
                                        <?php
                                    }
                                ?>
                            </form>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn" data-bs-dismiss="modal">Appliquer filtre</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>