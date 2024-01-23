<?php declare(strict_types=1);

namespace ReqSync\Services;

use ReqSync\Entities\ReqCollection;
use ReqSync\Entities\ReqItem;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ReqExtractionService
{
    public function extractFromReqDoc(string $reqDocPath): ReqCollection
    {
        return $this->extractFromMarkdownFile($reqDocPath);
    }

    public function extractFromSourceCode(string $sourceCodePath): ReqCollection
    {
        $requirementsCollection = new ReqCollection();

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sourceCodePath));
        foreach ($files as $file) {
            if ($file->isFile() && strtolower($file->getExtension()) === 'php') {
                $content = file_get_contents($file->getPathname());
                $requirementsCollection->merge($this->extractFromPhpCode($content, $file->getPathname()));
            }
        }

        return $requirementsCollection;
    }

    public function extractFromMarkdownFile(string $sourcePath): ReqCollection
    {
        $content = file_get_contents($sourcePath);
        return $this->extractFromText($content, $sourcePath);
    }

    public function extractFromText(string $content, string $filename, int $initialLineNumber = 1): ReqCollection
    {
        $requirementsCollection = new ReqCollection();

        $lines = explode("\n", $content);
        foreach ($lines as $lineNumber => $line) {
            // Searching for the occurrence of `[R]` in the line
            $pos = strpos($line, '[R]');
            if ($pos === false) {
                continue;
            }

            // [R] If the marker is escaped like this `[R]`, skip it.
            if ($pos > 0 && $line[$pos - 1] === '`') {
                continue;
            }

            // [R] Whenever we encounter a marker in the file, we take the rest of the line as a requirement item.
            $substring = trim(substr($line, $pos + strlen('[R]')));

            $reqItem = new ReqItem(
                $substring,
                $filename,
                $lineNumber + $initialLineNumber,
            );

            $requirementsCollection->add($reqItem);
        }

        return $requirementsCollection;
    }

    public function extractFromPhpCode(string $content, string $filename): ReqCollection
    {
        $requirementsCollection = new ReqCollection();

        $tokens = token_get_all($content);

        foreach ($tokens as $token) {
            if (is_array($token)) {
                if ($token[0] == T_COMMENT || $token[0] == T_DOC_COMMENT) {
                    // [R] Whenever we encounter a marker in a comment, we take the rest of the line as a requirement item.
                    $comment = $token[1];
                    $line = $token[2];
                    $requirementsCollection->merge($this->extractFromText($comment, $filename, $line));
                }
            }
        }

        return $requirementsCollection;
    }
}