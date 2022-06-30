# Parser-tool 

Parser-tool are commands to convert data from one format to another.

 - XML to Csv
 - XML to Sqlite [TBD]
 - XML to Google Sheet [TBD]

Built with Docker and [Symfony Components](https://symfony.com/components). as console application.


## How to use
Download or clone project from repository.

## How to start project

From project root build docker.

    $ docker-compose build

After successfully built start docker container.

    $ docker-compose up

## How to use Application.

From project root run commands.

    $ docker-compose exec php bin/console 
    "Parser tool" 'v0.1.0'
    
    Usage:
      command [options] [arguments]
    
    Options:
      -h, --help            Display help for the given command. When no command is given display help for the list command
      -q, --quiet           Do not output any message
      -V, --version         Display this application version
          --ansi|--no-ansi  Force (or disable --no-ansi) ANSI output
      -n, --no-interaction  Do not ask any interactive question
      -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
    
    Available commands:
      completion      Dump the shell completion script
      help            Display help for a command
      list            List commands
     ps
      ps:xmltocsv     Utility to convert XML to Csv
      ps:xmltosqlite  Utility to convert XML to Sqlite

## Converter commands are available as symfony console commands

**XML to CSV Converter**

### -h –help

**View help or available parameters using `--help`:**

     $ docker-compose exec php bin/console ps:xmltocsv --help
    Description:
      Utility to convert XML to Csv
    
    Usage:
      ps:xmltocsv [options] [--] <infile> <key>
    
    Arguments:
      infile                         XML file or URL to convert.
      key                            Key name of target element in file.
    
    Options:
      -o, --outfile[=OUTFILE]        CSV file to be written [default: "screen"]
      -e, --encoding[=ENCODING]      Set the encoding type. [default: "utf-8"]
      -l, --limit[=LIMIT]            Limit total lines written to CSV. [default: 0]
      -c, --columns[=COLUMNS]        Extract only given columns.
      -N, --no-heading[=NO-HEADING]  Skip the headers [default: false]
      -h, --help                     Display help for the given command. When no command is given display help for the list command
      -q, --quiet                    Do not output any message
      -V, --version                  Display this application version
          --ansi|--no-ansi           Force (or disable --no-ansi) ANSI output
      -n, --no-interaction           Do not ask any interactive question
      -v|vv|vvv, --verbose           Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

## Arguments: infile and key
You must use source **file** path or **web url** as first argument and **key** as second argument. **key** is  repeating group in your input.:

**URL**

    $ docker-compose exec php bin/console ps:xmltocsv https://www.w3schools.com/xml/plant_catalog.xml PLANT --outfile=/var/www/data/temp/plant_catalog.csv

**File**

    $ docker-compose exec php bin/console ps:xmltocsv /var/www/data/files/coffee_feed.xml item --outfile=/var/www/data/temp/coffee_feed.csv

## --outfile
The  `-o`  or  `--outfile`  parameter sets the file in which to save the converted results. If no parameter is set, then the output is written to the screen:

       $ docker-compose exec php bin/console ps:xmltocsv /var/www/data/files/coffee_feed.xml item --outfile=/var/www/data/temp/coffee_feed.csv

## --limit
The  `-l`  or  `--limit`  parameter sets number of records to convert. If no limit is provided it will convert all records. It must be number.

    $ docker-compose exec php bin/console ps:xmltocsv /var/www/data/files/coffee_feed.xml item --outfile=/var/www/data/temp/coffee_feed.csv  --limit=10

## --columns
The  `-c`  or  `--columns`  parameter sets only specific tags you want to convert .

    $ docker-compose exec php bin/console ps:xmltocsv /var/www/data/files/coffee_feed.xml item --outfile=/var/www/data/temp/coffee_feed.csv  --columns=entity_id,sku,CategoryName

## --no-heading
The  `-N`  or  `--no-heading`  provided then it will convert without keys and just data. Default value is false.

    $ docker-compose exec php bin/console ps:xmltocsv /var/www/data/files/coffee_feed.xml item --outfile=/var/www/data/temp/coffee_feed.csv  --columns=entity_id,sku,CategoryName --no-heading=true

## --encoding
In order to specify an encoding type for the file, pass the `-e` or `--encoding` parameter. The most common encoding types are UTF-8 and UTF-16. The default for this parameter is **UTF-8** if there is none set.


## Unit Test
    
    `$ docker-compose exec php vendor/bin/phpunit
    PHPUnit 9.5.21 #StandWithUkraine
    
    Runtime:       PHP 8.1.7
    Configuration: /var/www/phpunit.xml
    
    App (App\Tests\App)
    ✔ App console is working  107 ms
    ✔ App manager returns correct app name  1 ms
    ✔ App manager returns correct app version  1 ms
    ✔ App manager returns correct temp directory  1 ms
    ✔ App manager returns correct logger  1 ms
    
    Xml To Csv Command (App\Command\XmlToCsvCommand)
    ✔ Xml to csv command gives runtime exception on no arguments  2 ms
    ✔ Xml to csv command gives correct output  530 ms
    ✔ Xml to csv command gives correct return on wrong url file  830 ms
    ✔ Xml to csv command with correct file data  3 ms
    ✔ Xml to csv command with wrong file data  8 ms
    ✔ Xml to csv command with no file data  3 ms
    
    Xml To Sqlite Command (App\Command\XmlToSqliteCommand)
    ✔ Xml to sqlite command gives runtime exception on no arguments  3 ms
    ✔ Xml to sqlite command gives correct output  529 ms
    ✔ Xml to sqlite command gives correct return on wrong url file  441 ms
    ✔ Xml to sqlite command with no file data  5 ms
    
    Xml Parser (App\Tests\Converter\Parser\XmlParser)
    ✔ Xml parser prepares all keys and return correct data  2 ms
    ✔ Xml parser write data on screen without header  4 ms
    ✔ Xml parser with wrong data  2 ms
    ✔ Xml parser parse and push data with no file  1 ms
    
    Csv Writer (App\Tests\Converter\Writer\CsvWriter)
    ✔ Csv writer object created successfully  1 ms
    ✔ Csv writer display data on file without header  6 ms
    ✔ Csv writer display data on file wit header  13 ms
    ✔ Csv writer display data on file wit header with column  3 ms
    ✔ Csv writer display data on screen without header  1 ms
    ✔ Csv writer write data on screen with header  1 ms
    ✔ Csv writer write data on screen with header with column  1 ms
    
    Kernel (App\Tests\Kernel)
    ✔ Kernel initialize  4 ms
    
    Util (App\Tests\Util\Util)
    ✔ Make array flat returns correct array  1 ms
    
    Time: 00:02.537, Memory: 10.00 MB
    
    OK (28 tests, 62 assertions)
    `

**Unit Test Coverage command**

    $ docker-compose exec php vendor/bin/phpunit --coverage-html html-coverage

## Large File Testing

`$ docker-compose exec php bin/console ps:xmltocsv http://aiweb.cs.washington.edu/research/projects/xmltk/xmldata/data/tpc-h/lineitem.xml T --outfile=/var/www/data/temp/lineitem.csv`

## Encoding Testing
`$ docker-compose exec php bin/console ps:xmltocsv /var/www/data/files/encoding_checking2.xml  student -o/var/www/data/temp/encoding_checking.csv`



