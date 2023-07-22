<?php
    //On ajoute les paramètres et de conenxion eton se connecte à la bdd
    session_start();

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
    //////////////////////
    if(isset($_SESSION['mail'])){
        $mail=$_SESSION['adresseEmail'];
        echo "<script>alert(\"".$mail."\");</script>".PHP_EOL;
        $req1=$bdd->prepare("SELECT id_client from alizonbdd._client where email=?;");
        $req1->execute([$mail]);
        $client=$req1->fetch();
        $idCli=$client['id_client'];
        echo "<script>alert(\"".$idCli."\");</script>".PHP_EOL;
    }
    //////////////////////
    if(!isset($_COOKIE['panier'])){
        $_COOKIE['panier']=[];
    }

    $page= "$_SERVER[PHP_SELF]";

    $panier_compte=[];
    if(!isset($_SESSION['id'])){
        if($page=="/php/detail_produit.php"){
            if(isset($panier)){
                $panier_compte=$panier;
            }else{
                foreach (unserialize($_COOKIE['panier']) as $key) {
                    $panier_compte[]=$key;
                }
            }
        }else{
            foreach (unserialize($_COOKIE['panier']) as $key) {
                $panier_compte[]=$key;
            }
        }
    }else{
        $mail=$_SESSION['adresseEmail'];
        $req1=$bdd->prepare("SELECT id_client from alizonbdd._client where email=?;");
        $req1->execute([$mail]);
        $client=$req1->fetch();
        $idCli=$client['id_client'];

        $req2=$bdd->prepare("SELECT id_commande from alizonbdd._commande where _commande.statut_commande='en cours' and id_client =?;");
        $req2->execute([$idCli]);
        $commande=$req2->fetch();
        $idCom=$commande['id_commande'];

        $req3=$bdd->prepare("SELECT id_produit,nb_article from alizonbdd._panier where id_commande =?;");
        $req3->execute([$idCom]);
        $panier_compte=$req3->fetchAll();
    }

?>
<header>
    <!-- Première navbar "principale" avec la barre de recherche, l'icônes de profil, de panier, le logo,   -->
    <nav id="navbar" class="navbar row p-3 p-lg-2" >
        <div class="container-fluid col-12 justify-content-lg-evenly px-3">
            <a class="col-2" href="./accueil.php"><img id="logo" class="text-left" src="../images/logo_alizon.png" alt="Logo Alizon"/></a>
            <div class="col-6 col-md-8 d-block d-lg-none ">

            </div>
            <?php
                if(!isset($_SESSION['id'])){
            ?>
                <a href="./connexion.php" class="d-inline d-lg-none"><img id="pers" src="../images/personne.png"  alt="Logo utilisateur" ></a>
            <?php
                }else{
            ?>
                <a href="./profil.php" class="d-inline d-lg-none "><img id="pers" class="" src="../images/personne.png"  alt="Logo utilisateur" ></a>
            <?php
                }
            ?>
            <div class="btn_panier">
                
                <a href="./panier.php" class="d-inline d-lg-none" ><img id="panier" class="d-block mx-auto" src="../images/panier.png" alt="Logo panier"/>                
                <?php
                    if(!empty($panier_compte)>=1){
                        echo"<span>". count($panier_compte) ." </span>";
                    }
                ?> 
                </a>
            </div>       
                        
            <?php
                if(!isset($_SESSION['id'])){
            ?> 
                <!-- barre de recherche -->
                <form id="form_barre_recherche" class="d-flex col-12 col-lg-4 col-xl-5 border border-dark border-2 ml-2" name="form_recherche" method="get" action="recherche.php" role="search">
                    <input id="search" name="keyword" class="form-control" type="text" placeholder="Rechercher un produit" autocomplete="off"  aria-label="Search">
                    <button class="btn p-0 border-0" type="submit" id="button-addon1" ><img id="recherche"  src="../images/loupe.png" alt="loupe recherche" /></button>
                </form>

                <!-- Résultat de la barre de recherche, lorsque l'utilisateur tappe un produit dans la barre -->
                <div id="res_barre1" class="d-flex col-12 col-lg-4 col-xl-5 rounded-3">
                    <div id="resultat_dynamique" class="rounded-3 w-100"></div>
                </div>
                   
            <?php
                }else{
            ?>
                 <!-- barre de recherche -->
                 <form id="form_barre_recherche" class="d-flex col-12 col-lg-5 col-xl-6 border border-dark border-2 ml-2" name="form_recherche" method="get" action="recherche.php" role="search" >
                    <input id="search" name="keyword" class="form-control" type="text" placeholder="Rechercher un produit" autocomplete="off"  aria-label="Search">
                    <button class="btn p-0 border-0" type="submit" id="button-addon1"><img id="recherche"  src="../images/loupe.png" alt="loupe recherche" /></button>
                </form>

                <!-- Résultat de la barre de recherche, lorsque l'utilisateur tappe un produit dans la barre -->
                <div id="res_barre2" class="d-flex col-12 col-lg-5 col-xl-6 rounded-3">
                    <div id="resultat_dynamique" class="rounded-3 w-100"></div>
                </div>
            <?php
                }
            ?>
            <!-- On importe Ajax et jquery pour faire focntionner la barre de recherche (Ce n'est pas du tout optimisé car on utilise une bibliothèque très grande pour une seule fonctionnalité), on ajoute aussi le script de la barre de recherche dynamique -->
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
            <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
            <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
            <script src="../javascript/recherche.js"></script>


            <?php
                //Si connecté alors on affiche les boutons pour se connecter ou s'inscrire
                if(!isset($_SESSION['id'])){
            ?>
            <div class="d-none d-lg-block col-lg-2 col-xl-2 text-center">
                <a id="btn_connecter" class="border border-dark border-2 px-lg-4 px-xl-5 hvr-sweep-to-right" href="./connexion.php">Se connecter</a>
            </div>
            <div class="d-none d-lg-block col-lg-2 col-xl-2 text-center">
                <a id="btn_inscription" class="border border-dark border-2 px-lg-4 px-xl-5 hvr-sweep-to-right" href="./creation_client.php"> &nbsp; &nbsp;S'inscrire &nbsp;</a>
            </div>
                
            <?php
                //Sinon on affiche l'icône de profil
                }else{
            ?>  
                <!-- Le lien contient du JS pour afficher lors d'un click un bloc avec une liste de lien  -->
                <a href="#" onclick="if(document.getElementById('nav_compte').style.display == 'block') {document.getElementById('nav_compte').style.display = 'none'} else {document.getElementById('nav_compte').style.display='block'};" class="d-none d-lg-inline col-1"><img id="pers" class="d-block mx-auto" src="../images/personne.png" alt="Logo profil"/></a>
                <nav id="nav_compte" style="display: none;">
                    <ul>
                        <li class="border-bottom border-2"><a href="./profil.php" ><img src="../images/personne.png" alt="Icone profil">Mon compte</a></li>
                        <li  class="border-bottom border-2"><a href="./commande.php"><img src="../images/livraison-de-colis.png" alt="Icone colis">Mes commandes</a></li>
                        <li class="m-0 py-0"><a href="./deco.php" class=""><img src="../images/option-de-deconnexion.png" alt="Icone déconnexion">Déconnexion</a></li>
                    </ul>
                </nav>
                
                <!-- Script pour faire en sorte que le bloc s'affiche et se retire si on clique n'importe ou sur la page -->
                <script src="../javascript/compte.js"></script>
            <?php
                }
            ?>
            <div class="btn_panier">
                
                <a href="./panier.php" class="d-none d-lg-inline col-lg-1" ><img id="panier" class="d-block mx-auto" src="../images/panier.png" alt="Logo panier"/>                
                <?php
                    //Petite pastille pour afficher le nombre de produits dans le panier
                    if(!empty($panier_compte)>=1){
                        echo"<span>". count($panier_compte) ." </span>";
                    }
                ?> 
                </a>

            </div>
        </div>
    </nav>
</header>

<!-- Div qui contient la navbar avec les liens vers toutes les catégories -->
<div id="div_cat">
    <nav id="categorie" class="navbar navbar-expand row ">
        <div class="container-fluid col-12 justify-content-between" style="padding: 0 25px">
            <div class="col-2 col-lg-2 d-flex justify-content-center">
                <button id="img_menu" data-bs-toggle="offcanvas" href="#offcanvasExample"><img id="liste_catégorie" src="../images/Liste_categorie.png" alt="liste catégorie" class="me-1 me-lg-3"></button>
                <button data-bs-toggle="offcanvas" href="#offcanvasExample" class="btn_prod"><p class="m-0">Tous les<br>produits</p></button>
            </div>
            
            <a class="col-lg-2 p-2 nav-link fs-4" href="./accueil.php">Accueil</a>
            <?php
                //On va chercher le nom des catégories dans la bdd
                $i=1;
                $req = "SELECT nom,id_cat FROM alizonbdd._categorie;";
                $resultat = $bdd->query($req);

                //Puis on les affichent, selon leurs nombre ils prennent une taille différente
                foreach($resultat as $row){
                    if($i<=2){
                        $nom_maj = ucfirst(strtolower($row['nom'])); 
                        echo "<div id=\"div_categorie\"class=\"col-2 col-lg-2\">";
                        echo "<form action=\"categorie.php\" method=\"get\" class=\"w-100\">";
                            
                                echo "<input type=\"hidden\" name=\"idcategorie\" value=\"".$row['id_cat']."\">";
                            
                                echo "<button class=\"nomcat w-100 p-2 fs-4 text-center\" type=\"submit\">".$nom_maj."</button>";
                            
                        echo "</form>";
                        echo "</div>";
            
                    }else if($i<=3){
                        $nom_maj = ucfirst(strtolower($row['nom']));
                        echo "<div id=\"div_categorie\"class=\"col-2 col-lg-2\">";
                        echo "<form action=\"categorie.php\" method=\"get\" class=\"\">";
                            
                                echo "<input type=\"hidden\" name=\"idcategorie\" value=\"".$row['id_cat']."\">";
                            
                                echo "<button class=\"nomcat  w-100 p-2 fs-4 text-center\" type=\"submit\">".$nom_maj."</button>";
                            
                        echo "</form>";
                        echo "</div>";                  
                    }
                    $i++;
                }
            ?>
        </div>
    </nav>
</div>

<!-- Modal avec toutes les catégories et des liens prataiques-->
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="modal1">
    <div class="offcanvas-header">
        <?php
            //Si l'utilisateur est connecté on affiche son nom
            if(isset($_SESSION['id'])){
                echo "<a href=\"./profil.php\"><img id=\"pers_blanc\" src=\"../images/personne.png\" class=\"col-2\" alt=\"logo utilisateur\"></a>";
                echo "<h5 class=\"offcanvas-title col-8\" id=\"modal1\">Bonjour, ".$_SESSION['prenom']."</h5>";
            }
            //Sinon on lui demande de se connecter 
            else{
                echo "<a href=\"./connexion.php\"><img id=\"pers_blanc\" src=\"../images/personne.png\" class=\"col-2\" alt=\"logo utilisateur\"></a>";
                echo "<h5 class=\"offcanvas-title col-8\" id=\"modal1\"><a id=\"connec_modal\"href=\"./connexion.php\" >Connectez-vous</a></h5>";
            }
        ?>
        <button type="button" class="btn-close col-2" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div>
        <h5>Tendances</h5>
                <ul>
                    <li><a href="#" class="autres-cat">Meilleures ventes</a></li>
                    <li><a href="#" class="autres-cat">Promotions</a></li>
                    <li><a href="#" class="autres-cat">Commentaires</a></li>
                </ul>
                <hr>
        <h5>Catégories</h5>
                <ul>
                    <?php
                        //Affichage de toutes les catégories de la bdd
                        $req = "SELECT nom,id_cat FROM alizonbdd._categorie;";
                        $resultat = $bdd->query($req);
                        foreach($resultat as $row){
                            $cat_maj = ucfirst(strtolower($row['nom'])); 
                            echo "<li><form action=\"categorie.php\" id=\"form-categ\" method=\"get\" class=\"\">";
                            
                                echo "<input type=\"hidden\" class=\"nomcat2\" name=\"idcategorie\" value=\"".$row['id_cat']."\">";
                            
                                echo "<button class=\"nomcat2\" type=\"submit\">".$cat_maj."</button>";
                            
                        echo "</form></li>"; 
                        }
                    ?>
                </ul>
                <hr>
        <h5>Informations et aides</h5>
            <ul>
                <li><a class="autres-cat" href="./panier.php">Votre Panier</a></li>
                <?php

                //On change les liens si l'utilisateur est connecté ou non
                if(isset($_SESSION['id'])){
                    echo '<li><a class="autres-cat" href="./profil.php">Votre compte</a></li>';
                }
                if(isset($_SESSION['id'])){
                    echo '<li><a class="autres-cat" href="./commande.php">Vos commandes</a></li>';
                }
                
                if(isset($_SESSION['id'])){
                    echo '<li><a class="autres-cat" href="./deco.php">Déconnectez-vous</a></li>';
                }
                if(!isset($_SESSION['id'])){
                    echo '<li><a class="autres-cat" href="./connexion.php">Connectez-vous</a></li>';
                }
                    ?>
            </ul>
        </div>
    </div>
</div>
