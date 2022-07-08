<?php

declare(strict_types=1);

namespace App\Command;

use App\Exception\ReaderServiceNotFoundException;
use App\Exception\WriterServiceNotFoundException;
use App\Services\FileService;
use App\Services\ReaderService;
use App\Reader\Xml;
use App\Writer\Csv;
use App\Services\WriterService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *  Converter Command.
 */
class ConvertCommand extends Command
{
    public const OPTION_SOURCE = 'source';

    public const ARGUMENT_INFILE = 'infile';

    public const OPTION_TARGET = 'target';

    public const OPTION_KEY = 'key';

    public const OPTION_OUTFILE = 'outfile';

    public const OPTION_ENCODING = 'encoding';

    public const OPTION_LIMIT = 'limit';

    public const OPTION_ONLY_COLUMNS = 'only_columns';

    public const OPTION_SOURCE_TYPE = 'source_type';

    protected static $defaultName = 'ps:convert';

    private LoggerInterface $logger;

    private WriterService $writerService;

    private ReaderService $readerService;

    private FileService $fileService;

    public function __construct(
        WriterService $writerService,
        ReaderService $readerService,
        FileService $fileService,
        LoggerInterface $logger,
        string $name = null
    ) {
        $this->writerService = $writerService;
        $this->readerService = $readerService;
        $this->fileService = $fileService;
        $this->logger = $logger;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setDescription(
            'Utility to convert data from source format to target. Current Supported format is XML to csv'
        )
        ->addOption(
            self::OPTION_SOURCE,
            null,
            InputOption::VALUE_OPTIONAL,
            'Source type'
        )
        ->addOption(
            self::OPTION_TARGET,
            null,
            InputOption::VALUE_OPTIONAL,
            'Target type'
        )
        ->addOption(
            self::OPTION_SOURCE_TYPE,
            null,
            InputOption::VALUE_OPTIONAL,
            'Remote or local file.',
            'local'
        )
        ->addArgument(
            self::ARGUMENT_INFILE,
            InputArgument::REQUIRED,
            'Local file or URL to convert.'
        )
        ->addOption(
            self::OPTION_KEY,
            '-k',
            InputOption::VALUE_OPTIONAL,
            'Key name of target element in XML file. Mandatory when source is XML'
        )
        ->addOption(
            self::OPTION_OUTFILE,
            '-o',
            InputOption::VALUE_OPTIONAL,
            'Output file to be written',
            'screen'
        )
        ->addOption(
            self::OPTION_ENCODING,
            '-e',
            InputOption::VALUE_OPTIONAL,
            'Set the encoding type.',
            'utf-8'
        )
        ->addOption(
            self::OPTION_LIMIT,
            '-l',
            InputOption::VALUE_OPTIONAL,
            'Limit total lines written to output.',
            0
        )
        ->addOption(
            self::OPTION_ONLY_COLUMNS,
            '-oc',
            InputOption::VALUE_OPTIONAL,
            'Extract only given columns. Input is by comma(,) saperated values.'
        )
        ;
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $options = $input->getOptions();

        $filePath = $this->fileService->getFile(
            $options[self::OPTION_SOURCE_TYPE],
            $input->getArgument(self::ARGUMENT_INFILE)
        );

        try {
            $reader = match ($options[self::OPTION_SOURCE]) {
                'xml' => $this->readerService->setReader(
                    new Xml([
                        'filePath' => $filePath,
                        'keyNode' => $options[self::OPTION_KEY],
                        'encoding' => $options[self::OPTION_ENCODING]
                    ])
                ),
                default => throw new ReaderServiceNotFoundException(),
            };

            $columns = $reader->extractKeys();

            $writerService = match ($options[self::OPTION_TARGET]) {
                'csv' => $this->writerService->setWriter(
                    new Csv(
                        [
                            'targetFile' => $options[self::OPTION_OUTFILE],
                            'columns' => $columns,
                            'fixColumns' => $options[self::OPTION_ONLY_COLUMNS]
                        ]
                    )
                ),
                default => throw new WriterServiceNotFoundException(),
            };

            foreach ($reader->parse() as $index => $data) {
                if ((int) $options[self::OPTION_LIMIT] > 0 && $options[self::OPTION_LIMIT] == $index) {
                    return self::SUCCESS;
                }
                $writerService->write($data, $index);
            }

            $output->writeln($this->getHelper('formatter')->formatBlock('Data conversion completed for file '.$filePath, 'info'));
        } catch (\RuntimeException $e) {
            $this->logger->error($e->getMessage());
            $output->writeln($this->getHelper('formatter')->formatBlock(['Error!', $e->getMessage()], 'error'));
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
