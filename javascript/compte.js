document.onclick = function(event) {
    var target = event.target;
    var div = document.getElementById("nav_compte");
    var divParent = div.parentNode;
    if (!divParent.contains(target) && target != div && target.tagName != "A") {
        div.style.display = "none";
    }
}