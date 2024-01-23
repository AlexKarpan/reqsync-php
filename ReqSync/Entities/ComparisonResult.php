<?php declare(strict_types=1);

namespace ReqSync\Entities;

class ComparisonResult
{
    public int $reqDocCount = 0;
    public int $sourceCodeCount = 0;
    public int $matchedCount = 0;
    public int $reqDocUnmatchedCount = 0;
    public int $sourceCodeUnmatchedCount = 0;
    public array $reqDocUnmatchedItems = [];
    public array $sourceCodeUnmatchedItems = [];
}