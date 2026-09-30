document.addEventListener(
    "DOMContentLoaded",
    function () {

        const container =
            document.getElementById(
                "eventContainer"
            );


        if (!container) {
            return;
        }


        fetch("data/events.json")

            .then(function (response) {

                if (!response.ok) {

                    throw new Error(
                        "Unable to load events."
                    );

                }

                return response.json();

            })


            .then(function (events) {

                container.innerHTML = "";


                events.forEach(function (event) {

                    const card =
                        document.createElement(
                            "div"
                        );


                    card.className =
                        "event-card";


                    card.innerHTML = `

                        <h3>
                            ${event.name}
                        </h3>

                        <p>
                            <strong>Date:</strong>
                            ${event.date}
                        </p>

                        <p>
                            <strong>Venue:</strong>
                            ${event.venue}
                        </p>

                    `;


                    container.appendChild(card);

                });

            })


            .catch(function (error) {

                container.innerHTML =
                    "<p>Unable to load events.</p>";

                console.error(error);

            });

    }
);