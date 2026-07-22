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
        const liContainer = document.createElement("li");
        liContainer.id = "burger-reserver-item";
        liContainer.className = "menu-item burger-btn-container";

        function toggleMenu() {
            const isOpen = menu.classList.toggle("open");
            burger.classList.toggle("active");

            if (isOpen) {
                document.body.style.overflow = "hidden";
                liContainer.appendChild(btnReserver);
                navList.appendChild(liContainer);
            } else {
                document.body.style.overflow = "";
                headerActions.insertBefore(btnReserver, burger);
            }
        }

        burger.addEventListener("click", function (e) {
            e.stopPropagation();
            toggleMenu();
        });

        navList.addEventListener("click", function (e) {
            if (e.target.tagName === "A" && menu.classList.contains("open")) {
                toggleMenu();
            }
        });

        document.addEventListener("click", function (e) {
            if (!menu.contains(e.target) && !burger.contains(e.target) && menu.classList.contains("open")) {
                toggleMenu();
            }
        });

        window.addEventListener("resize", function () {
            if (window.innerWidth > 900 && menu.classList.contains("open")) {
                headerActions.insertBefore(btnReserver, burger);
                menu.classList.remove("open");
                burger.classList.remove("active");
                document.body.style.overflow = "";
            }
        });
    }

    // =========================================
    // RÉSERVATION LOVE ROOM — calendrier + prix en direct
    // =========================================
    const checkIn = document.getElementById("lip_check_in");
    const checkOut = document.getElementById("lip_check_out");
    const priceAmount = document.getElementById("lip_price_amount");

    if (!checkIn || !checkOut) return;

    if (typeof flatpickr === "undefined") {
        // Si flatpickr.min.js n'a pas pu se charger (bloqueur de pub/vie privée, erreur réseau, etc.),
        // on le signale clairement dans la console au lieu de laisser les champs de dates bruts
        // (format AAAA-MM-JJ) sans calendrier ni calcul de prix.
        console.warn("[L'Instant Plaisir] flatpickr n'a pas pu être chargé : le calendrier de réservation et le calcul du prix ne fonctionneront pas sur cette page.");
        return;
    }

    function formatEuro(value) {
        return value.toLocaleString("fr-FR", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + " €";
    }

    // Construit une date AAAA-MM-JJ à partir des composants LOCAUX (pas d'UTC), pour que la clé
    // corresponde exactement aux dates calculées côté serveur (PHP utilise le fuseau horaire du site).
    // ATTENTION : ne jamais utiliser .toISOString() ici, ça convertit en UTC et décale la date d'un
    // jour en arrière avec le fuseau français (UTC+1/+2) — c'était la cause du prix qui ne s'affichait jamais.
    function formatYMD(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, "0");
        const d = String(date.getDate()).padStart(2, "0");
        return `${y}-${m}-${d}`;
    }

    function addDays(dateStr, days) {
        const d = new Date(dateStr + "T00:00:00");
        d.setDate(d.getDate() + days);
        return formatYMD(d);
    }

    function updatePrice() {
        if (!priceAmount) return;

        const defaultText = priceAmount.dataset.default || "…";

        if (!window.lipRoomDayPrices) {
            priceAmount.textContent = defaultText;
            return;
        }

        const start = new Date(checkIn.value + "T00:00:00");
        const end = new Date(checkOut.value + "T00:00:00");

        if (isNaN(start) || isNaN(end) || end <= start) {
            priceAmount.textContent = defaultText;
            return;
        }

        let total = 0;
        const cursor = new Date(start);
        while (cursor < end) {
            const key = formatYMD(cursor);
            total += window.lipRoomDayPrices[key] || 0;
            cursor.setDate(cursor.getDate() + 1);
        }

        priceAmount.textContent = total > 0 ? formatEuro(total) : defaultText;
    }

    const disabledRanges = window.lipDisabledRanges || [];

    try {
        const fpCheckOut = flatpickr(checkOut, {
            locale: "fr",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d/m/Y",
            minDate: addDays(checkIn.value, 1),
            disable: disabledRanges,
            onChange: updatePrice,
        });

        flatpickr(checkIn, {
            locale: "fr",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d/m/Y",
            minDate: "today",
            disable: disabledRanges,
            onChange: function (selectedDates, dateStr) {
                const nextDay = addDays(dateStr, 1);
                fpCheckOut.set("minDate", nextDay);
                if (checkOut.value && checkOut.value <= dateStr) {
                    fpCheckOut.setDate(nextDay);
                }
                updatePrice();
            },
        });

        updatePrice();
    } catch (err) {
        console.error("[L'Instant Plaisir] Erreur lors de l'initialisation du calendrier de réservation :", err);
    }

});