<?php
session_start();
include '../../config.php';
mysqli_set_charset($conn, "utf8mb4");

/* ===============================
   1. CEK LOGIN ADMIN
================================ */
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

/* ===============================
   2. VALIDASI ID
================================ */
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    die('ID tidak valid');
}
$id = (int)$_POST['id'];

/* ===============================
   3. AMBIL is_active (KHUSUS)
================================ */
$is_active = (isset($_POST['is_active']) && $_POST['is_active'] == 1) ? 1 : 0;

/* ===============================
   4. FIELD TEKS SAJA
================================ */
$fields = [
    'title','description','hero_badge',
    'stat_user','stat_rating','stat_support',
    'feature_1','feature_2','feature_3',
    'step_1','step_2','step_3',
    'testi_1_name','testi_1_text',
    'testi_2_name','testi_2_text',
    'testi_3_name','testi_3_text',
    'faq_1_q','faq_1_a','faq_2_q','faq_2_a',
    'faq_3_q','faq_3_a','faq_4_q','faq_4_a',
    'cta_title','cta_desc','playstore_link','cta_text'
];

/* ===============================
   5. BUILD QUERY
================================ */
$sql = "UPDATE landing SET ";
$params = [];
$types  = '';

foreach ($fields as $field) {
    $sql .= "`$field` = ?, ";
    $params[] = trim($_POST[$field] ?? '');
    $types .= 's';
}

/* is_active */
$sql .= "is_active = ?, ";
$params[] = $is_active;
$types .= 'i';

/* ===============================
   6. HANDLE LOGO (OPTIONAL)
================================ */
if (!empty($_FILES['logo']['name'])) {
    $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif','svg'];

    if (!in_array($ext, $allowed)) {
        die('Format logo tidak didukung');
    }

    if (!is_dir('../../assets/logos')) {
        mkdir('../../assets/logos', 0777, true);
    }

    $logo = 'logo_' . time() . '.' . $ext;
    move_uploaded_file($_FILES['logo']['tmp_name'], "../../assets/logos/$logo");

    $sql .= "logo = ?, ";
    $params[] = $logo;
    $types .= 's';
}

/* ===============================
   7. FINAL QUERY
================================ */
$sql = rtrim($sql, ', ') . " WHERE id = ?";
$params[] = $id;
$types .= 'i';

/* ===============================
   8. EXECUTE
================================ */
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);

header('Location: landing.php?success=1');
exit;