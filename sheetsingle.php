<?php
include('func.php');


function getJsonSingleSheet($sheet){
$singleSheet = $sheet;
$daysrrowindex = 0;
$titleSheet = "";

foreach ($singleSheet as $k => $row) {
  if(in_array("DAY", $row)){
    $daysrrowindex = $k;
    break;
  }

  foreach($row as $el){
    if(!empty($el)){
      $el = trim($el);
      if(!str_contains($titleSheet,$el)){
        $titleSheet.= $el ." ";        
      }
    }
  }

}

$batchList = [];
foreach($singleSheet[$daysrrowindex] as $k => $batches){
      if(!empty($batches)){
         $batches = trim($batches);
        if (preg_match('~[0-9]+~', $batches)) {
          $batchList[$k] = $batches;
        }
      }
}




$suspected_end_common = [];
foreach($singleSheet[$daysrrowindex] as $k => $batches){
      if(empty($batches)){
         $batches = trim($batches);
        if (!preg_match('~[0-9]+~', $batches)) {
          $no_of_subs = count(array_filter(array_column($singleSheet , $k), function($x) { return !empty($x); }));
          $suspected_end_common[$k] = $no_of_subs;
        }
      }
}

$break_start = array_key_first($batchList) - 1;
$end_col = getMagnificentKeys($suspected_end_common);
$end_col[$break_start] = "break";
ksort($end_col);


$lastKey = key(array_slice($singleSheet[$daysrrowindex], -1, 1, true));

foreach ($singleSheet as $k => $row) {
   if ($k < $daysrrowindex + 1 ) {
     unset($singleSheet[$k]);
   }
}




$timeline_nos = arrayColumn($singleSheet , $lastKey - 1);
// $timeline = arrayColumn($singleSheet , $lastKey);  
$timeline = arrayColumn($singleSheet , 2);  



foreach ($end_col as $k => $v) {
  if($v == "break"){
      unset($end_col[$k]);
      $next_break = array_search($v, $end_col); 
      if(!empty($next_break)){
     $lecsToCopy = addArraysLec(arrayColumn($singleSheet , $k+1) , arrayColumn($singleSheet , $next_break));


        for ($x = $k+1; $x < $next_break; $x++) {
         if(array_key_exists($x,$batchList)){
                  // print_r([$batchList[$x],$lecsToCopy]);
           foreach ($singleSheet as $index => $row) {
                 if (array_key_exists($x, $row)) {
                if(!empty($lecsToCopy[$index])){
                   $singleSheet[$index][$x] = $lecsToCopy[$index];
                    }
                 }
           }
         }
        }

       }
      }else if($v == "batchdet"){
        $col_coom = addArrays(arrayColumn($singleSheet , $k-1) , arrayColumn($singleSheet , $k));
        foreach ($singleSheet as $key => $value) {
         $singleSheet[$key][$k - 1] = @$col_coom[$key];
       }
      }
      
}
  



  $finalBatches = [];


foreach($batchList as $batchkey=>$name){
  $batch_det = arrayColumn($singleSheet , $batchkey);  
  // print_r([$timeline,$batch_det]);
  //  $batchList[$batchkey] = ["name" => $batchList[$batchkey] ];
  // $batchList[$batchkey]["shedule"] = etgSchedule($timeline, $batch_det);
  $finalBatches[] = [ "name" => $batchList[$batchkey] , "schedule" =>etgSchedule($timeline, $batch_det) ];
}

return ["name" => trim($titleSheet) , "batches" => $finalBatches];
}


function save_json($path,$array){
 // $fp = fopen($path, 'w');
//fwrite($fp, json_encode($array));
//fclose($fp);
  return file_put_contents($path,json_encode($array));
  
}

function get_json($path){ return json_decode(file_get_contents($path), true);
  
}