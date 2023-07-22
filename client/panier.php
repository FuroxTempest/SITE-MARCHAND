<?php
    session_start();
    include('./fonction_panier.php');
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

    // on modifie la quantité du produit
    if(isset($_POST['idprod']) && isset($_POST['qtt_prod'])){
        $idprod = $_POST['idprod'];
        $qtt_prod = $_POST['qtt_prod'];
        if(!isset($_SESSION['id'])){
            modifier_quantite_produit($idprod,$qtt_prod);
        }else{
            if($qtt_prod < 0){
                echo <<<html
                <div id="notification_non">
                    Vous ne pouvez pas ajouter une valeure négative
                </div>
                html;
            }else if($qtt_prod == 0 ){
                $idclient=$_SESSION['id'];
                
                $reqCommande=$bdd->prepare("SELECT id_commande from alizonbdd._commande where id_client =? and statut_commande='en cours';");
                $reqCommande->execute([$idclient]);
                $commande=$reqCommande->fetch();
                $idcommande=$commande['id_commande'];

                $reqSupQte=$bdd->prepare("DELETE from alizonbdd._panier WHERE id_commande=? and id_produit=? ;");
                $reqSupQte->execute([$idcommande,$idprod]);

                modifier_quantite_produit($idprod,$qtt_prod);
                echo <<<html
                <div id="notification_ok">
                    Le produit a bien été supprimé du panier
                </div>
                html;
                
            }else{
                $idclient=$_SESSION['id'];
                
                $reqCommande=$bdd->prepare("SELECT id_commande from alizonbdd._commande where id_client =? and statut_commande='en cours';");
                $reqCommande->execute([$idclient]);
                $commande=$reqCommande->fetch();
                $idcommande=$commande['id_commande'];

                $reqMajQte=$bdd->prepare("UPDATE alizonbdd._panier SET nb_article = ? WHERE id_commande=? and id_produit=? ;");
                $reqMajQte->execute([$qtt_prod,$idcommande,$idprod]);

                modifier_quantite_produit($idprod,$qtt_prod);
            }
        }
        header('location : panier.php');
        $_POST['refresh'] = true;
    }

    if(isset($_GET['vid_uniq'])){
        if(!isset($_SESSION['id'])){
            $id_produit=$_GET['vid_uniq'];
            supprimer_produit($id_produit);
        }else{
            $idClient=$_SESSION['id'];

            $reqComCli=$bdd->prepare("SELECT id_commande from alizonbdd._commande where id_client =? and statut_commande='en cours';");
            $reqComCli->execute([$idClient]);
            $com=$reqComCli->fetch();
            $ComCli=$com['id_commande'];

            $SupProd=$bdd->prepare("DELETE from alizonbdd._panier WHERE id_commande=? and id_produit=?");
            $SupProd->execute([$ComCli,$_GET['vid_uniq']]);
            echo "<script>alert(\"".$_GET['vid_uniq']."\n"."\")</script>".PHP_EOL;
        }
    }


    if(isset($_GET['vider'])){
        if(!isset($_SESSION['id'])){
            vider();
        }else{
            
        $mail=$_SESSION['adresseEmail'];
        $reqRech=$bdd->prepare("SELECT id_client from alizonbdd._client where email=?;");
        $reqRech->execute([$mail]);
        $cli=$reqRech->fetch();
        $idClient=$cli['id_client'];

        $reqComCli=$bdd->prepare("SELECT id_commande from alizonbdd._commande where id_client =? and statut_commande='en cours';");
        $reqComCli->execute([$idClient]);
        $com=$reqComCli->fetchAll();
        foreach($com as $cle){
            $idCommande=$cle['id_commande'];
            $reqSupPanier=$bdd->prepare("DELETE from alizonbdd._panier where id_commande= ?");
            $reqSupPanier->execute([$idCommande]);
            
        }

        unset($panierTemporaire);
        unset($panier);
        }
    }


    

    if(!isset($_SESSION['id'])){
        if(unserialize($_COOKIE['panier']) == array() ){
            $panier = []; 
        }else {
            $panier = [];  
            foreach (unserialize($_COOKIE['panier']) as $key) {
                try{
                    foreach($bdd->query("select ".$key[1]." as \"quantite\", _produit.id_produit,_produit.stock, _produit.nomprod as nom , _produit.prix_ttc as prix_art , _produit.id_categorie, _categorie.nom as \"nomcat\" from alizonbdd._produit inner join alizonbdd._categorie on _categorie.id_cat = _produit.id_categorie where _produit.id_produit=".$key[0].";") as $row) {
                        array_push($panier,$row);
                    }
                }catch(PDOException $e){
                    echo "Erreur ! : " . $e->getMessage() . "<br/>";
                    die();
                }
            }
        }

    }else {

        $panierTemporaire=[];
        $mail=$_SESSION['adresseEmail'];
        $req1=$bdd->prepare("SELECT id_client from alizonbdd._client where email=?;");
        $req1->execute([$mail]);
        $client=$req1->fetch();
        $idCli=$client['id_client'];

        $req2=$bdd->prepare("SELECT id_commande from alizonbdd._commande where id_client =? and _commande.statut_commande = 'en cours';");
        $req2->execute([$idCli]);
        $commande=$req2->fetch();
        $idCom=$commande['id_commande'];

        $req3=$bdd->prepare("SELECT id_produit,nb_article from alizonbdd._panier where id_commande =?;");
        $req3->execute([$idCom]);
        $panier=$req3->fetchAll();

        foreach($panier as $key){
            $idProd=$key['id_produit'];
            $nbArt=$key['nb_article'];
            $data=array(0=>$idProd,1=>$nbArt);
            array_push($panierTemporaire, $data);
        }

        $panier = [];
        foreach ($panierTemporaire as $key) {
            try{
                foreach($bdd->query("select ".$key[1]." as \"quantite\", _produit.id_produit,_produit.stock, _produit.nomprod as nom , _produit.prix_ttc as prix_art , _produit.id_categorie, _categorie.nom as \"nomcat\" from alizonbdd._produit inner join alizonbdd._categorie on _categorie.id_cat = _produit.id_categorie where _produit.id_produit=".$key[0].";") as $row) {
                    array_push($panier,$row);
                }
            }catch(PDOException $e){
                echo "Erreur ! : " . $e->getMessage() . "<br/>";
                die();
            }
        }
        
    }

?>

<!DOCTYPE html>

<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier | ALIZON </title>    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../images/favicon.ico" />


</head>

<body>
    <?php
        include("header.php");
        include("notification.php");
    ?>

<main class="main_panier">
    <?php 
    if ($panier==array()) {
        echo "<div class=\"gauche\" style=\"width:100%;\"> ";
    } else {
        echo "<div class=\"gauche\">   ";
    } 
    ?> 
        <div class="titre_panier" > 
            <h1 >PANIER</h1>
        </div>

        

        <div class="contenue_panier">

        <?php
            if ($panier==array()) {
                echo "<h2 class=\"panier_vide\">Votre panier est vide !</h2>";
            }else {
                foreach ($panier as $key ) {
                    include("./carte_panier.php");
                } 
            }

        ?>

        </div>
    </div>

    <?php
        if ($panier!=array()) {
                
    ?>

    <div class="droite">
        <div class="titre_panier" > 
            <h2>RECAPITULATIF </h2>
        </div>
        
        <div  class="resume_panier">
            
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
                        echo "<li  \"><p> &#x2022 $key[nom]&nbsp;x&nbsp$key[quantite]&nbsp:&nbsp</p> <p> ". number_format($mintot, 2, ",", " ")."&nbsp;€</p></li>";
                    }
                ?>
            </ul>
            <?php
                 echo "<h4>Total : ". number_format($total, 2, ",", " ")." €</h4>";
            ?>

                <button  class="btn_payer" data-bs-toggle="modal" data-bs-target="#verif">PASSER LA COMMANDE</button>

        </div>



        <div class="vider_panier">
            <form action="panier.php" method="get" >
                <input type="hidden" name='vider' value='true'>

                <button type="button"  class="btn_vider" data-bs-toggle="modal" data-bs-target="#videModal">VIDER LE PANIER</button>            

                <!--modal de confirmation-->
                <div class="modal fade" id="videModal" tabindex="-1" aria-labelledby="videLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="videLabel">Confirmation</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Êtes-vous certain de vouloir supprimer tout votre panier?
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
    <?php
        }

    if (!isset($_SESSION['id'])) {
        include("finaliser_panier_visiteur.php");
    }else {
        include("finaliser_panier_client.php");
 
    }
    ?>

</main> 

    <?php
        include("footer.php");
    ?>
</body>
</html>
