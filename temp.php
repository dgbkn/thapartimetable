<?php


$subject = "UES103";
$regex = '/^[UP][A-Z0-9]{5}$/';

if (preg_match($regex, $subject, $matches)) {
    // The first word of the string matches the regex
    $firstWord = $matches[0];
    echo "First word matched: " . $firstWord;
} else {
    // The first word of the string does not match the regex
    echo "No match found for the first word.";
}

  ?>