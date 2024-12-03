document.addEventListener("DOMContentLoaded", function () {
    const editClassForm = document.getElementById("edit-class-form");
    const classId = new URLSearchParams(window.location.search).get('id');
    const rest2apiUrl = document.getElementById("rest2ApiUrl").getAttribute("data-api-url");

    const userId = localStorage.getItem("id");

    if (!userId) {
        alert("User ID is required. Please log in again.");
        window.location.href = "login.php";
        return;
    }

    if (!classId) {
        alert("Class ID is required.");
        window.location.href = "dashboard.php";
        return;
    }

    fetch(`${rest2apiUrl}classes/${classId}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === "200" && data.data) {
                const classData = data.data;

                document.getElementById("name").value = classData.name;
                document.getElementById("start").value = new Date(classData.start).toISOString().slice(0, 16); // format datetime-local
                document.getElementById("end").value = new Date(classData.end).toISOString().slice(0, 16); // format datetime-local
                document.getElementById("capacity").value = classData.capacity;
                document.getElementById("member_only").value = classData.member_only;
            } else {
                alert("Failed to load class data.");
            }
        })
        .catch(error => {
            console.error(error);
            alert("There was an error fetching the class data.");
        });

    editClassForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const updatedClassData = {
            id: classId,
            name: document.getElementById("name").value,
            start: document.getElementById("start").value,
            end: document.getElementById("end").value,
            capacity: document.getElementById("capacity").value,
            member_only: document.getElementById("member_only").value,
            userId: userId
        };

        fetch(`${rest2apiUrl}classes`, {
            method: "POST", headers: {
                "Content-Type": "application/json",
            }, body: JSON.stringify(updatedClassData)
        })
            .then(response => response.json())
            .then(response => {
                const {status, message} = response;

                if (status === "200" || status === "201") {
                    alert("Class updated successfully!");
                    window.location.href = "index.php";
                } else {
                    alert(message || "An error occurred while updating the class.");
                }
            })
            .catch(error => {
                console.error(error);
                alert("There was an error processing the request.");
            });
    });
});
