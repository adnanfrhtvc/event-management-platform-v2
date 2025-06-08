const homePage = {
    init: function () {
        const eventCardsContainer = document.getElementById("event-cards");
        if (!eventCardsContainer) return;

        this.loadCategories();

        fetch("https://eventease-v4kuo.ondigitalocean.app/backend/api/events")
            .then(response => response.json())
            .then(events => {
                if (!Array.isArray(events)) return;

                const sortedEvents = events.reverse();
                const recentEvents = sortedEvents.slice(0, 3);

                eventCardsContainer.innerHTML = "";

                recentEvents.forEach(event => {
                    const card = document.createElement("div");
                    card.className = "col";
                    card.innerHTML = `
                        <div class="card shadow h-100">
                            <img src="static/${event.image || 'fls.jpg'}" class="card-img-top" alt="${event.name}">
                            <div class="card-body">
                                <h5 class="card-title">${event.name}</h5>
                                <p class="card-text">${event.description}</p>
                                <p><strong>Location:</strong> ${event.location}</p>
                                <p><strong>Date:</strong> ${new Date(event.event_date).toLocaleDateString()}</p>
                                <a href="#event_details" class="btn btn-primary view-details" data-event-id="${event.id}">View Details</a>
                            </div>
                        </div>
                    `;
                    eventCardsContainer.appendChild(card);
                });
            })
            .catch(error => {
                console.error("Failed to load events:", error);
            });
    },

    loadCategories: function () {
        fetch("https://eventease-v4kuo.ondigitalocean.app/backend/api/categories")
            .then(response => response.json())
            .then(categories => {
                if (!Array.isArray(categories)) return;

                const container = document.getElementById("category-buttons");
                if (!container) return;

                container.innerHTML = "";

                categories.forEach((category, index) => {
                    const btn = document.createElement("button");
                    btn.className = `btn btn-${this.getCategoryColor(index)}`;
                    btn.textContent = category.name;
                    container.appendChild(btn);
                });
            })
            .catch(error => {
                console.error("Failed to load categories:", error);
            });
    },

    getCategoryColor: function (index) {
        const colors = ["primary", "secondary", "success", "danger", "warning", "info", "dark"];
        return colors[index % colors.length];
    }
};
