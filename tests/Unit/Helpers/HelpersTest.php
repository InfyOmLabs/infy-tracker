<?php

namespace Tests\Unit\Helpers;

use Tests\TestCase;

class HelpersTest extends TestCase
{
    public function testExplodeTrimRemoveEmptyValuesFromArray()
    {
        $result = explode_trim_remove_empty_values_from_array(" hello   ");
        $this->assertEquals(["hello"], $result);

        $result = explode_trim_remove_empty_values_from_array(" hello,test   ");
        $this->assertEquals(["hello", "test"], $result);

        $result = explode_trim_remove_empty_values_from_array(" hello , test   ");
        $this->assertEquals(["hello", "test"], $result);

        $result = explode_trim_remove_empty_values_from_array(" hello , ,, test   ");
        $this->assertEquals(["hello", "test"], $result);
    }
}
