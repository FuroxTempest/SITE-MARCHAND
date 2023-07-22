// constantes pour cibler les différents éléments de la bannière
const banniere_cookies = document.getElementById("cgu-modal");
const btn_cookies = document.getElementById("btn_accepter");
const btn_fermer = document.getElementById("fermer_cookies");
const mask = document.getElementById("mask");

// évenement au clic sur le bouton "accepter"
btn_cookies.addEventListener("click", () => {
    banniere_cookies.classList.remove("active");
    document.cookie = "cookiesBannerAccepted=true; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/";
});

// évenement au clic sur le bouton "fermer"
btn_fermer.addEventListener("click", () => {
    banniere_cookies.classList.remove("active");
});

// affichage de la bannière si les cgu & cgv ne sont pas acceptées
setTimeout(() => {
    if (!document.cookie.includes("cookiesBannerAccepted=true")) {
        banniere_cookies.classList.add("active");
    }
}, 1000);
