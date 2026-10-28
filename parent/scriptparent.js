// ===============================
// LANGUES
// ===============================

const translations = {

    fr: {
        title: "EduConnect - Espace Parent",
        hello: "Bonjour Parent",
        dashboard: "Tableau de bord",
        notes: "Notes",
        absences: "Absences",
        notifications: "Notifications",
        profil: "Profil",
        settings: "Paramètres",
        logout: "Déconnexion"
        
    },

    en: {
        title: "EduConnect - Parent Area",
        hello: "Hello Parent",
        dashboard: "Dashboard",
        notes: "Grades",
        absences: "Absences",
        notifications: "Notifications",
        profil: "Profile",
        settings: "Settings",
        logout: "Logout"
    },

    ar: {
        title: "EduConnect - فضاء ولي الأمر",
        hello: "مرحبا ولي الأمر",
        dashboard: "لوحة التحكم",
        notes: "النقط",
        absences: "الغيابات",
        notifications: "الإشعارات",
        profil: "الملف الشخصي",
        settings: "الإعدادات",
        logout: "تسجيل الخروج"
    }

};

// ===============================
// COOKIES
// ===============================

function setCookie(name, value, days) {

    let date = new Date();

    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));

    let expires = "expires=" + date.toUTCString();

    document.cookie =
        name + "=" + value + ";" + expires + ";path=/";
}

function getCookie(name) {

    let cname = name + "=";

    let decodedCookie = decodeURIComponent(document.cookie);

    let ca = decodedCookie.split(';');

    for(let i = 0; i < ca.length; i++) {

        let c = ca[i];

        while(c.charAt(0) == ' ') {
            c = c.substring(1);
        }

        if(c.indexOf(cname) == 0) {
            return c.substring(cname.length, c.length);
        }
    }

    return "";
}

// ===============================
// LANGUE ACTUELLE
// ===============================

let currentLang = getCookie("lang") || "fr";

// ===============================
// TRADUCTION
// ===============================

function applyLanguage() {

    document.documentElement.lang = currentLang;

    document.documentElement.dir =
        currentLang === "ar" ? "rtl" : "ltr";

    const t = translations[currentLang];

    // TOPBAR
    const title = document.querySelector(".topbar h1");

    if(title){
        title.textContent = t.title;
    }

    const hello = document.querySelector(".user span");

    if(hello){
        hello.textContent = t.hello;
    }

    // SIDEBAR
    const menu = document.querySelectorAll(".sidebar ul li");

    if(menu.length >= 7){

        menu[0].innerHTML =
            `<i class="fa fa-home"></i> ${t.dashboard}`;

        menu[1].innerHTML =
            `<i class="fa fa-chart-bar"></i> ${t.notes}`;

        menu[2].innerHTML =
            `<i class="fa fa-calendar"></i> ${t.absences}`;

        menu[3].innerHTML =
            `<i class="fa fa-bell"></i> ${t.notifications}`;

        menu[4].innerHTML =
            `<i class="fa fa-user"></i> ${t.profil}`;

        menu[5].innerHTML =
            `<i class="fa fa-cog"></i> ${t.settings}`;

        menu[6].innerHTML =
            `<a href="../authentification/logout.php" class="logout-link">
                <i class="fa fa-sign-out-alt"></i>
                ${t.logout}
            </a>`;
    }
}

// ===============================
// CHANGER LANGUE
// ===============================

function changeLanguage(lang){

    currentLang = lang;

    setCookie("lang", lang, 30);

    applyLanguage();
}

// ===============================
// POPUP
// ===============================

function openPopup(type){

    const popup = document.getElementById("popup");

    const title = document.getElementById("popup-title");

    const body = document.getElementById("popup-body");

    popup.style.display = "flex";

    // ==========================
    // NOTES
    // ==========================

    if(type === "notes"){

        title.innerHTML = `
            <i class="fa fa-chart-bar"></i>
            Notes
        `;

        body.innerHTML = `
            <p>
                Consultez toutes les notes de votre enfant
                directement dans le tableau principal.
            </p>
        `;
    }

    // ==========================
    // ABSENCES
    // ==========================

    else if(type === "absences"){

        title.innerHTML = `
            <i class="fa fa-calendar"></i>
            Absences
        `;

        body.innerHTML = `
            <p>
                Les absences seront affichées ici
                prochainement.
            </p>
        `;
    }

    // ==========================
    // NOTIFICATIONS
    // ==========================

    else if(type === "notifications"){

        title.innerHTML = `
            <i class="fa fa-bell"></i>
            Notifications
        `;

        body.innerHTML = `
            <div class="notif-item">
                <i class="fa fa-circle-info"></i>
                <span>
                    Nouvelle mise à jour des notes disponible.
                </span>
            </div>

            <div class="notif-item">
                <i class="fa fa-file"></i>
                <span>
                    Bulletin téléchargeable depuis le dashboard.
                </span>
            </div>
        `;
    }

    // ==========================
    // PROFIL
    // ==========================

    else if(type === "profil"){

        title.innerHTML = `
            <i class="fa fa-user"></i>
            Profil Parent
        `;

        body.innerHTML = `
            <p>
                Informations du parent connecté.
            </p>
        `;
    }

    // ==========================
    // PARAMETRES
    // ==========================

    else if(type === "parametres"){

        title.innerHTML = `
            <i class="fa fa-cog"></i>
            Paramètres
        `;

        body.innerHTML = `

            <div class="settings-container">

                <div class="setting-box">

                    <h4>Mode sombre</h4>

                    <button onclick="toggleDarkMode()"
                            class="popup-btn">

                        Activer / Désactiver

                    </button>

                </div>

                <div class="setting-box">

                    <h4>Langue</h4>

                    <select onchange="changeLanguage(this.value)"
                            class="lang-select">

                        <option value="fr">Français</option>

                        <option value="en">English</option>

                        <option value="ar">العربية</option>

                    </select>

                </div>

            </div>

        `;
    }
}

// ===============================
// FERMER POPUP
// ===============================

function closePopup(){

    document.getElementById("popup").style.display = "none";
}

// ===============================
// MODE SOMBRE
// ===============================

function toggleDarkMode(){

    document.body.classList.toggle("dark-mode");

    if(document.body.classList.contains("dark-mode")){

        setCookie("theme", "dark", 30);

    } else {

        setCookie("theme", "light", 30);
    }
}

// ===============================
// CLICK EXTERIEUR POPUP
// ===============================

window.onclick = function(event){

    const popup = document.getElementById("popup");

    if(event.target == popup){

        closePopup();
    }
}

// ===============================
// CHARGEMENT PAGE
// ===============================

window.onload = function(){

    applyLanguage();

    const theme = getCookie("theme");

    if(theme === "dark"){

        document.body.classList.add("dark-mode");
    }
}