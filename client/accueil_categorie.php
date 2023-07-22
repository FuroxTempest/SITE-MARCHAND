<article style="margin: 10px; padding: 20px; ">
<?php
  // On affiche le titre de la categorie et on le rend plus stylisé 
  $titre = ucfirst(strtolower($categorie)); 
  echo "<h2 style=\"font-weight:bold\" class=\"titre_categorie_accueil\"> $titre : </h2>";

  // On recupere la liste des produits de la categorie et on les ajoutes dans le tableau $produits
  $produits = array();
  foreach($bdd->query("select _produit.stock,_produit.id_produit,_produit.id_categorie,_produit.nomprod as nom,_produit.prix_ttc as prix_art,_produit.descriptif from alizonbdd._produit inner join alizonbdd._categorie on _produit.id_categorie = _categorie.id_cat where  _categorie.nom = '$categorie' ;") as $row) {
    array_push($produits,$row);
  }



  echo "<div id=\"".$titre."\" class=\"row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-4\" >";
  

    // On affiche 3 prooduits avec la page $carte_produitds sur la liste des produits $produits 
    $i= 0;
    foreach ($produits as $key) {
      if ( $i<4) {
        include("./carte_produit.php");
          $i=$i+1;
      }
    } 

  ?>
    </div>
</article>
