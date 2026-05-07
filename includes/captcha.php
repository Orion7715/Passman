<?php
session_start();

// توليد نص عشوائي
$permitted_chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
$captcha_string = substr(str_shuffle($permitted_chars), 0, 6);
$_SESSION['captcha_result'] = $captcha_string;

// إنشاء الصورة
$image = imagecreatetruecolor(120, 45);
imagealphablending($image, false);
imagesavealpha($image, true);

// الألوان
$background_color = imagecolorallocate($image, 26, 26, 26); // نفس لون الخلفية في تصميمك #1a1a1a
$text_color = imagecolorallocate($image, 52, 211, 153);    // لون الزمرد (Emerald-400)
$noise_color = imagecolorallocate($image, 50, 50, 50);

imagefill($image, 0, 0, $background_color);

// إضافة ضجيج (نقاط وعشوائية) لمنع الـ Bots
for($i=0; $i<100; $i++) {
    imagesetpixel($image, rand(0,120), rand(0,45), $noise_color);
}

// رسم النص (يمكنك استخدام خط .ttf إذا أردت احترافية أكبر)
imagestring($image, 5, 25, 15, $captcha_string, $text_color);

header('Content-type: image/png');
imagepng($image);
imagedestroy($image);
