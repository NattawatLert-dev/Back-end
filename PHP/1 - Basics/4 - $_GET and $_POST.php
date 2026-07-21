<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <form action="index.php" method="get">
        <label>username:</label><br>
        <input type="text" name="username"><br>
        <label>password:</label><br>
        <input type="password" name="password"><br>
        <input type="submit" value="Log in">
    </form>

    <hr>

    <form action="index.php" method="post">
        <label>quantity:</label><br>
        <input type="text" name="quantity"><br>
        <input type="submit" value="total">
    </form>

</body>
</html>
<?php
    // $_GET และ $_POST = ตัวแปรพิเศษ (Superglobals) ที่ใช้รับข้อมูลจากฟอร์ม HTML
    //                    ข้อมูลจะถูกส่งไปยังไฟล์ที่กำหนดไว้ใน action ของ <form>
    //                    เช่น <form action="some_file.php" method="get">

    // $_GET = ข้อมูลจะถูกแนบไปกับ URL
    //         ไม่ปลอดภัย (ข้อมูลมองเห็นได้ใน URL)
    //         มีข้อจำกัดด้านความยาวของข้อมูล
    //         สามารถบันทึกเป็น Bookmark พร้อมค่าข้อมูลได้
    //         คำขอแบบ GET สามารถถูกแคช (Cache) ได้
    //         เหมาะสำหรับหน้าค้นหา (Search Page)

    // $_POST = ข้อมูลจะถูกส่งอยู่ภายใน Body ของ HTTP Request
    //          ปลอดภัยกว่า (ข้อมูลไม่แสดงใน URL)
    //          ไม่มีข้อจำกัดด้านความยาวของข้อมูล
    //          ไม่สามารถ Bookmark พร้อมข้อมูลได้
    //          คำขอแบบ POST จะไม่ถูกแคช
    //          เหมาะสำหรับการส่งข้อมูลสำคัญ เช่น ชื่อผู้ใช้ รหัสผ่าน หรือข้อมูลในฟอร์ม

    echo "{$_GET["username"]} <br>";
    echo "{$_GET["password"]} <br>";

    //================================

    $item = "pizza";
    $price = 5.99;
    $quantity = $_POST["quantity"];
    $total = null;

    $total = $price * $quantity;

    echo"You have ordered {$quantity} x {$item}/s <br>";
    echo"Your total is: \${$total}";

?> 
