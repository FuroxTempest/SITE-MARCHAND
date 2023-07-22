<html>
<?php
session_start();
include('./connect_params.php');
try{
    //connexion à la bdd
    $bdd = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass,
    [PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
    );
}catch (PDOException $e){
    print "Erreur ! : " . $e->getMessage() . "<br/>";
    die();
}

try{
    if(!isset($_GET['id_prod'])){
        $idprod = $_GET['idprod'];
    }else{
        $idprod = $_GET['id_prod'];
    }
    $reqinfo = $bdd->prepare("SELECT nomprod,prix_ttc,descriptif,quantite,stock FROM alizonbdd._produit WHERE id_produit = ?;");
    $reqinfo->execute([$idprod]);
    $info_prod = $reqinfo->fetch();

    $catinfo = $bdd->prepare("SELECT nom FROM alizonbdd._categorie c inner join alizonbdd._produit p on c.id_cat = p.id_categorie WHERE p.id_produit = ?;");
    $catinfo->execute([$idprod]);
    $info_cat = $catinfo->fetch();

    $id_vendeur=$info_prod['id_vendeur'];

    $reqvendeur = $bdd->prepare("SELECT raison_sociale from alizonbdd._vendeur where id_vendeur = ?;");
    $reqvendeur->execute([$id_vendeur]);
    $info_vendeur = $reqvendeur->fetch();

}catch (PDOException $e){
    echo "Erreur ! : " . $e->getMessage() . "<br/>";
    die();
}

//si on veut ajouter le produit au panier...
if(isset($_GET['id_prod'])){
    //...visiteur
    if(!isset($_SESSION['id'])){
        $trouve=false;
        $qtt = $_GET['qtt_prod'];
        //on récupère le contenu du panier
        foreach (unserialize($_COOKIE['panier']) as $i){
            //si le produit y est déjà
            if($i[0]==$_GET['id_prod']){
                $requete=$bdd->prepare("SELECT stock from alizonbdd._produit where  _produit.id_produit=?;");
                $requete->execute([$i[0]]);
                $stck=$requete->fetch();
                $stock=$stck['stock'];
    
                if ( ( $_GET['qtt_prod'] > 0 ) and ($stock > $_GET['qtt_prod'])) {
                    $i[1]= $qtt;
                    $bon = true;
                }else {
                    $i[1]= $i[1];
                }
                $trouve=true;
            }
            $panier[]=$i;

        }
        //si on a déjà le produit dans le panier
        if($trouve==true){
            //on met le nouveau contenu du panier dans le cookie
            $_COOKIE['panier']=[];
            setcookie('panier', serialize($panier), time()+3600*24);
            if($bon==true){
                header("Location: detail_produit.php?idprod=$_GET[id_prod]&notif=maj");      

            }else {
                header("Location: detail_produit.php?idprod=$_GET[id_prod]&notif=non");      

            }
        }
        //si le produit n'est pas dans le panier
        if($trouve==false){
            $panier=[];
            // on récupère le panier
            foreach (unserialize($_COOKIE['panier']) as $key) {
                $panier[] = $key;
            }
            $_COOKIE['panier']=[];
            // on ajoute un article

            $panier[] = array(0 => $_GET['id_prod'], 1 => $_GET['qtt_prod']);
            // on met à jour le panier
            setcookie('panier', serialize($panier), time()+3600*24);

            header("Location: detail_produit.php?idprod=$_GET[id_prod]&notif=ok");      
    }
        //...client     
    }else{
            $qtt=$_GET['qtt_prod'];

            //vérification que la qtt ne dépasse pas le stock restant;
            $SelecStock=$bdd->prepare("SELECT stock from alizonbdd._produit where id_produit=?");
            $SelecStock->execute([$_GET['id_prod']]);
            $ValeurStock=$SelecStock->fetch();
            $stock=$ValeurStock['stock'];
            if(($stock <=$qtt)||($qtt<=0)){ 
                if ($qtt<=0) {
                    echo <<<html
                    <div id="notification_non">
                        Vous ne pouvez pas ajouter une valeure négative
                    </div>
                    html;
                }
                if ($stock <=$qtt) {
                    echo <<<html
                    <div id="notification_non">
                        La valeur entré dépasse le nombre de produit en stock
                    </div>
                    html;
                }

            }else{
                // processus d'ajout d'un produit au panier
                $idCli=$_SESSION['id'];

                // on vérifie si le client à déja un commande en cours
                $req2=$bdd->prepare("SELECT id_commande from alizonbdd._commande where id_client =?  and statut_commande='en cours';");
                $req2->execute([$idCli]);
                $row=$req2->rowCount();
                
                // si il n'en à pas on la crée
                if($row==0){

                    $reqAddCom =$bdd->prepare("INSERT into alizonbdd._commande(prix_final,statut_commande,id_client,date_commande) VALUES (0,'en cours',?,NOW());");
                    $reqAddCom->execute([$idCli]);
            
                }
                
                //on selectionne l'id de la commande
                $req3=$bdd->prepare("SELECT id_commande from alizonbdd._commande where id_client =? and statut_commande='en cours';");
                $req3->execute([$idCli]);
                $commande=$req3->fetch();
                $idCom=$commande['id_commande'];

                //on selectionne le prix de l'article
                $req4=$bdd->prepare("SELECT prix_ttc from alizonbdd._produit where id_produit =?;");
                $req4->execute([$_GET['id_prod']]);
                $prix=$req4->fetch();
                $prixTot=$prix['prix_ttc'];

                // on regarde si le produit est déja dans son panier ou pas
                $idProd=$_GET['id_prod'];
                $req5=$bdd->prepare("SELECT nb_article from alizonbdd._panier where id_commande=? and id_produit=?;");
                $req5->execute([$idCom,$idProd]);
                $row2=$req5->rowCount();
                
                // si l'article n'est pas déja dans le panier
                if($row2==0){
                    // on ajoute le produit au panier
                    $insertPan=$bdd->prepare("INSERT INTO alizonbdd._panier(id_commande,id_produit, nb_article, prix_total, reduction_totale) VALUES (?,?,?,?,?)");
                    $insertPan-> execute([$idCom,$_GET['id_prod'],$qtt,$qtt*$prixTot,0]);
                    echo <<<html
                    <div id="notification_ok">
                        Le produit a bien été ajouté au panier
                    </div>
                    html;
                // sinon
                }else{
                    // on augmente la quantité de l'article dans son panier par 1
                    $liArt=$req5->fetch();
                    $nbArt=$liArt['nb_article'];
                    $nbArt=$qtt;

                    $updatePan=$bdd->prepare("UPDATE alizonbdd._panier SET nb_article=?,prix_total=? WHERE id_commande=? and id_produit=? ;");
                    $updatePan->execute([$nbArt,$nbArt*$prixTot,$idCom,$idProd]);
                    echo <<<html
                    <div id="notification_ok">
                        La quantité a bien été mise a jour
                    </div>
                    html;
                }
            }
    }
}


// Test redution :
$reduction = false;
$reductions = array();
$date_ajd = new DateTime();



foreach($bdd->query("select _produit.id_produit,_promotion.reduction,_promotion.date_debut,_promotion.date_fin from alizonbdd._produit inner join alizonbdd._promotion on _promotion.id_produit=_produit.id_produit where  _produit.id_produit = ".$_GET['idprod']." ;") as $reduc) {
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

$vendeur=$bdd->prepare("SELECT _vendeur.nom,_vendeur.id_vendeur from alizonbdd._vendeur inner join alizonbdd._produit on _vendeur.id_vendeur = _produit.id_vendeur where _produit.id_produit=?");
$vendeur->execute([$idprod]);
$Vendeur=$vendeur->fetch();



?>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php echo"<title>".$info_prod['nomprod'] ."| ALIZON</title>";?>
    <link rel="icon" href="../images/favicon.ico" />

    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400&display=swap" rel="stylesheet"> 
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
    <symbol id="check-circle-fill" viewBox="0 0 16 16">
        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
    </symbol>   
    </svg>   
    
</head>
<body style="background-color:#E6E6E6;">
    <?php

        include("./header.php");
        include("./notification.php")
    ?>

    <main id="detail_produit">
        <!--Zone principale-->
        
            <div class="haut">
            <!--Zone du carousel-->
            <article class="carouselle">

                <div id="car_prod" class="carousel slide" data-bs-ride="false">
                    <!--Creation des boutons indicateurs-->
                    <div class="carousel-indicators">
                    <?php
                        $i=0;
                        foreach(glob("../images/".$idprod."_*.*",GLOB_NOESCAPE)as $image){
                            if($i==0){
                                echo "<button type=\"button\" data-bs-target=\"#car_prod\" data-bs-slide-to=\"0\" class=\"active\" aria-current=\"true\" aria-label=\"Slide 2\"></button>";
                            }else{
                                echo "<button type=\"button\" data-bs-target=\"#car_prod\" data-bs-slide-to=\"$i\" aria-label=\"Slide ".($i+1)."\"></button>";
                            }
                            $i++;
                        }
                    ?>
                        
                    </div>

                    <!--Images internes au carousel-->
                    <div class="carousel-inner" style="border-radius: 5px;">
                        <?php
                            $i=1;
                            #Pour chaque image trouvé grace au chemin
                            foreach(glob("../images/".$idprod."_*.*",GLOB_NOESCAPE)as $image){
                                #creation de l'élement de carousel avec l'image
                                if($i==1){
                                    echo "<div class=\"carousel-item img-zoom-container active\">";
                                    echo "    <img src=\"$image\" class=\"d-block w-100\" alt=\"$i e image\" >";
                                    echo "</div>";
                                }else{
                                    echo "<div class=\"carousel-item img-zoom-container\">";
                                    echo "    <img src=\"$image\" class=\"d-block w-100\" alt=\"$i e image\" >";
                                    echo "</div>";
                                }
                                $i++;
                            }
                        ?>      
                    </div>

                    <!--Creations des boutons gauche-droite-->
                    <button class="carousel-control-prev" type="button" data-bs-target="#car_prod" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#car_prod" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </article>

            <!--Zone "propriétés" avec nom, caractéristiques, prix/promos, quantité, indicateur de stock-->
            <article class="propriete">
                <?php
                    echo "<div class=\"titre\">";
                        if (($info_prod['stock']<20) AND ($info_prod['stock']>0)) {
                            echo "<img style=\"width:60px ;\" class=\"logo_attention\" src=\"../images/logo_attention_milieu.png\" title=\"Stock faible\" >";
                        }elseif ($info_prod['stock']==0) {
                            echo "<img  style=\"width:50px ;\" class=\"logo_attention\" src=\"../images/logo_attention_vide.png\" title=\"Bientôt de nouveau en stock\" >";
                        }else {
                            echo "<div></div>";
                        }
                    echo "<h1>".ucfirst($info_prod['nomprod'])."</h1>";
                    echo "</div>";
                    
                    echo "<h4>Catégorie : ".$info_cat['nom']."</h4>";
            
                    echo "<h4>Quantité à l'achat : ".$info_prod['quantite']."</h4>";

                    
                    echo "<form action=\"vendeur.php\" method=\"get\" style=\"animation:none;\" class=\"\">";
                    
                    echo "      <input type=\"hidden\"  name=\"id_vendeur\" value=\"".$Vendeur['id_vendeur']."\">";
                
                    echo "      <button class=\"nom_vendeur\" style=\"background-color: #F5F5F5; border: none; text-align:left; font-size: 30px;margin-left: 70px; margin-bottom: 25px; \" type=\"submit\">Vendeur : ".$Vendeur['nom']."</button>";
                
                    echo "</form> " ; 
                

                    echo "<article>";

                    $pourcentage = (100-$reductions['reduction'])*0.01;
                    if ($reduction == false) {
                        echo "<div class=\"prix\"><h2>Prix&nbsp;: </h2> <div class=\"chiffre\">".number_format($info_prod['prix_ttc'], 2, ",", " ")."€ <span> prix ttc</span></div> </div>";

                    }else {
                        echo "<div class=\"prix_reduit\"><h2 style=\"    margin-top: 25px; \">Prix&nbsp;:&nbsp;&nbsp;</h2>  <div class=\"chiffre\"> <h2 class=\"prx\"> ".number_format($info_prod['prix_ttc'], 2, ",", " ")." € </h2> <h2 class=\"prx_reduit\">".number_format($info_prod['prix_ttc']*$pourcentage, 2, ",", " ")."€</h2> <span> prix ttc</span></div> </div>";
                    }

                    
                    echo "</article>";
                ?>
                <!--Zone achat/panier-->
                <div clas="form">

                    <?php
                    $trouve=false;
                    if(!isset($_SESSION['adresseEmail'])){
                        foreach (unserialize($_COOKIE['panier']) as $panier){
                            if($panier[0]==$idprod){
                                $trouve=true;
                                $qtt = $panier[1];
                            }
                        }
                    }else {

                        $mail=$_SESSION['adresseEmail'];
                        $req1=$bdd->prepare("SELECT id_client from alizonbdd._client where email=?;");
                        $req1->execute([$mail]);
                        $client=$req1->fetch();
                        $idCli=$client['id_client'];

                        $req3=$bdd->prepare("SELECT id_commande from alizonbdd._commande where id_client =? and statut_commande='en cours';");
                        $req3->execute([$idCli]);
                        $commande=$req3->fetch();
                        $idCom=$commande['id_commande'];
                    

                        $idProd=$_GET['id_prod'];
                        $req5=$bdd->prepare("SELECT nb_article from alizonbdd._panier where id_commande=? and id_produit=?;");
                        $req5->execute([$idCom,$idprod]);

                        $qtts= $req5->fetch();
                        $qtt = $qtts['nb_article'];

                        if($qtt==0){
                            $trouve = false;
                        }else{
                            $trouve = true;
                        }

                        
                    }
                    ?>

                    <form action="detail_produit.php" class="form_ajout_panier" method="GET">
                        <?php
                         echo "<input type=\"hidden\" name=\"idprod\" value=\"".$idprod."\">";
                         echo "<input type=\"hidden\" name=\"id_prod\" value=\"".$idprod."\">";

                        if ($trouve==false) {
                            echo "<input type=\"number\" name=\"qtt_prod\" value=\"1\">";
                        }else{
                            echo "<input type=\"number\" name=\"qtt_prod\" value=\"".$qtt."\">";
                        }
                        echo "<button  type=\"submit\" >Ajouter au panier</button>";
                       
                        ?>
                        
                    </form>
                </div>
            </article>
        </div>
        <!--Zone description du produit-->
        <article class="description">
            <p >
                <?php
                    echo $info_prod['descriptif'];
                ?>
            </p>
        </article>

    </main>
    <?php
        include("./footer.php");
    ?>
    </body>
</html>
