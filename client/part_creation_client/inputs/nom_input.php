<!--Nom-->
<div class="form-floating mb-3" >
    <?php
        if(isset($_POST['nomF'])){
            echo "<input type=\"text\" class=\"form-control form-control-lg\" id=\"nom\" name=\"nomF\" value=\"" . $_POST['nomF'] . "\" placeholder=\"Nom \" required>";
        }else{
            echo "<input type=\"text\" class=\"form-control form-control-lg\" id=\"nom\" name=\"nomF\" placeholder=\"Nom \" required>";
        }
    ?>
    <label for=nom>Nom</label>
    <span id="nomErr"></span>
</div>