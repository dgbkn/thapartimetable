<?php

function parseTime($inputString) {
    // Remove any leading/trailing whitespaces
    $inputString = trim($inputString);

    // Get AM/PM from the last two characters of the input string
    $ampm = substr($inputString, -2);

    // Get the time part by removing the AM/PM from the input string
    $timePart = substr($inputString, 0, -2);
    $timePart = trim($timePart);

    // Separate the hours and minutes by splitting at non-numeric characters
    $timeParts = preg_split('/[^0-9]+/', $timePart, -1, PREG_SPLIT_NO_EMPTY);
    if (count($timeParts) >= 2) {
        $hours = (int) $timeParts[0];
        $minutes = (int) $timeParts[1];
    } else {
        $hours = $minutes = 0;
    }

    // Create a DateTime object using the parsed values
    $dateTimeString = sprintf('%02d:%02d %s', $hours, $minutes, $ampm);
    $dateTimeObject = DateTime::createFromFormat('h:i A', $dateTimeString);

    return $dateTimeObject;
}


// Test the function with sample input strings
$strings = array("9:40: AM", "08:50:AM", "10:30 AM", "6.50 PM");

foreach ($strings as $string) {
    $timeInfo = parseTime($string);

    // Display the extracted values
    print_r($timeInfo);
    echo "        ";
}
  ?>