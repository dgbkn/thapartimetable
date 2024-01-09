<?php



function getMagnificentKeys($arr)
{
    $keys = [];
    $prev = 0;
    foreach ($arr as $k => $v) {
        if (abs($prev - $v) > 7) {
            // $keys[] = $k;
            $keys[$k] = "break";
        } else {
            $prev = $v;
            $keys[$k] = "batchdet";

        }
    }
    return $keys;
}



function addArrays($a, $b)
{
    foreach ($a as $k => $v) {
        $vv = $b[$k];
        if (!empty($v) && !empty($vv)) {
            $a[$k] = $v . "," . $vv;
        }
    }
    return $a;
}



function checkLec($s)
{
    $sub = subjectReturner($s);
    if ($sub) {
        if ($sub[3] == "L") {
            return true;
        }
    }
    return false;
}

function addArraysLec($a, $b)
{
    $lec = [];
    foreach ($a as $k => $v) {
        if (!empty($a[$k - 1]) && !empty($v) && checkLec($a[$k - 1])) {
            $lec[$k - 1] = $a[$k - 1];
            if (!empty($b[$k])) {
                $lec[$k] = $v . "," . $b[$k];
            } else {
                $lec[$k] = $v;
            }
        } else {
            $lec[$k] = '';
        }
    }
    return $lec;
}

function trimArray(&$array)
{
    while (empty(end($array))) {
        array_pop($array);
    }
    while (empty($array[0])) {
        unset($array[0]);
    }

}

function arrayColumn($multiArray, $index)
{
    $columnIndex = $index;
    $columnWithIndexes = array();
    foreach ($multiArray as $key => $row) {
        if (isset($row[$columnIndex])) {
            $columnWithIndexes[$key] = $row[$columnIndex];
        }
    }
    return $columnWithIndexes;
}



function subjectReturner($subjectString)
{
  $subs=json_decode(file_get_contents("subjects.json"), true);
    $bestSubRegex = '/[UP][A-Za-z]{2}\d{3}/m';

    if (preg_match_all($bestSubRegex, $subjectString, $matches, PREG_SET_ORDER, 0)) {
        // $remainingString = str_replace($matches[0], '', $subjectString);
        $remainingString = preg_replace($bestSubRegex, '', $subjectString);
        $trimmedString = trim($remainingString);
        $trimmedString = ltrim($trimmedString, '/');
        $firstLetter = substr($trimmedString, 0, 1);

        if (!empty($firstLetter)) {
          $names_onlyCode = array_column($matches,0);
          foreach($names_onlyCode as $ok=>$noc){
            $noc = trim($noc);
          if(array_key_exists($noc, $subs)) {
            $names_onlyCode[$ok] = trim($subs[$noc]["name"]) . " " . $noc;
           }
          }

          // print_r($names_onlyCode);
          
          $names = implode('/',$names_onlyCode);
          // print_r([$names,$subjectString]);
            if ($firstLetter == "L") {
                return ["Lecture", $names, 1, $firstLetter];
            } else if ($firstLetter == "T") {
                return ["Tutorial", $names, 1, $firstLetter];
            } else if ($firstLetter == "P") {
                return ["Practical", $names, 2, $firstLetter];
            } else if ($firstLetter == "E") {
                return ["Elective", $names, 1, $firstLetter];
            }
        }
    }
    return false;

}


      

function etgSchedule($timings, $schedule)
{
    // print_r([$timings,$schedule]);
    $days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
    $events = [];
    // $event = ["details": "" ,"time": ""];
    $cur_week_index = 0;
    foreach ($timings as $skey => $time) {
        $start = parseTime($time);
        if($start == false) {
            print_r($time);
            continue;
        }
        $strStart = $start->format('h:i A');

        if ($strStart == "06:50 PM") {
            $cur_week_index++;
        }   

        $weekday = $days[$cur_week_index];
        $s = $schedule[$skey];
        if (!empty($s)) {
            $isSubject = subjectReturner($s);
            if (!empty($isSubject)) {
                [$type, $code, $no_of_row_next, $firstLetter] = $isSubject;

                $m = $no_of_row_next * 50;



                if (!empty($strStart)) {
                        $end = $start->add(new DateInterval('PT' . $m . 'M'));
                        $strEnd = $end->format('h:i A');
                        $fTime = "$strStart - $strEnd";
                        $ll = $no_of_row_next + $skey;

                        $extra = "";
                        for ($i = $skey + 1; $i <= $ll; $i++) {
                            $extra .= ' ' . $schedule[$i];
                        }
                        $details = "$code $type $extra";
                        $details =  str_replace("\n", " ", $details);
                        $events[] = ["details" => $details, "time" => $fTime, "week" => $weekday];
                }

            }
        }

    }
    return $events;
}

function displayShedule($timings, $schedule)
{
    print_r(etgSchedule($timings, $schedule));
}




function parseTime($inputString) {
    $inputString = trim($inputString);
    $ampm = substr($inputString, -2);
    $timePart = substr($inputString, 0, -2);
    $timePart = trim($timePart);

    $timeParts = preg_split('/[^0-9]+/', $timePart, -1, PREG_SPLIT_NO_EMPTY);
    if (count($timeParts) >= 2) {
        $hours = (int) $timeParts[0];
        $minutes = (int) $timeParts[1];
    } else {
        $hours = $minutes = 0;
    }
    $dateTimeString = sprintf('%02d:%02d %s', $hours, $minutes, $ampm);
    $dateTimeObject = DateTime::createFromFormat('h:i A', $dateTimeString);

    return $dateTimeObject;
}



?>