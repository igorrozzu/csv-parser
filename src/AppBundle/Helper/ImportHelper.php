<?php

namespace AppBundle\Helper;

use AppBundle\Component\DoctrineWriter;
use Ddeboer\DataImport\Reader\CsvReader;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Validator\Validator\RecursiveValidator as Validator;

/**
 * Class ImportHelper
 * @package AppBundle\Helper
 */
class ImportHelper
{
    const CSV_EXT = 'csv';
    private $em;
    private $validator;

    /**
     * ImportHelper constructor.
     * @param Validator $validator
     * @param EntityManager $em
     */
    public function __construct(Validator $validator, EntityManager $em)
    {
        $this->validator = $validator;
        $this->em = $em;
    }

    /**
     * @param string $filename
     * @return CsvReader
     * @throws FileNotFoundException
     */
    public function getReader(string $filename): CsvReader
    {
        try {
            $file = new \SplFileObject($filename);
            if ($file->getExtension() === self::CSV_EXT) {
                $csvReader = new CsvReader($file);
                $csvReader->setHeaderRowNumber(0);

                return $csvReader;
            } else {
                throw new FileNotFoundException('Invalid format file. Need csv format');
            }
        } catch (\RuntimeException $e) {
            throw new FileNotFoundException('Failed to open stream: No such file or directory');
        }
    }

    /**
     * @param bool $testMode
     * @param string $entityName
     * @return DoctrineWriter|null
     */
    public function getDoctrineWriter(bool $testMode, string $entityName)
    {
        $doctrineWriter = null;
        if (!$testMode) {
            $doctrineWriter = new DoctrineWriter($this->em, $entityName);
        }

        return $doctrineWriter;
    }

    /**
     * @param $entity
     * @return array
     */
    public function getRules($entity): array
    {
        $constraints = [];
        $rules = $this->validator->getMetadataFor($entity);
        foreach ($rules->properties as $attribute => $propMetadata) {
            foreach ($propMetadata->getConstraints() as $constraint) {
                $constraints[$attribute][] = $constraint;
            }
        }

        return $constraints;
    }
}
