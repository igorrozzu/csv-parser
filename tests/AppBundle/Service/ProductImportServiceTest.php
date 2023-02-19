<?php

namespace Tests\AppBundle\Service;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductImportServiceTest extends WebTestCase
{
    private $importer;

    /**
     * @return mixed
     */
    private function getImporter()
    {
        if ($this->importer === null) {
            $kernel = static::bootKernel();
            $this->importer = $kernel->getContainer()->get('app.import.product');
            $mapping = $kernel->getContainer()->getParameter('mapping.product');
            $this->importer->setMapping($mapping);
        }

        return $this->importer;
    }

    /**
     * @param string $fileName
     * @return string
     */
    private function getFilePath(string $fileName): string
    {
        return 'tests/AppBundle/Files/' . $fileName;
    }

    public function testImportProcessPositive()
    {
        $reader = $this->getImporter()->getHelper()->getReader($this->getFilePath('stock-good.csv'));
        $result = $this->getImporter()->process($reader, null);
        $this->assertEquals($result->getErrorCount(), 0);
        $this->assertEquals($this->getImporter()->getSkippedRows(), 0);
    }

    public function testImportProcessInvalidData()
    {
        $reader = $this->getImporter()->getHelper()->getReader($this->getFilePath('stock-invalid-data.csv'));
        $result = $this->getImporter()->process($reader, null);
        $this->assertEquals($result->getErrorCount(), 4);
        $this->assertEquals($this->getImporter()->getSkippedRows(), 0);
    }

    public function testImportProcessInvalidRuleForCostAndStock()
    {
        $reader = $this->getImporter()->getHelper()->getReader($this->getFilePath('stock-invalid-rule-stock-cost.csv'));
        $result = $this->getImporter()->process($reader, null);
        $this->assertEquals($result->getErrorCount(), 0);
        $this->assertEquals($this->getImporter()->getSkippedRows(), 3);
    }

    public function testImportProcessDuplicateProductCode()
    {
        $this->expectException(UniqueConstraintViolationException::class);
        $reader = $this->getImporter()->getHelper()->getReader($this->getFilePath('stock-dublicate-product-code.csv'));
        $writer = $this->getImporter()->getHelper()->getDoctrineWriter(false, 'AppBundle:Product');
        $this->getImporter()->process($reader, $writer);
    }
}
