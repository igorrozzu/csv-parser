<?php

namespace AppBundle\Service;

use AppBundle\Helper\ImportHelper;
use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Result;
use Ddeboer\DataImport\Step\FilterStep;
use Ddeboer\DataImport\Step\MappingStep;
use Ddeboer\DataImport\Step\ValidatorStep;
use Ddeboer\DataImport\Step\ValueConverterStep;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Validator\ValidatorInterface as Validator;

/**
 * Class ImportService
 * @package AppBundle\Service
 */
class ImportService implements ImportInterface
{
    protected $helper;
    protected $validator;
    protected $em;
    protected $skippedRows = 0;
    protected $exceptions = [];
    protected $mapping;

    /**
     * ImportService constructor.
     * @param ImportHelper $helper
     * @param Validator $validator
     * @param EntityManager $em
     */
    public function __construct(ImportHelper $helper, Validator $validator, EntityManager $em)
    {
        $this->validator = $validator;
        $this->em = $em;
        $this->helper = $helper;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getEntityName(string $name): string
    {
        return 'AppBundle:' . ucfirst($name);
    }

    /**
     * @param CsvReader $reader
     * @param $writer
     * @return Result
     */
    public function process(CsvReader $reader, $writer): Result
    {
        $workflow = new Workflow($reader);
        $workflow->setSkipItemOnFailure(true);
        if (isset($writer)) {
            $workflow->addWriter($writer);
        }

        $mapping = new MappingStep($this->mapping);
        return $workflow
            ->addStep($mapping, 1)
            ->process();
    }

    /**
     * @return int
     */
    public function getSkippedRows(): int
    {
        return $this->skippedRows;
    }

    /**
     * @return array
     */
    public function getExceptions(): array
    {
        return $this->exceptions;
    }

    /**
     * @return ImportHelper
     */
    public function getHelper(): ImportHelper
    {
        return $this->helper;
    }

    /**
     * @param mixed $mapping
     */
    public function setMapping($mapping)
    {
        $this->mapping = $mapping;
    }
}