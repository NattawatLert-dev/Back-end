<?php
    // if statement = คำสั่งที่ใช้ตรวจสอบเงื่อนไข หากเงื่อนไขเป็นจริง (true) ให้ทำงานตามคำสั่งที่กำหนด
    //                หากเงื่อนไขเป็นเท็จ (false) จะไม่ทำคำสั่งนั้น

    $age = 21;

    if($age >= 18){
        echo"You may enter this site <br>";
    }
    elseif($age == 0){
        echo"You were just born <br>";
    }
    else{
        echo"You must be 18+ to enter <br>";
    }

    $hour = 100;
    $rate = 15;
    $weekly_pay = null;

    if($hour <= 0){
        $weekly_pay = 0;
    }
    elseif($hour <= 40){
        $weekly_pay = $hour * $rate;
    }
    else{
        $weekly_pay = ($rate * 40) + (($hour - 40) * ($rate * 1.5));
    }

    echo"You made {$weekly_pay} this week";
?> 
