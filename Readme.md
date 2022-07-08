# Parser-tool 

Parser-tool is command to convert data from one format to another.

 - XML to Csv
 - XML to Sqlite [TBD]
 - XML to Google Sheet [TBD]

Built with Docker and [Symfony Components](https://symfony.com/components). as console application.


## How to use
Download or clone project from repository.
    `git clone https://github.com/JanaksinhJadeja/parser-tool.git`

## How to start project

From project root build docker.

    $ docker-compose build

After successfully built start docker container.

    $ docker-compose up

## How to use Application.

From project root run commands.

    $ docker-compose exec $ php bin/console 
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
    completion  Dump the shell completion script
    help        Display help for a command
    list        List commands
    ps
    ps:convert  Utility to convert data from source format to target. Current Supported format is XML to csv


## Converter commands are available as symfony console commands

**XML to CSV Converter**

### -h –help

**View help or available parameters using `--help`:**

     $ docker-compose exec php bin/console ps:convert --help
    Description:
    Utility to convert data from source format to target. Current Supported format is XML to csv
    
    Usage:
    ps:convert [options] [--] <infile>
    
    Arguments:
    infile                              Local file or URL to convert.
    
    Options:
        --source[=SOURCE]               Source type
        --target[=TARGET]               Target type
        --source_type[=SOURCE_TYPE]     Remote or local file. [default: "local"]
    -k, --key[=KEY]                     Key name of target element in XML file. Mandatory when source is XML
    -o, --outfile[=OUTFILE]             Output file to be written [default: "screen"]
    -e, --encoding[=ENCODING]           Set the encoding type. [default: "utf-8"]
    -l, --limit[=LIMIT]                 Limit total lines written to output. [default: 0]
    -h, --help                          Display help for the given command. When no command is given display help for the list command
    -q, --quiet                         Do not output any message
    -V, --version                       Display this application version
    --ansi|--no-ansi                Force (or disable --no-ansi) ANSI output
    -n, --no-interaction                Do not ask any interactive question
    -oc, --only_columns[=ONLY_COLUMNS]  Extract only given columns. Input is by comma(,) saperated values.
    -v|vv|vvv, --verbose                Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug


## Arguments: infile
You must use source **file** path or **web url** as argument.

**URL**

    $ docker-compose exec php bin/console ps:convert --source=xml --target=csv --source_type=remote  --key=PLANT https://www.w3schools.com/xml/plant_catalog.xml

**File**

    $ docker-compose exec php bin/console ps:convert --source=xml --target=csv --source_type=local  --key=item /var/www/data/files/coffee_feed.xml

## --key
The  `-k`  or  `--key`  parameter is use to repeating group in your input. Use for XML file.

## --outfile
The  `-o`  or  `--outfile`  parameter sets the file in which to save the converted results. If no parameter is set, then the output is written to the screen:

       $ docker-compose exec php bin/console ps:convert --source=xml --target=csv --source_type=local  --key=item /var/www/data/files/coffee_feed.xml --outfile=/var/www/data/temp/coffee_feed.csv

## --limit
The  `-l`  or  `--limit`  parameter sets number of records to convert. If no limit is provided it will convert all records. It must be number.

    $ docker-compose exec php bin/console ps:convert --source=xml --target=csv --source_type=local  --key=item /var/www/data/files/coffee_feed.xml --limit=10

## --only_columns
The  `-oc`  or  `--oc`  parameter sets only specific tags you want to convert .

    $ docker-compose exec php bin/console ps:convert --source=xml --target=csv --source_type=local  --key=item /var/www/data/files/coffee_feed.xml --limit=10  --only_columns=entity_id,sku,CategoryName

## --encoding
In order to specify an encoding type for the file, pass the `-e` or `--encoding` parameter. The most common encoding types are UTF-8 and UTF-16. The default for this parameter is **UTF-8** if there is none set.


## Unit Test
    
    `$ docker-compose exec php vendor/bin/phpunit
    PHPUnit 9.5.21 #StandWithUkraine

    Runtime:       PHP 8.1.7
    Configuration: /var/www/phpunit.xml
    
    App (App\Tests\App)
    ✔ App console is working  119 ms
    ✔ App manager returns correct app name  1 ms
    ✔ App manager returns correct app version  5 ms
    ✔ App manager returns correct temp directory  1 ms
    ✔ App manager returns correct logger  1 ms
    
    Convert Command (App\Command\ConvertCommand)
    ✔ Convert command gives runtime exception on no arguments  2 ms
    ✔ Wrong source gives exception  4 ms
    ✔ Wrong target gives exception  5 ms
    ✔ Xml to csv command gives correct output  560 ms
    ✔ Xml to csv command gives correct return on wrong url file  535 ms
    ✔ Xml to csv command with correct file data  67 ms
    ✔ Xml to csv command with wrong file data  32 ms
    ✔ Xml to csv command with no file data  3 ms
    
    Kernel (App\Tests\Kernel)
    ✔ Kernel initialize  2 ms
    
    Xml (App\Tests\Reader\Xml)
    ✔ Xml reader should return file not found on file path not given  1 ms
    ✔ Xml reader should generate runtime exception on key node is not given  1 ms
    ✔ Parse returns correct data  1 ms
    ✔ Extract keys correct data  1 ms
    ✔ Parse returns exception on in correct data  1 ms
    
    Util (App\Tests\Util\Util)
    ✔ Make array flat returns correct array  1 ms
    
    Csv (App\Tests\Writer\Csv)
    ✔ Csv writer display data on screen without header  1 ms
    ✔ Csv writer display data on screen with header  1 ms
    ✔ Csv writer display data on file wit header  55 ms
    ✔ Csv writer display data on file wit header with column  33 ms
    
    Time: 00:01.462, Memory: 10.00 MB
    
    OK (24 tests, 28 assertions)

    `
**Unit Test Coverage command**

    $ docker-compose exec php vendor/bin/phpunit --coverage-html html-coverage

## Large File Testing
    $ docker-compose exec php bin/console ps:convert --source=xml --target=csv --source_type=remote  --key=T http://aiweb.cs.washington.edu/research/projects/xmltk/xmldata/data/tpc-h/lineitem.xml --outfile=/var/www/data/temp/lineitem.csv
## Encoding Testing
    docker-compose exec php bin/console ps:convert --source=xml --target=csv --source_type=local  --key=student /var/www/data/files/encoding_checking2.xml --outfile=/var/www/data/temp/encoding_checking.csv


