<?php
    session_start();
?>
<html>
    <head>
    <meta name="viewport"> 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <title>Connexion | ALIZON</title>
    <link rel="icon" href="../images/favicon.ico" />
    </head>

    <body>
        <?php
        echo "
        <article id=\"connexion\">
            <h2 id=\"h2_connexion\">Connexion</h2>
            <div id=\"cadre\" >
                <form action=\"test_connexion_vendeur.php\" method=\"POST\">
                    
                    ";
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
                    echo "
                    <div id=\"form_connexion\">
                        <div>
                            <input class=\"form_connexion_mdp\" type=\"password\"  name=\"mdp\" id=\"password-input\" placeholder=\"Mot de passe\" required>
                            <img class=\"fa fa-eye\" id=\"password-toggle\" src=\"../images/oeil.png\" for=\"password-input\" id=\"\" >
                        </div>

                        <a href=\"./recup_mdp.php\" class=\"mdp_oublie\">Mot de passe oublié?</a>
                    </div>
                
                <ul id=\"liste_connexion\" >
                    <li><a href=\"./connexion_vendeur.php\"><button type=\"submit\" class=\"\">Se connecter</button></a></li>
                </ul>
                
                </form>
            </div>
        </article>  ";
        ?>

        <script>
            let passwordInput = document.getElementById("password-input");
            let passwordToggle = document.getElementById("password-toggle");
    
            passwordToggle.addEventListener("click", function() {
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                passwordToggle.src = "../images/les-yeux-croises.png";
    
    
            } else {
                passwordInput.type = "password";
    
                passwordToggle.src = "../images/oeil.png";
            }
            });
        </script>
        
        </body>
    
    
    </html>
