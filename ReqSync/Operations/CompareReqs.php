<?php declare(strict_types=1);

namespace ReqSync\Operations;

use ReqSync\Entities\ComparisonResult;
use ReqSync\Services\ReqCollectionComparisonService;
use ReqSync\Services\ReqExtractionService;

class CompareReqs
{
    public function __construct(
        private readonly ReqExtractionService $reqExtractionService,
        private readonly ReqCollectionComparisonService $reqCollectionComparisonService,
    ) {
    }

    public function __invoke(string $sourceCodePath, string $reqDocPath): ComparisonResult
    {
        $reqDocRequirementsCollection = $this->reqExtractionService->extractFromReqDoc($reqDocPath);
        $sourceCodeRequirementsCollection = $this->reqExtractionService->extractFromSourceCode($sourceCodePath);

        return $this->reqCollectionComparisonService->compare($reqDocRequirementsCollection, $sourceCodeRequirementsCollection);
    }
}