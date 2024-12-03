<?php
require_once 'config/variable.php';
$error_message = $error_message ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="scripts/login.js" defer></script>
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-sm mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold mb-4">Login Admin Panel</h2>

    <?php if ($error_message): ?>
        <div class="mb-4 text-red-500"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <form id="loginForm">
        <div class="mb-4">
            <label for="username" class="block text-sm font-semibold">Username</label>
            <input type="text" id="username" name="username" class="w-full p-2 border rounded-lg" required>
        </div>
        <div class="mb-4">
            <label for="password" class="block text-sm font-semibold">Password</label>
            <input type="password" id="password" name="password" class="w-full p-2 border rounded-lg" required>
        </div>
        <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg">Login</button>
    </form>
</div>

<div id="apiUrl" data-api-url="<?php echo REST_API_URL; ?>" style="display:none;"></div>

</body>
</html>
