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
        <title>Modification compte client | ALIZON</title>
        <link rel="icon" href="../images/favicon.ico" />

    </head>



    <body>
        <?php
        include("header.php");
        $bdd->exec("set schema 'alizonbdd';");
        ?>
        <main>
                <div id="container_form" class="container">
                    <div class="row justify-content-center">
                        <h1 id="h1_cc">Modification </h1>
                        <form id="form_connex" class="col-10 col-md-7 needs-validation" action="" method="post">
                            
                        <?php
                            if(isset($_POST['emailF'])){
                                try{
                                    $up=$bdd->prepare("UPDATE _client SET nom=?, prenom=?, email=?, telephone=?, rue=?, code=?, ville=? where id_client=?;");
                                    $up->execute([$_POST['nomF'],$_POST['prenomF'],$_POST['emailF'],$_POST['telF'],$_POST['rueF'],$_POST['codeF'],$_POST['villeF'],$_POST['idclient']]);
                                }catch(PDOException $e){
                                    echo "Erreur ! : " . $e->getMessage() . "<br/>";
                                    die();
                                }
                                echo'<script>alert("modification réussie");</script>';
                            
                            }
                            foreach($bdd->query("SELECT * FROM _client WHERE id_client =". $_POST['idclient'].";") as $client){
                                ?>
                                    <form id="form_connex" action="detail_client.php" method="post">
                                        <?php
                                            echo "<input type=\"hidden\" name=\"idclient\" value=\"".$_POST['idclient']."\">";
                                        ?>



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
                                            <div class="row mb-3">
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
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="flexCheckIndeterminate">
                                                <label class="form-check-label" for="flexCheckIndeterminate">
                                                    Modification du mot de passe
                                                </label>
                                            </div>
                                            
                                            <button id="b_val" type="button" class="btn d-grid col-6 mx-auto" data-bs-toggle="modal" data-bs-target="#exampleModal">Modifier</button>
                                        
                                            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Êtes-vous certains de vouloir modifier?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                            <button type="submit" class="btn btn-primary">Confirmer</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </form>
                        <?php            
                            }
                        ?>
            </main>
    

        <?php
            include("footer.php");
        ?>
    </body>
</html>
