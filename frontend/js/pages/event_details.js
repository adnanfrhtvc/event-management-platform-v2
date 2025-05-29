$(document).on("spapp:page:changed", function (e, page) {
  if (page === "event_details") {
    console.log("Event details view loaded");
    loadEventDetails(); // Now it runs at the right time
  }
});

window.addEventListener("hashchange", loadEventDetails);

async function loadEventDetails() {
  try {
    const eventId = sessionStorage.getItem('selectedEventId');
    if (!eventId) throw new Error("No event selected.");

    const response = await fetch(`/event-management-platform-v2/backend/api/events/${eventId}`);
    if (!response.ok) throw new Error(`Failed to load event.`);

    const event = await response.json();

    document.getElementById('event-title').textContent = event.title;
    document.getElementById('event-location').textContent = event.location;
    document.getElementById('event-description').textContent = event.description;

    const eventDate = new Date(event.event_date);
    document.getElementById('event-date').textContent = 
      isNaN(eventDate.getTime()) || event.event_date.startsWith("0000")
        ? 'Date to be announced'
        : eventDate.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
          });

    // Use organizer_id as fallback
    document.getElementById('event-organizer').textContent = "Organizer #" + event.organizer_id;

  } catch (error) {
    console.error("Error loading event:", error);
    document.querySelector('.eventdetails-container').innerHTML = `
      <div class="alert alert-danger mt-5">
        <h4>Error Loading Event</h4>
        <p>${error.message}</p>
        <a href="#events" class="btn btn-outline-danger mt-3">Back to Events</a>
      </div>
    `;
  }
}
