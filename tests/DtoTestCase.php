<?php

namespace Tests;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\Exceptions\CannotCastEnum;
use Spatie\LaravelData\Exceptions\CannotCreateData;

abstract class DtoTestCase extends TestCase
{
    protected function createDto(string $dtoClass, array|Request $data): object
    {
        return $dtoClass::from($data);
    }

    protected function runCreationFromSnakecaseArrayAssertions(string $dtoClass, array $data): void
    {
        $dto = $this->createDto($dtoClass, $data);
        $dtoAsArray = $dto->toArray();

        $this->assertInstanceOf($dtoClass, $dto);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $dtoAsArray[Str::snake($key)]);
        }
    }

    protected function runCreationFromCamelcaseArrayAssertions(string $dtoClass, array $data): void
    {
        $dto = $this->createDto($dtoClass, $data);
        $dtoAsArray = $dto->toArray();

        $this->assertInstanceOf($dtoClass, $dto);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $dtoAsArray[Str::snake($key)]);
        }
    }

    protected function runCreationFromEmptyArrayAssertions(string $dtoClass): void
    {
        $this->expectException(CannotCreateData::class);

        $this->createDto($dtoClass, []);
    }

    protected function runCreationFromArrayWithInvalidValuesAssertions(string $dtoClass, array $data): void
    {
        $this->expectException(ValidationException::class);

        $this->createDto($dtoClass, $data);
    }

    protected function runCreationFromArrayWithInvalidEnumValuesAssertions(string $dtoClass, array $data): void
    {
        $this->expectException(CannotCastEnum::class);

        $this->createDto($dtoClass, $data);
    }

    protected function runCreationFromRequestAssertions(string $dtoClass, Request $request): void
    {
        $requestData = $request->all();
        $dto = $this->createDto($dtoClass, $request);
        $dtoAsArray = $dto->toArray();

        $this->assertInstanceOf($dtoClass, $dto);

        foreach ($requestData as $key => $value) {
            $this->assertEquals($value, $dtoAsArray[Str::snake($key)]);
        }
    }

    protected function runCreationFromEmptyRequestAssertions(string $dtoClass): void
    {
        $this->expectException(ValidationException::class);

        $this->createDto($dtoClass, new Request());
    }

    protected function runCreationFromRequestWithInvalidValuesAssertions(string $dtoClass, Request $request): void
    {
        $this->expectException(ValidationException::class);

        $this->createDto($dtoClass, $request);
    }
}
