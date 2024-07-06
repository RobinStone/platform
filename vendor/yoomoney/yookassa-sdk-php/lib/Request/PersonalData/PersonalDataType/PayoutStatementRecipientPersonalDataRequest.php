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

namespace YooKassa\Request\PersonalData\PersonalDataType;

use DateTime;
use YooKassa\Model\Metadata;
use YooKassa\Model\PersonalData\PersonalDataType;
use YooKassa\Request\PersonalData\CreatePayoutStatementRecipientPersonalDataRequestBuilder;
use YooKassa\Validator\Constraints as Assert;

/**
 * Класс, представляющий модель PayoutStatementRecipientPersonalDataRequest.
 *
 * @category Class
 * @package  YooKassa\Model
 * @author   cms@yoomoney.ru
 * @link     https://yookassa.ru/developers/api
 *
 * @property string $type Значение — `payout_statement_recipient`. Тип персональных данных.
 * @property string $lastName Фамилия пользователя.
 * @property string $last_name Фамилия пользователя.
 * @property string $firstName Имя пользователя.
 * @property string $first_name Имя пользователя.
 * @property string|null $middleName Отчество пользователя. Обязательный параметр, если есть в паспорте.
 * @property string|null $middle_name Отчество пользователя. Обязательный параметр, если есть в паспорте.
 * @property Metadata|null $metadata Любые дополнительные данные, которые нужны вам для работы (например, ваш внутренний идентификатор заказа).
 * @property DateTime $birthdate Дата рождения. Передается в формате [ISO 8601](https://en.wikipedia.org/wiki/ISO_8601)
*/
class PayoutStatementRecipientPersonalDataRequest extends AbstractPersonalDataRequest
{
    /**
     * Фамилия пользователя.
     *
     * @var string|null
     */
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(max: self::MAX_LENGTH_LAST_NAME)]
    #[Assert\Length(min: 1, charset: 'UTF-8')]
    #[Assert\Regex("/^[—–‐\\-a-zA-Zа-яёА-ЯЁ ]*$/u")]
    private ?string $_last_name = null;

    /**
     * Имя пользователя.
     *
     * @var string|null
     */
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(max: self::MAX_LENGTH_FIRST_NAME)]
    #[Assert\Length(min: 1)]
    #[Assert\Regex("/^[—–‐\\-a-zA-Zа-яёА-ЯЁ ]*$/u")]
    private ?string $_first_name = null;

    /**
     * Отчество пользователя. Обязательный параметр, если есть в паспорте.
     *
     * @var string|null
     */
    #[Assert\Type('string')]
    #[Assert\Length(max: self::MAX_LENGTH_LAST_NAME)]
    #[Assert\Length(min: 1)]
    #[Assert\Regex("/^[—–‐\\-a-zA-Zа-яёА-ЯЁ ]*$/u")]
    private ?string $_middle_name = null;

    /**
     * Дата рождения. Передается в формате [ISO 8601](https://en.wikipedia.org/wiki/ISO_8601)
     *
     * @var DateTime|null
     */
    #[Assert\NotBlank]
    #[Assert\Date]
    #[Assert\Type('DateTime')]
    private ?DateTime $_birthdate = null;

    public function __construct(?array $data = [])
    {
        parent::__construct($data);
        $this->setType(PersonalDataType::PAYOUT_STATEMENT_RECIPIENT);
    }
    /**
     * Возвращает last_name.
     *
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->_last_name;
    }

    /**
     * Устанавливает last_name.
     *
     * @param string|null $last_name Фамилия пользователя.
     *
     * @return self
     */
    public function setLastName(?string $last_name = null): self
    {
        $this->_last_name = $this->validatePropertyValue('_last_name', $last_name);
        return $this;
    }

    /**
     * Проверяет наличие фамилии пользователя в запросе.
     *
     * @return bool True если фамилия пользователя задана, false если нет
     */
    public function hasLastName(): bool
    {
        return !empty($this->_last_name);
    }

    /**
     * Возвращает first_name.
     *
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->_first_name;
    }

    /**
     * Устанавливает first_name.
     *
     * @param string|null $first_name Имя пользователя.
     *
     * @return self
     */
    public function setFirstName(?string $first_name = null): self
    {
        $this->_first_name = $this->validatePropertyValue('_first_name', $first_name);
        return $this;
    }

    /**
     * Проверяет наличие имени пользователя в запросе.
     *
     * @return bool True если имя пользователя задано, false если нет
     */
    public function hasFirstName(): bool
    {
        return !empty($this->_first_name);
    }

    /**
     * Возвращает middle_name.
     *
     * @return string|null
     */
    public function getMiddleName(): ?string
    {
        return $this->_middle_name;
    }

    /**
     * Устанавливает middle_name.
     *
     * @param string|null $middle_name Отчество пользователя. Обязательный параметр, если есть в паспорте.
     *
     * @return self
     */
    public function setMiddleName(?string $middle_name = null): self
    {
        $this->_middle_name = $this->validatePropertyValue('_middle_name', $middle_name);
        return $this;
    }

    /**
     * Проверяет наличие отчества пользователя в запросе.
     *
     * @return bool True если отчество пользователя задано, false если нет
     */
    public function hasMiddleName(): bool
    {
        return !empty($this->_middle_name);
    }

    /**
     * Возвращает birthdate.
     *
     * @return DateTime|null
     */
    public function getBirthdate(): ?DateTime
    {
        return $this->_birthdate;
    }

    /**
     * Устанавливает birthdate.
     *
     * @param DateTime|string|null $birthdate Дата рождения. Передается в формате [ISO 8601](https://en.wikipedia.org/wiki/ISO_8601)
     *
     * @return self
     */
    public function setBirthdate(DateTime|string|null $birthdate = null): self
    {
        $this->_birthdate = $this->validatePropertyValue('_birthdate', $birthdate);
        return $this;
    }

    /**
     * Проверяет наличие даты рождения в запросе.
     *
     * @return bool True если дата рождения задано, false если нет
     */
    public function hasBirthdate(): bool
    {
        return !empty($this->_birthdate);
    }

    /**
     * Проверяет на валидность текущий объект
     *
     * @return bool True если объект запроса валиден, false если нет
     */
    public function validate(): bool
    {
        if (!$this->hasType()) {
            $this->setValidationError('PayoutStatementRecipientPersonalDataRequest type not specified');

            return false;
        }
        if (!$this->hasLastName()) {
            $this->setValidationError('PayoutStatementRecipientPersonalDataRequest last_name not specified');

            return false;
        }
        if (!$this->hasFirstName()) {
            $this->setValidationError('PayoutStatementRecipientPersonalDataRequest first_name not specified');

            return false;
        }
        if (!$this->hasBirthdate()) {
            $this->setValidationError('PayoutStatementRecipientPersonalDataRequest birthdate not specified');

            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function toArray(): array
    {
        $result = parent::toArray();
        $result['birthdate'] = $this->_birthdate?->format('Y-m-d');
        return $result;
    }

    /**
     * Возвращает билдер объектов запросов создания платежа.
     *
     * @return CreatePayoutStatementRecipientPersonalDataRequestBuilder Инстанс билдера объектов запросов
     */
    public static function builder(): CreatePayoutStatementRecipientPersonalDataRequestBuilder
    {
        return new CreatePayoutStatementRecipientPersonalDataRequestBuilder();
    }
}

