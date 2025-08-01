<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// تأكد من وجود ملف الاتصال بقاعدة البيانات
require_once 'db.php';

// قراءة بيانات POST الخام (raw POST data)
$input_data = json_decode(file_get_contents('php://input'), true);

// التحقق من وجود معرف الطالب في البيانات الخام أو من $_REQUEST
if (!isset($input_data['student_id']) && !isset($_REQUEST['student_id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "معرف الطالب مطلوب",
        "student_info" => null,
        "grades" => [],
        "attendance" => [],
        "average_grade" => 0
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$student_id = isset($input_data['student_id']) ? intval($input_data['student_id']) : intval($_REQUEST['student_id']);

// التحقق من اتصال قاعدة البيانات
if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "خطأ في الاتصال بقاعدة البيانات: " . $conn->connect_error,
        "student_info" => null,
        "grades" => [],
        "attendance" => [],
        "average_grade" => 0
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// جلب معلومات الطالب
$student_sql = "SELECT name, grade FROM students WHERE id = ?";
$student_stmt = $conn->prepare($student_sql);

if (!$student_stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "خطأ في إعداد استعلام الطالب: " . $conn->error,
        "student_info" => null,
        "grades" => [],
        "attendance" => [],
        "average_grade" => 0
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$student_stmt->bind_param("i", $student_id);
$student_stmt->execute();
$student_result = $student_stmt->get_result();
$student_info = $student_result->fetch_assoc();

if (!$student_info) {
    echo json_encode([
        "status" => "error",
        "message" => "لم يتم العثور على الطالب برقم: " . $student_id,
        "student_info" => null,
        "grades" => [],
        "attendance" => [],
        "average_grade" => 0
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// جلب الدرجات مع معلومات المعلم
$grades_sql = "SELECT g.subject_name, g.grade, g.published, g.created_at, t.name AS teacher_name 
               FROM grades g 
               LEFT JOIN teachers t ON g.teacher_id = t.id 
               WHERE g.student_id = ?";
$grades_stmt = $conn->prepare($grades_sql);

if (!$grades_stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "خطأ في إعداد استعلام الدرجات: " . $conn->error,
        "student_info" => null,
        "grades" => [],
        "attendance" => [],
        "average_grade" => 0
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$grades_stmt->bind_param("i", $student_id);
$grades_stmt->execute();
$grades_result = $grades_stmt->get_result();
$grades = [];

while ($row = $grades_result->fetch_assoc()) {
    $grades[] = $row;
}

// جلب معلومات الحضور
$attendance_sql = "SELECT subject, absence_count FROM attendance WHERE student_id = ?";
$attendance_stmt = $conn->prepare($attendance_sql);

if (!$attendance_stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "خطأ في إعداد استعلام الحضور: " . $conn->error,
        "student_info" => null,
        "grades" => [],
        "attendance" => [],
        "average_grade" => 0
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$attendance_stmt->bind_param("i", $student_id);
$attendance_stmt->execute();
$attendance_result = $attendance_stmt->get_result();
$attendance = [];

while ($row = $attendance_result->fetch_assoc()) {
    $attendance[] = $row;
}

// حساب متوسط الدرجات
$total_grade = 0;
$grade_count = 0;
foreach ($grades as $grade) {
    if (isset($grade['published']) && $grade['published'] == 1 && is_numeric($grade['grade'])) {
        $total_grade += floatval($grade['grade']);
        $grade_count++;
    }
}
$average_grade = $grade_count > 0 ? round($total_grade / $grade_count, 2) : 0;

// إعداد الاستجابة النهائية
$response = [
    "status" => "success",
    "student_info" => $student_info,
    "grades" => $grades,
    "attendance" => $attendance,
    "average_grade" => $average_grade
];

echo json_encode($response, JSON_UNESCAPED_UNICODE);

// إغلاق جميع الاستعلامات والاتصال
$student_stmt->close();
$grades_stmt->close();
$attendance_stmt->close();
$conn->close();
?> 