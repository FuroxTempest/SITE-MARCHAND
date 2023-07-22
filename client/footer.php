<footer>
    <div>
        <!-- Nav du footer -->
        <nav class="footer_nav">
            <a href="./accueil.php" ><img id="logo_footer" src="../images/logo_alizon.png"></a>
            <div class="footer_txt">
                <ul class="legale">
                    <h5>Informations légales</h5>
                    <li><a href="./cgu.php">Condition générale d'utilisation</a></li>
                    <li><a href="./cgv.php">Condition générale de vente</a></li>
                    <li><a href="./mentions_legales.php">Mentions légales</a></li>
                </ul>

                <ul class="compte">
                    <h5>Mon compte</h5>
                    <?php
                    //Si l'utilisateur est connecté alors on affiche cette liste
                    if(!isset($_SESSION['id'])){
                    ?>
                        <li><a href="./connexion.php">Mon profil</a></li>
                        <li><a href="./connexion.php">Mes commandes</a></li>
                        <li><a href="./connexion.php">Me connecter</a></li>
                    <?php
                    //Sinon celle-ci
                    }else{
                    ?>
                        <li><a href="./profil.php">Mon profil</a></li>
                        <li><a href="./commande.php">Mes commandes</a></li>
                        <li><a href="./deco.php">Me déconnecter</a></li>
                    <?php
                    }
                    ?>
                </ul>

                <ul class="information"> 
                    <h5>Informations</h5>
                    <li>© O'Ratio Corp 2022</li>
                    <li>Rue edouard Branly</li>
                    <li>22 300 Lannion</li>
                    <li>France</li>
                </ul>
            </div>
        </nav>
    </div>
</footer>

<!-- Flèche qui permet de revenir en haut de la page -->
<nav>
    <a id="fleche_pos" href="#navbar" class="back-to-top hidden"><img id="fleche" src="../images/fleche-vers-le-haut.png" alt="fleche"></a>
</nav>

<!-- Barre pour accepter les cookies  -->
<div id="cgu-modal" class="banniere_c">
        <p class="d-inline">En clickant sur le bouton "j'accepte", vous acceptez nos <a href="./cgu.php">conditions générales d'utilisation</a> et nos <a href="./cgv.php">conditions générales de vente</a>.</p>
        <button id="btn_accepter" class="d-inline">J'accepte</button>
        <div id="fermer_cookies" class="d-inline">
            <a href="#">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="white" class="bi bi-x-lg" viewBox="0 0 16 16">
                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/>
                </svg>
            </a>
        </div>
</div>

<!-- Script pour quela flèche ne s'affiche que lorsque l'on scroll -->
<script src="../javascript/fleche_retour.js"></script>

<!-- Script pour ne plus afficher la bannière des cookies une fois que l'utilisateur les a acceptés  -->
<script src="../javascript/banniere_cookies.js"></script>