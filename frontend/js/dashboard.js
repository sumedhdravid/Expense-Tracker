const token =
    localStorage.getItem("authToken");


if (!token) {

    window.location.href =
        "login.html";
}


const user =
    JSON.parse(
        localStorage.getItem("user") || "{}"
    );


const welcomeMessage =
    document.getElementById(
        "welcomeMessage"
    );


if (user.name) {

    welcomeMessage.textContent =
        "Welcome back, " + user.name + "!";
}


const logoutButton =
    document.getElementById(
        "dashboardLogout"
    );


logoutButton.addEventListener(
    "click",
    function () {

        localStorage.removeItem(
            "authToken"
        );

        localStorage.removeItem(
            "user"
        );

        window.location.href =
            "login.html";
    }
);


loadDashboard();


async function loadDashboard() {

    try {

        const response =
            await fetch(
                "../api/expenses/summary.php",
                {
                    method: "GET",

                    headers: {
                        "Authorization":
                            "Bearer " + token
                    }
                }
            );


        const result =
            await response.json();


        if (!result.success) {

            document.getElementById(
                "chartMessage"
            ).textContent =
                result.message;

            return;
        }


        document.getElementById(
            "totalExpenses"
        ).textContent =
            result.summary.total_expenses;


        document.getElementById(
            "totalAmount"
        ).textContent =
            "₹" +
            Number(
                result.summary.total_amount
            ).toFixed(2);


        if (
            !result.categories ||
            result.categories.length === 0
        ) {

            document.getElementById(
                "chartMessage"
            ).textContent =
                "No expense data available yet.";

            return;
        }


        const labels =
            result.categories.map(
                function (item) {
                    return item.category;
                }
            );


        const values =
            result.categories.map(
                function (item) {
                    return Number(item.total);
                }
            );


        new Chart(
            document.getElementById(
                "expenseChart"
            ),
            {
                type: "pie",

                data: {

                    labels: labels,

                    datasets: [
                        {
                            data: values
                        }
                    ]
                },

                options: {

                    responsive: true,

                    maintainAspectRatio: false,

                    plugins: {

                        legend: {
                            position: "bottom"
                        }

                    }
                }
            }
        );

    } catch (error) {

        console.error(error);

        document.getElementById(
            "chartMessage"
        ).textContent =
            "Unable to load dashboard data.";
    }
}