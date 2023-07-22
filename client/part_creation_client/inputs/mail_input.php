<!--Mail-->
<div class="form-floating mb-3" id="cases_connex" style="margin-top:30px;">
    <?php
        //si on a récupéré le mail dans le post (si l'utilisateur a déjà rempli son mail mais qu'il a subit une erreur)
        if(isset($_POST['emailF'])){
            
            //Si l'erreur est que le mail est déjà utilisé
            if(isset($_POST['mailD'])){
                //On re-rempli le mail avec le champs indiqué comme incorrecte
                echo "<input type=\"email\" class=\"form-control form-control-lg is-invalid\" id=\"email\" name=\"emailF\" value=\"" . $_POST['emailF'] . "\" placeholder=\"nom@axemple.com\" required>";
                //On afiche cet erreur
                echo "<div id=\"mailHelp\" class=\"form-text\" style=\"color: #FF0000;\">adresse mail déjà utilisé</div>";
                //sinon
            }else{
                //On re-rempli le mail
                echo "<input type=\"email\" class=\"form-control form-control-lg\" id=\"email\" name=\"emailF\" value=\"" . $_POST['emailF'] . "\" placeholder=\"nom@axemple.com\" required>";
            }
        }else{
            echo "<input type=\"email\" class=\"form-control form-control-lg\" id=\"email\" name=\"emailF\" placeholder=\"nom@axemple.com\" required>";
        }
    ?>
    <label for=email>Adresse mail</label>
    <span for=email id=emailErr></span>
</div>