<?php

namespace Tests\AppBundle\Helper;

use Ddeboer\DataImport\Reader;
use Ddeboer\DataImport\Writer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * Class ImportHelperTest
 * @package Tests\AppBundle\Helper
 */
class ImportHelperTest extends WebTestCase
{
    private $helper;

    /**
     * @return mixed
     */
    private function getHelper()
    {
        if ($this->helper === null) {
            $kernel = static::bootKernel();
            $this->helper = $kernel->getContainer()->get('app.helper.import');
        }

        return $this->helper;
    }

    /**
     * @param string $fileName
     * @return string
     */
    private function getFilePath(string $fileName): string
    {
        return 'tests/AppBundle/Files/' . $fileName;
    }

    public function testGetReaderInvalidFilename()
    {
        $this->expectException(FileNotFoundException::class);
        $this->getHelper()->getReader('123456789');
    }

    public function testGetReaderInvalidFormatFile()
    {
        $this->expectException(FileNotFoundException::class);
        $this->getHelper()->getReader($this->getFilePath('stock.txt'));
    }

    public function testGetReaderPositive()
    {
        $reader = $this->getHelper()->getReader($this->getFilePath( 'stock.csv'));
        if ($reader instanceof Reader) {
            $this->assertTrue(true);
        } else {
            $this->assertFalse(true, 'Object is not Reader type');
        }
    }

    public function testGetDoctrineWriterPositive()
    {
        $writer = $this->getHelper()->getDoctrineWriter(false, 'AppBundle:Product');
        if ($writer instanceof Writer) {
            $this->assertTrue(true);
        } else {
            $this->assertFalse(true, 'Object is not Writer type');
        }
    }

    public function testGetDoctrineWriterInTestMode()
    {
        $writer = $this->getHelper()->getDoctrineWriter(true, 'AppBundle:Product');
        $this->assertNull($writer);
    }

    public function testGetRulesPositive()
    {
        $rules = $this->getHelper()->getRules();
        $this->assertInternalType('array', $rules);
    }
}
