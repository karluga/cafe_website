// Dark Mode Toggle with Emoji Change
const toggleBtn = document.getElementById("butt");

if (toggleBtn) {
    toggleBtn.addEventListener("click", function () {
        document.body.classList.toggle("dark-mode");

        if (document.body.classList.contains("dark-mode")) {
            localStorage.setItem("darkMode", "enabled");
            toggleBtn.textContent = "☽";   // Moon when dark mode is ON
        } else {
            localStorage.removeItem("darkMode");
            toggleBtn.textContent = "☼";   // Sun when dark mode is OFF
        }
    });
}

// Load saved dark mode preference + set correct emoji
if (localStorage.getItem("darkMode") === "enabled") {
    document.body.classList.add("dark-mode");
    if (toggleBtn) toggleBtn.textContent = "☽";
} else {
    if (toggleBtn) toggleBtn.textContent = "☼";
}

document.addEventListener("DOMContentLoaded", function() 
{

    const searchInput = document.getElementById("search");
    const cards = document.querySelectorAll(".col-md-4");

    searchInput.addEventListener("input", () => 
    {
        const query = searchInput.value.toLowerCase().trim();

        cards.forEach(card => 
        {
            const title = card.querySelector("h3").textContent.toLowerCase();
            const description = card.querySelector("p").textContent.toLowerCase();

            const matches = title.includes(query) || description.includes(query);

            card.style.display = matches ? "block" : "none";
        });
    });
});

function validateInput() 
{
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const msg = document.getElementById('message').value.trim();

    if(!name || !email || !msg) 
    {
        alert("All fields are required!");
        return false;
    }
    return true;
}

function getCurrentYear()
{
    const d = new Date();
    let year = d.getFullYear();

    document.getElementById("year").innerHTML = "©" + year + " Cafe Bastions";
}

const foot = document.getElementById("feet");

foot.addEventListener("mouseenter", function()
{
    foot.style.background = "#860303";
});
foot.addEventListener("mouseleave", function()
{
    foot.style.background = "#222";
});