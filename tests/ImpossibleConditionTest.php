<?php declare(strict_types=1);

use Ewald\MagoTest\ImpossibleCondition;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ImpossibleCondition::class)]
class ImpossibleConditionTest extends TestCase
{
    public function testExampleWithPlainArray(): void
    {
        $plainArray = [
            ['a'],
            ['b'],
            ['c', 'd'],
            [
                ['x'],
                ['y'],
            ],
        ];
        $result = (new ImpossibleCondition())->example($plainArray);
        $this->assertTrue($result);
    }

    public function testExampleWithAssociativeArray(): void
    {
        $associativeArray = [
            'a' => [
                'x' => ['x'],
                'y' => ['y'],
            ],
            'b' => ['b'],
        ];
        $result = (new ImpossibleCondition())->example($associativeArray);
        $this->assertFalse($result);
    }
}
