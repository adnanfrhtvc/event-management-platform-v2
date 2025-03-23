document.addEventListener("DOMContentLoaded", function() {
    var app = $.spapp({
        defaultView: "home",  
        templateDir: "views/" 
    });

    // Defining routes for all pages
    app.route({ view: "home", load: "home.html" });
    app.route({ view: "dashboard", load: "dashboard.html" });
    app.route({ view: "events", load: "events.html" });
    app.route({ view: "create_event", load: "create_event.html" });
    app.route({ view: "event_details", load: "event_details.html" });
    app.route({ view: "edit_event", load: "edit_event.html" });
    app.route({ view: "ticket", load: "ticket.html" });
    app.route({ view: "profile", load: "profile.html" });
    app.route({ view: "register", load: "register.html" });
    app.route({ view: "login", load: "login.html" });

    // Run SPApp
    app.run();
});
