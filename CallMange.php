<?php
require "navbar.php";
if (!isset($_SESSION['is_admin'])) {
    header("Location: index.php");
    exit;
}



$message = ""; // משתנה לאחסון הודעות למשתמש

try {
   

    // טיפול בטופס
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_call'])) {
        $numberCall = $_POST['number_call'];
        $status = $_POST['status'];
        $comment = $_POST['comment'];

        if ($status === 'סגור') {
            // שליפת הקריאה מטבלת `calls`
            $stmt = $pdo->prepare("SELECT * FROM calls WHERE number_call = :number_call");
            $stmt->execute([':number_call' => $numberCall]);
            $callData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($callData) {
                // העברת הקריאה לטבלת `closed_calls`
                $stmt = $pdo->prepare("
                    INSERT INTO closed_calls (number_call, worker_id, Content_call, PICUTRE, IS_SOS, STATUS, DATE, admin_comment)
                    VALUES (:number_call, :worker_id, :Content_call, :PICUTRE, :IS_SOS, :STATUS, :DATE, :admin_comment)
                ");
                $stmt->execute([
                    ':number_call' => $callData['number_call'],
                    ':worker_id' => $callData['worker_id'],
                    ':Content_call' => $callData['Content_call'],
                    ':PICUTRE' => $callData['PICUTRE'],
                    ':IS_SOS' => $callData['IS_SOS'],
                    ':STATUS' => $status,
                    ':DATE' => $callData['DATE'],
                    ':admin_comment' => $comment
                ]);

                // מחיקת הקריאה מטבלת `calls`
                $stmt = $pdo->prepare("DELETE FROM calls WHERE number_call = :number_call");
                $stmt->execute([':number_call' => $numberCall]);

                $message = "הקריאה הועברה בהצלחה לקריאות סגורות.";
            } else {
                $message = "שגיאה: לא נמצאה קריאה להעברה.";
            }
        } else {
            // עדכון סטטוס בטבלת `calls`
            $stmt = $pdo->prepare("UPDATE calls SET STATUS = :status WHERE number_call = :number_call");
            $stmt->execute([
                ':status' => $status,
                ':number_call' => $numberCall
            ]);
            $message = "הסטטוס של הקריאה עודכן בהצלחה.";
        }
    }

    // משיכת כל הקריאות
    $stmt = $pdo->prepare("SELECT number_call, worker_id, Content_call, PICUTRE, IS_SOS, `STATUS`, `DATE` FROM calls");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $message = "שגיאה: " . $e->getMessage();
}
?>

    <style>
        /* עיצוב גוף הדף */
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

        /* עיצוב הודעות */
        .message {
            padding: 10px;
            margin: 20px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* עיצוב הכותרת */
        .header {
            background-color: #1a202c;
            color: white;
            padding: 2px;
            text-align: center;
            font-size: 12px;
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

        /* עיצוב טופס הפעולה */
        .action-form {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .select,
        .textarea,
        .button {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .select {
            background-color: #fff;
        }

        .textarea {
            resize: none;
            height: 60px;
        }

        .button {
            background-color: #003d85;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #0056b3;
        }
    </style>


    <!-- Header -->
    <header class="header">
        <h1 class="header-title">רשימת כל הקריאות</h1>
        <p class="header-subtitle">צפה, עדכן או נהל קריאות קיימות במערכת</p>
    </header>   

    
    <!-- הודעות למשתמש -->
    <?php if (!empty($message)): ?>
        <div class="message <?php echo strpos($message, 'שגיאה') === false ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

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
                    <th>פעולות</th>
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
                                <?php if (!empty($row['PICUTRE'])): ?>
                                    <a href="<?php echo htmlspecialchars($row['PICUTRE']); ?>" target="_blank">
                                        <img src="<?php echo htmlspecialchars($row['PICUTRE']); ?>" alt="תמונה" class="image">
                                    </a>
                                <?php else: ?>
                                    אין תמונה
                                <?php endif; ?>
                            </td>
                            <td><?php echo $row['IS_SOS'] ? 'כן' : 'לא'; ?></td>
                            <td><?php echo htmlspecialchars($row['STATUS']); ?></td>
                            <td><?php echo htmlspecialchars($row['DATE']); ?></td>
                            <td>
                                <form action="" method="POST" class="action-form">
                                    <input type="hidden" name="number_call" value="<?php echo $row['number_call']; ?>">
                                    <select name="status" required class="select">
                                        <option value="פתוח" <?php echo $row['STATUS'] === 'פתוח' ? 'selected' : ''; ?>>פתוח</option>
                                        <option value="סגור" <?php echo $row['STATUS'] === 'סגור' ? 'selected' : ''; ?>>סגור</option>
                                    </select>
                                    <textarea name="comment" placeholder="הזן תגובה" class="textarea"></textarea>
                                    <button type="submit" name="update_call" class="button">עדכן</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="no-data">לא נמצאו קריאות.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
