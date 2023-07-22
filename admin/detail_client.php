<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./style_admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400&display=swap" rel="stylesheet">
    <link rel="icon" href="../images/favicon.ico" />
    <title>Vendeur | Détail Client</title>
</head>
<body>
<nav class="navbar p-3 p-lg-2 justify-content-center">
        <!-- barre de recherche -->
        <form id="form_barre_recherche" class="d-flex col-6 border border-dark border-3 ml-2" name="form_recherche" method="get" action="recherche_client.php" role="search" >
            <input id="search" name="keyword" class="form-control" type="text" placeholder="Rechercher un client" autocomplete="off"  aria-label="Search">
            <button class="btn p-0 border-0" type="submit" id="button-addon1" name=""><img id="recherche"  src="../images/loupe.png" alt="loupe recherche" /></button>
        </form>
    </nav>
    <?php

    include("head_admin.php");


    //1) paramètre de la bdd
    include("../php/connect_params.php");
    //2) ouvrir la bdd
    $bdd = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass,
    [PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
    );
    $bdd->exec("set schema 'alizonbdd';");
    ?>
        <main>
            <?php
                if(isset($_POST['mdp'])){
                    echo"";///////////////////////////////////////////////////RECUP MOT DE PASSE///////////////////////////////////////////////
                }//On récupère les infos client
                foreach($bdd->query("SELECT nom,prenom,email,telephone,rue,code,ville FROM _client WHERE id_client = $_POST[idclient];") as $client){
                    //Si le formulaire de modification a été envoyé
                    if(isset($_POST['emailF'])){
                        //Si les valeurs sont identiques aux originales
                        if($_POST['nomF'] == $client['nom'] && $_POST['prenomF'] == $client['prenom'] && $_POST['emailF'] == $client['email'] && $_POST['telF'] == $client['telephone'] && $_POST['rueF'] == $client['rue'] && $_POST['codeF'] == $client['code'] && $_POST['villeF'] == $client['ville'])
                        {
                            //on avertit l'admin
                            echo "<script>
                                alert(\" Les informations sont identiques aux originales, aucune modification\");
                            </script>";
                        //sinon
                        }else{
                            //On remplace les anciennes valeurs pas les nouvelles
                            try{
                                $up=$bdd->prepare("UPDATE _client SET nom=?, prenom=?, email=?, telephone=?, rue=?, code=?, ville=? where id_client=?;");
                                $up->execute([$_POST['nomF'],$_POST['prenomF'],$_POST['emailF'],$_POST['telF'],$_POST['rueF'],$_POST['codeF'],$_POST['villeF'],$_POST['idclient']]);
                            }catch(PDOException $e){
                                echo "Erreur ! : " . $e->getMessage() . "<br/>";
                                die();
                            }
                        }
                        //On confirme les modifications
                        echo'<script>alert("modification réussie");</script>';
                    
                    }
                }foreach($bdd->query("SELECT nom,prenom,email,telephone,rue,code,ville FROM _client WHERE id_client = $_POST[idclient];") as $client){
                    ?>
                        <!--Le formulaire qui permet de modifier les données du client, les champs sont pré-remplis avec les infos actuelles-->
                        <form id="form_connex" action="detail_client.php" method="post">
                            <?php
                                echo "<input type=\"hidden\" name=\"idclient\" value=\"".$_POST['idclient']."\">";
                            ?>
                                <!--Mail-->
                                <div class="form-floating mb-3" id="cases_connex">
                                    <?php
                                        echo "<input type=\"email\" class=\"form-control form-control-lg\" id=\"email\" name=\"emailF\" value=\"" . $client['email'] . "\" placeholder=\"nom@axemple.com\" required>";
                                        
                                    ?>
                                    <label for=email>Adresse mail</label>
                                </div>
                                <!--Nom-->
                                <div class="form-floating mb-3" id="cases_connex">
                                    <?php
                                        echo "<input type=\"text\" class=\"form-control form-control-lg\" id=\"nom\" name=\"nomF\" value=\"" . $client['nom'] . "\" placeholder=\"Nom de famille\" required>";
                                        
                                    ?>
                                    <label for=nom>Nom</label>
                                </div>
                                <!--Prenom-->
                                <div class="form-floating mb-3" id="cases_connex">
                                    <?php
                                        echo "<input type=\"text\" class=\"form-control form-control-lg\" id=\"prenom\" name=\"prenomF\" value=\"" . $client['prenom'] . "\" placeholder=\"Prénom\" required>";
                                    ?>
                                    <label for=prenom>Prénom</label>
                                </div>
                                <!--telephone-->
                                <div class="form-floating mb-3" id="cases_connex">
                                    <?php
                                        echo "<input type=\"tel\" class=\"form-control form-control-lg\" id=\"tel\" name=\"telF\" value=\"" . $client['telephone'] . "\" placeholder=\"Numéro de téléphone\" pattern=\"^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$\" required>";
                                    ?>
                                    <label for=tel>Numéro de téléphone</label>
                                </div>
                                <!--adresse-->
                                <div class="row mb-3" id="cases_connex">
                                    <?php
                                        echo "<div class=\"col form-floating\">";
                                        echo "<input type=\"text\" class=\"form-control form-control-lg\" id=\"rue\" name=\"rueF\" value=\"" . $client['rue'] . "\" placeholder=\"Numéro & rue\" required>";
                                        echo "<label for=\"rue\">Numéro & rue</label>";
                                        echo "</div>";
                                        echo "<div class=\"col form-floating\">";
                                        echo "<input type=\"text\" class=\"form-control form-control-lg\" id=\"code\" name=\"codeF\" value=\"" . $client['code'] . "\" placeholder=\"code postal\" required>";
                                        echo "<label for=\"code\">Code postal</label>";
                                        echo "</div>";
                                        echo "<div class=\"col-md form-floating\">";
                                        echo "<input type=\"text\" class=\"form-control form-control-lg\" id=\"ville\" name=\"villeF\" value=\"" . $client['ville'] . "\" placeholder=\"Nville\" required>";
                                        echo "<label for=\"ville\">Ville</label>";
                                        echo "</div>";
                                    ?>
                                    </div>
                                <!--Le bouton n'est pas un submit, il ne sert "qu'à" ouvrir le modal-->
                                <button id="b_val" type="button" class="btn d-grid col-6 mx-auto" data-bs-toggle="modal" data-bs-target="#exampleModal">Modifier</button>
                                <!--Le modal de confirmation-->
                                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <!--Contenu du modal, les informations à préciser avant la modification-->
                                            <div class="modal-body">
                                                Êtes-vous certains de vouloir modifier?
                                            </div>
                                            <div class="modal-footer ">
                                                <!--bouton pour annuler les modifications-->
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                <!--Ce bouton est un submit-->
                                                <button type="submit" class="btn btn-primary">Confirmer</button>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    <?php
                }

            ?>



        </main>
    </body>
</html>
