<!DOCTYPE HTML>
<html>
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
        <title>Creation compte client</title>
        <link rel="icon" href="../images/favicon.ico" />

    </head>
    <body id="body">

    <?php
        include("./header.php");
        //si l'utilisateur a saisi son mail mais pas encore sa réponse
        if(isset($_GET['mailF']) && !isset($_GET['repF'])){
            //on vérifie si il existe un compte lié à ce mail
            $reqmail = $bdd->prepare("SELECT email FROM alizonbdd._client WHERE email = ?;");
            $reqmail->execute(array($_GET['mailF']));
            $mailexist = $reqmail->rowCount();
            if($mailexist == 0) {
                //si il n'y en a pas, on donne une valeur à la valeur exist
                $exist=0;
            }else{
                //sinon on récupère sa question pour pouvoir lui la demander
                $reqQuest = $bdd->prepare("SELECT quest FROM alizonbdd._client inner join alizonbdd._question on alizonbdd._client.id_quest=alizonbdd._question.id_quest WHERE email = ?;");
                $reqQuest->execute(array($_GET['mailF']));
                $quest = $reqQuest->fetch();//variable contenant la question ($quest['quest'])
            }
        }
        //sil'utilisateur a saisi sa réponse
        if(isset($_GET['repF'])){
            //on la compare à celle enregistré lors de la création de son compte
            $reqrep = $bdd->prepare("SELECT reponse FROM alizonbdd._client WHERE email = ?;");
            $reqrep->execute(array($_GET['mailF']));
            $reponse = $reqrep->fetch();
            //la réponse etant hashé on doit utiliser password_verify
            if(password_verify($_GET['repF'],$reponse['reponse'])){
                
            }else{
                //si les réponses ne correspondent pas, on initialise la variable repFausse
                $repFausse=0;
            }
        }
        //si il a saisi son nouveau mot de passe...
        if(isset($_GET['pswF'])){
            //et qu'il l'a rentré correctement lors dans le champ de confirmation
            if($_GET['pswF']==$_GET['pswFConf']){
                //on hash le mot de passe
                $psw=password_hash($_GET['pswF'], PASSWORD_DEFAULT);
                //puis on l'envoie dans la bdd
                $reqrep = $bdd->prepare("UPDATE alizonbdd._client SET mdp=? WHERE email = ?;");
                $reqrep->execute(array($psw,$_GET['mailF']));
            }else{
                //sinon on initialise la variable mdpFaux
                $mdpFaux=0;
            }
        }
    ?>

        <main id="recup_mdp">
            <h1 class="h1_recup_mdp" >Récupération de votre mot de passe</h1>
            <section class="cadre">
                
                <?php
                    //si l'utilisateur n'a pas encore saisi son mail ou que son mail ne correspond à aucun compte existant
                    if(!isset($_GET['mailF']) || isset($exist)){
                        //si son mail ne correspond à aucun compte existant
                        if(isset($exist)){
                ?>
                    <!--On affiche le formulaire de demande de mail avec son mail "faux" prérempli avec le champ affiché comme invalide-->
                    <form action="recup_mdp.php" id="form_connex" class="needs-validation" method="get">
                        <h2>Saisissez votre adresse mail :</h2>
                        <div class="form-floating mb-3">
                            <?php
                            echo"<input class=\"form-control form-control-lg is-invalid\" value=\"".$_GET['mailF']."\" type=\"text\" id=\"mail\" name=\"mailF\" placeholder=\"mail\" required>";
                            ?>
                            <label for="mail">mail</label>
                            <span style="color: red;">Cette adresse mail n'existe pas</span>
                        </div>
                        <input class="btn btn-primary" type="submit" value="Continuer">
                    </form>
                <?php
                        }else{
                            //sinon on affiche le formulaire de demande de mail vide
                ?>
                    <form action="recup_mdp.php" id="form_connex" class=" needs-validation" method="get">
                        <h2>Saisissez votre adresse mail :</h2>
                        <div class="form-floating mb-3">
                            <input class="form-control form-control-lg" type="text" id="mail" name="mailF" placeholder="mail" required>
                            <label for="mail">mail</label>
                        </div>
                        <input class="btn btn-primary" type="submit" value="Continuer">
                    </form>
                <?php
                        }
                    //sinon si il n'a pas encore saisi sa réponse ou qu'il n'a pas saisi la bonne réponse ET qu'il n'a pas encore saisi son mot de passe
                    }else if((!isset($_GET['repF']) || isset($repFausse)) && !isset($_GET['pswF'])){
                        //si il a saisi une réponse fausse
                        if(isset($repFausse)){
                ?>
                    <!--On affiche le formulaire de question secrète avec la réponse saisi précédemment par l'utilisateur avec le champ affiché comme invalid-->
                    <form action="recup_mdp.php" id="form_connex" class=" needs-validation" method="get">
                        <?php
                        //on doit garder le mail saisi par l'utilisateur jusqu'à la fin
                        echo "<input type=\"hidden\" name=\"mailF\" value=\"".$_GET['mailF']."\">";

                        echo "<h2>Répondez à votre question :</h2>";
                        //On affiche ici la question qu'a choisi l'utilisateur
                        echo "<h3>".$quest['quest']."</h3>";
                        ?>
                        <div class="form-floating mb-3">
                            <?php
                            echo"<input class=\"form-control form-control-lg is-invalid\" type=\"text\" id=\"rep\" name=\"repF\" value=\"".$_GET['repF']."\" placeholder=\"Réponse\" required>";
                            ?>
                            <label for="rep">Réponse</label>
                            <span style="color: red;">Ce n'est pas la bonne réponse</span>
                        </div>
                        <input class="btn btn-primary" type="submit" value="Continuer">
                    </form>
                <?php
                        }else{
                        //sinon, on affiche le formulaire de question secrète vide
                ?>
                    <form action="recup_mdp.php" id="form_connex" class=" needs-validation" method="get">
                        <?php
                        //on doit garder le mail saisi par l'utilisateur jusqu'à la fin
                        echo "<input type=\"hidden\" name=\"mailF\" value=\"".$_GET['mailF']."\">";

                        echo "<h2>Répondez à votre question :</h2>";
                        //On affiche ici la question qu'a choisi l'utilisateur
                        echo "<h3>".$quest['quest']."</h3>";
                        ?>
                        <div class="form-floating mb-3">
                            <input class="form-control form-control-lg" type="text" id="rep" name="repF" placeholder="Réponse" required>
                            <label for="rep">Réponse</label>
                        </div>
                        <input class="btn btn-primary" type="submit" value="Continuer">
                    </form>
                <?php
                        }
                    //sinon si le mot de passe n'a pas encore été saisi ou que le mot de passe saisie et la confirmation ne correspondent pas
                    }else if(!isset($_GET['pswF']) || isset($mdpFaux)){
                        //si le mot de passe et la confirmation ne correspondent pas
                        if(isset($mdpFaux)){
                ?>
                    <!--On affiche le formulaire de saisie du nouveau mot de passe avec les champs affichés comme invalides-->
                    <form action="recup_mdp.php" id="form_connex" class=" needs-validation" method="get">
                        <h2>Saisissez votre nouveau mot de passe</h2>
                        <?php
                        //on doit garder le mail saisi par l'utilisateur jusqu'à la fin
                        echo "<input type=\"hidden\" name=\"mailF\" value=\"".$_GET['mailF']."\">";
                        ?>
                        <div class="form-floating mb-3">
                            <input class="form-control form-control-lg is-invalid" type="password" id="psw" name="pswF" placeholder="Mot de passe" required>
                            <label for="rep">Mot de passe</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control form-control-lg is-invalid" type="password" id="pswConf" name="pswFConf" placeholder="Confirmation" required>
                            <label for="rep">Confirmation</label>
                        </div>
                        <p style="color: red;">Vos saisies ne correspondent pas</p>
                        <input class="btn btn-primary" type="submit" value="Continuer">
                    </form>
                <?php
                        }else{
                            //sinon on affiche le formulaire de saisie de nouveau mot de passe vide
                ?>
                    <form action="recup_mdp.php" id="form_connex" class=" needs-validation" method="get">
                        <h2>Saisissez votre nouveau mot de passe</h2>
                        <?php
                        //on doit garder le mail saisi par l'utilisateur jusqu'à la fin
                        echo "<input type=\"hidden\" name=\"mailF\" value=\"".$_GET['mailF']."\">";
                        ?>
                        <div class="form-floating mb-3">
                            <input class="form-control form-control-lg" type="password" id="psw" name="pswF" placeholder="Mot de passe" minlength="8" required>
                            <label for="rep">Mot de passe</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control form-control-lg" type="password" id="pswConf" name="pswFConf" placeholder="Confirmation" minlength="8" required>
                            <label for="rep">Confirmation</label>
                        </div>
                        <input class="btn btn-primary" type="submit" value="Continuer">
                    </form>
                <?php
                        }
                        //sinon (=l'utilisateur a saisie un nouveau mot de passe valide)
                    }else{
                ?>
                    <section id="modif_mdp_resussie" >
                        <h2>Modification du mot de passe enregistré</h2>
                        <form action="./connexion.php" method="get">
                            <?php
                            //on envoie le mail une dernière fois pour le préremplir lors de la connexion
                            echo "<input type=\"hidden\" name=\"email\" value=\"".$_GET['mailF']."\">";
                            ?>
                            <input type="submit" class="btn btn-primary" value="Se connecter">
                        </form>
                    </section>
                <?php
                    }
                ?>

            </section>
        </main>
        <?php
        include("./footer.php");
        ?>
    </body>
</html>