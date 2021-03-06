<?php

function is_prime ($number)
{
    for($divider = 2; $divider <= sqrt($number); $divider++) {
        if($number % $divider == 0) {
            return false;
        }
    }
    return true;
}

function stringToArray ($str)
{
    return json_decode($str, 1);
}

//function arrToJson (array $arr)
//{
//    return json_encode($arr);
//}

function is_primeOrDivider ($number)
{
    for($divider = 2; $divider <= sqrt($number); $divider++) {
        if($number % $divider == 0) {
            return $divider;
        }
    }
    return 'true';
}

function countData(array $data)
{
    $multiple = $data['firstNum'] * $data['secondNum'];
    $arrSum = str_split($multiple);
    return array_sum($arrSum);
}

function insertToDb($pdo, $value, $queue_name)
{
    $sql = "INSERT INTO queue (value, queue_name) VALUES (?,?)";
    $pdo->prepare($sql)->execute([$value, $queue_name]);
}