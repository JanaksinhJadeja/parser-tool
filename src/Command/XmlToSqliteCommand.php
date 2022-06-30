<?php

declare(strict_types=1);

namespace App\Command;

use App\AppManagerInterface;
use App\Converter\Parser\XmlParser;
use App\Converter\Writer\SqliteWriter;
use App\Util\Util;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * XML to Sqlite converter Command.
 */
class XmlToSqliteCommand extends Command
{
    protected static $defaultName = 'ps:xmltosqlite';

    private AppManagerInterface $appManager;

    public function __construct(AppManagerInterface $appManager, string $name = null)
    {
        $this->appManager = $appManager;
        $this->logger = $appManager->getLogger();
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setDescription('Utility to convert XML to Sqlite');
        $this->addArgument('infile', InputArgument::REQUIRED, 'XML file or URL to convert.');
        $this->addArgument('key', InputArgument::REQUIRED, 'Key name of target element in file.');
        $this->addOption('outfile', 'o', InputOption::VALUE_OPTIONAL, 'CSV file to be written', 'screen');
        $this->addOption('encoding', 'e', InputOption::VALUE_OPTIONAL, 'Set the encoding type.', 'utf-8');
        $this->addOption('limit', '-l', InputOption::VALUE_OPTIONAL, 'Limit total lines written to CSV.', 0);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('infile');
        $key = $input->getArgument('key');
        $formatter = $this->getHelper('formatter');
        $options = $input->getOptions();

        if (filter_var($filePath, FILTER_VALIDATE_URL)) {
            try {
                $filePath = Util::writeDataFromURL($filePath);
            } catch (\Exception $e) {
                $errorMessages = ['Error!', $e->getMessage().' '.$filePath];
                $formattedBlock = $formatter->formatBlock($errorMessages, 'error');
                $this->logger->error($e->getMessage().' '.$filePath);
                $output->writeln($formattedBlock);
                return self::FAILURE;
            }
        }

        try {
            $targetWriter = new SqliteWriter($this->appManager, $options['outfile']);
            $xmlParser = new XmlParser($targetWriter, $filePath);
            $columns = $xmlParser->prepareAllKeys($key, (int)$options['limit']);
            $xmlParser->parseAndPushData($key, $columns, (int)$options['limit'], $options['encoding']);
        } catch (\RuntimeException $e) {
            $errorMessages = ['Error!', $e->getMessage()];
            $formattedBlock = $formatter->formatBlock($errorMessages, 'error');
            $this->logger->error($e->getMessage());
            $output->writeln($formattedBlock);
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
