<?php
require_once 'config/variable.php';

$pageTitle = "Dashboard";
ob_start();
?>

<main class="max-w-6xl mx-auto mt-6 bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold mb-4">Classes</h2>
    <table class="min-w-full bg-gray-100 table-auto rounded-lg shadow-md">
        <thead>
        <tr class="bg-gray-200">
            <th class="px-4 py-2 text-left">Class Name</th>
            <th class="px-4 py-2 text-left">Start Time</th>
            <th class="px-4 py-2 text-left">End Time</th>
            <th class="px-4 py-2 text-left">Capacity</th>
            <th class="px-4 py-2 text-left">Quantity</th>
            <th class="px-4 py-2 text-left">Member Only</th>
            <th class="px-4 py-2 text-left">Actions</th>
        </tr>
        </thead>
        <tbody id="class-list">
        <tr>
            <td colspan="7" class="px-4 py-2 text-center text-gray-500">Loading...</td>
        </tr>
        </tbody>
    </table>
    <a href="add_class.php" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add
        Class</a>
</main>
<script src="scripts/classList.js"></script>
<?php
$pageContent = ob_get_clean();
require_once "layout/wrapper.php";
?>
