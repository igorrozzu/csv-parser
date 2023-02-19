<?php

namespace AppBundle\Service;

use AppBundle\Helper\ImportHelper;
use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Result;

/**
 * Interface ImportInterface
 * @package AppBundle\Service
 */
interface ImportInterface
{
    /**
     * @param string $name
     * @return string
     */
    public function getEntityName(string $name): string;

    /**
     * @param CsvReader $reader
     * @param $writer
     * @return Result
     */
    public function process(CsvReader $reader, $writer): Result;

    /**
     * @return int
     */
    public function getSkippedRows(): int;

    /**
     * @return array
     */
    public function getExceptions(): array;

    /**
     * @return ImportHelper
     */
    public function getHelper(): ImportHelper;

    /**
     * @param mixed $mapping
     */
    public function setMapping($mapping);
}