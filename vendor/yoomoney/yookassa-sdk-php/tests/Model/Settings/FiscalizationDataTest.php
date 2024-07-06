<?php

/*
* The MIT License
*
* Copyright (c) 2024 "YooMoney", NBСO LLC
*
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in
* all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
* THE SOFTWARE.
*/

namespace Tests\YooKassa\Model\Settings;

use Exception;
use Tests\YooKassa\AbstractTestCase;
use Datetime;
use YooKassa\Model\Metadata;
use YooKassa\Model\Settings\FiscalizationData;

/**
 * FiscalizationDataTest
 *
 * @category    ClassTest
 * @author      cms@yoomoney.ru
 * @link        https://yookassa.ru/developers/api
 */
class FiscalizationDataTest extends AbstractTestCase
{
    protected FiscalizationData $object;

    /**
     * @return FiscalizationData
     */
    protected function getTestInstance(): FiscalizationData
    {
        return new FiscalizationData();
    }

    /**
     * @return void
     */
    public function testFiscalizationDataClassExists(): void
    {
        $this->object = $this->getMockBuilder(FiscalizationData::class)->getMockForAbstractClass();
        $this->assertTrue(class_exists(FiscalizationData::class));
        $this->assertInstanceOf(FiscalizationData::class, $this->object);
    }

    /**
     * Test property "enabled"
     * @dataProvider validEnabledDataProvider
     * @param mixed $value
     *
     * @return void
     * @throws Exception
     */
    public function testEnabled(mixed $value): void
    {
        $instance = $this->getTestInstance();
        $instance->setEnabled($value);
        self::assertNotNull($instance->getEnabled());
        self::assertNotNull($instance->enabled);
        self::assertEquals($value, is_array($value) ? $instance->getEnabled()->toArray() : $instance->getEnabled());
        self::assertEquals($value, is_array($value) ? $instance->enabled->toArray() : $instance->enabled);
        self::assertIsBool($instance->getEnabled());
        self::assertIsBool($instance->enabled);
    }

    /**
     * Test invalid property "enabled"
     * @dataProvider invalidEnabledDataProvider
     * @param mixed $value
     * @param string $exceptionClass
     *
     * @return void
     */
    public function testInvalidEnabled(mixed $value, string $exceptionClass): void
    {
        $instance = $this->getTestInstance();

        $this->expectException($exceptionClass);
        $instance->setEnabled($value);
    }

    /**
     * @return array[]
     * @throws Exception
     */
    public function validEnabledDataProvider(): array
    {
        $instance = $this->getTestInstance();
        return $this->getValidDataProviderByType($instance->getValidator()->getRulesByPropName('_enabled'));
    }

    /**
     * @return array[]
     * @throws Exception
     */
    public function invalidEnabledDataProvider(): array
    {
        $instance = $this->getTestInstance();
        return $this->getInvalidDataProviderByType($instance->getValidator()->getRulesByPropName('_enabled'));
    }

    /**
     * Test property "provider"
     * @dataProvider validProviderDataProvider
     * @param mixed $value
     *
     * @return void
     * @throws Exception
     */
    public function testProvider(mixed $value): void
    {
        $instance = $this->getTestInstance();
        $instance->setProvider($value);
        self::assertNotNull($instance->getProvider());
        self::assertNotNull($instance->provider);
        self::assertEquals($value, is_array($value) ? $instance->getProvider()->toArray() : $instance->getProvider());
        self::assertEquals($value, is_array($value) ? $instance->provider->toArray() : $instance->provider);
    }

    /**
     * Test invalid property "provider"
     * @dataProvider invalidProviderDataProvider
     * @param mixed $value
     * @param string $exceptionClass
     *
     * @return void
     */
    public function testInvalidProvider(mixed $value, string $exceptionClass): void
    {
        $instance = $this->getTestInstance();

        $this->expectException($exceptionClass);
        $instance->setProvider($value);
    }

    /**
     * @return array[]
     * @throws Exception
     */
    public function validProviderDataProvider(): array
    {
        $instance = $this->getTestInstance();
        return $this->getValidDataProviderByType($instance->getValidator()->getRulesByPropName('_provider'));
    }

    /**
     * @return array[]
     * @throws Exception
     */
    public function invalidProviderDataProvider(): array
    {
        $instance = $this->getTestInstance();
        return $this->getInvalidDataProviderByType($instance->getValidator()->getRulesByPropName('_provider'));
    }
}
