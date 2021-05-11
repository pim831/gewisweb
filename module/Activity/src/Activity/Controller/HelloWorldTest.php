<?php
use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase
{
       public function testCanBeUsedAsString()
    {
        $this->assertEquals(
            'user@example.com',
            Email::fromString('user@example.com')
        );
    }
}


