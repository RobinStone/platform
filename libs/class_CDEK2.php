<?php
class CDEK2 {
    private $clientId = '';
    private $clientSecret = '';
    private $token = '';
    private $header = [];
    public $errors = [];
    private $is_test = true;


    function __construct(bool $is_test = false) {
        if($is_test === true) {
            $this->clientId = 'wqGwiQx0gg8mLtiEKsUinjVSICCjtTEP';
            $this->clientSecret = 'RmAmgvSgSl1yirlz9QupbzOJVqhCxcP5';
            $this->is_test = true;
        } else {
            $this->clientId = getParam('sdec_name');
            $this->clientSecret = getParam('sdec_pass');
            $this->is_test = false;
        }

        $curl1 = curl_init();
        $postFieldsAr = array(
            "grant_type" => "client_credentials",
            "client_id" => $this->clientId,
            "client_secret" => $this->clientSecret
        );
        $url = "https://api.cdek.ru/v2/oauth/token?parameters";
        if($is_test) {
            $url = "https://api.edu.cdek.ru/v2/oauth/token?parameters";
        }
        curl_setopt_array($curl1, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($postFieldsAr)
        ));
        $response = curl_exec($curl1);
        curl_close($curl1);
        $objJSON1 = json_decode($response);
        if ($objJSON1 === false) {
            $errors[] = "Ошибка, сервер CDEK вернул ответ не в JSON формате.";
        }
        if (isset($objJSON1->access_token) and (($objJSON1->access_token . "") != "")) {
            $this->token = $objJSON1->access_token;
            $this->header[] = "Content-Type: application/json; charset=utf-8";
            $this->header[] = "Authorization: Bearer " . $this->token;
        } else {
            $errors[] = "Ошибка, ответ сервера CDEK не содержит свойства access_token или этот параметр пустой.";
        }
    }

    private function decoder($obj) {
        return json_decode($obj, true);
    }

    public function get_token() {
        return $this->token;
    }

    public static function get_point_at_code(string $code="KSK175", bool $is_test=false) {
        $B = new CDEK2($is_test);
        return $B->get_point_one($code);
    }

    private function get_point_one(string $code="KSK175") {
        $path = "https://api.cdek.ru/v2/deliverypoints?code=".$code;
        if($this->is_test) {
            $path = "https://api.edu.cdek.ru/v2/deliverypoints?code=".$code;
        }

        if( $curl2 = curl_init() ) {
            curl_setopt($curl2, CURLOPT_URL, $path);
            curl_setopt($curl2, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl2, CURLOPT_FOLLOWLOCATION,true);
            curl_setopt($curl2, CURLOPT_ENCODING,'');
            curl_setopt($curl2, CURLOPT_MAXREDIRS,10);
            curl_setopt($curl2, CURLOPT_TIMEOUT,0);
            curl_setopt($curl2, CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
            curl_setopt($curl2, CURLOPT_HTTPHEADER, $this->header);
            curl_setopt($curl2, CURLOPT_CUSTOMREQUEST, 'GET');
            $response2 = curl_exec($curl2);
            curl_close($curl2);
        }
        $response2 = json_decode($response2, true);
        if(isset($response2[0])) {
            return $response2[0];
        }
        return false;
    }

    public static function get_all_tarifs(int $from_cityCDEKid, int $to_cityCDEKid, int $weight, bool $is_test=false): array
    {
        $D = new CDEK2($is_test);
        $ans = $D->get_tarif_price($from_cityCDEKid, $to_cityCDEKid, $weight);
        $rows = [];
        if(isset($ans['tariff_codes'])) {
            foreach($ans['tariff_codes'] as $item) {
                $rows[$item['tariff_name']] = $item;
            }
        }
        return $rows;
    }

    public static function get_tarif_price_from_cities(
        int $id_city_cdek_from,
        int $id_city_cdek_to,
        int $weight=100,
        TARIF_CDEK $tarif=TARIF_CDEK::EXPRESS_W_W,
        bool $is_test=false
    ): bool|int
    {
        $D = new CDEK2($is_test);
        $ans = $D->get_tarif_price($id_city_cdek_from, $id_city_cdek_to, $weight);
        if(isset($ans['tariff_codes'])) {
            $rows = [];
            foreach($ans['tariff_codes'] as $item) {
                $rows[$item['tariff_name']] = $item;
            }
            if(isset($rows[$tarif->value])) {
                return (int)$rows[$tarif->value]['delivery_sum'];
            }
        }
        return false;
    }

    private function get_tarif_price(int $from_cityCDEKid, int $to_cityCDEKid, int $weight) {
        $arr = [
            "type"=>2,
            "lang"=>"rus",
            "from_location"=> [
                    "code"=>$from_cityCDEKid
            ],
            "to_location"=>[
                    "code"=>$to_cityCDEKid
            ],
            "packages"=>[
                [
                    "weight"=>$weight,
                ]
            ]
        ];

        $curl2 = curl_init();
        $URL_com = 'https://api.cdek.ru/v2/calculator/tarifflist';
        if($this->is_test) {
            $URL_com = 'https://api.edu.cdek.ru/v2/calculator/tarifflist';
        }
        $requestObjectString = json_encode($arr);
        curl_setopt_array($curl2,array(
            CURLOPT_URL => $URL_com,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER =>$this->header,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $requestObjectString
        ));
        $response2 = curl_exec($curl2);
        curl_close($curl2);

        $responser = json_decode($response2, true);
        return $responser;
    }

    public function create_bar_code(string $order_uuid) {
        $arr = [
            "orders"=> [
                [
                    "order_uuid"=> $order_uuid
                ]
            ],
            "copy_count"=> 1,
            "format"=> "A4"
        ];

        $curl2 = curl_init();
        $URL_com = 'https://api.cdek.ru/v2/print/barcodes';
        if($this->is_test) {
            $URL_com = 'https://api.edu.cdek.ru/v2/print/barcodes';
        }
        $requestObjectString = json_encode($arr);
        curl_setopt_array($curl2,array(
            CURLOPT_URL => $URL_com,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER =>$this->header,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $requestObjectString
        ));
        $response2 = curl_exec($curl2);
        curl_close($curl2);

        $responser = json_decode($response2);
        return $responser;
    }

    public function get_pdf_bar_code(string $uuid_bar_code) {
        $path = "https://api.cdek.ru/v2/print/barcodes/";
        if($this->is_test) {
            $path = "https://api.edu.cdek.ru/v2/print/barcodes/";
        }

        if( $curl2 = curl_init() ) {
            curl_setopt($curl2, CURLOPT_URL, $path.$uuid_bar_code);
            curl_setopt($curl2, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl2, CURLOPT_FOLLOWLOCATION,true);
            curl_setopt($curl2, CURLOPT_ENCODING,'');
            curl_setopt($curl2, CURLOPT_MAXREDIRS,10);
            curl_setopt($curl2, CURLOPT_TIMEOUT,0);
            curl_setopt($curl2, CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
            curl_setopt($curl2, CURLOPT_HTTPHEADER, $this->header);
            curl_setopt($curl2, CURLOPT_CUSTOMREQUEST, 'GET');
            $response2 = curl_exec($curl2);
            curl_close($curl2);
        }
        $response2 = json_decode($response2);
        return $response2;
    }

    public function get_file_bar_code(string $path) {
        $ans = "";
        if( $curl2 = curl_init() ) {
            curl_setopt($curl2, CURLOPT_URL, $path);
            curl_setopt($curl2, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl2, CURLOPT_FOLLOWLOCATION,true);
            curl_setopt($curl2, CURLOPT_ENCODING,'');
            curl_setopt($curl2, CURLOPT_MAXREDIRS,10);
            curl_setopt($curl2, CURLOPT_TIMEOUT,0);
            curl_setopt($curl2, CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
            curl_setopt($curl2, CURLOPT_HTTPHEADER, $this->header);
            curl_setopt($curl2, CURLOPT_CUSTOMREQUEST, 'GET');
            $response2 = curl_exec($curl2);

            if (curl_errno($curl2)) {
                Message::addError('Ошибка cURL: ' . curl_error($curl2));
            } else {
                $httpCode = curl_getinfo($curl2, CURLINFO_HTTP_CODE);
                if ($httpCode == 200) {
                    file_put_contents("./RESURSES/BUFFER/bar_code.pdf", $response2);
                    $ans = "Файл успешно скачан.";
                } else {
                    $ans = "Ошибка: $httpCode - $response2";
                }
            }
            curl_close($curl2);
        }
        return $ans;
    }

    public function create_delivery(
        $delivery_point,   // PRM50  - code склада в Перми (в этом случае)
        $product_number,
        $items,   // закоментированный участок массива ниже, позволяет узнать пример заполнения массивов товаров
        $recipient_name,
        $recipient_phone,
        $recipient_email,
        $comment
        )
    {
        $items = [
            [
                "ware_key"=>'fbsjufb335454w3w-3fjefnbk',
                "payment"=>[
                    "value"=>202,
                ],
                "name"=>'супер - чайник за 2002',
                "cost"=>202,
                "amount"=>1,
                "weight"=>200,
                "url"=>"https://kokonk.com"
            ],
        ];

        $arr = [
//            "type"=>2, // 2 - для любого типа договора
            "number"=>$product_number,
            "delivery_point"=>$delivery_point,
            "comment"=>$comment,
            "delivery_recipient_cost"=>[
                "value"=>55
            ],
            "from_location"=>[
                "code"=>"44",
                "fias_guid"=>"",
                "postal_code"=>"",
                "longitude"=>"",
                "latitude"=>"",
                "country_code"=>"",
                "region"=>"",
                "sub_region"=>"",
                "city"=>"Москва",
                "kladr_code"=>"",
                "address"=>"г. Домодедово Микрорайон Белые Столбы владение 'Склады' 104 стр. 5/2 индекс 142050"
            ],
            "packages"=>[
                "number"=>$product_number,
                "comment"=>"Упаковка",
                "height"=>15,
                "items"=>$items,
                "length"=>20,
                "weight"=>200,
                "width"=>15
            ],
            "recipient"=> [
                "name"=>$recipient_name,
                "email"=>$recipient_email,
                "phones"=>[
                    "number"=>$recipient_phone
                ]
            ],
            "sender"=> [
                "name"=>"",
                "phones"=>[
                    "number"=>"+79134637228"
                ]
            ],
            "tariff_code"=>136    // 62  // 10
        ];
        $curl2 = curl_init();
        $URL_com = 'https://api.cdek.ru/v2/orders';
        if($this->is_test) {
            $URL_com = 'https://api.edu.cdek.ru/v2/orders';
        }
        $requestObjectString = json_encode($arr);
        curl_setopt_array($curl2,array(
            CURLOPT_URL => $URL_com,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER =>$this->header,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $requestObjectString
        ));
        $response2 = curl_exec($curl2);
        curl_close($curl2);

        $responser = json_decode($response2);

        $uuid = $responser->entity->uuid;
        if($responser->requests[0]->state == 'ACCEPTED') {
            return $this->get_status($uuid);
        }
        return $responser;
    }

    public static function get_CDEK_city(string $city_name) {
        $D = new CDEK2(false);
        $ans = $D->get_place_params($city_name, 1);
        if(isset($ans[0])) {
            return $ans[0];
        }
        return false;
    }

    public function get_place_params(string $city_name, int $count_answers=1000, string $country_code="RU") {
        $city_name = urlencode($city_name);

        $path = "https://api.cdek.ru/v2/location/cities/";
        if($this->is_test) {
            $path = "https://api.edu.cdek.ru/v2/location/cities/";
        }

        $comm = [
            "city"=>$city_name,
            "country_codes"=>$country_code,
            "size"=>$country_code
        ];
        foreach($comm as $k=>$v) {
            $arr[] = $k."=".$v;
        }
        if( $curl2 = curl_init() ) {
            curl_setopt($curl2, CURLOPT_URL, $path."?".implode("&", $arr));
            curl_setopt($curl2, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl2, CURLOPT_FOLLOWLOCATION,true);
            curl_setopt($curl2, CURLOPT_ENCODING,'');
            curl_setopt($curl2, CURLOPT_MAXREDIRS,10);
            curl_setopt($curl2, CURLOPT_TIMEOUT,0);
            curl_setopt($curl2, CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
            curl_setopt($curl2, CURLOPT_HTTPHEADER, $this->header);
            curl_setopt($curl2, CURLOPT_CUSTOMREQUEST, 'GET');
            $response2 = curl_exec($curl2);
            curl_close($curl2);
        }
        $response2 = json_decode($response2, true);
        return $response2;
    }

    public function get_all_cities($size, $page=0, $region_code='RU') {
        $comm = [
            'size'=>$size,
            'page'=>$page,
            'country_codes'=>$region_code
        ];
        foreach($comm as $k=>$v) {
            $arr[] = $k.'='.$v;
        }
        if( $curl2 = curl_init() ) {
            curl_setopt($curl2, CURLOPT_URL, 'https://api.cdek.ru/v2/location/cities/?'.implode('&', $arr));
            curl_setopt($curl2, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl2, CURLOPT_HTTPHEADER, $this->header);
            $response2 = curl_exec($curl2);
            curl_close($curl2);
        }
        return $this->decoder($response2);
    }

    public function get_all_regions($size, $page=0, $region_code='RU') {
        $comm = [
            'size'=>$size,
            'page'=>$page,
            'country_codes'=>$region_code
        ];
        foreach($comm as $k=>$v) {
            $arr[] = $k.'='.$v;
        }
        if( $curl2 = curl_init() ) {
            curl_setopt($curl2, CURLOPT_URL, 'https://api.cdek.ru/v2/location/regions?'.implode('&', $arr));
            curl_setopt($curl2, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl2, CURLOPT_HTTPHEADER, $this->header);
            $response2 = curl_exec($curl2);
            curl_close($curl2);
        }
        $response2 = json_decode($response2);
        return $response2;
    }

    public function get_offices($city_number) {
        $path = "https://api.cdek.ru/v2/deliverypoints/";
        if($this->is_test) {
            $path = "https://api.edu.cdek.ru/v2/deliverypoints/";
        }
        $comm = [
            "city_code"=>(int)$city_number,
            "type"=>"ALL"
        ];
        $arr = [];
        foreach($comm as $k=>$v) {
            $arr[] = $k.'='.$v;
        }
        $curl2 = curl_init();
        curl_setopt_array($curl2,array(
            CURLOPT_URL => $path.'?'.implode('&', $arr),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER =>$this->header
        ));
        $response2 = curl_exec($curl2);
        curl_close($curl2);
        $response2 = $this->decoder($response2);
        foreach($response2 as $k=>$v) {
            if(isset($v['office_image_list'])) {
                $imgs = [];
                foreach($v['office_image_list'] as $itm) {
                    $imgs[] = $itm['url'];
                }
                unset($v['office_image_list']);
                $v['IMGS'] = $imgs;
                $response2[$k] = $v;
            }
            if(isset($v['work_time_list'])) {
                $work_times = [];
                foreach($v['work_time_list'] as $itm) {
                    $work_times[$itm['day']] = $itm['time'];
                }
                unset($v['work_time_list']);
                $v['WORK_TIMES'] = $work_times;
                $response2[$k] = $v;
            }
            if(isset($v['phones'])) {
                $phones = [];
                foreach($v['phones'] as $itm) {
                    $phones[] = $itm['number'];
                }
                unset($v['phones']);
                $v['PHONES'] = $phones;
                $response2[$k] = $v;
            }
        }
        return $response2;
    }

    public function get_status($order_uuid) {
        $curl3 = curl_init();
        $URL_com = 'https://api.cdek.ru/v2/orders/'.$order_uuid;
        if($this->is_test) {
            $URL_com = 'https://api.edu.cdek.ru/v2/orders/'.$order_uuid;
        }
        if( $curl3 = curl_init() ) {
            curl_setopt($curl3, CURLOPT_URL, $URL_com);
            curl_setopt($curl3, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl3, CURLOPT_HTTPHEADER,$this->header);
            $response2 = curl_exec($curl3);
            curl_close($curl3);
        }
        return json_decode($response2);
    }

    public function city_namme_to_code(string $city_name, string $country=""): int|bool
    {
        $ans = $this->get_place_params($city_name, 1, $country);
        if(!empty($ans)) {
            return $ans[0]->code;
        }
        return false;
    }

    public function test($arr) {
        $curl2 = curl_init();
        $URL_com = 'https://api.cdek.ru/v2/orders';
        if($this->is_test) {
            $URL_com = 'https://api.edu.cdek.ru/v2/orders';
        }
        $requestObjectString = json_encode($arr);
        curl_setopt_array($curl2,array(
            CURLOPT_URL => $URL_com,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER =>$this->header,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $requestObjectString
        ));
        $response2 = curl_exec($curl2);
        curl_close($curl2);

        $responser = json_decode($response2);

        $uuid = $responser->entity->uuid;
        if($responser->requests[0]->state == 'ACCEPTED') {
            return $this->get_status($uuid);
        }
        return $responser;
    }

}