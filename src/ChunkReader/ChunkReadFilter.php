<?php

namespace App\ChunkReader;

class ChunkReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    /**
     * Start row
     * @var integer
     */
    private $startRow = 0;
    /**
     * End row
     * @var integer
     */
    private $endRow   = 0;

    /**
     * Set the list of rows that we want to read
     * @param   integer $startRow
     * @param   integer $chunkSize
     * @return  void
     */
    public function setRows($startRow, $chunkSize) {
        $this->startRow = $startRow;
        $this->endRow   = $startRow + $chunkSize;
    }

    /**
     * Read the cell
     * @param  string   $column
     * @param  integer  $row
     * @param  string   $worksheetName
     * @return boolean
     */
    public function readCell($columnAddress, $row, $worksheetName = '') {
        //  Only read the heading row, and the configured rows
        if (($row == 1) || ($row >= $this->startRow && $row < $this->endRow)) {
            return true;
        }
        return false;
    }
}
?>