<?php
class APIBRIGE {
    private string $login;
    private string $password;
    private string $path;

    /**
     * Создаёт связь с удалённым API
     *
     * @param string $site_address_domain - напр. (https://site.by)
     * @param string $entity - название класса в API (имя сущности напр. chat)
     * @param string $login - логин запроса к API
     * @param string $password - пароль запроса к API
     */
    function __construct(string $site_address_domain, string $entity, string $login, string $password) {
        $this->login = $login;
        $this->password = $password;
        $this->path = $site_address_domain."/api/".$entity;
    }

    /**
     * Отправляет GET запрос в API и получает ответ
     *
     * @param array $path_params - параметры в строке запроса без ключей (напр. [8, 'type', ...])
     * @param string $get_params - параметры в строке запроса но с ключами (напр. 'id=2&login=vasil...')
     * @return mixed - возвращает строку или json
     */
    function api_get(array $path_params=[], string $get_params=""): mixed
    {
        $url = $this->path;
        if(!empty($path_params)) {
            $url .= "/" . implode("/", $path_params);
        }
        if($get_params !== '') {
            $url .= "?".$get_params;
        }
        $username = $this->login;
        $password = $this->password;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $headers = [
            'Authorization: Basic ' . base64_encode($username . ':' . $password)
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        if ($response === FALSE) {
            return('Error occurred: ' . curl_error($ch));
        }
        curl_close($ch);
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return('Error decoding JSON');
        }
        return $data;
    }

    /**
     * Отправляет в API POST запрос с параметрами
     *
     * @param array $params - параметры в теле запроса (напр. ['login'=>'vasil', 'id'=>7])
     * @return mixed - вернёт строку или JSON
     */
    function api_post(array $params): mixed
    {
        $data = $params;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $headers = [
            'Authorization: Basic ' . base64_encode($this->login . ':' . $this->password),
            'Content-Type: application/json'
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        if ($response === FALSE) {
            return('Error occurred: ' . curl_error($ch));
        }
        curl_close($ch);
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return('Error decoding JSON');
        }
        return $data;
    }

    /**
     * Отправляет в API PUT запрос с параметрами
     *
     * @param array $path_params - параметры в строке запроса без ключей (напр. [8, 'type', ...])
     * @param array $params - параметры в теле запроса (напр. ['login'=>'vasil', 'id'=>7])
     * @return mixed - вернёт строку или JSON
     */
    function api_put(array $path_params=[], array $params=[]): mixed
    {
        $url = $this->path;
        if(!empty($path_params)) {
            $url .= "/" . implode("/", $path_params);
        }
        $username = $this->login;
        $password = $this->password;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        $headers = [
            'Authorization: Basic ' . base64_encode($username . ':' . $password),
            'Content-Type: application/json'
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        if ($response === FALSE) {
            return('Error occurred: ' . curl_error($ch));
        }
        curl_close($ch);
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return('Error decoding JSON');
        }
        return $data;
    }

    /**
     * Отправляет в API DELETE запрос с параметрами
     *
     * @param array $path_params - параметры в строке запроса без ключей (напр. [8, 'type', ...])
     * @param array $params - параметры в теле запроса (напр. ['login'=>'vasil', 'id'=>7])
     * @return mixed - вернёт строку или JSON
     */
    function api_delete(array $path_params=[], array $params=[]): mixed
    {
        $url = $this->path;
        if(!empty($path_params)) {
            $url .= "/" . implode("/", $path_params);
        }
        $username = $this->login;
        $password = $this->password;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        $headers = [
            'Authorization: Basic ' . base64_encode($username . ':' . $password),
            'Content-Type: application/json'
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        if ($response === FALSE) {
            return('Error occurred: ' . curl_error($ch));
        }
        curl_close($ch);
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return('Error decoding JSON');
        }
        return $data;
    }

}