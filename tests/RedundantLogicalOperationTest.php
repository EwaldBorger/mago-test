<?php declare(strict_types=1);

use Ewald\MagoTest\RedundantLogicalOperation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RedundantLogicalOperation::class)]
class RedundantLogicalOperationTest extends TestCase
{
    public function testFromArrayRight()
    {
        $result = (new RedundantLogicalOperation())->fromArrayRight([[], ['name' => 'a']]);
        $this->assertCount(1, $result);
    }

    public function testFromArrayLeft()
    {
        $result = (new RedundantLogicalOperation())->fromArrayLeft([[], ['name' => 'a']]);
        $this->assertCount(1, $result);
    }
}
