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

namespace Tests\YooKassa\Request\Payments\PaymentData;

use InvalidArgumentException;
use YooKassa\Request\Payments\PaymentData\PaymentDataMobileBalance;

/**
 * AbstractTestPaymentDataPhone
 *
 * @category    ClassTest
 * @author      cms@yoomoney.ru
 * @link        https://yookassa.ru/developers/api
 */
abstract class AbstractTestPaymentDataPhone extends AbstractTestPaymentData
{
    /**
     * @dataProvider validPhoneDataProvider
     *
     * @param mixed $value
     */
    public function testGetSetPhone(mixed $value): void
    {
        /** @var PaymentDataMobileBalance $instance */
        $instance = $this->getTestInstance();

        $instance->setPhone($value);
        self::assertEquals($value, $instance->getPhone());
        self::assertEquals($value, $instance->phone);

        $instance = $this->getTestInstance();
        $instance->phone = $value;
        self::assertEquals($value, $instance->getPhone());
        self::assertEquals($value, $instance->phone);
    }

    /**
     * @dataProvider invalidPhoneDataProvider
     *
     * @param mixed $value
     */
    public function testSetInvalidPhone(mixed $value): void
    {
        $this->expectException(InvalidArgumentException::class);

        /** @var PaymentDataMobileBalance $instance */
        $instance = $this->getTestInstance();
        $instance->setPhone($value);
    }

    /**
     * @dataProvider invalidPhoneDataProvider
     *
     * @param mixed $value
     */
    public function testSetterInvalidPhone(mixed $value): void
    {
        $this->expectException(InvalidArgumentException::class);

        /** @var PaymentDataMobileBalance $instance */
        $instance = $this->getTestInstance();
        $instance->phone = $value;
    }

    public function validPhoneDataProvider(): array
    {
        return [
            ['0123'],
            ['45678'],
            ['901234'],
            ['5678901'],
            ['23456789'],
            ['012345678'],
            ['9012345678'],
            ['90123456789'],
            ['012345678901'],
            ['5678901234567'],
            ['89012345678901'],
            ['234567890123456'],
        ];
    }

    public function invalidPhoneDataProvider(): array
    {
        return [
            [true],
            ['2345678901234567'],
        ];
    }
}
