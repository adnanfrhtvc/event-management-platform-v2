function maybeLoadEvents() {
    if (location.hash === '#events') {
        loadEvents();
    }
}

maybeLoadEvents();

// Load events whenever hash changes
window.addEventListener('hashchange', maybeLoadEvents);


async function loadEvents() {
    try {
        const response = await fetch('/event-management-platform-v2/backend/api/events');

        console.log("Response status:", response.status);

        if (!response.ok) throw new Error('Failed to load events');
        
        const events = await response.json();
        console.log("Events loaded:", events);

        const container = document.getElementById('events-container');
        container.innerHTML = '';

        events.forEach(event => {
            container.insertAdjacentHTML('beforeend', createEventCard(event));
        });

    } catch (error) {
        console.error("Error loading events:", error);
        showError(error);
    }
}


function createEventCard(event) {
    return `
    <div class="col-md-4 mb-4">
        <div class="card">
            <img src="/event-management-platform-v2/frontend/static/fls.jpg" 
                 class="card-img-top" 
                 alt="${event.title}">
            <div class="card-body">
                <h5 class="card-title">${event.title}</h5>
                <p class="card-text">${event.description}</p>
                <a href="#event_details" class="btn btn-primary view-details" data-event-id="${event.id}">View Details</a>
            </div>
        </div>
    </div>
    `;
}

document.addEventListener('click', function(e) {
  if (e.target.classList.contains('view-details')) {
    const eventId = e.target.dataset.eventId;
    sessionStorage.setItem('selectedEventId', eventId);
    console.log('Stored event ID:', eventId); // Debug log
  }
});

function showError(error) {
    const container = document.getElementById('events-container');
    container.innerHTML = `
        <div class="alert alert-danger">
            Error loading events: ${error.message}
        </div>
    `;
}