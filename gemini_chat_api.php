<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$api_key = "YOUR_GEMINI_KEY";
$api_url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=$api_key";
$serpapi_key = "a11825ebe2264e83913e44b22d7c2070ba21b686eacd1467ecba5f1d6296d4f4"; // <-- שים פה את המפתח שלך מ־SerpAPI

// חיפוש חכם בגוגל דרך SerpAPI
function serpapi_google_snippet($query, $api_key) {
    $url = "https://serpapi.com/search.json?q=" . urlencode($query) . "&hl=he&api_key=" . $api_key;
    $response = file_get_contents($url);
    $result = json_decode($response, true);

    // תעדף featured_snippet
    if (!empty($result['answer_box']['snippet'])) {
        return $result['answer_box']['snippet'];
    }
    // נסה snippet מהתוצאה הראשונה
    if (!empty($result['organic_results'][0]['snippet'])) {
        return $result['organic_results'][0]['snippet'];
    }
    // לינק לתוצאה הראשונה אם אין תקציר
    if (!empty($result['organic_results'][0]['link'])) {
        return "קישור למידע: " . $result['organic_results'][0]['link'];
    }
    return null;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=crm;charset=utf8mb4", "root", "");
} catch (PDOException $e) {
    echo json_encode(['reply' => "שגיאת מסד נתונים: " . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userMessage = $_POST['message'] ?? '';

    // בדיקה ב-DB
    $stmt = $pdo->prepare("SELECT Content_call, admin_comment FROM closed_calls WHERE Content_call LIKE :q AND STATUS = 'סגור' AND admin_comment != '' ORDER BY DATE DESC LIMIT 2");
    $stmt->execute([':q' => "%$userMessage%"]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $contextFromDB = "";
    if ($rows) {
        foreach ($rows as $row) {
            $contextFromDB .= "קריאה דומה במערכת: " . $row['Content_call'] . "\nפתרון שניתן: " . $row['admin_comment'] . "\n---\n";
        }
    }

    // חיפוש ברשת - תקציר מהאינטרנט!
    $googleSnippet = serpapi_google_snippet($userMessage, $serpapi_key);

    $prompt = "";
    if ($contextFromDB) {
        $prompt .= "מידע ממערכת שירות:\n$contextFromDB\n";
    }
    if ($googleSnippet) {
        $prompt .= "מידע שמצאתי ברשת: $googleSnippet\n";
    }
    $prompt .= "שאלה של משתמש: $userMessage\n";
    $prompt .= "אנא הסבר בשפה פשוטה, שלב-שלב, מה הפתרון לבעיה. התייחס למידע שמצאתי, אך אם יש צורך – נסח תשובה כללית וברורה גם אם המידע לא מספיק. \n";
    $prompt .= "דוגמה לתשובה טובה: אם שואלים 'המחשב לא נדלק' – ענה כך: ראשית, בדוק שהמחשב מחובר לחשמל. אם הבעיה לא נפתרת, נסה להוציא את הכבל ולהחזירו. אם עדיין לא עובד – פנה לטכנאי.\n";
    
    $data = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $prompt]
                ]
            ]
        ]
    ];

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $api_url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);
    $response = curl_exec($curl);
    curl_close($curl);

    $result = json_decode($response, true);

    // תמיד תן עדיפות לתשובה מהבינה
    if (isset($result['candidates'][0]['content']['parts'][0]['text']) && $result['candidates'][0]['content']['parts'][0]['text']) {
        $reply = $result['candidates'][0]['content']['parts'][0]['text'];
    } elseif ($contextFromDB) {
        $reply = "לא התקבלה תשובה מהבינה, אך מצאתי תשובה מהמערכת:\n" . $contextFromDB;
    } elseif ($googleSnippet) {
        $reply = "לא התקבלה תשובה מהבינה, אך הנה מידע מהרשת:\n" . $googleSnippet;
    } else {
        $reply = "לא נמצאה תשובה. נסה לנסח מחדש או לפנות לתמיכה.";
    }

    echo json_encode(['reply' => $reply]);
    exit;
}
?>
