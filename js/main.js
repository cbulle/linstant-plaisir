document.addEventListener("DOMContentLoaded", function () {

// =========================================
// GESTION DU BURGER — VERSION CLASSIQUE & FLUIDE
// =========================================
const burger = document.querySelector(".burger");
const menu = document.querySelector(".main-nav");
const navList = document.querySelector(".nav-list-luxe");
const headerActions = document.querySelector(".header-actions");
const btnReserver = document.querySelector(".btn-reserver-luxe");

if (burger && menu && navList && btnReserver && headerActions) {
    // Création d'un conteneur <li> unique pour accueillir le bouton dans la liste mobile
    const liContainer = document.createElement("li");
    liContainer.id = "burger-reserver-item";
    liContainer.className = "menu-item burger-btn-container"; // Garde la cohérence de style

    function toggleMenu() {
        const isOpen = menu.classList.toggle("open");
        burger.classList.toggle("active"); // Pour l'effet de croix

        if (isOpen) {
            // Empêche le défilement de la page en arrière-plan quand le menu est ouvert
            document.body.style.overflow = "hidden";
            // On déplace le bouton "Réserver" à la fin de la liste du menu mobile
            liContainer.appendChild(btnReserver);
            navList.appendChild(liContainer);
        } else {
            document.body.style.overflow = "";
            // On remet le bouton à sa place d'origine dans le header
            headerActions.insertBefore(btnReserver, burger);
        }
    }

    burger.addEventListener("click", function (e) {
        e.stopPropagation();
        toggleMenu();
    });

    // Fermeture en cliquant sur un lien du menu (essentiel pour les ancres/pages)
    navList.addEventListener("click", function (e) {
        if (e.target.tagName === "A" && menu.classList.contains("open")) {
            toggleMenu();
        }
    });

    // Fermeture en cliquant en dehors du menu
    document.addEventListener("click", function (e) {
        if (!menu.contains(e.target) && !burger.contains(e.target) && menu.classList.contains("open")) {
            toggleMenu();
        }
    });

    // Sécurité : Redimensionnement de l'écran (> 900px)
    window.addEventListener("resize", function() {
        if (window.innerWidth > 900 && menu.classList.contains("open")) {
            headerActions.insertBefore(btnReserver, burger);
            menu.classList.remove("open");
            burger.classList.remove("active");
            document.body.style.overflow = "";
        }
    });
}
    // =========================================
    // PAGE CHAMBRE — fonctions de manipulation
    // =========================================
    function fixRoomPage() {

        var images = document.getElementById('hb_room_images');
        var tabs = document.querySelector('.hb-single-room-details, .hb_single_room_details');
        var form = document.querySelector('.wphb-single-room-booking-container');

        // 1) Créer une ligne "galerie + formulaire" côte à côte — une seule fois
        if (images && form && !images.dataset.topRowBuilt) {
            var topRow = document.createElement('div');
            topRow.className = 'hb-top-row';
            images.parentNode.insertBefore(topRow, images);
            topRow.appendChild(images);
            topRow.appendChild(form);
            images.dataset.topRowBuilt = '1';
        }

        // 2) Placer les onglets juste après cette ligne — une seule fois
        var builtRow = document.querySelector('.hb-top-row');
        if (tabs && builtRow && !tabs.dataset.tabsMoved) {
            builtRow.parentNode.insertBefore(tabs, builtRow.nextSibling);
            tabs.dataset.tabsMoved = '1';
        }

        // 3) Masquer Adultes & Enfants, fixer leurs valeurs
        ['adult_qty', 'child_qty'].forEach(function (name) {
            var input = document.querySelector('input[name="' + name + '"]');
            if (input && !input.dataset.hidden) {
                var group = input.closest('.hb-booking-room-form-group');
                if (group) group.style.display = 'none';
                input.value = name === 'adult_qty' ? '2' : '0';
                input.dataset.hidden = '1';
            }
        });

        // 4) Masquer le lien/bouton "View details"
        document.querySelectorAll('.wphb-single-room-booking-container a, .wphb-single-room-booking-container span, .wphb-single-room-booking-container button')
            .forEach(function (el) {
                var t = el.textContent.trim().toLowerCase();
                if (t.includes('view details') || t.includes('voir les détails') || t.includes('détails')) {
                    el.style.display = 'none';
                }
            });

        // 5) Repositionner la grille de produits boutique après les onglets
        var shopBlock = document.getElementById('shop-suggestions-luxe');
        var placedTabs = document.querySelector('.hb-single-room-details, .hb_single_room_details');
        if (shopBlock && placedTabs && !shopBlock.dataset.moved) {
            placedTabs.parentNode.insertBefore(shopBlock, placedTabs.nextSibling);
            shopBlock.dataset.moved = '1';
        }

        // 6) Mettre à jour le bloc prix (le plus bas de la semaine, ou prix du jour sélectionné)
        updateRoomPriceBox();
    }

    // Tentative immédiate
    fixRoomPage();

    // MutationObserver — attend le chargement AJAX de HB
    var observer = new MutationObserver(function () {
        fixRoomPage();
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    // Arrêter l'observer après 10s pour ne pas peser
    setTimeout(function () { observer.disconnect(); }, 10000);

    // Tentatives espacées en backup
    [300, 600, 1000, 2000].forEach(function (delay) {
        setTimeout(fixRoomPage, delay);
    });

});// =========================================
// BLOC PRIX — semaine / date sélectionnée
// =========================================
function formatEuro(value) {
    return value.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
}

function findCheckInInput() {
    return document.querySelector(
        '.wphb-single-room-booking-container input[name="check_in_date"], ' +
        '.wphb-single-room-booking-container input[name="checkin"], ' +
        '.wphb-single-room-booking-container input[name="check-in-date"], ' +
        '.wphb-single-room-booking-container input[name="start_date"], ' +
        '.wphb-single-room-booking-container input[name="hb_check_in"]'
    );
}

function updateRoomPriceBox() {
    var label = document.querySelector('.wphb-room-tmpl-dates-available .price .title-price');
    var amount = document.querySelector('.wphb-room-tmpl-dates-available .price .price_value .amount, .wphb-room-tmpl-dates-available .price bdi');
    var checkIn = findCheckInInput();

    if (!label || !amount) return;

    if (checkIn && checkIn.value && window.hbRoomWeekPrices && window.hbRoomWeekPrices[checkIn.value]) {
        label.textContent = 'Prix :';
        amount.textContent = formatEuro(window.hbRoomWeekPrices[checkIn.value]);
    } else if (window.hbRoomLowestPrice) {
        label.textContent = 'Prix à partir de';
        amount.textContent = formatEuro(window.hbRoomLowestPrice);
    }
}

// Vérifie en continu la valeur du champ check-in (plus fiable qu'un event
// 'change', car le datepicker jQuery UI ne le déclenche pas toujours)
var lastCheckInValue = null;
setInterval(function () {
    var checkIn = findCheckInInput();
    if (checkIn && checkIn.value !== lastCheckInValue) {
        lastCheckInValue = checkIn.value;
        updateRoomPriceBox();
    }
}, 400);