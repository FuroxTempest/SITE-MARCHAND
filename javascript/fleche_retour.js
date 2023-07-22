const showOnPx = 200;
const backToTopButton = document.querySelector(".back-to-top")

const scrollContainer = () => {
return document.documentElement || document.body;
};

// quand l'utilisateur scroll, on affiche la flèche
document.addEventListener("scroll", () => {
    if (scrollContainer().scrollTop > showOnPx) {
        backToTopButton.classList.remove("hidden")
    } else {
        backToTopButton.classList.add("hidden")
    }
})