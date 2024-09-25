<?php
/**
 * This code is licensed under the MIT License.
 *
 * Copyright (c) 2018 Appwilio (http://appwilio.com), greabock (https://github.com/greabock), JhaoDa (https://github.com/jhaoda)
 * Copyright (c) 2018 Alexey Kopytko <alexey@kopytko.com> and contributors
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

declare(strict_types=1);

namespace CdekSDK\Requests;

use CdekSDK\Responses\PreAlertResponse;
use JMS\Serializer\Annotation as JMS;

/**
 * @deprecated https://github.com/cdek-it/sdk2.0
 *
 * Создание преалерта. Метод используется для создания сводного реестра (преалерта), содержащего все накладные, товары по которым передаются на доставку.
 *
 * @JMS\XmlRoot(name="PreAlert")
 *
 * @psalm-suppress InvalidExtendClass
 *
 * @deprecated
 */
final class PreAlertRequest extends DeliveryRequest
{
    public const ADDRESS = '/addPreAlert';
    public const RESPONSE = PreAlertResponse::class;

    /**
     * @JMS\XmlAttribute
     *
     * @JMS\Type("string")
     *
     * @var string
     */
    protected $PvzCode;

    /**
     * Дата планируемой передачи. В формате YYYY-MM-DD.
     *
     * @JMS\XmlAttribute
     *
     * @JMS\Type("DateTimeImmutable<'Y-m-d'>")
     *
     * @var \DateTimeImmutable
     */
    protected $PlannedMeetingDate;
}
