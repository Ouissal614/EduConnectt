console.log("SCRIPT ADMIN OK");

/* =========================
   GRAPHIQUE RISQUE
========================= */
const ctx = document.getElementById('riskChart');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: [
            'Élèves stables',
            'Élèves en difficulté',
            'Élèves en risque'
        ],
        datasets: [{
            data: [stable, difficulte, risque],
            backgroundColor: [
                '#22c55e',
                '#f59e0b',
                '#ef4444'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        cutout: '60%',
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

/* =========================
   POPUP
========================= */
function openPopup(type) {

    const popup = document.getElementById("popup");
    const title = document.getElementById("popup-title");
    const body  = document.getElementById("popup-body");

    popup.style.display = "flex";

    /* ── Paramètres ── */
    if(type === "parametres") {

        title.innerHTML = `<i class="fa fa-gear"></i> Paramètres`;

        body.innerHTML = `
            <div class="settings-box">
                <h3>Mode d'affichage</h3>
                <button onclick="toggleDarkMode()" class="settings-btn">
                    🌙 Mode sombre
                </button>
            </div>
        `;
    }

    /* ── Envoyer notification ── */
    else if(type === "envoyer-notif") {

        title.innerHTML = `<i class="fa fa-envelope"></i> Envoyer une notification`;

        // Construire les options select parents
        let options = `<option value="">-- Choisir un parent --</option>`;
        parents.forEach(p => {
            options += `<option value="${p.id}">${p.nom}</option>`;
        });

        body.innerHTML = `

            ${notifSuccess ? `
                <div style="background:#f0fff4;border:1px solid #9ae6b4;
                            border-radius:8px;padding:10px 14px;color:#276749;
                            margin-bottom:14px;font-size:13px;text-align:center;">
                    ✅ ${notifSuccess}
                </div>` : ''}

            ${notifErreur ? `
                <div style="background:#fff5f5;border:1px solid #fc8181;
                            border-radius:8px;padding:10px 14px;color:#c53030;
                            margin-bottom:14px;font-size:13px;text-align:center;">
                    ❌ ${notifErreur}
                </div>` : ''}

            <form method="POST" style="display:flex;flex-direction:column;gap:14px;margin-top:10px;">

                <div>
                    <label style="font-size:13px;font-weight:600;
                                  color:#374151;display:block;margin-bottom:5px;">
                        Destinataire (parent)
                    </label>
                    <select name="id_parent" required
                        style="width:100%;padding:10px 12px;
                               border:1.5px solid #e5e7eb;border-radius:8px;
                               font-size:14px;font-family:inherit;background:#f9fafb;">
                        ${options}
                    </select>
                </div>

                <div>
                    <label style="font-size:13px;font-weight:600;
                                  color:#374151;display:block;margin-bottom:5px;">
                        Type de notification
                    </label>
                    <select name="type"
                        style="width:100%;padding:10px 12px;
                               border:1.5px solid #e5e7eb;border-radius:8px;
                               font-size:14px;font-family:inherit;background:#f9fafb;">
                        <option value="alerte">🔴 Alerte</option>
                        <option value="info">🔵 Information</option>
                        <option value="avertissement">🟠 Avertissement</option>
                    </select>
                </div>

                <div>
                    <label style="font-size:13px;font-weight:600;
                                  color:#374151;display:block;margin-bottom:5px;">
                        Message
                    </label>
                    <textarea name="message" required
                        placeholder="Écrivez votre message ici..."
                        style="width:100%;padding:10px 12px;
                               border:1.5px solid #e5e7eb;border-radius:8px;
                               font-size:14px;font-family:inherit;background:#f9fafb;
                               min-height:100px;resize:vertical;"></textarea>
                </div>

                <button type="submit" name="envoyer_notif"
                    style="padding:12px;
                           background:linear-gradient(135deg,#1e3c72,#2a5298);
                           color:white;border:none;border-radius:8px;
                           font-size:15px;font-weight:600;cursor:pointer;
                           transition:all 0.2s;">
                    <i class="fa fa-paper-plane"></i> Envoyer
                </button>

            </form>
        `;
    }

    /* ── Rapport ── */
    else if(type === "rapport") {
        title.innerHTML = `<i class="fa fa-chart-column"></i> Rapport`;
        body.innerHTML  = `<p>Fonctionnalité en cours de développement.</p>`;
    }

    /* ── Autres ── */
    else {
        title.innerHTML = type;
        body.innerHTML  = `<p>Contenu : ${type}</p>`;
    }
}

function closePopup() {
    document.getElementById("popup").style.display = "none";
}

/* =========================
   MODE SOMBRE
========================= */
function toggleDarkMode() {
    document.body.classList.toggle("dark-mode");
    localStorage.setItem(
        "darkMode",
        document.body.classList.contains("dark-mode")
    );
}

/* =========================
   FERMER EN CLIQUANT DEHORS
========================= */
window.onclick = function(event) {
    const popup = document.getElementById("popup");
    if(event.target === popup) closePopup();
};

/* =========================
   INITIALISATION
========================= */
window.onload = function() {

    // Restaurer le mode sombre
    if(localStorage.getItem("darkMode") === "true") {
        document.body.classList.add("dark-mode");
    }

    // Ouvrir le popup si un message existe après envoi
    if(notifSuccess || notifErreur) {
        openPopup("envoyer-notif");
    }
};