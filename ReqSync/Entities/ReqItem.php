<?php declare(strict_types=1);

namespace ReqSync\Entities;

class ReqItem
{
    public string $contentHash;

    public function __construct(
        public string $content,
        public string $filename,
        public int $line,
    ) {
        // [R] Let's use SHA-1 like git does.
        $this->contentHash = sha1($content);
    }
}