<?php declare(strict_types=1);

namespace ReqSync\Services;

use ReqSync\Entities\ComparisonResult;
use ReqSync\Entities\ReqCollection;

class ReqCollectionComparisonService
{
    public function compare(ReqCollection $reqDocRequirementsCollection, ReqCollection $sourceCodeRequirementsCollection): ComparisonResult
    {
        // [R] The app calculates the totals:
        $result = new ComparisonResult();
        $result->reqDocCount = $reqDocRequirementsCollection->count();
        $result->sourceCodeCount = $sourceCodeRequirementsCollection->count();

        $reqDocKeyedCollection = $this->keyByContentHash($reqDocRequirementsCollection);

        // [R] The app displays the line numbers of requirements in the requirements doc which are missing from the source code.
        foreach ($sourceCodeRequirementsCollection->items as $item) {
            if (isset($reqDocKeyedCollection[$item->contentHash])) {
                // We count matched items here because one req in the doc can match multiple reqs in the code
                $result->matchedCount++;
            } else {
                $result->sourceCodeUnmatchedCount++;
                $result->sourceCodeUnmatchedItems[] = $item;
            }
        }

        $sourceCodeKeyedCollection = $this->keyByContentHash($sourceCodeRequirementsCollection);

        // [R] The app displays the file names and line numbers of requirements in the source code which are missing from the requirements doc.
        foreach ($reqDocRequirementsCollection->items as $item) {
            if (!isset($sourceCodeKeyedCollection[$item->contentHash])) {
                $result->reqDocUnmatchedCount++;
                $result->reqDocUnmatchedItems[] = $item;
            }
        }

        return $result;
    }

    private function keyByContentHash(ReqCollection $collection): array
    {
        $keyedCollection = [];

        foreach ($collection->items as $item) {
            $keyedCollection[$item->contentHash] = $item;
        }

        return $keyedCollection;
    }
}