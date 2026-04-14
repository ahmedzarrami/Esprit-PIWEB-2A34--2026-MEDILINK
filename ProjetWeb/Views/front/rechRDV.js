/**
 * rechRDV.js — Recherche de médecins par ville ou spécialité
 * Injecte dynamiquement l'interface dans <div id="rechSection">
 */

const MEDECINS = [
    { nom: "Dr. Ahmed",   spec: "Cardiologue",  ville: "Tunis",    note: "4.9", exp: "12 ans", initials: "AH", cardId: "card-ahmed",   avatarBg: "#eff4ff", avatarColor: "#1a56db" },
    { nom: "Dr. Sara",    spec: "Dermatologue",  ville: "Sfax",     note: "4.8", exp: "8 ans",  initials: "SA", cardId: "card-sara",    avatarBg: "#ecfdf5", avatarColor: "#0da271" },
    { nom: "Dr. Youssef", spec: "Dentiste",      ville: "Tunis",    note: "4.7", exp: "10 ans", initials: "YO", cardId: "card-youssef", avatarBg: "#fff7ed", avatarColor: "#e05a2b" },
];

document.addEventListener("DOMContentLoaded", function () {
    const section = document.getElementById("rechSection");
    if (!section) return;
    injectHTML(section);
    bindEvents();
    renderResults(MEDECINS);
});

function injectHTML(container) {
    const specialites = [...new Set(MEDECINS.map(m => m.spec))];
    container.innerHTML = `
        <div class="rech-card">
            <div class="rech-filters">
                <div class="rech-form-group">
                    <label class="rech-label">Spécialité</label>
                    <select id="rechSpec" class="rech-input">
                        <option value="">Toutes les spécialités</option>
                        ${specialites.map(s => `<option value="${s}">${s}</option>`).join("")}
                    </select>
                </div>
                <div class="rech-form-group">
                    <label class="rech-label">Ville</label>
                    <input type="text" id="rechVille" class="rech-input" placeholder="Ex: Tunis, Sfax…">
                </div>
                <button id="rechReset" class="btn-rech-reset">Réinitialiser</button>
            </div>
            <div class="rech-tags" id="rechTags">
                <button class="rech-tag active" data-spec="">Tous</button>
                ${specialites.map(s => `<button class="rech-tag" data-spec="${s}">${s}</button>`).join("")}
            </div>
            <div class="rech-info" id="rechInfo"></div>
            <div class="rech-grid" id="rechGrid"></div>
        </div>`;
}

function renderResults(list) {
    const grid = document.getElementById("rechGrid");
    const info = document.getElementById("rechInfo");
    if (!grid) return;

    info.innerHTML = list.length
        ? `<strong>${list.length}</strong> médecin${list.length > 1 ? "s" : ""} trouvé${list.length > 1 ? "s" : ""}`
        : "";

    if (!list.length) {
        grid.innerHTML = `<div class="rech-empty"><div class="rech-empty-icon">🔍</div><strong>Aucun résultat</strong>Essayez une autre ville ou spécialité.</div>`;
        return;
    }

    grid.innerHTML = list.map(m => `
        <div class="rech-doc-card">
            <div class="rech-avatar" style="background:${m.avatarBg};color:${m.avatarColor}">${m.initials}</div>
            <div class="rech-doc-name">${m.nom}</div>
            <div class="rech-doc-spec">${m.spec}</div>
            <div class="rech-doc-city">📍 ${m.ville}</div>
            <div class="rech-doc-meta">
                <div class="rech-meta-item"><strong>${m.note}</strong>Note</div>
                <div class="rech-meta-item"><strong>${m.exp}</strong>Exp.</div>
            </div>
            <button class="btn-rech-rdv" onclick="rechSelectMedecin('${m.nom}','${m.spec}','${m.cardId}')">Prendre RDV</button>
        </div>`).join("");
}

function filterAndRender() {
    const spec  = document.getElementById("rechSpec").value.trim().toLowerCase();
    const ville = document.getElementById("rechVille").value.trim().toLowerCase();
    const filtered = MEDECINS.filter(m => {
        const matchSpec  = !spec  || m.spec.toLowerCase().includes(spec);
        const matchVille = !ville || m.ville.toLowerCase().includes(ville);
        return matchSpec && matchVille;
    });
    renderResults(filtered);
}

function setActiveTag(specValue) {
    document.querySelectorAll(".rech-tag").forEach(tag => {
        tag.classList.toggle("active", tag.dataset.spec === specValue);
    });
    document.getElementById("rechSpec").value = specValue;
    filterAndRender();
}

function bindEvents() {
    document.getElementById("rechSpec").addEventListener("change", function () { setActiveTag(this.value); });
    document.getElementById("rechVille").addEventListener("input", filterAndRender);
    document.getElementById("rechReset").addEventListener("click", function () {
        document.getElementById("rechSpec").value  = "";
        document.getElementById("rechVille").value = "";
        setActiveTag("");
    });
    document.getElementById("rechTags").addEventListener("click", function (e) {
        const tag = e.target.closest(".rech-tag");
        if (tag) setActiveTag(tag.dataset.spec);
    });
}

function rechSelectMedecin(nom, spec, cardId) {
    if (typeof selectMedecin === "function") selectMedecin(nom, spec, cardId);
    const formCard = document.querySelector(".form-card");
    if (formCard) formCard.scrollIntoView({ behavior: "smooth", block: "start" });
}