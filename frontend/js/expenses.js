const expenseForm =
    document.getElementById("expenseForm");


/* =========================
   ADD EXPENSE
========================= */

if (expenseForm) {

    expenseForm.addEventListener(
        "submit",
        async function (event) {

            event.preventDefault();

            const token =
                localStorage.getItem("authToken");

            if (!token) {

                window.location.href =
                    "login.html";

                return;
            }

            const category =
                document.getElementById("category").value;

            const amount =
                document.getElementById("amount").value;

            const comments =
                document
                    .getElementById("comments")
                    .value
                    .trim();

            const message =
                document.getElementById(
                    "expenseMessage"
                );

            message.style.color = "#333";

            message.textContent =
                "Adding expense...";

            try {

                const response = await fetch(
                    "../api/expenses/create.php",
                    {
                        method: "POST",

                        headers: {
                            "Content-Type":
                                "application/json",

                            "Authorization":
                                "Bearer " + token
                        },

                        body: JSON.stringify({
                            category: category,
                            amount: amount,
                            comments: comments
                        })
                    }
                );

                const result =
                    await response.json();

                if (result.success) {

                    message.style.color =
                        "green";

                    message.textContent =
                        result.message;

                    expenseForm.reset();

                } else {

                    message.style.color =
                        "red";

                    message.textContent =
                        result.message;
                }

            } catch (error) {

                console.error(error);

                message.style.color =
                    "red";

                message.textContent =
                    "Unable to connect to the server.";
            }
        }
    );
}


/* =========================
   VIEW EXPENSES
========================= */

const expenseTableBody =
    document.getElementById(
        "expenseTableBody"
    );

if (expenseTableBody) {

    loadExpenses();
}


async function loadExpenses() {

    const token =
        localStorage.getItem("authToken");

    if (!token) {

        window.location.href =
            "login.html";

        return;
    }

    try {

        const response = await fetch(
            "../api/expenses/list.php",
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

            expenseTableBody.innerHTML = `
                <tr>
                    <td colspan="6">
                        ${escapeHTML(
                            result.message
                        )}
                    </td>
                </tr>
            `;

            return;
        }

        if (result.expenses.length === 0) {

            expenseTableBody.innerHTML = `
                <tr>
                    <td colspan="6">
                        No expenses found.
                    </td>
                </tr>
            `;

            return;
        }

        expenseTableBody.innerHTML = "";

        result.expenses.forEach(
            function (expense) {

                const row =
                    document.createElement("tr");

                row.innerHTML = `
                    <td>
                        ${escapeHTML(
                            expense.category
                        )}
                    </td>

                    <td>
                        ₹${Number(
                            expense.amount
                        ).toFixed(2)}
                    </td>

                    <td>
                        ${formatDate(
                            expense.created_at
                        )}
                    </td>

                    <td>
                        ${formatDate(
                            expense.updated_at
                        )}
                    </td>

                    <td>
                        ${escapeHTML(
                            expense.comments || "-"
                        )}
                    </td>

                    <td>
                        <button
                            class="edit-button"
                            onclick='editExpense(
                                ${JSON.stringify(expense)}
                            )'>
                            Edit
                        </button>

                        <button
                            class="delete-button"
                            onclick="deleteExpense(
                                ${expense.id}
                            )">
                            Delete
                        </button>
                    </td>
                `;

                expenseTableBody.appendChild(row);
            }
        );

    } catch (error) {

        console.error(error);

        expenseTableBody.innerHTML = `
            <tr>
                <td colspan="6">
                    Unable to load expenses.
                </td>
            </tr>
        `;
    }
}


/* =========================
   EDIT EXPENSE
========================= */

async function editExpense(expense) {

    const category =
        prompt(
            "Enter category:",
            expense.category
        );

    if (category === null) {
        return;
    }

    const amount =
        prompt(
            "Enter amount:",
            expense.amount
        );

    if (amount === null) {
        return;
    }

    const comments =
        prompt(
            "Enter comments:",
            expense.comments || ""
        );

    if (comments === null) {
        return;
    }

    if (category.trim() === "") {

        alert(
            "Category cannot be empty."
        );

        return;
    }

    if (
        amount.trim() === "" ||
        isNaN(amount) ||
        Number(amount) <= 0
    ) {

        alert(
            "Please enter a valid amount."
        );

        return;
    }

    const token =
        localStorage.getItem("authToken");

    try {

        const response = await fetch(
            "../api/expenses/update.php",
            {
                method: "PUT",

                headers: {
                    "Content-Type":
                        "application/json",

                    "Authorization":
                        "Bearer " + token
                },

                body: JSON.stringify({

                    id: expense.id,

                    category:
                        category.trim(),

                    amount:
                        amount,

                    comments:
                        comments.trim()
                })
            }
        );

        const result =
            await response.json();

        if (result.success) {

            alert(
                "Expense updated successfully."
            );

            loadExpenses();

        } else {

            alert(result.message);
        }

    } catch (error) {

        console.error(error);

        alert(
            "Unable to update expense."
        );
    }
}


/* =========================
   DELETE EXPENSE
========================= */

async function deleteExpense(id) {

    const confirmed =
        confirm(
            "Are you sure you want to delete this expense?"
        );

    if (!confirmed) {
        return;
    }

    const token =
        localStorage.getItem("authToken");

    try {

        const response = await fetch(
            "../api/expenses/delete.php",
            {
                method: "DELETE",

                headers: {
                    "Content-Type":
                        "application/json",

                    "Authorization":
                        "Bearer " + token
                },

                body: JSON.stringify({
                    id: id
                })
            }
        );

        const result =
            await response.json();

        if (result.success) {

            alert(
                "Expense deleted successfully."
            );

            loadExpenses();

        } else {

            alert(result.message);
        }

    } catch (error) {

        console.error(error);

        alert(
            "Unable to delete expense."
        );
    }
}


/* =========================
   LOGOUT
========================= */

const logoutButton =
    document.getElementById(
        "logoutButton"
    );

if (logoutButton) {

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
}


/* =========================
   HELPERS
========================= */

function formatDate(dateString) {

    const date =
        new Date(
            dateString.replace(
                " ",
                "T"
            )
        );

    return date.toLocaleString();
}


function escapeHTML(value) {

    const div =
        document.createElement("div");

    div.textContent = value;

    return div.innerHTML;
}