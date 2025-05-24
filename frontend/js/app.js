document.addEventListener("DOMContentLoaded", function() {
    var app = $.spapp({
        defaultView: "home",  
        templateDir: "views/" 
    });

    // Define all routes
    app.route({
    view: "home",
    load: "home.html",
    onCreate: function () {
        $.getScript("js/pages/home.js", function () {
            if (typeof homePage !== 'undefined' && typeof homePage.init === 'function') {
                homePage.init();
            } else {
                console.error("homePage.init() is not defined!");
            }
        });
    }
});

    app.route({
        view: "dashboard",
        load: "dashboard.html",
    });


    app.route({ view: "events", load: "events.html" });
    app.route({ view: "create_event", load: "create_event.html" });
    app.route({
        view: "event_details",
        load: "event_details.html",
        onCreate: function() {
        console.log("Event details view loaded"); // Debug log
        }
    });

    app.route({ view: "edit_event", load: "edit_event.html" });
    app.route({ view: "ticket", load: "ticket.html" });
    app.route({ view: "profile", load: "profile.html" });

    app.route({ 
        view: "register", 
        load: "register.html", 
        onCreate: function() {
            $.getScript("js/auth/register.js", function() {
                if (typeof registerPage !== 'undefined' && typeof registerPage.init === 'function') {
                    registerPage.init();
                } else {
                    console.error("registerPage.init() is not defined!");
                }
            });
        }
    });

    app.route({
        view: "login",
        load: "login.html",
        onCreate: async function () {
            const module = await import("./pages/login.js");
            module.default.init();
        }
    });

    app.run();

    // Update navbar on every page change
    $(document).on("spapp:pageChange", function() {
        auth.updateNavbar();
    });
});
