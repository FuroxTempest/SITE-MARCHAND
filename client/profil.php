<?php


session_start();

include("../php/connect_params.php");
try{
    $bdd = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass,
    [PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
    );
}catch (PDOException $e){
    print "Erreur ! : " . $e->getMessage() . "<br/>";
    die();
}
$bdd->exec("set schema 'alizonbdd';");

if ($_POST['form']=='informations') {
    $add=$bdd->prepare("UPDATE _client SET  nom=?, prenom=?, email=?, telephone=? where id_client=?;");
    $add->execute([$_POST['nom'],$_POST['prenom'],$_POST['email'],$_POST['telephone'],$_POST['id_client']]);
    $_SESSION['prenom'] = $_POST['prenom'];
    $_SESSION['adresseEmail'] = $_POST['email'];
}

if ($_POST['form']=='adresse') {
    $add=$bdd->prepare("UPDATE _client SET  rue=?, code=?, ville=? where id_client=?;");
    $add->execute([$_POST['rue'],$_POST['code'],$_POST['ville'],$_POST['id_client']]);
}





?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
        <link rel="stylesheet" href="../css/style.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400&display=swap" rel="stylesheet">
        <title>Profil | ALIZON</title>
        <link rel="icon" href="../images/favicon.ico" />

    </head>



    <body id="profil">

        <?php
            include("header.php");
        ?>
        <div class="main">

        
        <div id="petit_menu">   
            <a href="profil.php"><b>Mon compte</b></a>
            <a href="commande.php">Mes commandes</a>
            <a href="deco.php">Déconnexion</a>
        </div>

        <?php
            //On récupère les infos du client

            $id=$_SESSION['id'];
            $req1=$bdd->prepare("SELECT id_client,nom,prenom,email,rue,code,ville,telephone from alizonbdd._client where id_client=? ;");
            $req1->execute([$id]);
            $result = $req1->fetchAll();
        ?>

        <h1>Mon compte</h1>
        <main class="encadrements">
            <article class="cadre informations">
                <h2>Informations personnelles</h2>

                <div>
                    <div class="txt">
                        <?php
                        //On les affiche
                        echo '<p> ' .$result[0]['nom'].', '.$result[0]['prenom'].'</p>';
                        echo '<p> ' .$result[0]['email'].'</p>';
                        echo '<p> ' .$result[0]['telephone'].'</p>';
                        ?>
                        <!--
                             A faire un autre modal : 
                             <p><a href="recup_mdp.php">Modifier mon mot de passe </a></p>
                        -->
                    </div>
                    <div>
                        <button data-bs-toggle="modal" data-bs-target="#modal_informations" >Modifier</button>
                    </div>

                </div>


            </article>
            <article class="cadre adresse">
                <h2>Adresse de livraison</h2>
                <div>
                    <div class="txt">
                        <?php
                        echo '<p> ' .$result[0]['rue'].'</p>';
                        echo '<p> ' .$result[0]['code'].'</p>';
                        echo '<p> ' .$result[0]['ville'].'</p>';
                        ?>

                    </div>
                    <div>
                    <button data-bs-toggle="modal" data-bs-target="#modal_adresse" >Modifier</button>
                    </div>
                </div>
            </article>
        </main>
        </div>


        <div class="modal fade" id="modal_informations" tabindex="-1" aria-labelledby="modal_informations" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content ">
                <div class="modal-header">
                    <h1 class="modal-title " id="modalLabel">Informations personnelles</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="m_informations" action="profil.php" method="post">


                    <input type="hidden" name="form" value="informations">
                    <?php
                    echo "<input type=\"hidden\" name=\"id_client\" value=".$result[0]['id_client'].">";
                    ?>


                    <div>
                        <div class="form-floating mb-3" >
                            <?php
                                echo "<input type=\"text\" class=\"form-control form-control-lg\" id=\"nom\" name=\"nom\" value=\"" . $result[0]['nom'] . "\" placeholder=\"Nom \" required>";
                                
                            ?>
                            <label for=nom>Nom</label>
                        </div>

                        <div class="form-floating mb-3" >
                            <?php
                                echo "<input type=\"text\" class=\"form-control form-control-lg\" id=\"prenom\" name=\"prenom\" value=\"" . $result[0]['prenom'] . "\" placeholder=\"Prénom\" required>";
                            ?>
                            <label for=prenom>Prénom</label>
                        </div>

                        <div class="form-floating mb-3" id="cases_connex">
                            <?php

                                echo "<input type=\"email\" class=\"form-control form-control-lg\" value=\"" .$result[0]['email'] . "\" id=\"email\" name=\"email\" placeholder=\"nom@axemple.com\" required>";
                                
                            ?>
                            <label for=email>Adresse mail</label>
                        </div>

                        <div class="form-floating mb-3" id="cases_connex">
                                <?php
                                    echo "<input type=\"tel\" title=\"n° invalide\" class=\"form-control form-control-lg\" id=\"tel\" value=\"" . $result[0]['telephone'] . "\" name=\"telephone\" placeholder=\"Numéro de téléphone\" pattern=\"^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$\" required>";
                                ?>
                                <label for=tel>Numéro de téléphone</label>
                        </div>
                    </div>

                    <button type="submit">Modifier</button>
                    

                    </form>
                </div>
            </div>
        </div>
        </div>


        <div class="modal fade" id="modal_adresse" tabindex="-1" aria-labelledby="modal_adresse" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content ">
                <div class="modal-header">
                    <h1 class="modal-title " id="modalLabel">Adresse de livraison</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="m_adresse" action="profil.php" method="post">

                        <input type="hidden" name="form" value="adresse">
                        <?php
                        echo "<input type=\"hidden\" name=\"id_client\" value=".$result[0]['id_client'].">";
                        ?>

                        <div>
                            <div class="form-floating mb-3" >
                                <?php
                                    echo "<input type=\"text\" class=\"form-control form-control-lg\" id=\"rue\" name=\"rue\" value=\"" . $result[0]['rue'] . "\" placeholder=\"Rue \" required>";
                                    
                                ?>
                                <label for=nom>Rue</label>
                            </div>

                            <div class="form-floating mb-3" >
                                <?php
                                    echo "<input type=\"text\" class=\"form-control form-control-lg\" id=\"code\" name=\"code\" value=\"" .$result[0]['code']. "\" placeholder=\"Code Postale\" required>";
                                ?>
                                <label for=prenom>Code Postal</label>
                            </div>

                            <div class="form-floating mb-3" >
                                <?php
                                    echo "<input type=\"text\" class=\"form-control form-control-lg\" id=\"ville\" name=\"ville\" value=\"" .$result[0]['ville']. "\" placeholder=\"Ville\" required>";
                                ?>
                                <label for=prenom>Ville</label>
                            </div>

                        </div>

                        <button type="submit">Modifier</button>
                    

                    </form>
                </div>
            </div>
        </div>
        </div>

        <?php
            include("footer.php");
        ?>
    </body>
</html>

