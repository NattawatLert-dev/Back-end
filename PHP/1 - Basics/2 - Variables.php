<?php
    // ตัวแปร (variable) = พื้นที่เก็บข้อมูลที่สามารถนำกลับมาใช้ซ้ำได้
    //                     สามารถเก็บข้อมูลประเภท String, Integer, Float และ Boolean

    $name = "Nattawat";
    $food = "pizza";
    $email = "fake123@gmail.com";

    $age = 21;
    $users = 2;
    $quantity = 3;

    $gpa = 2.5;
    $price = 4.99;
    $tax_rate = 5.1;

    $employed = true;
    $online = false;
    $for_sale = true;

    $total = null;

    echo "Hello {$name} <br>";                // Hello Nattawat
    echo "{$name} like {$food} <br>";         // Nattawat like pizza
    echo "Your email is {$email} <br>";       // Your email is fake123@gmail.com 

    echo "You are {$age} <br>";                          // You are 21
    echo "There are {$users} users online <br>";         // There are 2 users online
    echo "You would like to buy {$quantity} items <br>"; // You would like to buy 3 items
    
    echo "Your gpa is: {$gpa} <br>";                 // Your gpa is: 2.5
    echo "You pizza is \${$price} <br>";             // You pizza is $4.99
    echo "The slaes tax rate is: {$tax_rate}% <br>"; // The slaes tax rate is: 5.1%
   
    echo "Online status: {$online} <br>";            // Online status: 1

    $total = $quantity * $price;
    echo "Your total is: \${$total}";     // Your total is: $14.97
?> 
