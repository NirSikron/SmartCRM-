<?php
session_start();

$host = "localhost";
$dbname = "crm";
$username = "root";
$password = "";

$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>

<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title><?php echo isset($pageTitle) ? $pageTitle : "SMART CRM"; ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<body class="bg-gray-200">
  <nav class="bg-gray-900 text-white p-4 flex justify-between items-center">
    <div class="flex items-center space-x-4">
        <span class="font-bold">ברוך הבא, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</span>
    </div>
    
    <div class="flex space-x-6">


        <?php if ($_SESSION['is_admin'] == 1)
        { ?>
            <a href="admin.php" class="hover:text-gray-300 flex items-center space-x-3">
                <i class="fas fa-home"></i>
                <span>דף הבית</span>
        
        <a href="CallMange.php" class="hover:text-gray-300 flex items-center space-x-3">
            <i class="fas fa-phone"></i>
            <span>ניהול קריאות</span>
        </a>

            <?php } else { ?>
        <a href="user.php" class="hover:text-gray-300 flex items-center space-x-3">
            <i class="fas fa-home"></i>
            <span>דף הבית</span>
        </a>
        <?php } ?>

       
        <a href="OpenCall.php" class="hover:text-gray-300 flex items-center space-x-3">
            <i class="fas fa-briefcase"></i>
            <span>פתיחת קריאה </span>
        <a href="<?php echo isset($_SESSION['is_admin']) && $_SESSION['is_admin'] ? 'closed_calls.php' : 'previous_calls.php'; ?>" class="hover:text-gray-300 flex items-center space-x-3">
    <i class="fas fa-briefcase"></i>
    <span>קריאות סגורות</span>
</a>


 <?php if ($_SESSION['is_admin'] == 0)
        { ?>
        <a href="openCallExplain.php" class="hover:text-gray-300 flex items-center space-x-3">
            <i class="fas fa-phone"></i>
            <span>איך פותחים קריאה?</span>
        </a>
        <?php } ?>

        <a href="chat.php" class="hover:text-gray-300 flex items-center space-x-3">
            <i class="fas fa-chair"></i>
            <span>Chat </span>
        </a>

         <?php if ($_SESSION['is_admin'] == 1)
        { ?>
        <a href="messages.php" class="hover:text-gray-300 flex items-center space-x-3">
            <i class="fas fa-phone"></i>
            <span>הודעות </span>
        </a>
        <?php } ?>
        <a href="Contact.php" class="hover:text-gray-300 flex items-center space-x-3">
            <i class="fas fa-chair"></i>
            <span>צור קשר</span>
        </a>
    </div>
    
    <form action="logout.php" method="post">
        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
            התנתק
        </button>
    </form>
</nav>



