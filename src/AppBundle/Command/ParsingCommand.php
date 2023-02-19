<?php

namespace AppBundle\Command;

use AppBundle\Service\ImportInterface;
use Ddeboer\DataImport\Result;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ParsingCommand
 * @package AppBundle\Command
 */
class ParsingCommand extends ContainerAwareCommand
{
    private $output;

    /**
     *
     */
    protected function configure()
    {
        $this->setName('app:parse-csv')
            ->setDescription('Parsing CSV file and write data to database')
            ->setHelp('This command allows you to parse CSV file and write data to database');

        $this->addArgument('fileName', InputArgument::REQUIRED, 'The path to file.')
            ->addArgument('entityName', InputArgument::REQUIRED, 'The name of entity.')
            ->addOption('testMode', null, InputOption::VALUE_NONE, 'Run test parsing without inserting to database.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output): bool
    {
        $this->output = $output;
        $fileName = $input->getArgument('fileName');
        $entityName = $input->getArgument('entityName');
        $testMode = $input->getOption('testMode');
        $mapping = $this->getContainer()->getParameter('mapping.' . $entityName);
        if (!$mapping) {
            $this->output->writeln(sprintf('Mapping %s is not found', 'mapping.' . $entityName));
            return false;
        }
        $importer = $this->getContainer()->get('app.import.' . $entityName);
        if (!$importer) {
            $this->output->writeln(sprintf('Import service %s is not found', 'app.import.' . $entityName));
            return false;
        }
        $importer->setMapping($mapping);

        try {
            $reader = $importer->getHelper()->getReader($fileName);
        } catch (\Exception $e) {
            $this->output->writeln($e->getMessage());
            return false;
        }
        $writer = $importer->getHelper()->getDoctrineWriter($testMode, $importer->getEntityName($entityName));
        try {
            $result = $importer->process($reader, $writer);
        } catch (UniqueConstraintViolationException $e) {
            $this->output->writeln('Duplicate field');

            return false;
        } catch (\Exception $e) {
            $this->output->writeln($e->getMessage());

            return false;
        }
        $this->printTotal($result, $importer);
        $this->printReport($result, $importer);

        return true;
    }

    /**
     * @param Result $result
     * @param ImportInterface $service
     */
    protected function printTotal(Result $result, ImportInterface $service)
    {
        $this->output->writeln(sprintf(
            "Total processed count: %d \n Success count: %d \n Failed count: %d \n",
            $result->getTotalProcessedCount() + $service->getSkippedRows(),
            $result->getSuccessCount(),
            $result->getErrorCount() + $service->getSkippedRows()
        ));
    }

    /**
     * @param Result $result
     * @param ImportInterface $service
     */
    protected function printReport(Result $result, ImportInterface $service)
    {
        $counter = 1;
        $message = "Report: \n";
        if ($result->getExceptions()->count() === 0 && count($service->getExceptions()) === 0) {
            $message .= 'All data is valid. The import was successful';
        } else {
            foreach ($result->getExceptions() as $exception) {
                $message .= $exception->getLineNumber() + $counter++." row, invalid data:\n";
                foreach ($exception->getViolations() as $violation) {
                    $message .= "\t".$violation->getMessage()."\n";
                }
            }
            foreach ($service->getExceptions() as $exception) {
                $message .= $exception->getMessage()."\n";
            }
        }

        $this->output->writeln($message);
    }
}
