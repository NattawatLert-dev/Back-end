<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="index.php" method="post">
        <label>Enter a number to count to:</label><br>
        <input type="text" name="counter">
        <input type="submit" value="start">
    </form>
</body>
</html>

<?php
    // for loop = คำสั่งวนซ้ำที่ใช้ทำงานเดิมซ้ำ ๆ ตามจำนวนครั้งที่กำหนด

    $counter = $_POST["counter"];

    for($i = 1; $i <= $counter; $i++){
        echo$i . "<br>";
    }

?> 
