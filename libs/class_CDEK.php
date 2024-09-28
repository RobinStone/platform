<?php
use CdekSDK\Requests;

class CDEK {
    private static $client;
    public static array $errors = [];

    private static function client_set() {
        require_once 'vendor/autoload.php';
        self::$client = new \CdekSDK\CdekClient(getParam('sdec_name'), getParam('sdec_pass'));
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
                'CODE'=>$item->Code,
                'NAME'=>$item->Name,
                'ADDRESS'=>$item->Address,
            ];
            foreach ($item->OfficeImages as $image) {
                $itm['IMAGES'][] = '<img loading="lazy" src="'.$image->getUrl().'" width="200" height="150">';
            }

            $rows[] = $itm;
        }
        return $rows;
    }
}