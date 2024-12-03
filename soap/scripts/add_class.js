document.addEventListener("DOMContentLoaded", function () {
    const addClassForm = document.getElementById("add-class-form");
    const rest2apiUrl = document.getElementById("rest2ApiUrl").getAttribute("data-api-url");

    const userId = localStorage.getItem("id");

    if (!userId) {
        alert("User ID not found. Please log in again.");
        window.location.href = "login.php";
        return;
    }

    addClassForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const classData = {
            name: document.getElementById("name").value,
            start: document.getElementById("start").value,
            end: document.getElementById("end").value,
            capacity: document.getElementById("capacity").value,
            member_only: document.getElementById("member_only").value
        };

        const formattedStart = formatDate(classData.start);
        const formattedEnd = formatDate(classData.end);

        const bodyData = {
            name: classData.name,
            start: formattedStart,
            end: formattedEnd,
            capacity: classData.capacity,
            member_only: classData.member_only,
            userId: userId
        };

        fetch(`${rest2apiUrl}classes`, {
            method: "POST", headers: {
                "Content-Type": "application/json",
            }, body: JSON.stringify(bodyData)
        })
            .then(response => response.json())
            .then(response => {
                const {status, message} = response;

                if (status === "200" || status === "201") {
                    alert("Class added successfully!");
                    window.location.href = "index.php";
                } else {
                    alert(message || "An error occurred while adding the class.");
                }
            })
            .catch(error => {
                console.error(error);
                alert("There was an error processing the request.");
            });
    });
});

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toISOString();
}
