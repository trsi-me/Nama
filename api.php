<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'Config.php';

// معالجة طلبات OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// الحصول على action من GET أو POST
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : 'register');

// ========== معالجة GET requests ==========
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'get_all') {
        try {
            // جمع جميع التسجيلات من جميع الجداول
            $allRegistrations = [];
            
            // التسجيلات في الدورات
            $stmt = $conn->query("SELECT id, name, email, phone, type, title, status, notes, registration_date, 'course' as event_type FROM course_registrations ORDER BY registration_date DESC");
            $courses = $stmt->fetchAll();
            foreach ($courses as $course) {
                $allRegistrations[] = $course;
            }
            
            // التسجيلات في المحاضرات
            $stmt = $conn->query("SELECT id, name, email, phone, type, title, status, notes, registration_date, 'lecture' as event_type FROM lecture_registrations ORDER BY registration_date DESC");
            $lectures = $stmt->fetchAll();
            foreach ($lectures as $lecture) {
                $allRegistrations[] = $lecture;
            }
            
            // التسجيلات في الندوات
            $stmt = $conn->query("SELECT id, name, email, phone, type, title, status, notes, registration_date, 'seminar' as event_type FROM seminar_registrations ORDER BY registration_date DESC");
            $seminars = $stmt->fetchAll();
            foreach ($seminars as $seminar) {
                $allRegistrations[] = $seminar;
            }
            
            // رسائل التواصل
            $stmt = $conn->query("SELECT id, name, email, phone, subject as title, message as notes, status, created_at as registration_date, 'contact' as event_type FROM contact_messages ORDER BY created_at DESC");
            $contacts = $stmt->fetchAll();
            foreach ($contacts as $contact) {
                $allRegistrations[] = $contact;
            }
            
            echo json_encode([
                'success' => true,
                'data' => $allRegistrations,
                'count' => count($allRegistrations)
            ], JSON_UNESCAPED_UNICODE);
            exit();
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'حدث خطأ في جلب البيانات',
                'error' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }
    }
}

// ========== معالجة DELETE requests ==========
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' || ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete')) {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) $data = $_POST;
    
    $id = isset($data['id']) ? intval($data['id']) : 0;
    $table = isset($data['table']) ? $data['table'] : '';
    
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'معرف غير صحيح'
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    // تحديد الجدول إذا لم يتم تحديده
    if (empty($table)) {
        // البحث في جميع الجداول
        $tables = ['course_registrations', 'lecture_registrations', 'seminar_registrations', 'contact_messages'];
        foreach ($tables as $tbl) {
            $stmt = $conn->prepare("SELECT id FROM {$tbl} WHERE id = ?");
            $stmt->execute([$id]);
            if ($stmt->fetch()) {
                $table = $tbl;
                break;
            }
        }
    }
    
    if (empty($table)) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'لم يتم العثور على التسجيل'
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    try {
        $stmt = $conn->prepare("DELETE FROM {$table} WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'تم حذف التسجيل بنجاح'
        ], JSON_UNESCAPED_UNICODE);
        exit();
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'حدث خطأ في حذف البيانات',
            'error' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }
}

// ========== معالجة POST requests (التسجيل) ==========
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'طريقة الطلب غير مسموحة'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// الحصول على البيانات
$data = json_decode(file_get_contents('php://input'), true);

// إذا لم تكن JSON، حاول الحصول من POST عادي
if (!$data) {
    $data = $_POST;
}

// تنظيف البيانات
$name = isset($data['name']) ? trim($data['name']) : '';
$email = isset($data['email']) ? trim($data['email']) : '';
$phone = isset($data['phone']) ? trim($data['phone']) : '';
$type = isset($data['type']) ? trim($data['type']) : '';

// معالجة eventTitle و eventDate من script.js
$title = '';
if (isset($data['eventTitle'])) {
    $title = trim($data['eventTitle']);
} elseif (isset($data['title'])) {
    $title = trim($data['title']);
}

$eventDate = isset($data['eventDate']) ? trim($data['eventDate']) : '';

// معالجة رسائل التواصل
$subject = isset($data['subject']) ? trim($data['subject']) : '';
$message = isset($data['message']) ? trim($data['message']) : '';
$notes = isset($data['notes']) ? trim($data['notes']) : '';

// إذا كانت رسالة تواصل
if ($type === 'رسالة تواصل' || $type === 'contact' || !empty($subject) || !empty($message)) {
    // التحقق من البيانات المطلوبة
    if (empty($name) || empty($email) || empty($phone) || empty($subject) || empty($message)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'جميع الحقول مطلوبة'
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    // التحقق من صحة البريد الإلكتروني
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'البريد الإلكتروني غير صحيح'
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    try {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, subject, message, status, created_at) 
                                VALUES (?, ?, ?, ?, ?, 'جديد', NOW())");
        $stmt->execute([$name, $email, $phone, $subject, $message]);
        
        $lastId = $conn->lastInsertId();
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'تم إرسال رسالتك بنجاح! سنتواصل معك قريباً',
            'data' => [
                'id' => $lastId,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'subject' => $subject,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit();
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'حدث خطأ في إرسال الرسالة',
            'error' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }
}

// التحقق من البيانات المطلوبة للتسجيلات
if (empty($name) || empty($email) || empty($phone) || empty($type)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'جميع الحقول مطلوبة (الاسم، البريد الإلكتروني، الجوال، النوع)'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// التحقق من صحة البريد الإلكتروني
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'البريد الإلكتروني غير صحيح'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// تحديد النوع والجدول المناسب
$normalizedType = '';
$table = '';

// معالجة أنواع مختلفة من النصوص
if (stripos($type, 'دورة') !== false || $type === 'course' || stripos($title, 'دورة') !== false) {
    $normalizedType = 'دورة';
    $table = 'course_registrations';
    if (empty($title)) $title = 'دورة علمية';
} elseif (stripos($type, 'محاضرة') !== false || $type === 'lecture' || stripos($title, 'محاضرة') !== false) {
    $normalizedType = 'محاضرة';
    $table = 'lecture_registrations';
    if (empty($title)) $title = 'محاضرة علمية';
} elseif (stripos($type, 'ندوة') !== false || $type === 'seminar' || stripos($title, 'ندوة') !== false) {
    $normalizedType = 'ندوة';
    $table = 'seminar_registrations';
    if (empty($title)) $title = 'ندوة علمية';
} elseif (stripos($type, 'نشرة') !== false || $type === 'newsletter' || stripos($type, 'نشرة بريدية') !== false) {
    // اشتراك في النشرة البريدية
    try {
        // التحقق من عدم الاشتراك المسبق
        $stmt = $conn->prepare("SELECT id FROM newsletter_subscriptions WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            echo json_encode([
                'success' => false,
                'message' => 'هذا البريد مشترك مسبقاً في النشرة البريدية'
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }
        
        $stmt = $conn->prepare("INSERT INTO newsletter_subscriptions (email, name, subscribed_at) VALUES (?, ?, NOW())");
        $stmt->execute([$email, $name]);
        
        echo json_encode([
            'success' => true,
            'message' => 'تم الاشتراك في النشرة البريدية بنجاح',
            'data' => [
                'id' => $conn->lastInsertId(),
                'email' => $email,
                'name' => $name
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit();
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo json_encode([
                'success' => false,
                'message' => 'هذا البريد مشترك مسبقاً'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'حدث خطأ في النظام: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        exit();
    }
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'نوع التسجيل غير صحيح. استخدم: course/دورة, lecture/محاضرة, seminar/ندوة, أو newsletter/نشرة'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// إدراج البيانات في قاعدة البيانات
try {
    $sql = "INSERT INTO {$table} (name, email, phone, type, title, status, notes, registration_date) 
            VALUES (?, ?, ?, ?, ?, 'ينتظر', ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    $notesText = !empty($eventDate) ? "تاريخ الفعالية: {$eventDate}" : $notes;
    $result = $stmt->execute([$name, $email, $phone, $normalizedType, $title, $notesText]);
    
    if ($result) {
        $lastId = $conn->lastInsertId();
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'تم التسجيل بنجاح! سيتم التواصل معك قريباً',
            'registration_id' => $lastId,
            'data' => [
                'id' => $lastId,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'type' => $normalizedType,
                'title' => $title,
                'status' => 'ينتظر',
                'registration_date' => date('Y-m-d H:i:s')
            ]
        ], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'فشل التسجيل. حاول مرة أخرى'
        ], JSON_UNESCAPED_UNICODE);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'حدث خطأ في النظام',
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>