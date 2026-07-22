<?php
    // array = "ตัวแปร" ชนิดหนึ่งที่สามารถเก็บข้อมูลได้หลายค่าพร้อมกัน
    //         โดยข้อมูลแต่ละค่าจะถูกเก็บไว้ในตำแหน่ง (Index) ของ Array

    $foods =  array("apple", "orange", "banana", "coconut");

    // echo $foods[0] . "<br>";
    // echo $foods[1] . "<br>";
    // echo $foods[2] . "<br>";
    // echo $foods[3] . "<br>";

    // $foods[0] = "pineapple";
    // array_push($foods, "pineapple");
    // array_pop($foods); =  ลบสมาชิกตัวสุดท้ายของ Array ออก และคืนค่าที่ถูกลบออกมา
    // array_shift($foods); = ลบสมาชิกตัวแรกของ Array ออกและเลื่อนสมาชิกที่เหลือมาแทนที่พร้อมคืนค่าที่ถูกลบออกมา
    // $reversed_foods = array_reverse($foods);
    echo count($foods);

    foreach($foods as $food){
        echo $food . "<br>";
    }
    
?> 
