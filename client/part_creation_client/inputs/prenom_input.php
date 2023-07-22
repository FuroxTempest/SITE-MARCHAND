<!--Prenom-->
<div class="form-floating mb-3" >
    <?php
        if(isset($_POST['prenomF'])){
            echo "<input type=\"text\" class=\"form-control form-control-lg\" id=\"prenom\" name=\"prenomF\" value=\"" . $_POST['prenomF'] . "\" placeholder=\"Prénom\" required>";
        }else{
            echo "<input type=\"text\" class=\"form-control form-control-lg\" id=\"prenom\" name=\"prenomF\" placeholder=\"Prénom\" required>";
        }
    ?>
    <label for=prenom>Prénom</label>
    <span id="prenomErr"></span>
</div>