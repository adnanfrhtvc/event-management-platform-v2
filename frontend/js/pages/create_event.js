document.addEventListener("DOMContentLoaded", function () {
  // Only initialize if the page is loaded via SPApp
  $(document).on("spapp:loaded", function (e, page) {
    if (page !== "create_event") return;

    const form = document.getElementById("createEventForm");
    const messageBox = document.getElementById("eventMessage");

    if (!form) return;

    form.addEventListener("submit", async function (e) {
      e.preventDefault();

      const token = localStorage.getItem("token");
      const organizer_id = localStorage.getItem("user_id");

      if (!token || !organizer_id) {
        return displayMessage("You must be logged in to create an event.", "danger");
      }

      // Form values
      const title = form.title.value.trim();
      const event_date = form.event_date.value.trim();
      const category_id = form.category_id.value;
      const location = form.location.value.trim();
      const description = form.description.value.trim();

      // Validation
      if (!title || !event_date || !category_id || !location || !description) {
        return displayMessage("Please fill in all required fields.", "danger");
      }

      // Prepare JSON body
      const eventData = {
        title,
        event_date,
        organizer_id,
        category_id,
        location,
        description
      };

      try {
        const response = await fetch("http://localhost/event-management-platform-v2/backend/api/events", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Authorization": `Bearer ${token}`
          },
          body: JSON.stringify(eventData)
        });

        const result = await response.json();

        if (response.ok) {
          displayMessage("Event created successfully!", "success");
          form.reset();
        } else {
          displayMessage(result.error || "Failed to create event.", "danger");
        }
      } catch (err) {
        console.error(err);
        displayMessage("Network or server error occurred.", "danger");
      }
    });

    function displayMessage(message, type) {
      if (!messageBox) return;
      messageBox.textContent = message;
      messageBox.className = `alert alert-${type} mt-3`;
      messageBox.classList.remove("d-none");
    }
  });
});
