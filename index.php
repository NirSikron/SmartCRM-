<?php
$host = "localhost";
$dbname = "crm";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    session_start();

    $message = "";
    $messageClass = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
        if ($_POST['action'] == 'login') {
            $worker_id = $_POST['worker_id'];
            $password = $_POST['password'];

            $stmt = $pdo->prepare("SELECT full_name, is_admin FROM login WHERE worker_id = ? AND password = ?");
            $stmt->execute([$worker_id, $password]);

            if ($stmt->rowCount() > 0) {
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
    die("שגיאת חיבור לשרת: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SMART CRM - התחברות</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      background: linear-gradient(to right, #ffd466, #b99df0);
      background-image: url("uploads/LOGOCRM.png"); 
      background-repeat: no-repeat;
      background-size: cover;
      background-position: center top;
      padding-top: 30px;
    }

    .logo {
      margin-top: 40px;
      margin-bottom: 20px;
    }

    .logo img {
      max-width: 220px;
      height: auto;
    }

    .container {
      background: rgba(255, 255, 255, 0.95);
      padding: 2.5rem;
      border-radius: 15px;
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
      max-width: 660px;
      width: 90%;
      text-align: center;
    }

    h1 {
      font-size: 2rem;
      font-weight: bold;
      color: #002b5c;
      margin-bottom: 1.5rem;
    }

    label {
      display: block;
      text-align: right;
      margin-bottom: 0.5rem;
      font-size: 0.9rem;
      color: #003366;
    }

    input {
      width: 100%;
      padding: 0.8rem;
      border: 1px solid #ccc;
      border-radius: 6px;
      margin-bottom: 1rem;
      font-size: 0.9rem;
      text-align: right;
      background-color: #f9f9f9;
    }

    input:focus {
      border-color: #004a99;
      outline: none;
      box-shadow: 0 0 5px rgba(0, 74, 153, 0.5);
    }

    .login-button {
      width: 100%;
      padding: 0.9rem;
      background: linear-gradient(to right, #004080, #0073e6);
      color: #fff;
      border: none;
      border-radius: 6px;
      font-size: 1rem;
      font-weight: bold;
      cursor: pointer;
    }

    .login-button:hover {
      opacity: 0.95;
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

  <div class="logo">
    <img src="uploads/LOGO1.png" alt="SMART CRM Logo">
  </div>

  <div class="container">
    <h1>התחברות</h1>
    <?php if (!empty($message)): ?>
      <div class="message <?php echo htmlspecialchars($messageClass); ?>">
        <?php echo htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>
    <form method="post" action="">
      <input type="hidden" name="action" value="login">
      <label for="worker_id">שם משתמש</label>
      <input id="worker_id" name="worker_id" type="text" placeholder="הזן את שם המשתמש שלך" required>

      <label for="password">סיסמה</label>
      <input id="password" name="password" type="password" placeholder="הזן את הסיסמה שלך" required>

      <button type="submit" class="login-button">התחבר</button>
    </form>
  </div>

</body>
</html>
