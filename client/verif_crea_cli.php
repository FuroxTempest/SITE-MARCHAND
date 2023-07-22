<?php
    $cookie_panier = unserialize($_COOKIE['panier']);

    setcookie('panier');
    unset($_COOKIE['panier']);
?>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../css/style.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
        <title>Creation compte client</title>
        <link rel="icon" href="../images/favicon.ico" />

    </head>
    
    <body style="background-color:var(--background_body_color);">
        <?php
            include("./header.php");
        ?>

<?php
        //on récupère les infos clients
        $nom=$_POST['nomF'];
        $prenom=$_POST['prenomF'];
        $mail=$_POST['emailF'];
        $tel=$_POST['telF'];
        $rue=$_POST['rueF'];
        $code=$_POST['codeF'];
        $ville=$_POST['villeF'];
        $psw=$_POST['pswF'];
        $quest=$_POST['questF'];
        $rep=$_POST['repF'];
        //on hash le mot de passe
        $psw = password_hash($psw, PASSWORD_DEFAULT);
        $rep = password_hash($rep, PASSWORD_DEFAULT);
        //on récupère les potentielles infos de la bdd
        $reqmail = $bdd->prepare("SELECT email FROM alizonbdd._client WHERE email = ?;");
        $reqmail->execute(array($mail));
        $mailexist = $reqmail->rowCount();
        //si on ne récupère rien de la bdd
        if($mailexist == 0) {
            //on peut ajouter le nouveau client à la BDD
            try{
                $insertmbr = $bdd->prepare("INSERT INTO alizonbdd._client(nom, prenom, email, rue, code, ville, telephone, mdp, id_quest, reponse) VALUES(?,?,?,?,?,?,?,?,?,?);");
                $insertmbr->execute([$nom, $prenom, $mail, $rue, $code, $ville, $tel, $psw,$quest,$rep]);
                $erreur = "
                    <div>
                        <p >Votre compte a bien été créé !</p>
                        <form action=\"./accueil.php\"> <input  type=\"submit\" value=\"Accueil\">  </form>
                    </div>";
                    //on lui démarre une session avec diverses infos importantes
                session_start();
                $_SESSION['nom']=$nom;
                $_SESSION['prenom']=$prenom;
                $_SESSION['adresseEmail']=$mail;
                $_SESSION['motDePasse']=$psw;

                //on récupère l'id client (on s'en sert pour vérifier la connexion et récupérer les informations de la bdd)
                $reqID = $bdd->prepare("SELECT id_client FROM alizonbdd._client WHERE email = ?;");
                $reqID->execute(array($mail));
                $id_cli=$reqID->fetch();
                $_SESSION['id']=$id_cli['id_client'];

                foreach ($cookie_panier as $key) {

                    $qtt=$key[1];

                    // processus d'ajout
                    $nom=$_SESSION['nom'];
                    $req1=$bdd->prepare("SELECT id_client from alizonbdd._client where nom=?;");
                    $req1->execute([$nom]);
                    $client=$req1->fetch();
                    $idCli=$client['id_client'];


                    $req2=$bdd->prepare("SELECT id_commande from alizonbdd._commande where id_client =?  and statut_commande='en cours';");
                    $req2->execute([$idCli]);
                    $row=$req2->rowCount();
                    
                    
                    if($row==0){

                        $reqAddCom =$bdd->prepare("INSERT into alizonbdd._commande(prix_final,statut_commande,id_client,date_commande) VALUES (0,'en cours',?,now());");


                        $reqAddCom->execute([$idCli]);
                
                    }
                    
                    $req3=$bdd->prepare("SELECT id_commande from alizonbdd._commande where id_client =? and statut_commande='en cours';");
                    $req3->execute([$idCli]);
                    $commande=$req3->fetch();
                    $idCom=$commande['id_commande'];
                
                    $req4=$bdd->prepare("SELECT prix_ttc from alizonbdd._produit where id_produit =?;");
                    $req4->execute([$key[0]]);
                    $prix=$req4->fetch();
                    $prixTot=$prix['prix_ttc'];

                    $idProd=$_GET['id_prod'];
                    $req5=$bdd->prepare("SELECT nb_article from alizonbdd._panier where id_commande=? and id_produit=?;");
                    $req5->execute([$idCom,$key[0]]);
                    $row2=$req5->rowCount();
                    
                    if($row2==0){
                        $insertPan=$bdd->prepare("INSERT INTO alizonbdd._panier(id_commande,id_produit, nb_article, prix_total, reduction_totale) VALUES (?,?,?,?,?)");
                        $insertPan-> execute([$idCom,$key[0],$qtt,$qtt*$prixTot,0]);
                    }else{
                        $liArt=$req5->fetch();
                        $nbArt=$liArt['nb_article'];
                        $nbArt=$nbArt+$qtt;

                        $updatePan=$bdd->prepare("UPDATE alizonbdd._panier SET nb_article=?,prix_total=? WHERE id_commande=? and id_produit=? ;");
                        $updatePan->execute([$nbArt,$nbArt*$prixTot,$idCom,$key[0]]);
                    }
                
                }

                


            }catch (PDOException $e){
                echo "Erreur ! : " . $e->getMessage() . "<br/>";
                die();
            }
            ?>
            <?php
            
        //sinon
        } else {
            //le mail est déjà utilisé donc on affiche à l'utilisateur un message d'erreur et on renvoie les informations dans le formulaire
            $erreur = "
                <div>
                    <p >Adresse mail déjà utilisée !</p>
                    <form action=\"./creation_client.php\" method=\"POST\" name=\"form_cli\">
                        <input type=hidden name=\"nomF\" value=\"" . $_POST['nomF'] . "\">
                        <input type=hidden name=\"prenomF\" value=\"" . $_POST['prenomF'] . "\">
                        <input type=hidden name=\"telF\" value=\"" . $_POST['telF'] . "\">
                        <input type=hidden name=\"rueF\" value=\"" . $_POST['rueF'] . "\">
                        <input type=hidden name=\"codeF\" value=\"" . $_POST['codeF'] . "\">
                        <input type=hidden name=\"villeF\" value=\"" . $_POST['villeF'] . "\">
                        <input type=hidden name=\"emailF\" value=\"" . $_POST['emailF'] . "\">
                        <input type=hidden name=\"mailD\" value=\"" . $_POST['emailF'] . "\">
                        <input type=hidden name=\"questF\" value=\"" . $_POST['questF'] . "\">
                        <input type=hidden name=\"repF\" value=\"" . $_POST['repF'] . "\">
                        <button id=\"b_val\" type=\"submit\" >Retour</button>
                    </form>
                </div>";
        }
    ?>
        <section id="section_verif_crea" >
            <p>
                <?php
                    //on affiche le message à l'utilisateur
                    echo $erreur;
                ?>
            </p>
        </section>
        <?php
            include("./footer.php");
        ?>
    </body>
</html>
