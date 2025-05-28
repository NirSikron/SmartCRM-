<?php
$pageTitle = "פתיחת קריאה"; // או כל שם מותאם לעמוד הזה

require "navbar.php";

if (!isset($_SESSION['is_admin'])) {
    header("Location: index.php");
    exit;
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $message = "";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $workerId = $_POST['workerId'];
        $callContent = $_POST['callContent'];
        $urgentText = $_POST['urgent'];
        
        $dateOpened = $_POST['dateOpened'];
        $imagePath = "";

        // המרת הערך "כן"/"לא" ל-1/0
        $urgent = ($urgentText === 'כן') ? 1 : 0;

        // העלאת תמונה (אם יש)
        $image = $_FILES['image'];
        if ($image["name"] != "") {
            $imageName = time() . '_' . $image['name'];
            $imageTmpName = $image['tmp_name'];
            $imagePath = 'uploads/' . $imageName;

            move_uploaded_file($imageTmpName, $imagePath);
        }

        // הכנסת הנתונים למסד הנתונים
        $stmt = $pdo->prepare("INSERT INTO calls (worker_id, Content_call, PICUTRE, IS_SOS,STATUS, DATE) 
                                VALUES (:worker_id, :content_call, :picture, :is_sos,:Status, :date)");
        $stmt->execute([
            ':worker_id' => $workerId,
            ':content_call' => $callContent,
            ':picture' => $imagePath,
            ':is_sos' => $urgent,
            ':Status'=>"Open",
            ':date' => $dateOpened
        ]);

        $message = "הפנייה נוספה בהצלחה!";
    }
} catch (PDOException $e) {
    $message = "שגיאה: " . $e->getMessage();
}

date_default_timezone_set('Asia/Jerusalem'); // הגדרת אזור זמן
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>הוספת פנייה</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
      
        .side-menu {
            width: 250px;
            background-color: #003d85;
            color: white;
            height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 20px;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
            position: fixed;
            right: 0; /* מיקום בצד ימין */
            top: 0;
        }

        .side-menu a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .side-menu a i {
            margin-left: 10px;
            font-size: 24px;
        }

        .side-menu a:hover {
            text-decoration: underline;
        }

        .container {
            flex: 1;
            max-width: 600px;
            margin: auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-right: 300px; /* מרווח מהתפריט */
            margin-top: 50px;
        }

        header {
            background-color: #003d85;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 8px 8px 0 0;
        }

        header h1 {
            margin: 0;
            font-size: 18px;
        }

        header a {
            color: white;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
        }

        header a:hover {
            text-decoration: underline;
        }

        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        form input, form textarea, form select, form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        form input:focus, form textarea:focus, form select:focus {
            outline: none;
            border-color: #003d85;
            box-shadow: 0px 0px 5px rgba(0, 61, 133, 0.5);
        }

        form button {
            background-color: #003d85;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        form button:hover {
            background-color: #0056b3;
        }

        form div {
            margin-bottom: 20px; /* ריווח בין שורות */
        }

        .message {
            text-align: center;
            color: green;
            font-size: 16px;
            margin-top: 15px;
        }

        .error {
            text-align: center;
            color: red;
            font-size: 16px;
            margin-top: 15px;
        }
    </style>
</head>
<body>

    <div class="container">
        <header>
            <h1>פורטל קריאות</h1>
        </header>
        <h1>הוספת פנייה חדשה</h1>
        <form action="" method="POST" enctype="multipart/form-data">
            <div>
                <label for="workerId">מספר עובד:</label>
                <input type="text" id="workerId" name="workerId" value="<?php echo $_SESSION['worker_id']; ?>" readonly>
            </div>
            <div>
                <label for="callContent">תוכן הקריאה:</label>
                <textarea id="callContent" name="callContent" rows="4" required></textarea>
            </div>
            <div>
                <label for="image">תמונה להצגה (JPG):</label>
                <input type="file" id="image" name="image" accept=".jpg,.jpeg">
            </div>
            <div>
                <label for="urgent">האם הקריאה דחופה?</label>
                <select id="urgent" name="urgent" required>
                    <option value="לא">לא</option>
                    <option value="כן">כן</option>
                </select>
            </div>
            <div>
                <label for="dateOpened">תאריך פתיחת הפנייה:</label>
                <input type="text" id="dateOpened" name="dateOpened" value="<?php echo date('Y-m-d H:i'); ?>" readonly>
            </div>
            <button type="submit">הוסף פנייה</button>
        </form>
        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
