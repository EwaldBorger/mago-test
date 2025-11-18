<?php declare(strict_types=1);

use Ewald\MagoTest\NeedsParentheses;
use Ewald\MagoTest\NeedsParenthesesObject;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NeedsParentheses::class)]
class NeedsParenthesesTest extends TestCase
{
    public function testConstructWithObjects()
    {
        $items = [
            new NeedsParenthesesObject(['name' => 'a']),
            new NeedsParenthesesObject(['name' => 'b']),
        ];
        $needsParentheses = new NeedsParentheses($items);
        $this->assertCount(2, $needsParentheses->collection);
    }

    public function testConstructWithArrays()
    {
        $items = [
            ['name' => 'a'],
            ['name' => 'b'],
        ];
        $needsParentheses = new NeedsParentheses($items);
        $this->assertCount(2, $needsParentheses->collection);
    }
}
