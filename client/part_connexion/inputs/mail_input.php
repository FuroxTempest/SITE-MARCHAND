<?php
    if(isset($_GET['email'])){
        echo"
            <div id=\"form_connexion\">
                <input class=\"form_connexion\" type=\"email\" name=\"email\" value=\"".$_GET['email']."\" placeholder=\"E-mail\" required>
            </div>";
    }else{
        echo"
            <div id=\"form_connexion\">
                <input class=\"form_connexion\" type=\"email\" name=\"email\"  placeholder=\"E-mail\" required>
            </div>";
    }
?>