<?php

declare(strict_types=1);

namespace App\Converter\Parser;

use App\Converter\Exception\FileNotFoundException;
use App\Converter\Exception\InvalidXmlFormatFoundException;
use App\Util\Util;
use App\Converter\Writer\WriterInterface;
use XMLReader;

/**
 * Primary purpose of XML Parser is to parse given file and push data to target Writer.
 */
class XmlParser implements ParserInterface
{
    private WriterInterface $targetWriter;

    private string $filePath;

    /**
     * @inheritDoc
     */
    public function __construct(WriterInterface $targetWriter, string $filePath)
    {
        $this->targetWriter = $targetWriter;
        $this->filePath = $filePath;
    }

    /**
     * @inheritDoc
     */
    public function parseAndPushData(string $keyNode, array $columns, int $limit = 0, $encoding = 'UTF-8', $noHeading = false)
    {
        if (file_exists($this->filePath)) {
            $xmlReader = new XMLReader();
            $xmlReader->open($this->filePath);
            $i = 0;
            $dataArray = [];
            $firstWrite = true;

            // Disable libxml errors and allow user to fetch error information as needed
            libxml_use_internal_errors(true);
            while ($xmlReader->read()) {
                if ($xmlReader->nodeType == XMLReader::ELEMENT) {
                    if ($xmlReader->name == $keyNode) {
                        $i++;
                        $dataXml = '<?xml version="1.0" encoding="'.$encoding.'"?>';
                        $dataXml .= '<psitem>';
                        $dataXml .= $xmlReader->readInnerXML();
                        $dataXml .= '</psitem>';
                        $dataArray[] = json_decode(json_encode(simplexml_load_string($dataXml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
                        if ($i == $limit) {
                            break;
                        }
                        // write 100 records at a time.
                        if ($i % 100 == 0) {
                            $this->targetWriter->writeData($dataArray, $firstWrite, $columns);
                            $firstWrite = false;
                            $dataArray = [];
                        }
                    }
                }
            }

            // If error found during parsing then throw exception.
            if (libxml_get_last_error()) {
                $errorXML = libxml_get_last_error();
                throw new InvalidXmlFormatFoundException($errorXML->message.' '.$errorXML->file.' '.$errorXML->line);
            }

            $this->targetWriter->writeData($dataArray, $firstWrite, $columns);
        } else {
            throw new FileNotFoundException('Unable to open file.');
        }
    }

    /**
     * @inheritDoc
     */
    public function prepareAllKeys($keyNode, int $limit = 0): array
    {
        if (file_exists($this->filePath)) {
            $xmlReader = new XMLReader();
            $xmlReader->open($this->filePath);
            $i = 0;
            $keys = [];
            libxml_use_internal_errors(true);
            while ($xmlReader->read()) {
                if ($xmlReader->nodeType == XMLReader::ELEMENT) {
                    if ($xmlReader->name == $keyNode) {
                        $i++;
                        $dataXml = '<?xml version="1.0" encoding="UTF-8"?>';
                        $dataXml .= '<psitem>';
                        $dataXml .= $xmlReader->readInnerXML();
                        $dataXml .= '</psitem>';
                        $dataArray = json_decode(json_encode(simplexml_load_string($dataXml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
                        $NewKeys = array_flip(array_keys(Util::makeArrayFlat($dataArray)));
                        $keys = array_merge($keys, $NewKeys);
                        if ($i == $limit) {
                            break;
                        }
                    }
                }
            }

            if (libxml_get_last_error()) {
                $errorXML = libxml_get_last_error();
                throw new InvalidXmlFormatFoundException($errorXML->message.' '.$errorXML->file.' '.$errorXML->line);
            }

            return array_map(function ($input) {
                return null;
            }, $keys) ;
        } else {
            throw new FileNotFoundException('Unable to open file: '.$this->filePath);
        }
    }
}
