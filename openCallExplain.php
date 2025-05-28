<?php
require "navbar.php";

if (!isset($_SESSION['is_admin'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <title>דף עם סרטון גדול</title>
    <style>
        body {
            direction: rtl;
            font-family: Arial, sans-serif;
            background: #f8f9fa;
        }
        .video-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }
        video {
            width: 90vw;            /* 90% מרוחב החלון */
            max-width: 1200px;      /* מקסימום 1200 פיקסלים */
            height: auto;           /* שומר על פרופורציה */
            border-radius: 20px;
            box-shadow: 0 4px 16px #8882;
            background: #000;       /* מונע "הברקה" בלבן בהתחלה */
            display: block;
        }
    </style>
</head>
<body>
    <!-- סרטון גדול במרכז הדף -->
    <div class="video-container">
        <video autoplay loop muted>
            <source src="/CRM-Login/uploads/howToOpenCalls.mp4" type="video/mp4">
            הדפדפן שלך לא תומך בוידאו.
        </video>
    </div>
</body>
</html>
