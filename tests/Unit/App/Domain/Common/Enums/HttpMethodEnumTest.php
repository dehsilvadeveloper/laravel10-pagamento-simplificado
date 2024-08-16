<?php

namespace Tests\Unit\App\Domain\Common\Enums;

use Tests\TestCase;
use App\Domain\Common\Enums\HttpMethodEnum;

class HttpMethodEnumTest extends TestCase
{
    /**
     * @group enums
     * @group common
     */
    public function test_can_get_enum_values(): void
    {
        $this->assertEquals('GET', HttpMethodEnum::GET->value);
        $this->assertEquals('POST', HttpMethodEnum::POST->value);
        $this->assertEquals('PUT', HttpMethodEnum::PUT->value);
        $this->assertEquals('DELETE', HttpMethodEnum::DELETE->value);
        $this->assertEquals('PATCH', HttpMethodEnum::PATCH->value);
    }
}
