<?php declare(strict_types=1);

namespace ReqSync;

use ReqSync\Entities\ComparisonResult;

class ConsoleApp
{
    public const string VERSION = '0.1.0';

    private string $sourceFolderPath;
    private string $reqDocPath;

    public function run(array $args): void
    {
        $this->displayCopyrightMessage();

        // [R] The app can extract snippets with requirements from a requirements doc and from PHP source code and compare them.
        $this->parseAndValidateArgs($args);
        $comparisonResult = $this->handleOperation($this->sourceFolderPath, $this->reqDocPath);
        $this->dumpComparisonResult($comparisonResult);
    }

    /**
     * [R] The app displays a title message at startup.
     */
    private function displayCopyrightMessage(): void
    {
        echo "ReqSync for PHP v." . self::VERSION . " by Alexander Karpan and contributors.\n";
    }

    /**
     * [R] The parameters are provided via the command line:
     */
    private function parseAndValidateArgs(array $args): void
    {
        try {
            // if run it like this: 'php reqsync.php', the first argument is the script name
            // so we need to remove it
            if (isset($args[0]) && $args[0] === $_SERVER['SCRIPT_NAME']) {
                array_shift($args);
            }

            if (count($args) < 2) {
                throw new \InvalidArgumentException('Not enough arguments');
            }

            if (count($args) > 2) {
                throw new \InvalidArgumentException('Too many arguments');
            }

            $this->sourceFolderPath = $args[0];
            if (!file_exists($this->sourceFolderPath) || !is_dir($this->sourceFolderPath)) {
                throw new \InvalidArgumentException('Source folder does not exist');
            }

            $this->reqDocPath = $args[1];
            if (!file_exists($this->reqDocPath)) {
                throw new \InvalidArgumentException('Requirements document does not exist');
            }
        } catch (\InvalidArgumentException $e) {
            // [R] The app displays a help message if the user does not provide the correct arguments.
            echo $e->getMessage() . PHP_EOL;
            $this->displayHelpMessage();
            exit(1);
        }
    }

    private function displayHelpMessage(): void
    {
        echo "Usage: reqsync <source-folder> <req-doc>\n\n";
    }

    private function handleOperation(string $sourceFolderPath, string $reqDocPath): ComparisonResult
    {
        $reqExtractionService = new Services\ReqExtractionService();
        $compareReqsService = new Services\ReqCollectionComparisonService();
        $operation = new Operations\CompareReqs($reqExtractionService, $compareReqsService);
        return $operation->__invoke($sourceFolderPath, $reqDocPath);
    }

    private function dumpComparisonResult(ComparisonResult $comparisonResult): void
    {
        echo "Req doc count: {$comparisonResult->reqDocCount}\n";
        echo "Source code count: {$comparisonResult->sourceCodeCount}\n";
        echo "Matched count: {$comparisonResult->matchedCount}\n";
        echo "Req doc unmatched count: {$comparisonResult->reqDocUnmatchedCount}\n";
        echo "Source code unmatched count: {$comparisonResult->sourceCodeUnmatchedCount}\n";

        if (count($comparisonResult->reqDocUnmatchedItems) > 0) {
            echo "Req doc unmatched items:\n";
            foreach ($comparisonResult->reqDocUnmatchedItems as $item) {
                echo " - {$item->line}: {$item->content}\n";
            }
        }

        if (count($comparisonResult->sourceCodeUnmatchedItems) > 0) {
            echo "Source code unmatched items:\n";
            foreach ($comparisonResult->sourceCodeUnmatchedItems as $item) {
                echo " - {$item->filename}:{$item->line}: {$item->content}\n";
            }
        }
    }
}