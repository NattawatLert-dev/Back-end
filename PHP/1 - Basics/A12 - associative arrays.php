<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="index.php" method="post">
        <label>Enter a country</label>
        <input type="text" name="country">
        <input type="submit">
    </form>
</body>
</html>
<?php
    // associative array = อาร์เรย์ที่เก็บข้อมูลในรูปแบบ คู่ของ Key => Value
    //                     โดยใช้ Key เป็นตัวระบุเพื่อเข้าถึงข้อมูล
    //                     แทนการใช้ Index ตัวเลขเหมือน Array ทั่วไป
    
    // countries => capitals
    // id => username
    // item => price

    $capitals = array("USA"=>"Washington D.C", 
                      "Japan"=>"Kyoto", 
                      "South Korea"=>"Seoul", 
                      "India"=>"New Delhi");

    // echo $capitals["USA"];

    // $capitals["USA"] = "Las Vegas";
    // array_pop($capitals);
    // array_shift($capitals);
    // $keys = array_keys($capitals);
    // $values = array_values($capitals);
    // $capitals = array_flip($capitals);
    // $capitals = array_reverse($capitals);
    // echo count($capitals);

    // foreach($capitals as $key => $value){
    //     echo"{$key} {$value} <br>";
    // }

    $capital = $capitals[$_POST["country"]];

    echo $capital;
?> 
