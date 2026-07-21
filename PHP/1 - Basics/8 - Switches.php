<?php
    // switch = คำสั่งที่ใช้แทนการเขียน if...else if หลาย ๆ เงื่อนไข
    //          ทำให้โค้ดสั้นลง อ่านง่ายขึ้น และมีประสิทธิภาพมากกว่าในบางกรณี

    $grade = 'A';

    switch($grade){
        case "A": 
            echo"You did great";
            break;
        case "B":
            echo"You did good";
        case "C":
            echo"You did okay";
            break;
        case "D":
            echo"You did poorly";
            break;
        case "F":
            echo"You failed";
            break;
        default:
            echo"{$grade} is not valid";
    }

?> 
