<!--adresse-->
<div id="adresse" style="margin-top:50px;" >
    <?php
        if(isset($_POST['rueF'])){
            echo "<div class=\"col form-floating\">";
            echo "<input type=\"text\" class=\"form-control form-control-lg\" id=\"rue\" name=\"rueF\" value=\"" . $_POST['rueF'] . "\" placeholder=\"Numéro & rue\" required>";
            echo "<label for=\"rue\">Numéro & rue</label>";
            echo "<span id=\"rueErr\"></span>";
            echo "</div>";
            echo "<div class=\"col form-floating\">";
            echo "<input type=\"text\" class=\"form-control form-control-lg\" id=\"code\" name=\"codeF\" value=\"" . $_POST['codeF'] . "\" placeholder=\"code postal\" required>";
            echo "<label for=\"code\">Code postal</label>";
            echo "<span id=\"codeErr\"></span>";
            echo "</div>";
            echo "<div class=\"col-md form-floating\" >";
            echo "<input type=\"text\" class=\"form-control form-control-lg\" id=\"ville\" style=\"width:100%;\" name=\"villeF\" value=\"" . $_POST['villeF'] . "\" placeholder=\"Nville\" required>";
            echo "<label for=\"ville\">Ville</label>";
            echo "<span id=\"villeErr\"></span>";
            echo "</div>";
        }else{
            echo "<div class=\"col form-floating\">";
            echo "<input type=\"text\" class=\"form-control form-control-lg\" id=\"rue\" name=\"rueF\" placeholder=\"Numéro & rue\" required>";
            echo "<label for=\"rue\">Numéro & rue</label>";
            echo "<span id=\"rueErr\"></span>";
            echo "</div>";
            echo "<div class=\"col form-floating\">";
            echo "<input type=\"text\" class=\"form-control form-control-lg\" id=\"code\" name=\"codeF\" placeholder=\"code postal\" required>";
            echo "<label for=\"code\">Code postal</label>";
            echo "<span id=\"codeErr\"></span>";
            echo "</div>";
            echo "<div class=\"col form-floating\" >";
            echo "<input type=\"text\" class=\"form-control form-control-lg\" id=\"ville\" name=\"villeF\" placeholder=\"Nville\" style=\"width:100%;\" required>";
            echo "<label for=\"ville\">Ville</label>";
            echo "<span id=\"villeErr\"></span>";
            echo "</div>";
        }
    ?>
</div>