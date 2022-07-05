<?php
// Файлы phpmailer
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

$title = "Тема письма";
$file = $_FILES['file'];

$c = true;

//Преобразование из JSON в обычный формат
$jsonText = $_POST['Товары'];
$myArray = json_decode($jsonText, true);

// Формирование самого письма
$prod = '';

foreach ($myArray as $key => $value) {
    $cat = $value["category"];
    $title = $value["title"];
    $quantity = $value["quantity"];
    $price = $value["price"];
    $prod .= "
        <tr>
            <td style='padding: 10px; border: #e9e9e9 1px solid;'>$title</td>
            <td style='padding: 10px; border: #e9e9e9 1px solid;'>$quantity</td>
            <td style='padding: 10px; border: #e9e9e9 1px solid;'>$price</td>
        </tr>
        ";
}

$title = "Заказ из магазина 'Impulse'";
foreach ( $_POST as $key => $value ) {
    if ( $value != "" && $key != "project_name" && $key != "admin_email" && $key != "form_subject" && $key != "Товары" ) {
    $body .= "
    " . ( ($c = !$c) ? '<tr>':'<tr style="background-color: #f8f8f8;">' ) . "
        <td style='padding: 10px; border: #e9e9e9 1px solid;'><b>$key</b></td>
        <td style='padding: 10px; border: #e9e9e9 1px solid;'>$value</td>
    </tr>
    ";
    }
}

$body = "<table style='width: 100%;'>$body . $prod</table>";

// Настройки PHPMailer
$mail = new PHPMailer\PHPMailer\PHPMailer();

try {
    $mail->isSMTP();
    $mail->CharSet = "UTF-8";
    $mail->SMTPAuth   = true;

    // Настройки вашей почты
    $mail->Host       = 'smtp.gmail.com'; // SMTP сервера вашей почты
    $mail->Username   = 'dumplingsoisaigon@gmail.com'; // Логин на почте
    $mail->Password   = 'hdzibdsrvgtjcizn'; // Пароль на почте
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;

    $mail->setFrom('dumplingsoisaigon@gmail.com', 'Заказ из магазина Impulse'); // Адрес самой почты и имя отправителя

    // Получатель письма
    $mail->addAddress('fpigletov@yandex.ru');

  // Прикрипление файлов к письму
    if (!empty($file['name'][0])) {
        for ($ct = 0; $ct < count($file['tmp_name']); $ct++) {
            $uploadfile = tempnam(sys_get_temp_dir(), sha1($file['name'][$ct]));
            $filename = $file['name'][$ct];
            if (move_uploaded_file($file['tmp_name'][$ct], $uploadfile)) {
                $mail->addAttachment($uploadfile, $filename);
                $rfile[] = "Файл $filename прикреплён";
            } else {
                $rfile[] = "Не удалось прикрепить файл $filename";
            }
        }
    }

    // Отправка сообщения
    $mail->isHTML(true);
    $mail->Subject = $title;
    $mail->Body = $body;

    $mail->send();

} catch (Exception $e) {
    $status = "Сообщение не было отправлено. Причина ошибки: {$mail->ErrorInfo}";
}