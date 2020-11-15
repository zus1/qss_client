<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IsbnGeneratorTest extends WebTestCase
{
    public function testIsbnGenerate()
    {
        self::bootKernel();
        $container = self::$container;
        $isbnGenerator = $container->get("App\Service\Isbn");

        $num = 10;
        $generated = $isbnGenerator->generate($num);

        $this->assertIsArray($generated);
        $this->assertCount($num, $generated);

        $isbnLength = 13;
        foreach($generated as $g) {
            $this->assertEquals($isbnLength, strlen($g));
        }
    }
}
