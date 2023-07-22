<!--mot de passe et confirmation-->
<?php
    //si le mdp est différent de celui de confirmation
    if(isset($_POST['pswF']) && $_POST['pswF']!=$_POST['pswFConf']){
            echo"<div class=\"form-floating mb-3\" id=\"cases_connex\" style=\"margin-top:50px;\">";
            echo "<input type=\"password\" class=\"form-control form-control-lg is-invalid\" id=\"psw\" name=\"pswF\" placeholder=\"Mot de passe\" minlength=\"8\" required>";
            echo "<label for=psw>Mot de passe</label>";
            echo "<span id=\"pswErr\"></span>";
            echo "<span id=\"pswMajErr\"></span>";
            echo "<span id=\"pswMinErr\"></span>";
            echo "<span id=\"pswNumErr\"></span>";
            echo "<span id=\"pswSpeErr\"></span>";
            echo "</div>";

            echo"<div class=\"form-floating mb-3\" id=\"cases_connex\"  style=\"margin-top:50px;\">";
            echo "<input type=\"password\" class=\"form-control form-control-lg is-invalid\" id=\"pswConf\" name=\"pswFConf\" placeholder=\"Confirmer le mot de passe\" minlength=\"8\" required>";
            echo "<label for=pswConf>Confirmation</label>";
            echo "<span id=\"pswConfErr\"></span>";
            echo "</div>";
            //on affiche l'erreur
            echo "<div id=\"pswHelp\" class=\"form-text\" style=\"color: #FF0000;\">Vos deux mots de passes ne correspondent pas</div>";

    
    }else{
            echo "<div class=\"form-floating mb-3\" id=\"cases_connex\"  style=\"margin-top:50px;\">";
            echo "<input type=\"password\" class=\"form-control form-control-lg\" id=\"psw\" name=\"pswF\" placeholder=\"Mot de passe\" minlength=\"8\" required>";
            echo "<label for=psw>Mot de passe</label>";
            echo "<span id=\"pswErr\"></span>";
            echo "<span id=\"pswMajErr\"></span>";
            echo "<span id=\"pswMinErr\"></span>";
            echo "<span id=\"pswNumErr\"></span>";
            echo "<span id=\"pswSpeErr\"></span>";
            echo "</div>";


            echo"<div class=\"form-floating mb-3\" id=\"cases_connex\"  style=\"margin-top:50px;\">";
            echo "<input  type=\"password\" class=\"form-control form-control-lg\" id=\"pswConf\" name=\"pswFConf\" placeholder=\"Confirmer le mot de passe\" minlength=\"8\" required>";
            echo "<label for=pswConf>Confirmation</label>";
            echo "<span id=\"pswConfErr\"></span>";
            echo "</div>";
        
    }
?>