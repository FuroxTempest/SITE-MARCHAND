
<?php
//On a le modèle de base de la notification pour notifier un succès
if ($_GET['notif']=='ok') {
    ?>
    <div id="notification_ok">
        Le produit à bien été ajouté au panier
    </div>

    <?php
}

//Un autre modèle pour le panier lorsque l'on met à jour la quantité d'un produit
if ($_GET['notif']=='maj') {
    ?>
    <div id="notification_ok">
        La quantité à bien été mise à jour
    </div>

    <?php
}

//Un pour les erreurs de quantité par exemple
if ($_GET['notif']=='non') {
    ?>
    <div id="notification_non">
        Erreur la quantité rentrée n'est pas valide
    </div>

    <?php
}

//Un pour le test des cartes bancaires
if ($_GET['notif']=="carte_non") {
    echo <<<html
    <div id="notification_non">
        La carte rentrée n'est pas valide
    </div>
    html;
}

//Un lorsque l'on vide le panier
if ($_GET['notif']=="panier_vide") {
    echo <<<html
    <div id="notification_ok">
        Panier vidé 
    </div>
    html;
}

//Un lorsque l'on supprime un produit dans le panier
if ($_GET['notif']=="produit_supprime") {
    echo <<<html
    <div id="notification_ok">
        Produit supprimé du panier 
    </div>
 html;
    
}



?>

