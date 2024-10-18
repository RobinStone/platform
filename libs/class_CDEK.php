<?php
use CdekSDK\Requests;
use CdekSDK\Common;
use CdekSDK\CdekApi;

class CDEK {
    private static $client;
    public static array $errors = [];
    private static bool $test = true;

    private static function client_set() {
        require_once 'vendor/autoload.php';
        if(self::$test) {
            self::$client = new \CdekSDK\CdekClient('wqGwiQx0gg8mLtiEKsUinjVSICCjtTEP', 'RmAmgvSgSl1yirlz9QupbzOJVqhCxcP5', new \GuzzleHttp\Client([
                'base_uri' => 'https://integration.cdek.ru',
            ]));
        } else {
            self::$client = new \CdekSDK\CdekClient(getParam('sdec_name'), getParam('sdec_pass'));
        }
    }

    private static function errors_reporting($response) {
        if ($response->hasErrors()) {
            foreach ($response->getMessages() as $message) {
                if ($message->getErrorCode() !== '') {
                    self::$errors[] = [
                        'CODE'=>$message->getErrorCode(),
                        'MESSAGE'=>$message->getMessage(),
                    ];
                }
            }
        }
    }

    public static function get_points(int $CDEK_city_id): array
    {
        self::client_set();

        $rows = [];

        $request = new Requests\PvzListRequest();
        $request->setCityId($CDEK_city_id);
        $request->setType(Requests\PvzListRequest::TYPE_ALL);
//        $request->setCashless(true);
//        $request->setCash(true);
//        $request->setCodAllowed(true);
//        $request->setDressingRoom(true);

        $response = self::$client->sendPvzListRequest($request);

        self::errors_reporting($response);

        foreach ($response as $item) {
            $itm = [
                'CITY_CODE'=>$item->Code,
                'NAME'=>$item->Name,
                'ADDRESS'=>$item->Address,
                'ADDR_COMMENT'=>$item->AddressComment,
                'PHONE'=>$item->Phone,
                'WORK_TIME'=>$item->WorkTime,
                'COORD_X'=>$item->coordX,
                'COORD_Y'=>$item->coordY,
                'ALL'=>$item
            ];
            foreach ($item->OfficeImages as $image) {
                $itm['IMAGES'][] = '<img loading="lazy" src="'.$image->getUrl().'" width="200" height="150">';
            }

            $rows[] = $itm;
        }
        return $rows;
    }

    public static function get_code_city(string $city_name) {
        self::client_set();
        return self::$client->sendCitiesRequest($city_name);
    }

    public static function create_order() {
        self::client_set();

        $order = new CdekSDK\Common\Order([
            'Number'     => 'TEST-12345622',
            'SendCityCode'    => 44, // Москва
            'RecCityPostCode' => '9556', // Новогрудок
            'RecipientName'    => 'Олег Корнаухов',
            'RecipientEmail'   => 'vohuanrok@mail.ru',
            'Phone'            => '+375 44 772-59-49',
            'TariffTypeCode'   => 1,
            'RecipientCompany' => 'нету компании',
            'Comment'          => 'Это тестовый заказ',
        ]);

        $order->setSender(CdekSDK\Common\Sender::create([
            'Company' => 'ЗАО «Рога и Копыта»',
            'Name'    => 'Петр Иванов',
            'Phone'   => '+7 (283) 101-11-20',
        ])->setAddress(CdekSDK\Common\Address::create([
            'Street' => 'Морозильная улица',
            'House'  => '2',
            'Flat'   => '101',
        ])));

        $order->setAddress(CdekSDK\Common\Address::create([
            'Street'  => 'Ломоносова',
            'House'   => '8',
            'Flat'    => '67',
        ]));

        $package = CdekSDK\Common\Package::create([
            'Number'  => 'TEST-123456',
            'BarCode' => 'TEST-123456',
            'Weight'  => 500, // Общий вес (в граммах)
            'SizeA'   => 10, // Длина (в сантиметрах), в пределах от 1 до 1500
            'SizeB'   => 10,
            'SizeC'   => 10,
            'Comment' => 'Обязательное описание вложения',
        ]);
        $order->addPackage($package);

        $order->addService(CdekSDK\Common\AdditionalService::create(CdekSDK\Common\AdditionalService::SERVICE_DELIVERY_TO_DOOR));

        $request = new CdekSDK\Requests\AddDeliveryRequest([
            'Number'          => 'TESTING123',
            'ForeignDelivery' => false,
            'Currency'        => 'RUB',
        ]);
        $request->addOrder($order);

        $response = self::$client->sendAddDeliveryRequest($request);

        if ($response->hasErrors()) {
            self::errors_reporting($response);
        }

        foreach ($response->getOrders() as $order) {
            // сверяем данные заказа, записываем номер
            $order->getNumber();
            $order->getDispatchNumber();
        }

        return $order;
    }
}