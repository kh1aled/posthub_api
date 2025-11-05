<?php 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Origin: http://localhost:3000"); // استبدل بـ Origin الخاص بالتطبيق React
header("Access-Control-Allow-Credentials: true"); // هذا للسماح بإرسال الكوكيز


?>