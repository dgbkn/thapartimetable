
<?php

require 'vendor/autoload.php';

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

$path = 'time.xlsx';
# open the file
$reader = ReaderEntityFactory::createXLSXReader();
$reader->open($path);

$sheets = [];
# read each cell of each row of each sheet
foreach ($reader->getSheetIterator() as $sheet) {
  $sheet_ = [];
    foreach ($sheet->getRowIterator() as $row) {
          $row_ =  [];
        foreach ($row->getCells() as $cell) {
         $val =  $cell->getValue();
          if (is_a($val, 'DateTime')){
            $val = $val->format('h:i A');
          }
      $row_[] = $val;
        }
      $sheet_[] = $row_;
    }
  
$sheets[] = ["name" =>$sheet->getName(), "sheet" => $sheet_];
  // print_r($sheet_);

}




$directory = './timetable';

if (!file_exists($directory)) {
  mkdir($directory, 0777, true);
}
  

include('sheetsingle.php');

$all_b = [];


foreach($sheets as $sheet){
  $sheet_json = getJsonSingleSheet($sheet["sheet"]);
  $json_name = trim($sheet["name"]);
  $json_name = str_replace(' ', '_', strtolower($json_name));
  $json_name = str_replace('/', '_or_', $json_name);
  $all_b[] = ["name" =>$sheet["name"],"path"=>$json_name . ".json" ];
  save_json("./timetable/" . $json_name . ".json",$sheet_json); 
  save_json("./timetable/base.json",$all_b); 
}
  
$reader->close();


// include('github.php');

// githubPush();

?>