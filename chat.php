<?php
$pageTitle = "SmartCRM - AI Chat "; // או כל שם מותאם לעמוד הזה

require "navbar.php"; // טעינת הניווט העליון
if (!isset($_SESSION['is_admin'])) {
    header("Location: index.php"); // הגנה למשתמשים לא מחוברים
    exit;
}
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>צ'אט עם Gemini</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* עיצוב כללי לדף */
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(120deg, #f8fafc 0%, #e2e8f0 100%);
            margin: 0;
            padding: 0;
        }

        /* אזור הצ'אט כולו, מתחת ל-navbar */
        .main-chat-area {
            width: 100vw;
            height: calc(100vh - 70px); /* גובה דינמי לפי גובה ה-navbar */
            display: flex;
            flex-direction: column;
        }

        /* תיבת ההודעות (בועות) */
        #chat-box {
            flex: 1;
            padding: 32px 10vw 24px 10vw; /* ריווחים פנימיים */
            background: transparent;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 14px; /* רווח בין בועות */
        }

        /* בועה בסיסית */
        .bubble {
            display: inline-block;
            max-width: 60vw;
            padding: 16px 22px;
            border-radius: 1.1em;
            line-height: 1.7;
            font-size: 1.1em;
            margin-bottom: 6px;
            word-break: break-word;
            box-shadow: 0 2px 12px #0001;
            animation: fadein 0.45s; /* הופעה רכה */
        }

        /* בועה של המשתמש */
        .me {
            align-self: flex-end;
            background: linear-gradient(90deg, #38b6ff 0%, #45eeb3 100%);
            color: #fff;
            border-bottom-right-radius: 0.4em;
        }

        /* בועה של הבוט */
        .bot {
            align-self: flex-start;
            background: #f1f5f9;
            color: #232323;
            border-bottom-left-radius: 0.4em;
        }

        /* שורת הקלט והכפתור */
        .input-row {
            display: flex;
            gap: 8px;
            padding: 16px 10vw 22px 10vw;
            background: transparent;
        }

        /* שדה ההקלדה */
        #user-input {
            flex: 1;
            padding: 13px 16px;
            border: 2px solid #cbd5e1;
            border-radius: 1em;
            font-size: 1em;
            outline: none;
            background: #fff;
            transition: border 0.18s;
            box-shadow: 0 2px 8px #0001;
        }

        /* אפקט צבע כשמתמקדים בשדה */
        #user-input:focus {
            border-color: #38b6ff;
        }

        /* כפתור השליחה */
        #send-btn {
            background: linear-gradient(90deg, #38b6ff 0%, #45eeb3 100%);
            color: #fff;
            border: none;
            padding: 0 32px;
            border-radius: 1em;
            font-size: 1em;
            cursor: pointer;
            box-shadow: 0 2px 8px #38b6ff44;
            transition: background 0.18s;
        }

        /* אפקט ריחוף לכפתור */
        #send-btn:hover {
            background: linear-gradient(90deg, #38b6ff 10%, #34d399 100%);
        }

        /* התאמות למסכים קטנים */
        @media (max-width: 900px) {
            #chat-box, .input-row {
                padding-right: 5vw;
                padding-left: 5vw;
            }
            .bubble { max-width: 80vw; font-size: 1em;}
        }

        @media (max-width: 600px) {
            #chat-box, .input-row {
                padding-right: 2vw;
                padding-left: 2vw;
            }
            .bubble { max-width: 96vw; font-size: 0.97em;}
        }

        /* אנימציית הופעה */
        @keyframes fadein {
            from { opacity: 0; transform: translateY(18px);}
            to { opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
<body>

<!-- אזור הצ'אט כולו -->
<div class="main-chat-area">
    <!-- תיבת ההודעות -->
    <div id="chat-box"></div>

    <!-- שורת הקלט והשליחה -->
    <form class="input-row" onsubmit="sendToChat(); return false;">
        <input type="text" id="user-input" autocomplete="off" placeholder="הקלד שאלה..." />
        <button type="submit" id="send-btn">שלח</button>
    </form>
</div>

<script>
    // פונקציה שמוסיפה הודעה חדשה לצ'אט
    function addMessage(sender, text) {
        let box = document.getElementById('chat-box');
        const msgDiv = document.createElement('div');
        msgDiv.className = 'bubble ' + (sender === 'אתה' ? 'me' : 'bot');
        msgDiv.innerHTML = text.replace(/\n/g, '<br>'); // שומר שורות
        box.appendChild(msgDiv);
        box.scrollTop = box.scrollHeight; // גלילה אוטומטית לתחתית
    }

    // שליחת הודעה לשרת וקבלת תגובה
    async function sendToChat() {
        let input = document.getElementById('user-input');
        let text = input.value.trim();
        if (!text) return;

        // הצגת ההודעה של המשתמש
        addMessage('אתה', text);
        input.value = '';
        input.disabled = true;
        document.getElementById('send-btn').disabled = true;

        // תצוגת "עובד על זה..."
        addMessage('Gemini', '<span style="color:#999;">&#8987; עובד על זה...</span>');

        // שליחת הנתונים ל־gemini_chat_api.php
        const response = await fetch('gemini_chat_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'message=' + encodeURIComponent(text)
        });

        let data = await response.json();

        // מחיקת הודעת "עובד על זה..."
        let box = document.getElementById('chat-box');
        if (box.lastChild && box.lastChild.classList.contains('bot')) {
            box.removeChild(box.lastChild);
        }

        // הצגת תגובת הבוט
        addMessage('Gemini', data.reply);

        // הפעלת שדה קלט מחדש
        input.disabled = false;
        document.getElementById('send-btn').disabled = false;
        input.focus();
    }

    // שליחה גם בלחיצה וגם בלחיצת Enter
    document.getElementById('send-btn').onclick = sendToChat;
    document.getElementById('user-input').onkeydown = (e) => {
        if (e.key === 'Enter') sendToChat();
    };
</script>
</body>
</html>
