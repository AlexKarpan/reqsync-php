<?php declare(strict_types=1);

namespace Services;

use ReqSync\Entities\ReqCollection;
use ReqSync\Entities\ReqItem;
use ReqSync\Services\ReqCollectionComparisonService;
use ReqSync\Services\ReqExtractionService;

class ReqCollectionComparisonServiceTest extends \PHPUnit\Framework\TestCase
{
    public function testCompare(): void
    {
        $reqDocCollection = new ReqCollection();
        $reqDocCollection->add(new ReqItem('First requirement.', 'reqDoc.txt', 5));
        $reqDocCollection->add(new ReqItem('Second requirement.', 'reqDoc.txt', 8));
        $reqDocCollection->add(new ReqItem('Third requirement.', 'reqDoc.txt', 9));

        $sourceCodeCollection = new ReqCollection();
        $sourceCodeCollection->add(new ReqItem('First requirement.', 'sourceCode1.php', 17));
        $sourceCodeCollection->add(new ReqItem('Third requirement.', 'sourceCode2.php', 22));
        $sourceCodeCollection->add(new ReqItem('Third requirement.', 'sourceCode3.php', 34));

        $service = new ReqCollectionComparisonService();
        $result = $service->compare($reqDocCollection, $sourceCodeCollection);

        $this->assertEquals(3, $result->reqDocCount);
        $this->assertEquals(3, $result->sourceCodeCount);
        $this->assertEquals(3, $result->matchedCount);
        $this->assertEquals(1, $result->reqDocUnmatchedCount);
        $this->assertEquals(0, $result->sourceCodeUnmatchedCount);
        $this->assertCount(1, $result->reqDocUnmatchedItems);
        $this->assertCount(0, $result->sourceCodeUnmatchedItems);
        $this->assertEquals('Second requirement.', $result->reqDocUnmatchedItems[0]->content);
        $this->assertEquals(8, $result->reqDocUnmatchedItems[0]->line);
        $this->assertEquals('reqDoc.txt', $result->reqDocUnmatchedItems[0]->filename);
    }
}