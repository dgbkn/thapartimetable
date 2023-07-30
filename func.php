<?php

function getMagnificentKeys($arr){
  $keys = [];
  $prev = 0;
  foreach($arr as $k => $v){
     if(abs($prev - $v) > 7){   
        // $keys[] = $k;
       $keys[$k] = "break";
     }else{
     $prev = $v;
     $keys[$k] = "batchdet";

     }
  }
return $keys;
}



function addArrays($a,$b){
  foreach($a as $k=>$v){
    $vv = $b[$k];
    if(!empty($v) && !empty($vv)){
      $a[$k] = $v . "," .$vv; 
    }
  }
  return $a;
}



function checkLec($s){
  if (checkSubjectByRegex($s)){
   $words = explode(' ', $s);
   $firstWord = $words[0];
    if(substr($firstWord, -1) == "L"){
      return true;
    }
   $secWord = $words[1];
  if(!empty($secWord) && $secWord[0] == "L"){
        return true;
  } 
}
  return false;
}

function addArraysLec($a,$b){
  $lec = [];
  foreach($a as $k=>$v){
    if(!empty($a[$k-1]) && !empty($v) && checkLec($a[$k-1])){
      $lec[$k-1] = $a[$k-1];
      if(!empty($b[$k])){
              $lec[$k] = $v . "," .$b[$k]; 
      }else{
              $lec[$k] = $v; 
      }
    }else{
      $lec[$k] = '';
    }
  }
  return $lec;
}

function trimArray(&$array){
  while (empty(end($array)))
{
    array_pop($array);
}
    while (empty($array[0]))
{
  unset($array[0]);
}
  
}

function arrayColumn($multiArray , $index){
$columnIndex = $index;
$columnWithIndexes = array();
foreach ($multiArray as $key => $row) {
    if (isset($row[$columnIndex])) {
        $columnWithIndexes[$key] = $row[$columnIndex];
    }
}
  return $columnWithIndexes;
}


function giveCorrectTime($start,$end,$d){
  $m = 50;
  $firstWord = strtok($d, " ");
  $letterAfterFirstWord = substr($d, strlen($firstWord) + 1, 1);
  
  if($letterAfterFirstWord == "P"){
    $m = 100;
  }
  
  $start_ = DateTime::createFromFormat('h:i A', $start);
  $end_ = DateTime::createFromFormat('h:i A', $end);

  if($end_ < $start_ && !empty($end)){
    $end_ = $start_->add(new DateInterval('PT' . $m . 'M'));
    $end = $start_->format('h:i A');
  }
  
  return "$start - $end";
}


// function etgSchedule($timings, $schedule) {
//   print_r([$timings,$schedule]);
//       $days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' );
//     $events = [];
//     // $event = ["details": "" ,"time": ""];
//     $cur_week_index = 0;  
//     foreach($timings as $skey => $time){
//        if(trim($time) == "06:50 PM"){
//          $cur_week_index++;
//        }

//      $weekday = $days[$cur_week_index];
//       $s = $schedule[$skey];
//       if(!empty($s)){  
//         $prevkey = array_key_last($events);
//         if(str_contains($s,"U")){
//           if(!empty($events)){
          
//           $events[$prevkey]["time"] = giveCorrectTime($events[$prevkey]["time"],$time,$events[$prevkey]["details"]);
//               //end the prev event;array_key_last($arr)
//           }
//           $events[] = ["details"=> "$s" ,"time"=> "$time","week"=> $weekday];
//         }else{
//            $events[$prevkey]["details"] =  $events[$prevkey]["details"] . " " . $s;
//         }          
//       }
  
//     }
//   return $events;
// }

function checkSubjectByRegex($str){
  //function to check subject code..
  $str = trim($str);
  $subjectRegex = '/^[UP][A-Z0-9]{5}$/';
  $subjectRegex = '/^[UP][A-Z0-9]+$/';
  $words = explode(' ', $str);
  $firstWord = $words[0];


  if (preg_match($subjectRegex, $firstWord, $matches)) {
   return true;
  }
  return false;
  
  // if(!empty($firstWord)){
  //   $f = $firstWord[0];
  //   if($f == $l){
  //     return true;
  //   }
  // }
  // return false;
}

function etgSchedule($timings, $schedule) {
  // print_r([$timings,$schedule]);
      $days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' );
    $events = [];
    // $event = ["details": "" ,"time": ""];
    $cur_week_index = 0;  
    foreach($timings as $skey => $time){
       if(trim($time) == "06:50 PM"){
         $cur_week_index++;
       }

       $weekday = $days[$cur_week_index];
       $s = $schedule[$skey];
      if(!empty($s)){  
        if(checkSubjectByRegex($s)){
            $start = $time;
            $start_ = DateTime::createFromFormat('h:i A', $start);
            $firstWord = strtok($s, " ");
            $letterAfterFirstWord = substr($s, strlen($firstWord) + 1, 1);
            
            $no_of_row_next = 1;
            if($letterAfterFirstWord == "P"){
              $no_of_row_next = 2;
            }
            
            $m = $no_of_row_next * 50;
            if(!empty($start_)){
            $end_ = $start_->add(new DateInterval('PT' . $m . 'M'));
            $end = $start_->format('h:i A');
            $ftime = "$start - $end";
            $ll = $no_of_row_next +  $skey;
          
             for ($i = $skey + 1; $i <= $ll; $i++) {
              $s .=  ' ' . $schedule[$i];
              }

            $events[] = ["details"=> "$s" ,"time"=> "$ftime","week"=> $weekday];
            }
          
        }
      }
  
    }
  return $events;
}

function displayShedule($timings, $schedule) {
  print_r(etgSchedule($timings, $schedule));
}




?>