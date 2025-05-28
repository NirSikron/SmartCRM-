<?php

require "navbar.php";

if (!isset($_SESSION['is_admin'])) {
    header("Location: user.php");
    exit;
}


$workerId = $_SESSION['worker_id'];

try {

    $stmt = $pdo->prepare("SELECT number_call, worker_id, Content_call, PICUTRE, IS_SOS, STATUS, DATE FROM closed_calls WHERE worker_id = :worker_id AND STATUS = 'סגור'");
    $stmt->execute([':worker_id' => $workerId]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("שגיאה: " . $e->getMessage());
}
?>


    <main class="container mx-auto my-8 p-4 bg-white rounded shadow">
        <h1 class="text-2xl font-bold mb-4 text-center sm:text-right">קריאות סגורות עבור : <?php echo htmlspecialchars($_SESSION['full_name']); ?></h1>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border">
                <thead>
                    <tr>
                        <th class="px-2 py-1 border">מספר קריאה</th>
                        <th class="px-2 py-1 border">מספר עובד</th>
                        <th class="px-2 py-1 border">תוכן הקריאה</th>
                        <th class="px-2 py-1 border">תמונה</th>
                        <th class="px-2 py-1 border">דחיפות</th>
                        <th class="px-2 py-1 border">סטטוס</th>
                        <th class="px-2 py-1 border">תאריך</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($result) > 0): ?>
                        <?php foreach ($result as $row): ?>
                            <tr class="text-center">
                                <td class="px-2 py-1 border"><?php echo htmlspecialchars($row['number_call']); ?></td>
                                <td class="px-2 py-1 border"><?php echo htmlspecialchars($row['worker_id']); ?></td>
                                <td class="px-2 py-1 border"><?php echo htmlspecialchars($row['Content_call']); ?></td>
                                <td class="px-2 py-1 border">
                                    <?php if ($row['PICUTRE']): ?>
                                        <img src="<?php echo htmlspecialchars($row['PICUTRE']); ?>" alt="תמונה" class="w-16 h-auto mx-auto">
                                    <?php else: ?>
                                        אין תמונה
                                    <?php endif; ?>
                                </td>
                                <td class="px-2 py-1 border"><?php echo $row['IS_SOS'] ? 'כן' : 'לא'; ?></td>
                                <td class="px-2 py-1 border">סגור</td>
                                <td class="px-2 py-1 border"><?php echo htmlspecialchars($row['DATE']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center px-4 py-2">לא נמצאו קריאות סגורות.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
