<!--telephone-->
<div class="form-floating mb-3" id="cases_connex" style="margin-top:50px;">
    <?php
        if(isset($_POST['telF'])){
            echo "<input type=\"tel\" class=\"form-control form-control-lg\" id=\"tel\" name=\"telF\" value=\"" . $_POST['telF'] . "\" placeholder=\"Numéro de téléphone\" pattern=\"^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$\" required>";
        }else{
            echo "<input type=\"tel\" title=\"n° invalide\" class=\"form-control form-control-lg\" id=\"tel\" name=\"telF\" placeholder=\"Numéro de téléphone\" pattern=\"^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$\" required>";
        }
    ?>
    <label for=tel>Numéro de téléphone</label>
        <span id="telErr"></span>
</div>