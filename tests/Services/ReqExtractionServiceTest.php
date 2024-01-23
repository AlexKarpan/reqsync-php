<?php declare(strict_types=1);

namespace Services;

use ReqSync\Services\ReqExtractionService;

class ReqExtractionServiceTest extends \PHPUnit\Framework\TestCase
{
    public function testExtractFromMarkdown(): void
    {
        $service = new ReqExtractionService();
        $collection = $service->extractFromText(<<<TEXT
            # About
            This is a test req doc.
            
            # Requirements
            [R] First requirement.
            
            Also:
             - [R] Second requirement.
             * [R] Third requirement.
             
            Ignored requirement: `[R]` Fourth requirement.
            TEXT,
            'bogus.txt'
        );

        $this->assertCount(3, $collection->items);

        $this->assertEquals('First requirement.', $collection->items[0]->content);
        $this->assertEquals(5, $collection->items[0]->line);

        $this->assertEquals('Second requirement.', $collection->items[1]->content);
        $this->assertEquals(8, $collection->items[1]->line);

        $this->assertEquals('Third requirement.', $collection->items[2]->content);
        $this->assertEquals(9, $collection->items[2]->line);
    }

    public function testExtractFromPhpCode(): void
    {
        $service = new ReqExtractionService();
        $collection = $service->extractFromText(<<<TEXT
            <?php declare(strict_types=1);
            
            namespace App\Services;
            
            class TestClass 
            {
                /**
                 * Test method.
                 * 
                 * [R] First requirement.
                 
                 * [R] Second requirement.
                 */
                public function testMethod(): void
                {
                    // [R] Third requirement.
                    echo "Hello world!";
                }
            }
            TEXT,
            'bogus.php'
        );

        $this->assertCount(3, $collection->items);

        $this->assertEquals('First requirement.', $collection->items[0]->content);
        $this->assertEquals(10, $collection->items[0]->line);

        $this->assertEquals('Second requirement.', $collection->items[1]->content);
        $this->assertEquals(12, $collection->items[1]->line);

        $this->assertEquals('Third requirement.', $collection->items[2]->content);
        $this->assertEquals(16, $collection->items[2]->line);
    }
}