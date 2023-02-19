<?php

namespace AppBundle\Service;

use AppBundle\Entity\Product;
use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Result;
use Ddeboer\DataImport\Step\FilterStep;
use Ddeboer\DataImport\Step\MappingStep;
use Ddeboer\DataImport\Step\ValidatorStep;
use Ddeboer\DataImport\Step\ValueConverterStep;
use Doctrine\ORM\EntityManager;
use Ddeboer\DataImport\Workflow\StepAggregator as Workflow;

/**
 * Class ImportService
 * @package AppBundle\Service
 */
class ProductImportService extends ImportService
{
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
        $validate = new ValidatorStep($this->validator);
        $validate->throwExceptions(true);
        $converter = new ValueConverterStep();
        $converter->add('[dateDiscontinued]', function ($dateDiscontinued) {
            return $dateDiscontinued === 'yes' ? new \DateTime() : null;
        });
        $product = new Product();
        foreach ($this->helper->getRules($product) as $attribute => $constraints) {
            foreach ($constraints as $constraint) {
                $validate->add($attribute, $constraint);
            }
        }
        $filter = new FilterStep();
        $costAndStockFilter = function ($data) use ($product) {
            $product->setCost((float) $data['cost']);
            $product->setStock($data['stock']);
            $errors = $this->validator->validate($product, null, ['costAndStockConstraint']);
            if ($errors->count() > 0) {
                ++$this->skippedRows;
                foreach ($errors as $error) {
                    $this->exceptions[] = $error;
                }

                return false;
            }

            return true;
        };
        $filter->add($costAndStockFilter);

        return $workflow
            ->addStep($mapping, 4)
            ->addStep($converter, 3)
            ->addStep($validate, 2)
            ->addStep($filter, 1)
            ->process();
    }
}