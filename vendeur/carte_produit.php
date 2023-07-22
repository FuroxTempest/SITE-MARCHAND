<?php
    echo '
    <div class="card">
        ';
        foreach(glob("../images/".$key['id_produit']."_*")as $image){
            
        }
        echo '<img src="'.$image.'" class="card-img-top"  alt="'.$key['nomprod'].'">';
        echo '
        <div class="card-body">
            <h5 class="card-title">'.ucfirst($key['nomprod']).'</h5>
            <p class="card-text">'.$key["descriptif"].'</p>
            <form action="./detail_produit_vendeur.php" method="GET">
                <input type="hidden" name="id_produit" value="'.$key['id_produit'].'"/>
                <input type="submit" class="btn btn-primary" value="Détails"/>
            </form>
        </div>
    </div>
    ';
?>
