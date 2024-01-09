<?php

require 'vendor/autoload.php';
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Reader\XLSX\Sheet;

// Load the Excel file
$path = 'time.xlsx';
$reader = ReaderEntityFactory::createXLSXReader();
$reader->open($path);

$sheetData = [];

// Check if there is at least one sheet in the file
$sheetIterator = $reader->getSheetIterator();
if (!$sheetIterator->valid()) {
    echo 'No sheets found in the Excel file.';
    exit;
}

// Read each cell of each row of the first sheet
/** @var Sheet $firstSheet */
$firstSheet = $sheetIterator[2];
foreach ($firstSheet->getRowIterator() as $row) {
    $rowData = [];
    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false); // Include empty cells as well

    foreach ($cellIterator as $cell) {
        $cellValue = $cell->getValue();

        // Check if the current cell is part of a merged cell
        $isMergedCell = false;
        $mergedRanges = $firstSheet->getMergedRanges();
        foreach ($mergedRanges as $mergedRange) {
            if ($cell->isInRange($mergedRange)) {
                // If the cell is part of a merged cell, get the rowspan and colspan
                $startCellCoordinates = $mergedRange->getStartCoordinates();
                $endCellCoordinates = $mergedRange->getEndCoordinates();
                $rowspan = $endCellCoordinates[0] - $startCellCoordinates[0] + 1;
                $colspan = $endCellCoordinates[1] - $startCellCoordinates[1] + 1;

                // Mark the current cell as a merged cell and break the loop
                $isMergedCell = true;
                break;
            }
        }

        // If the current cell is not part of a merged cell, use default rowspan and colspan values
        if (!$isMergedCell) {
            $rowspan = 1;
            $colspan = 1;
        }

        // Add the cell value, rowspan, and colspan to the row data
        $rowData[] = [
            'value' => $cellValue,
            'rowspan' => $rowspan,
            'colspan' => $colspan,
        ];
    }

    $sheetData[] = $rowData;
}

// Output the table in HTML format with basic styling
echo '<table border="1" style="border-collapse: collapse; font-family: Arial;">';
foreach ($sheetData as $rowData) {
    echo '<tr>';
    foreach ($rowData as $cellData) {
        $rowspan = $cellData['rowspan'];
        $colspan = $cellData['colspan'];
        $value = $cellData['value'];

        // Apply basic styling for cells
        $style = 'padding: 5px; text-align: center;';

        // Output the cell with rowspan and colspan attributes and styling
        echo '<td rowspan="' . $rowspan . '" colspan="' . $colspan . '" style="' . $style . '">' . $value . '</td>';
    }
    echo '</tr>';
}
echo '</table>';

$reader->close();

?>