<?php

declare(strict_types=1);

namespace App\Reader;

use App\Exception\FileNotFoundException;
use App\Exception\InvalidXmlFormatFoundException;
use App\Util\Util;
use XMLReader;

/**
 * Primary purpose of Class is to parse given file and return data.
 */
class Xml implements ReaderInterface
{
    private string $filePath;

    private string $keyNode;

    private string $encoding;

    public function __construct(array $options = [])
    {
        if (!isset($options['filePath']) || !file_exists($options['filePath'])) {
            throw new FileNotFoundException();
        }

        if (!isset($options['keyNode']) || $options['keyNode'] == '') {
            throw new \RuntimeException('Key Node is missing');
        }

        $this->filePath = $options['filePath'];
        $this->keyNode  = $options['keyNode'];
        $this->encoding = $options['encoding'] ?? 'UTF-8';
    }


    /**
     * @inheritDoc
     */
    public function parse(): \Generator
    {
        if (file_exists($this->filePath)) {
            $xmlReader = new XMLReader();
            $xmlReader->open($this->filePath);
            libxml_use_internal_errors(true);
            while ($xmlReader->read()) {
                if ($xmlReader->nodeType == XMLReader::ELEMENT) {
                    if ($xmlReader->name == $this->keyNode) {
                        $dataXml = '<?xml version="1.0" encoding="'.$this->encoding.'"?>';
                        $dataXml .= '<psitem>';
                        $dataXml .= $xmlReader->readInnerXML();
                        $dataXml .= '</psitem>';
                        yield json_decode(
                            json_encode(
                                simplexml_load_string(
                                    $dataXml,
                                    'SimpleXMLElement',
                                    LIBXML_NOCDATA
                                )
                            ),
                            true
                        );
                    }
                }
            }

            // If error found during parsing then throw exception.
            if (libxml_get_last_error()) {
                $errorXML = libxml_get_last_error();
                throw new InvalidXmlFormatFoundException($errorXML->message.' '.$errorXML->file.' '.$errorXML->line);
            }
        } else {
            throw new FileNotFoundException('Unable to open file.');
        }
    }

    /**
     * @inheritDoc
     */
    public function extractKeys(): array
    {
        $keys = [];
        foreach ($this->parse() as $data) {
            $NewKeys = array_flip(array_keys(Util::makeArrayFlat($data)));
            $keys = array_merge($keys, $NewKeys);
        }

        return array_combine(array_keys($keys), array_keys($keys));
    }
}
