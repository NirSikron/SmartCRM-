<?php
$pageTitle = "קריאות סגורות"; // או כל שם מותאם לעמוד הזה

require "navbar.php";
if (!isset($_SESSION['is_admin'])) {
    header("Location: index.php");
    exit;
}

try {
    // שליפת כל הקריאות שנסגרו
    $stmt = $pdo->prepare("SELECT number_call, worker_id, Content_call, PICUTRE, IS_SOS, STATUS, DATE, admin_comment FROM closed_calls order by DATE desc");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("שגיאה: " . $e->getMessage());
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
        margin: 0;
        padding: 0;
    }

    /* עיצוב ה-Navbar */
    .navbar {
        background-color:rgb(12, 15, 20);
        color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 0px 0px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .navbar a {
        color: white;
        text-decoration: none;
        font-weight: bold;
        margin: 0 8px;
        padding: 4px 8px;
        border-radius: 4px;
        transition: background-color 0.3s ease;
    }
    .navbar a:hover {
        background-color:rgb(32, 70, 111);
    }

    /* עיצוב הכותרת */
    .header {
        background-color: #1a202c;
        color: white;
        padding: 2px;
        text-align: center;
        font-size: 12px;
        margin-top: 0;
    }
    .header-title {
        font-size: 20px;
        font-weight: bold;
        margin: 0;
    }
    .header-subtitle {
        font-size: 12px;
        margin-top: 5px;
    }

    /* עיצוב הטבלה */
    .table-container {
        margin-top: 0;
        padding: 20px;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
        background-color: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .table th,
    .table td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: center;
    }
    .table th {
        background-color: #003d85;
        color: white;
        font-weight: bold;
    }
    .table tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    .table tr:hover {
        background-color: #e0e7ff;
    }
    .no-data {
        text-align: center;
        color: #999;
        font-size: 16px;
        padding: 20px;
    }
    a img {
        border: none;
    }
</style>

<!-- Header -->
<header class="header">
    <h1 class="header-title">קריאות סגורות</h1>
    <p class="header-subtitle">צפה, עדכן או נהל קריאות קיימות במערכת</p>
</header>

<!-- Table -->
<div class="table-container">
    <table class="table">
        <thead>
            <tr>
                <th>מספר קריאה</th>
                <th>מספר עובד</th>
                <th>תוכן הקריאה</th>
                <th>תמונה</th>
                <th>דחיפות</th>
                <th>סטטוס</th>
                <th>תאריך</th>
                <th>הערות </th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($result) > 0): ?>
                <?php foreach ($result as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['number_call']); ?></td>
                        <td><?php echo htmlspecialchars($row['worker_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['Content_call']); ?></td>
                        <td>
                            <?php if ($row['PICUTRE']): ?>
                                <a href="<?php echo htmlspecialchars($row['PICUTRE']); ?>" target="_blank">
                                    <img src="<?php echo htmlspecialchars($row['PICUTRE']); ?>" alt="תמונה" width="100">
                                </a>
                            <?php else: ?>
                                אין תמונה
                            <?php endif; ?>
                        </td>
                        <td><?php echo $row['IS_SOS'] ? 'כן' : 'לא'; ?></td>
                        <td><?php echo htmlspecialchars($row['STATUS']); ?></td>
                        <td><?php echo htmlspecialchars($row['DATE']); ?></td>
                        <td><?php echo htmlspecialchars($row['admin_comment']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="no-data">לא נמצאו קריאות סגורות.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
