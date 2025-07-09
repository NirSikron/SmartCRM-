<?php
$pageTitle = "דף הבית";       

require "navbar.php";

// בדיקה אם המשתנים קיימים בסשן לפני השימוש
$isAdmin = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : 0;
$fullName = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : "משתמש";

$stmtOpen = $pdo->query("SELECT COUNT(*) AS count FROM calls WHERE STATUS = 'Open'");
$openCalls = $stmtOpen->fetch(PDO::FETCH_ASSOC)['count'];

$stmtClosed = $pdo->query("SELECT COUNT(*) AS count FROM closed_calls WHERE STATUS = 'סגור'");
$closedCalls = $stmtClosed->fetch(PDO::FETCH_ASSOC)['count'];

$stmtEmergency = $pdo->query("SELECT COUNT(*) AS count FROM calls WHERE IS_SOS = 1");
$emergencyCalls = $stmtEmergency->fetch(PDO::FETCH_ASSOC)['count'];

$stmtRegular = $pdo->query("SELECT COUNT(*) AS count FROM calls WHERE IS_SOS = 0");
$regularCalls = $stmtRegular->fetch(PDO::FETCH_ASSOC)['count'];
?>

<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   <title>דיאגרמות קריאות</title>
</head>
<body class="bg-gray-200">
<main class="container mx-auto my-8 p-4 bg-white rounded shadow">
       <h1 class="text-2xl font-bold mb-4 text-center">סטטיסטיקות קריאות</h1>

       <div class="flex justify-center space-x-10">
           <div style="width: 25%;">
               <canvas id="callsChart"></canvas>
           </div>
           <div style="width: 50%;">
               <canvas id="sosChart"></canvas>
           </div>
       </div>
   </main>

   <script>
       const ctxCalls = document.getElementById('callsChart').getContext('2d');
       new Chart(ctxCalls, {
           type: 'pie',
           data: {
               labels: ['קריאות פתוחות', 'קריאות סגורות'],
               datasets: [{
                   data: [<?php echo $openCalls; ?>, <?php echo $closedCalls; ?>],
                   backgroundColor: ['#f39c12', '#2ecc71']
               }]
           },
           options: { responsive: true }
       });

       const ctxSOS = document.getElementById('sosChart').getContext('2d');
       new Chart(ctxSOS, {
           type: 'bar',
           data: {
               labels: ['קריאות חירום', 'קריאות רגילות'],
               datasets: [{
                   label: 'מספר קריאות',
                   data: [<?php echo $emergencyCalls; ?>, <?php echo $regularCalls; ?>],
                   backgroundColor: ['#e74c3c', '#3498db']
               }]
           },
           options: { responsive: true }
       });
   </script>
</body>
</html>
