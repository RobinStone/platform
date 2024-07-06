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

namespace Tests\YooKassa\Request\Receipts;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use YooKassa\Model\Receipt\ReceiptType;
use YooKassa\Model\Receipt\SettlementType;
use YooKassa\Request\Receipts\ReceiptResponseFactory;

/**
 * ReceiptResponseFactoryTest
 *
 * @category    ClassTest
 * @author      cms@yoomoney.ru
 * @link        https://yookassa.ru/developers/api
 */
class ReceiptResponseFactoryTest extends TestCase
{
    /**
     * @dataProvider invalidFactoryDataProvider
     */
    public function testInvalidFactory(array $value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $instance = new ReceiptResponseFactory();
        $instance->factory($value);
    }

    public static function invalidFactoryDataProvider(): array
    {
        return [
            [[]],
            [
                ['type' => new stdClass()]],
            [
                ['type' => SettlementType::POSTPAYMENT]],
            [
                [
                    'type' => ReceiptType::PAYMENT,
                    'refund_id' => 1,
                ],
            ],
            [
                [
                    'type' => ReceiptType::PAYMENT,
                    'payment_id' => 1,
                ],
            ],
            [
                [
                    'type' => ReceiptType::REFUND,
                    'payment_id' => 1,
                ],
            ],
        ];
    }
}
