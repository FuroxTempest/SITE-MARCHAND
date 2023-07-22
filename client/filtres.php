 <!-- Div avec les filtes -->
 <div id="div_parent_filtre" class="">
    <div class="m-2 mt-3" id="div_filtre">

        <!-- On affiche le nombre de résultat trouvés à la recherche de l'utiliteur -->
        <h4><?=count($tab)." ".(count($tab)>1?"résultats trouvés"."":"résultat trouvé "."") ?></h4>

        <!-- Div du filtre du prix -->
        <div class="div_filtre_prix ">
            <h4>Prix</h4>

            <!-- Formulaire qui contient les barres et input que l'on va récupérer en JS -->
            <form class="mt-3 p-2 pr-1 rounded-2 div_container_filtre" method="get">
                <label for="min-price" class="fs-5">Prix minimum :</label>
                <br>
                <input type="range" class="slider min-price-range" id="" name="min-price" min="0" max="<?php echo $prix_max ?>" value="0" step="1">
                <input type="number" class="input-filtre-prix min-price-input" id="" name="min-price" value="0" disabled="disabled">€</input>
                <br>
                <label for="max-price" class="mt-3 fs-5">Prix maximum :</label>
                <br>
                <input type="range" class="slider max-price-range" id="" name="max-price" min="0" max="<?php echo $prix_max ?>" value="<?php echo $prix_max ?>" step="1">
                <input type="number" class="input-filtre-prix max-price-input" id="" name="max-price" value="<?php echo $prix_max ?>" disabled="disabled">€</input>
            </form>
        </div>

        <!-- Div du filtre des vendeurs -->
        <div class="div_filtre_vendeur mt-3">
            <h4>Vendeur</h4>

            <!-- Formulaire qui contient tout les vendeurs -->
            <div class="mt-3 rounded-2 div_container_filtre">
                <form method="get" class="py-2 form_filtre_vendeur">
                    <?php
                        foreach($vend as $vendeur){
                            ?>
                                <div class="my-3 mx-2 fs-6 fs-xl-5 fs-xxl-4 form-check">
                                    <input class="form-check-input" type="checkbox" name="<?php echo $vendeur['raison_sociale']; ?>" id="<?php echo $vendeur['raison_sociale']; ?>" value="<?php echo $vendeur['id_vendeur']; ?>">
                                    <label class="form-check-label" for="<?php echo $vendeur['raison_sociale']?>" ><?php echo $vendeur['raison_sociale'] ?></label>
                                </div>
                            <?php
                        }
                    ?>
                </form>
            </div>
        </div>
    </div>
