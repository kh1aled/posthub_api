<?php
// تأكد من أن الطلب هو POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // تحديد المجلد الذي سيتم حفظ الصورة فيه
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // التحقق من أن الملف هو صورة حقيقية
    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if ($check !== false) {
            echo "الملف هو صورة - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "الملف ليس صورة.";
            $uploadOk = 0;
        }
    }

    // التحقق إذا كان الملف موجودًا مسبقًا
    if (file_exists($target_file)) {
        echo "عذراً، الملف موجود بالفعل.";
        $uploadOk = 0;
    }

    // التحقق من حجم الملف (مثال: عدم قبول الملفات الأكبر من 5 ميجا)
    if ($_FILES["fileToUpload"]["size"] > 5000000) {
        echo "عذراً، حجم الملف كبير جدًا.";
        $uploadOk = 0;
    }

    // السماح بأنواع معينة من الملفات فقط (مثل JPG، PNG)
    if (
        $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif"
    ) {
        echo "عذراً، فقط الملفات من النوع JPG, JPEG, PNG, و GIF مسموحة.";
        $uploadOk = 0;
    }

    // التحقق مما إذا كان قد حدث أي خطأ في الرفع
    if ($uploadOk == 0) {
        echo "عذراً، لم يتم رفع الملف.";
    } else {
        // إذا كانت كل الشروط محققة، نقوم برفع الملف
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo "تم رفع الملف ". basename($_FILES["fileToUpload"]["name"]). " بنجاح.";
        } else {
            echo "عذراً، حدث خطأ أثناء رفع الملف.";
        }
    }
}
?>
