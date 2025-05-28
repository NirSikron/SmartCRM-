<?php

require "navbar.php";

if (!isset($_SESSION['is_admin'])) {
    header("Location: index.php");
    exit;
}


try {


    function updateSessionCounts($pdo, $workerId) {
        $queryTotal = "SELECT COUNT(*) AS count FROM calls WHERE worker_id = :worker_id";
        $stmtTotal = $pdo->prepare($queryTotal);
        $stmtTotal->execute([':worker_id' => $workerId]);
        $totalCalls = $stmtTotal->fetch(PDO::FETCH_ASSOC)['count'];
        
        $queryClosed = "SELECT COUNT(*) AS count FROM closed_calls WHERE worker_id = :worker_id";
        $stmtClosed = $pdo->prepare($queryClosed);
        $stmtClosed->execute([':worker_id' => $workerId]);
        $closedCalls = $stmtClosed->fetch(PDO::FETCH_ASSOC)['count'];

        $_SESSION['totalCalls'] = $totalCalls;
        $_SESSION['closedCalls'] = $closedCalls;
    }

    $workerId = $_SESSION['worker_id'];
    updateSessionCounts($pdo, $workerId);

    $stmtOpen = $pdo->prepare("SELECT number_call, worker_id, Content_call, PICUTRE, IS_SOS, STATUS, DATE FROM calls WHERE worker_id = :worker_id AND STATUS = 'Open'");
    $stmtOpen->execute([':worker_id' => $workerId]);
    $result = $stmtOpen->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("שגיאה: " . $e->getMessage());
}
?>


    <main class="container mx-auto my-8 p-4 bg-white rounded shadow">
        <h1 class="text-2xl font-bold mb-4">הקריאות שלי</h1>
        <p>סה"כ קריאות: <?php echo $_SESSION['totalCalls']; ?></p>
        <p>סה"כ קריאות סגורות: <?php echo $_SESSION['closedCalls']; ?></p>
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
                        <tr>
                            <td class="px-2 py-1 border"><?php echo htmlspecialchars($row['number_call']); ?></td>
                            <td class="px-2 py-1 border"><?php echo htmlspecialchars($row['worker_id']); ?></td>
                            <td class="px-2 py-1 border"><?php echo htmlspecialchars($row['Content_call']); ?></td>
                            <td class="px-2 py-1 border">
                                <?php if ($row['PICUTRE']): ?>
                                    <a href="<?php echo htmlspecialchars($row['PICUTRE']); ?>" target="_blank">
                                        <img src="<?php echo htmlspecialchars($row['PICUTRE']); ?>" alt="תמונה" width="80">
                                    </a>
                                <?php else: ?>
                                    אין תמונה
                                <?php endif; ?>
                            </td>
                            <td class="px-2 py-1 border"><?php echo $row['IS_SOS'] ? 'כן' : 'לא'; ?></td>
                            <td class="px-2 py-1 border"><?php echo htmlspecialchars($row['STATUS']); ?></td>
                            <td class="px-2 py-1 border"><?php echo htmlspecialchars($row['DATE']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center px-4 py-2">לא נמצאו קריאות</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
