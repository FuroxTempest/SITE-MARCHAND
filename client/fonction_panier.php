<?php

function supprimer_produit($id_produit){
    // fonction appelée quand on clique sur la poubelle pour supprimer un produit du panier
    
    $i = 0;
    //on récupère le contenu du panier
    $panier = unserialize($_COOKIE['panier']);
    $temp_panier=[];
    //on cherche le produit à supprimer dans le panier
    foreach($panier as $produit) {
        //si on le trouve
        if(array_values($produit)[0] == $id_produit){
            //on en fais rien
        //sinon
        }else{
            //on récupère les produits qui ne sont pas à supprimer
            $temp_panier[]= $produit;
        }
        $i++;
    }

    //on re-met tout dans le cookie
    setcookie('panier', serialize($temp_panier), time() + 24*3600, null, null, true, true);
    header("Location: panier.php?notif=produit_supprime");
    die();
}

function modifier_quantite_produit($id_produit,$quantite){//non fonctionnel
    // fonction appelée quand on modifie la quantité d"un produit dans le panier
    $panier = unserialize($_COOKIE['panier']);
    
    $index = 0;
    foreach($panier as $produit) {
        if(array_values($produit)[0] == $id_produit){
            $panier[$index][1] = $quantite;
            if($quantite <= 0){
                supprimer_produit($id_produit);
            }
            break;
        }
        $index++;
    }
    unset($_COOKIE['panier']);
    setcookie('panier', serialize($panier), time() + 24*3600, null, null, true, true);
    header("Location: panier.php?notif=maj");
    die();
}

function vider(){

    // fonction appelée quand on clique sur le bouton vider le panier
    setcookie('panier');
    unset($_COOKIE['panier']);
    header("Location: panier.php?notif=panier_vide");

    
}

?>
