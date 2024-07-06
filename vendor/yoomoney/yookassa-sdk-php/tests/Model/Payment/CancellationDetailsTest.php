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

namespace Tests\YooKassa\Model\Payment;

use Exception;
use Tests\YooKassa\AbstractTestCase;
use Datetime;
use YooKassa\Model\Metadata;
use YooKassa\Model\Payment\CancellationDetails;

/**
 * CancellationDetailsTest
 *
 * @category    ClassTest
 * @author      cms@yoomoney.ru
 * @link        https://yookassa.ru/developers/api
 */
class CancellationDetailsTest extends AbstractTestCase
{
    protected CancellationDetails $object;

    /**
     * @return CancellationDetails
     */
    protected function getTestInstance(): CancellationDetails
    {
        return new CancellationDetails();
    }

    /**
     * @return void
     */
    public function testCancellationDetailsClassExists(): void
    {
        $this->object = $this->getMockBuilder(CancellationDetails::class)->getMockForAbstractClass();
        $this->assertTrue(class_exists(CancellationDetails::class));
        $this->assertInstanceOf(CancellationDetails::class, $this->object);
    }

    /**
     * Test property "party"
     * @dataProvider validPartyDataProvider
     * @param mixed $value
     *
     * @return void
     * @throws Exception
     */
    public function testParty(mixed $value): void
    {
        $instance = $this->getTestInstance();
        $instance->setParty($value);
        self::assertNotNull($instance->getParty());
        self::assertNotNull($instance->party);
        self::assertEquals($value, is_array($value) ? $instance->getParty()->toArray() : $instance->getParty());
        self::assertEquals($value, is_array($value) ? $instance->party->toArray() : $instance->party);
        self::assertContains($instance->getParty(), ['yoo_money', 'payment_network', 'merchant', 'yandex_checkout']);
        self::assertContains($instance->party, ['yoo_money', 'payment_network', 'merchant', 'yandex_checkout']);
    }

    /**
     * Test invalid property "party"
     * @dataProvider invalidPartyDataProvider
     * @param mixed $value
     * @param string $exceptionClass
     *
     * @return void
     */
    public function testInvalidParty(mixed $value, string $exceptionClass): void
    {
        $instance = $this->getTestInstance();

        $this->expectException($exceptionClass);
        $instance->setParty($value);
    }

    /**
     * @return array[]
     * @throws Exception
     */
    public function validPartyDataProvider(): array
    {
        $instance = $this->getTestInstance();
        return $this->getValidDataProviderByType($instance->getValidator()->getRulesByPropName('_party'));
    }

    /**
     * @return array[]
     * @throws Exception
     */
    public function invalidPartyDataProvider(): array
    {
        $instance = $this->getTestInstance();
        return $this->getInvalidDataProviderByType($instance->getValidator()->getRulesByPropName('_party'));
    }

    /**
     * Test property "reason"
     * @dataProvider validReasonDataProvider
     * @param mixed $value
     *
     * @return void
     * @throws Exception
     */
    public function testReason(mixed $value): void
    {
        $instance = $this->getTestInstance();
        $instance->setReason($value);
        self::assertNotNull($instance->getReason());
        self::assertNotNull($instance->reason);
        self::assertEquals($value, is_array($value) ? $instance->getReason()->toArray() : $instance->getReason());
        self::assertEquals($value, is_array($value) ? $instance->reason->toArray() : $instance->reason);
        self::assertContains($instance->getReason(), ['3d_secure_failed', 'call_issuer', 'card_expired', 'payment_method_limit_exceeded', 'payment_method_restricted', 'country_forbidden', 'general_decline', 'fraud_suspected', 'identification_required', 'insufficient_funds', 'invalid_card_number', 'invalid_csc', 'issuer_unavailable', 'canceled_by_merchant', 'permission_revoked', 'internal_timeout', 'expired_on_confirmation', 'expired_on_capture', 'unsupported_mobile_operator', 'deal_expired']);
        self::assertContains($instance->reason, ['3d_secure_failed', 'call_issuer', 'card_expired', 'payment_method_limit_exceeded', 'payment_method_restricted', 'country_forbidden', 'general_decline', 'fraud_suspected', 'identification_required', 'insufficient_funds', 'invalid_card_number', 'invalid_csc', 'issuer_unavailable', 'canceled_by_merchant', 'permission_revoked', 'internal_timeout', 'expired_on_confirmation', 'expired_on_capture', 'unsupported_mobile_operator', 'deal_expired']);
    }

    /**
     * Test invalid property "reason"
     * @dataProvider invalidReasonDataProvider
     * @param mixed $value
     * @param string $exceptionClass
     *
     * @return void
     */
    public function testInvalidReason(mixed $value, string $exceptionClass): void
    {
        $instance = $this->getTestInstance();

        $this->expectException($exceptionClass);
        $instance->setReason($value);
    }

    /**
     * @return array[]
     * @throws Exception
     */
    public function validReasonDataProvider(): array
    {
        $instance = $this->getTestInstance();
        return $this->getValidDataProviderByType($instance->getValidator()->getRulesByPropName('_reason'));
    }

    /**
     * @return array[]
     * @throws Exception
     */
    public function invalidReasonDataProvider(): array
    {
        $instance = $this->getTestInstance();
        return $this->getInvalidDataProviderByType($instance->getValidator()->getRulesByPropName('_reason'));
    }
}
