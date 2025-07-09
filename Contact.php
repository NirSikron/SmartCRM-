<?php
$pageTitle = "צור קשר! ";  

require "navbar.php";

if (!isset($_SESSION['is_admin'])) {
    header("Location: index.php");
    exit;
}



try {


    // בדיקה אם נשלח טופס
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // שליפת המידע מהטופס
        $full_name = $_SESSION['full_name']; // שם המשתמש מתוך הסשן
        $email = $_POST['email']; // מייל שהוזן בטופס
        $message_content = $_POST['message']; // תוכן ההודעה שהוזן בטופס

        // הכנת שאילתה להוספת המידע לטבלה
        $stmt = $pdo->prepare("
            INSERT INTO messages (full_name, mail, content)
            VALUES (:full_name, :mail, :content)
        ");

        // ביצוע השאילתה עם הנתונים
        $stmt->execute([
            ':full_name' => $full_name,
            ':mail' => $email,
            ':content' => $message_content
        ]);

        // הודעת הצלחה
        $message = "ההודעה נשלחה בהצלחה!";
        $messageClass = "success";
    }
} catch (PDOException $e) {
    // טיפול בשגיאת מסד נתונים
    $message = "שגיאה: לא ניתן לשלוח את ההודעה.";
    $messageClass = "error";
}
?>


    <main class="container mx-auto my-8 p-4 bg-white rounded shadow">
        <h1 class="text-2xl font-bold mb-4">צור קשר עם התמיכה</h1>

        <!-- הודעות הצלחה או שגיאה -->
        <?php if (isset($message)): ?>
            <div class="p-4 mb-4 text-white rounded <?php echo $messageClass === 'success' ? 'bg-green-500' : 'bg-red-500'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- טופס יצירת קשר -->
        <form action="" method="POST" class="space-y-4">
            <div>
                <label for="name" class="block text-gray-700">שם מלא</label>
                <input type="text" id="name" name="full_name" value="<?php echo htmlspecialchars($_SESSION['full_name']); ?>" readonly class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-500">
            </div>

            <div>
                <label for="email" class="block text-gray-700">אימייל</label>
                <input type="email" id="email" name="email" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-500">
            </div>

            <div>
                <label for="message" class="block text-gray-700">תוכן ההודעה</label>
                <textarea id="message" name="message" rows="5" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-500"></textarea>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">שלח</button>
        </form>
    </main>
</body>
</html>
