<?php
// הגדרות חיבור לבסיס הנתונים
$host = "localhost";
$dbname = "crm";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // הפעלת Session
    session_start();

    // משתנים להודעות (יזוהו דרך Session)
    $message = "";
    $messageClass = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
        if ($_POST['action'] == 'login') {
            $worker_id = $_POST['worker_id'];
            $password = $_POST['password'];

            // הכנת השאילתה לבדיקת המשתמש והסיסמה בבסיס הנתונים
            $stmt = $pdo->prepare("SELECT full_name, is_admin FROM login WHERE worker_id = ? AND password = ?");
            $stmt->execute([$worker_id, $password]);

            if ($stmt->rowCount() > 0) {
                // קבלת תוצאת `full_name` ו-`is_admin`
                //שלוף תוצאה מהמסד אחרי שהרצת שאילתה (SELECT) – והיא מחזירה את המידע בצורת מערך אסוציאטיבי.
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $is_admin = $user['is_admin'];
                $full_name = $user['full_name'];

                // שמירת הנתונים ב-Session
                $_SESSION['is_admin'] = $is_admin;
                $_SESSION['worker_id'] = $worker_id;
                $_SESSION['full_name'] = $full_name;

                if ($is_admin == 0) {
                    // התחברות בהצלחה כמשתמש רגיל
                    $_SESSION['message'] = "ברוך הבא, " . htmlspecialchars($full_name) . "!";
                    $_SESSION['messageClass'] = "success";
                    header("Location: user.php");
                    exit;
                } elseif ($is_admin == 1) {
                    // התחברות בהצלחה כאדמין
                    $_SESSION['message'] = "ברוך הבא אדמין, " . htmlspecialchars($full_name) . "!";
                    $_SESSION['messageClass'] = "success";
                    header("Location: admin.php");
                    exit;
                }
            } else {
                // נתונים שגויים
                $_SESSION['message'] = "שם משתמש או סיסמה שגויים, אנא בדוק את פרטי ההתחברות";
                $_SESSION['messageClass'] = "error";
            }

            // הפניה לדף הנוכחי (Post/Redirect/Get)
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    }

    // קריאה להודעה מה-Session
    if (!empty($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $messageClass = $_SESSION['messageClass'];

        // ניקוי ההודעה לאחר הצגה
        unset($_SESSION['message']);
        unset($_SESSION['messageClass']);
    }
} catch (PDOException $e) {
    // טיפול בשגיאות חיבור לבסיס הנתונים
    die("שגיאת חיבור לשרת: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>דף התחברות</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: linear-gradient(135deg, rgb(254, 199, 79), rgb(147, 154, 251));
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }
    .container {
      background: #fff;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      max-width: 400px;
      width: 100%;
      text-align: center;
    }
    h1 {
      font-size: 1.8rem;
      font-weight: bold;
      margin-bottom: 1.5rem;
    }
    label {
      display: block;
      text-align: right;
      margin-bottom: 0.5rem;
      font-size: 0.9rem;
      color: #333;
    }
    input {
      width: 100%;
      padding: 0.8rem;
      border: 1px solid #ddd;
      border-radius: 6px;
      margin-bottom: 1rem;
      font-size: 0.9rem;
      text-align: right;
    }
    input:focus {
      border-color: #4facfe;
      outline: none;
      box-shadow: 0 0 4px rgba(79, 172, 254, 0.6);
    }
    .forgot-password {
      text-align: right;
      font-size: 0.8rem;
      margin-bottom: 1.5rem;
    }
    .forgot-password a {
      color: #4facfe;
      text-decoration: none;
    }
    .forgot-password a:hover {
      text-decoration: underline;
    }
    .login-button {
      width: 100%;
      padding: 0.8rem;
      background: linear-gradient(to right, #4facfe, rgb(251, 244, 147));
      color: #fff;
      border: none;
      border-radius: 6px;
      font-size: 1rem;
      font-weight: bold;
      cursor: pointer;
      margin-bottom: 1.5rem;
    }
    .login-button:hover {
      opacity: 0.9;
    }
    .message {
      margin-bottom: 1.5rem;
      padding: 0.8rem;
      border-radius: 6px;
      font-size: 0.9rem;
    }
    .success {
      background-color: #d4edda;
      color: #155724;
    }
    .error {
      background-color: #f8d7da;
      color: #721c24;
    }
    .info {
      background-color: #d1ecf1;
      color: #0c5460;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>התחברות</h1>
    <?php if ($message): ?>
      <div class="message <?php echo htmlspecialchars($messageClass); ?>">
        <?php echo htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>
    <form method="post" action="">
      <input type="hidden" name="action" value="login">
      <div>
        <label for="worker_id">שם משתמש</label>
        <input id="worker_id" name="worker_id" type="text" placeholder="הזן את שם המשתמש שלך" required>
      </div>
      <div>
        <label for="password">סיסמה</label>
        <input id="password" name="password" type="password" placeholder="הזן את הסיסמה שלך" required>
      </div>
      <button type="submit" class="login-button">התחבר</button>
    </form>
  </div>
</body>
</html>
