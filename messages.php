<?php
$pageTitle = "הודעות שהתקבלו";  
require "navbar.php";

if (!isset($_SESSION['is_admin'])) {
    header("Location: index.php");
    exit;
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC");
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("שגיאת מסד נתונים: " . $e->getMessage());
}
?>

<main class="container mx-auto my-8 p-4 bg-white rounded shadow">
    <h1 class="text-2xl font-bold mb-4">רשימת פניות שהתקבלו</h1>

    <?php if (empty($messages)): ?>
        <p>לא נמצאו הודעות.</p>
    <?php else: ?>
        <table class="w-full border border-gray-300 text-right">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2">שם מלא</th>
                    <th class="border px-4 py-2">אימייל</th>
                    <th class="border px-4 py-2">תוכן ההודעה</th>
                    <th class="border px-4 py-2">תאריך</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $msg): ?>
                    <tr>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($msg['full_name']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($msg['mail']); ?></td>
                        <td class="border px-4 py-2"><?php echo nl2br(htmlspecialchars($msg['content'])); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($msg['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>
</body>
</html>
