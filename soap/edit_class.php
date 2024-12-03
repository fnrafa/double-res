<?php
require_once 'config/variable.php';
$pageTitle = "Edit Class";
ob_start();
?>

    <main class="max-w-6xl mx-auto mt-6 bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold mb-4">Edit Class</h2>
        <form id="edit-class-form" method="POST">
            <label for="name" class="block mb-2">Class Name</label>
            <input type="text" id="name" name="name" class="border px-4 py-2 w-full mb-4" required>

            <label for="start" class="block mb-2">Start Time</label>
            <input type="datetime-local" id="start" name="start" class="border px-4 py-2 w-full mb-4" required>

            <label for="end" class="block mb-2">End Time</label>
            <input type="datetime-local" id="end" name="end" class="border px-4 py-2 w-full mb-4" required>

            <label for="capacity" class="block mb-2">Capacity</label>
            <input type="number" id="capacity" name="capacity" class="border px-4 py-2 w-full mb-4" required>

            <label for="member_only" class="block mb-2">Member Only</label>
            <select id="member_only" name="member_only" class="border px-4 py-2 w-full mb-4">
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>

            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Update Class
            </button>
        </form>
    </main>
    <script src="scripts/edit_class.js" defer></script>

<?php
$pageContent = ob_get_clean();
require_once "layout/wrapper.php";
?>