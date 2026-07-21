<?php
    // Logical operators = ตัวดำเนินการทางตรรกะ ใช้สำหรับรวมหลายเงื่อนไขเข้าด้วยกัน
    //                     เช่น if(condition1 && condition2)

    // && = เป็นจริง (true) เมื่อทั้งสองเงื่อนไขเป็นจริง
    // || = เป็นจริง (true) เมื่อมีอย่างน้อยหนึ่งเงื่อนไขเป็นจริง
    //  ! = ใช้กลับค่าความจริง ถ้าค่าเดิมเป็น false จะกลายเป็น true
    //                     ถ้าค่าเดิมเป็น true จะกลายเป็น false

    $temp = 25;
    $cloudy = true;

    if($temp >= 0 && $temp <= 30){
        echo"The weather is good.<br>";
    }
    else{
        echo"The weather is bad.<br>";
    }

    if(!$cloudy){
        echo"It's sunny.<br>";
    }
    else{
        echo"It's cloudy.<br>";
    }

?> 
