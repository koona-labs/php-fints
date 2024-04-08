<?php

namespace Abiturma\PhpFints\Tests\Segments;

use Abiturma\PhpFints\Segments\HKEND;
use Abiturma\PhpFints\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class HKENDTest
 * @package Tests\Segments
 */
class HKENDTest extends TestCase
{


    #[Test]
    public function it_builds_an_end_of_dialog_segment()
    {
        $this->assertEquals("HKEND:1:1+myTestId'", (new HKEND())->setDialogId('myTestId')->toString());
    }
}
