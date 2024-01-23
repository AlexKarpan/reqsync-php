<?php declare(strict_types=1);

namespace ReqSync\Entities;

class ReqCollection
{
    public function __construct(
        public array $items = [],
    ) {
    }

    public function add(ReqItem $item): void
    {
        $this->items[] = $item;
    }

    public function merge(ReqCollection $collection): void
    {
        $this->items = array_merge($this->items, $collection->items);
    }

    public function count(): int
    {
        return count($this->items);
    }
}