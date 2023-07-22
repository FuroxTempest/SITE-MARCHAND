<?php
    echo '
    <div class="card" style="width: 18rem;">
        ';
        echo '
        <div class="card-body">
            <h5 class="card-title">Client : '.$key['nom']." ".$key['prenom'].'</h5>
            <p class="card-text">Numéro de commande : '.$key['id_commande'].'</p>
            <p class="card-text">Adresse : '.$key['adresse'].'</p>
            <form action="detail_commande.php" method="get">
                <input type="hidden" name="id_com" value="'.$key['id_commande'].'">
                <button class="btn btn-primary"  type="submit" style="background-color: #666666;">Voir détails</button>
            </form>
        </div>
    </div>
    ';
?>