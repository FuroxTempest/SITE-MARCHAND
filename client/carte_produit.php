<?php

// On se connecte a la BDD
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

// On verifie que la variable $_GET n'es pas vide, si elle n'est pas vide cela veut dire que le produit avec l'id $_GET['id_prod'] va être ajouté au panier  : 
if(isset($_GET['id_prod'])){
    // On verifie si l'adresse email dans $_SESSION est activé, si ce n'est pas le cas cela veut dire que l'utilisateur n'est pas connécté
    if(!isset($_SESSION['id'])){
        $trouve=false;
        $bon = false;
        // On fait le tour de la variable $_COOKIE['panier']
        foreach (unserialize($_COOKIE['panier']) as $i){
            // On teste si le produit de $_GET['id_prod'] est dans le panier 
            if($i[0]==$_GET['id_prod']){

                //On recupere le stock du produit 
                $requete=$bdd->prepare("SELECT stock from alizonbdd._produit where  _produit.id_produit=?;");
                $requete->execute([$i[0]]);
                $stck=$requete->fetch();
                $stock=$stck['stock'];
    
                // On verifie que la quantité rentré est bien valide 
                if ( ( $_GET['qtt_prod'] > 0 ) and ($stock > $_GET['qtt_prod'])) {
                    // Si elle est valide alors on modifie la quantite avec la variable $_GET['qtt_prod']
                    $i[1]= $_GET['qtt_prod'];
                    $bon = true;
                }else {
                    // Si elle n'est pas bonne alos la quantite ne changera pas 
                    $i[1]= $i[1];
                }
                $trouve=true;
            }
            $panier[]=$i;
        }
        // Si le produit est dans le panier
        if($trouve==true){
            // Si la qtt etait bonne alors on redirige vers la $page avec les informations nécéssaire et la notification qui correspond 

            if($bon==true){
                $_COOKIE['panier']=[];
                setcookie('panier', serialize($panier), time()+3600*24);
                if(isset($_GET['idcategorie'])){
                    header("Location: $_GET[page]?idcategorie=$_GET[idcategorie]&notif=maj");
                }
                elseif (isset($_GET['keyword'])) {
                    header("Location: $_GET[page]?keyword=$_GET[keyword]&notif=maj");
                }
                elseif (isset($_GET['idvendeur'])) {
                    header("Location: $_GET[page]?id_vendeur=$_GET[idvendeur]&notif=maj");
                }
                else {
                    header("Location: $_GET[page]?notif=maj");
                }
            }else {
                $_COOKIE['panier']=[];
                setcookie('panier', serialize($panier), time()+3600*24);

                if(isset($_GET['idcategorie'])){
                    header("Location: $_GET[page]?idcategorie=$_GET[idcategorie]&notif=non");
    
                }
                elseif (isset($_GET['keyword'])) {
                    header("Location: $_GET[page]?keyword=$_GET[keyword]&notif=non");
                }
                elseif (isset($_GET['idvendeur'])) {
                    header("Location: $_GET[page]?id_vendeur=$_GET[idvendeur]&notif=non");
                }
                else {
                    header("Location: $_GET[page]?notif=non");
                }
            }

        }
        if($trouve==false){
            // Si le produit n'est pas dans le panier alors on l'ajoute a celui ci 

            $panier=[];
            // on récupère le panier
            foreach (unserialize($_COOKIE['panier']) as $key) {
                $panier[] = $key;
            }
            $_COOKIE['panier']=[];
            // on ajoute un article

            $panier[] = array(0 => $_GET['id_prod'], 1 =>$_GET['qtt_prod']);
            // on met à jour le panier
            setcookie('panier', serialize($panier), time()+3600*24);

            if(isset($_GET['idcategorie'])){
                header("Location: $_GET[page]?idcategorie=$_GET[idcategorie]&notif=ok");

            }
            elseif (isset($_GET['keyword'])) {
                header("Location: $_GET[page]?keyword=$_GET[keyword]&notif=ok");
            }
            elseif (isset($_GET['idvendeur'])) {
                header("Location: $_GET[page]?id_vendeur=$_GET[idvendeur]&notif=ok");
            }
            else {
                header("Location: $_GET[page]?notif=ok");
            }
            die;
        }     
    }else{
        // L'utilisateur est connecte 


        $qtt=$_GET['qtt_prod'];

        
        $SelecStock=$bdd->prepare("SELECT stock from alizonbdd._produit where id_produit=?");
        $SelecStock->execute([$_GET['id_prod']]);
        $ValeurStock=$SelecStock->fetch();
        $stock=$ValeurStock['stock'];

        // 
        if($stock <=$qtt || $qtt <0){
            
            if(isset($_GET['idcategorie'])){
                header("Location: $_GET[page]?idcategorie=$_GET[idcategorie]&notif=non");

            }elseif (isset($_GET['keyword'])) {
                header("Location: $_GET[page]?keyword=$_GET[keyword]&notif=non");
            }
            elseif (isset($_GET['idvendeur'])) {
                header("Location: $_GET[page]?id_vendeur=$_GET[idvendeur]&notif=non");
            }
            else {
                header("Location: $_GET[page]?notif=non");
            }

        }else{
            // processus d'ajout au panier pour un client

            // Recuperation de l'id du client en fonction de son email
            $mail=$_SESSION['adresseEmail'];
            $req1=$bdd->prepare("SELECT id_client from alizonbdd._client where email=?;");
            $req1->execute([$mail]);
            $client=$req1->fetch();
            $idCli=$client['id_client'];

            // On verifie si leclient a une commande en cours
            $req2=$bdd->prepare("SELECT id_commande from alizonbdd._commande where id_client =?  and statut_commande='en cours';");
            $req2->execute([$idCli]);
            $row=$req2->rowCount();
            
            // S'il en a pas on l'a lui créée
            if($row==0){
                $reqAddCom =$bdd->prepare("INSERT into alizonbdd._commande(prix_final,statut_commande,id_client,date_commande) VALUES (0,'en cours',?,now());");
                $reqAddCom->execute([$idCli]);
            } 
            
            // On recuoere l'id de la commande 
            $req3=$bdd->prepare("SELECT id_commande from alizonbdd._commande where id_client =? and statut_commande='en cours';");
            $req3->execute([$idCli]);
            $commande=$req3->fetch();
            $idCom=$commande['id_commande'];
        
            // On recupere le prix ttc du produit  avec l'id : $_GET['id_prod']
            $req4=$bdd->prepare("SELECT prix_ttc from alizonbdd._produit where id_produit =?;");
            $req4->execute([$_GET['id_prod']]);
            $prix=$req4->fetch();
            $prixTot=$prix['prix_ttc'];

            // On verifie si le produit est deja dans le apnier ou pas 
            $idProd=$_GET['id_prod'];
            $req5=$bdd->prepare("SELECT nb_article from alizonbdd._panier where id_commande=? and id_produit=?;");
            $req5->execute([$idCom,$idProd]);
            $row2=$req5->rowCount();
            if($row2==0){
                $insertPan=$bdd->prepare("INSERT INTO alizonbdd._panier(id_commande,id_produit, nb_article, prix_total, reduction_totale) VALUES (?,?,?,?,?)");
                $insertPan-> execute([$idCom,$_GET['id_prod'],$qtt,$qtt*$prixTot,0]);
                if(isset($_GET['idcategorie'])){
                    header("Location: $_GET[page]?idcategorie=$_GET[idcategorie]&notif=ok");
    
                }elseif (isset($_GET['keyword'])) {
                    header("Location: $_GET[page]?keyword=$_GET[keyword]&notif=ok");
                }
                elseif (isset($_GET['idvendeur'])) {
                    header("Location: $_GET[page]?id_vendeur=$_GET[idvendeur]&notif=ok");
                }
                else {
                    header("Location: $_GET[page]?notif=ok");
                }
            }else{
                $requete=$bdd->prepare("SELECT stock from alizonbdd._produit where  _produit.id_produit=?;");
                $requete->execute([$idProd]);
                $stck=$requete->fetch();
                $stock=$stck['stock'];
                
                if ( ( $_GET['qtt_prod'] <= 0 ) || ($_GET['qtt_prod'] > $stock)) {
                    $nbArt=$nbArt;
                    

                    if(isset($_GET['idcategorie'])){
                        header("Location: $_GET[page]?idcategorie=$_GET[idcategorie]&notif=non");
        
                    }
                    elseif (isset($_GET['keyword'])) {
                        header("Location: $_GET[page]?keyword=$_GET[keyword]&notif=non");
                    }
                    elseif (isset($_GET['idvendeur'])) {
                        header("Location: $_GET[page]?id_vendeur=$_GET[idvendeur]&notif=non");
                    }
                    else {
                        header("Location: $_GET[page]?notif=non");
                    }
                }else {
                    $liArt=$req5->fetch();
                    $nbArt=$liArt['nb_article'];
                    $nbArt=$qtt;
    
                    $updatePan=$bdd->prepare("UPDATE alizonbdd._panier SET nb_article=?,prix_total=? WHERE id_commande=? and id_produit=? ;");
                    $updatePan->execute([$nbArt,$nbArt*$prixTot,$idCom,$idProd]); 

                    if(isset($_GET['idcategorie'])){
                        header("Location: $_GET[page]?idcategorie=$_GET[idcategorie]&notif=maj");
        
                    }elseif (isset($_GET['keyword'])) {
                        header("Location: $_GET[page]?keyword=$_GET[keyword]&notif=maj");
                    }
                    elseif (isset($_GET['idvendeur'])) {
                        header("Location: $_GET[page]?id_vendeur=$_GET[idvendeur]&notif=maj");
                    }
                    else {
                        header("Location: $_GET[page]?notif=maj");
                    }

                }
            }
        }
    }
}


// Test redution :
$reduction = false;
$reductions = array();
$date_ajd = new DateTime();

// On verifie si le produit a une promotion 
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

$vendeur=$bdd->prepare("SELECT _vendeur.nom , _vendeur.id_vendeur from alizonbdd._vendeur inner join alizonbdd._produit on _vendeur.id_vendeur = _produit.id_vendeur where _produit.id_produit=?");
$vendeur->execute([$key['id_produit']]);
$Vendeur=$vendeur->fetch();



?>


<article id="ligne_produit_categ"  class="col">
    <div style="background-color:#F5F5F5; border:none;" class="card carte_produit" >
        <?php
            // On affiche la derniere image lié au produit
            echo "<a href=\"detail_produit.php?idprod=".$key['id_produit']."\">";
            foreach(glob("../images/".$key['id_produit']."_*")as $image){ }
            echo "<img src=\"".$image."\" class=\"card-img-top\"  alt=\"$key[nom]\">";
            ?>
            </a>
        <div class="card-body" style="padding:0px;">
            <?php echo "<a href=\"detail_produit.php?idprod=".$key['id_produit']."\">";?>
            <div class="info">  

                <?php
                    // Nom du produit
                    echo "<h4 >".ucfirst($key['nom'])."</h4> " ;

                    // verifie si il y a une réduction sur le porduit et on affiche en conséquence 
                    $pourcentage = (100-$reductions['reduction'])*0.01;
                    if ($reduction == false) {
                        echo "<h3>".number_format($key['prix_art'], 2, ",", " ")."€ ";

                    }else {
                        echo "<h3> <div> <span>".number_format($key['prix_art'], 2, ",", " ")."€  </span> ".number_format($key['prix_art']*$pourcentage, 2, ",", " ")."€  </div> ";
                    }

                   
                    echo "<p class=\"ttc\" style=\" text-align:right;\">ttc</p>  </h3> ";
                ?>
            </div>
            <div>
                <div class="descriptif">
                
                    <?php   
                        echo "<form action=\"vendeur.php\" method=\"get\" style=\"animation:none;\" class=\"\">";
                        
                        echo "      <input type=\"hidden\"  name=\"id_vendeur\" value=\"".$Vendeur['id_vendeur']."\">";
                    
                        echo "      <button class=\"nom_vendeur\" style=\"background-color: #F5F5F5; border: none; \" type=\"submit\">Vendeur : ".$Vendeur['nom']."</button>";
                    
                        echo "</form> " ; 
                    ?>
                    <br><br> 
                    <?php echo "<p>  $key[descriptif] </p>"; ?>
                </div>
            </div>
            </a>
            <form action="carte_produit.php" method="get" onsubmit="setScroll();">
                <?php
                // On verifie si le porduit est dans le panier ou pas afin d'afficher la carte en conséquence 
                $trouve=false;
                if(!isset($_SESSION['adresseEmail'])){
                    $trouve=false;
                    foreach (unserialize($_COOKIE['panier']) as $panier){
                        if($panier[0]==$key['id_produit']){
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
                    $req5->execute([$idCom,$key['id_produit']]);

                    $qtts= $req5->fetch();
                    $qtt = $qtts['nb_article'];

                    if($qtt==0){
                        $trouve = false;
                    }else{
                        $trouve = true;
                    }

                    
                }

                if ($trouve == false) {
                    // Affichage si le produit n'est pas dans le panier
                    echo "<input type=\"hidden\" name=\"id_prod\" value=\"".$key['id_produit']."\">";
                    echo "<input type=\"hidden\" name=\"page\" value=\"".$page."\">";
                    echo "<input type=\"hidden\" name=\"qtt_prod\" value=\"1\">";
                    
                    
                    if(isset($idcat)){
                        echo "<input type=\"hidden\" name=\"idcategorie\" value=\"".$idcat."\">";
                        
                    }elseif (isset($keyword)) {
                        echo "<input type=\"hidden\" name=\"keyword\" value=\"".$keyword."\">";
                    } 
                    elseif(isset($idvendeur)){
                        echo "<input type=\"hidden\" name=\"idvendeur\" value=\"".$idvendeur."\">";
                    }

                    echo "<input type=\"submit\" class=\"bouton_ajt\"value=\"Ajouter au panier\">";

                }if ($trouve==true) {
                    // Affichage si le produit est dans le panier 
                    echo "<div class=\"ajouter_panier_bis\">";
                        echo "<input type=\"hidden\" name=\"id_prod\" value=\"".$key['id_produit']."\">";
                        
                        echo "<input type=\"hidden\" name=\"page\" value=\"".$page."\">";
                        
                        if(isset($idcat)){
                            echo "<input type=\"hidden\" name=\"idcategorie\" value=\"".$idcat."\">";

                        } 
                        elseif (isset($keyword)) {
                            echo "<input type=\"hidden\" name=\"keyword\" value=\"".$keyword."\">";
                        } 
                        elseif(isset($idvendeur)){
                            echo "<input type=\"hidden\" name=\"idvendeur\" value=\"".$idvendeur."\">";
                        }

                        ?>

                        <?php

                        echo "<input type=\"number\" name=\"qtt_prod\" class=\"qtt\" style=\"width:25%;\"  value=\"".$qtt."\">";
                        echo "<input onclick=\"window.location.href = './panier.php';\" type=\"button\" class=\"bouton_dans_panier\" value=\"Dans le panier\" style=\"width:70%; \">";
                        
                        
                        
                    echo "</div>";
                }

                    
                ?>
                    
            </form>
        </div>
    </div>
</article>

