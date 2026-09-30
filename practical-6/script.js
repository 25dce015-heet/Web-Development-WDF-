document.addEventListener(
    "DOMContentLoaded",
    function () {

        console.log(
            "StudentHub JavaScript Loaded"
        );


        const themeButton =
            document.getElementById(
                "themeButton"
            );


        if (themeButton) {

            themeButton.addEventListener(
                "click",
                function () {

                    document.body.classList.toggle(
                        "dark-mode"
                    );


                    if (
                        document.body.classList.contains(
                            "dark-mode"
                        )
                    ) {

                        localStorage.setItem(
                            "theme",
                            "dark"
                        );

                    } else {

                        localStorage.setItem(
                            "theme",
                            "light"
                        );

                    }

                }
            );

        }


        if (
            localStorage.getItem("theme")
            ===
            "dark"
        ) {

            document.body.classList.add(
                "dark-mode"
            );

        }


        const feedbackForm =
            document.getElementById(
                "feedbackForm"
            );


        if (feedbackForm) {

            feedbackForm.addEventListener(
                "submit",
                function () {

                    alert(
                        "Thank you for your feedback!"
                    );

                }
            );

        }

    }
);