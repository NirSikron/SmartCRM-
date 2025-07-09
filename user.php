<?php
$pageTitle = "איזור אישי";       

require "navbar.php";

if (!isset($_SESSION['is_admin'])) {
    header("Location: index.php");
    exit;
}


try {


    // פונקציה לעדכון המונים בסשן
    function updateSessionCounts($pdo, $workerId) {
        // ספירת כל הקריאות
        $queryTotal = "SELECT COUNT(*) AS count FROM calls WHERE worker_id = :worker_id";
        $stmtTotal = $pdo->prepare($queryTotal);
        $stmtTotal->execute([':worker_id' => $workerId]);
        $totalCalls = $stmtTotal->fetch(PDO::FETCH_ASSOC)['count'];

        // ספירת הקריאות הסגורות
        $queryClosed = "SELECT COUNT(*) AS count FROM closed_calls WHERE worker_id = :worker_id";
        $stmtClosed = $pdo->prepare($queryClosed);
        $stmtClosed->execute([':worker_id' => $workerId]);
        $closedCalls = $stmtClosed->fetch(PDO::FETCH_ASSOC)['count'];

        // עדכון הסשן
        $_SESSION['totalCalls'] = $totalCalls;
        $_SESSION['closedCalls'] = $closedCalls;
    }

    // קבלת מספר העובד מהסשן
    $workerId = $_SESSION['worker_id'];

    // עדכון מוני הסשן בכל טעינת דף
    updateSessionCounts($pdo, $workerId);

    $my_calls_count = $_SESSION['totalCalls'];
    $previous_calls_count = $_SESSION['closedCalls'];

} catch (PDOException $e) {
    die("שגיאה: " . $e->getMessage());
}



?>


  <main class="container mx-auto my-8 p-4 bg-white rounded shadow">
    <div class="flex justify-center space-x-reverse space-x-4">
      <a href="my_calls.php" class="bg-blue-500 text-white px-6 py-3 rounded hover:bg-blue-600 text-center">
        הפניות שלי
        <span class="block text-sm">(<?php echo $my_calls_count; ?>)</span>
      </a>
      <a href="previous_calls.php" class="bg-blue-500 text-white px-6 py-3 rounded hover:bg-blue-600 text-center">
        פניות קודמות
        <span class="block text-sm">(<?php echo $previous_calls_count; ?>)</span>
      </a>
    </div>
  </main>
</body>
</html>