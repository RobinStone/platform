<?php

use JetBrains\PhpStorm\NoReturn;

#[NoReturn] function print_response_status(array $ResponseArray=Response::STATUS_BAD_REQUEST) {
    http_response_code((int)$ResponseArray['CODE']);
    header('Content-Type: application/json');
    $response = array(
        'message' => $ResponseArray['TEXT'],
        'status' => (int)$ResponseArray['CODE']
    );
    echo json_encode($response);
    exit;
}
#[NoReturn] function print_response(array $params) {
    $response_codes = $params['status'];
    http_response_code((int)$response_codes['CODE']);

    header('Content-Type: application/json');
    echo json_encode(['message'=>$response_codes['CODE'], 'data'=>$params['data']]);
    exit;
}

enum Response {
    /**
     * HTTP код ответа для успешных запросов GET/PUT.
     *
     * @const  STATUS_OK
     */
    const STATUS_OK = [
        'CODE'=>200,
        'TEXT'=>'ok',
    ];

    /**
     * HTTP код ответа на успешный запрос POST.
     *
     * @const  STATUS_CREATED
     */
    const STATUS_CREATED = [
        'CODE'=>201,
        'TEXT'=>'created',
    ];

    /**
     * HTTP код ответа на запрос, в результате которого была создана
     * запланированная задача для выполнения фактического запроса.
     *
     * @const  STATUS_ACCEPTED
     */
    const STATUS_ACCEPTED = [
        'CODE'=>202,
        'TEXT'=>'accepted',
    ];

    /**
     * HTTP код ответа для успешного запроса, который не дал ответа
     * (например, запросы DELETE).
     *
     * @const  STATUS_NO_CONTENT
     */
    const STATUS_NO_CONTENT  = [
        'CODE'=>204,
        'TEXT'=>'no content',
    ];

    /**
     * Если маршруты API изменились (маловероятно) или если входящий запрос
     * не является безопасным (http), он будет перенаправлен на безопасную версию (https).
     *
     * @const  STATUS_MOVED_PERMANENTLY
     */
    const STATUS_MOVED_PERMANENTLY = [
        'CODE'=>301,
        'TEXT'=>'moved permanently',
    ];

    /**
     * Когда ресурс был найден в другом месте. При получении запроса
     * к устаревшей версии API на текущую версию API будет выдан ответ 302 Found.
     *
     * @const  STATUS_FOUND
     */
    const STATUS_FOUND = [
        'CODE'=>302,
        'TEXT'=>'found',
    ];

    /**
     * Если в запросе отправлен заголовок If-Modified-Since и ресурс не был изменен
     * с указанной даты, то этот ответ будет отправлен. Примечание. См. страницы
     * конкретных ресурсов для поддержки заголовка If-Modified-Since.
     *
     * @const  STATUS_NOT_MODIFIED
     */
    const STATUS_NOT_MODIFIED  = [
        'CODE'=>304,
        'TEXT'=>'not modified',
    ];

    /**
     * Выдается, когда был отправлен неверный запрос.
     * Например, из-за неверного синтаксиса или отсутствия необходимых данных.
     *
     * @const  STATUS_BAD_REQUEST
     */
    const STATUS_BAD_REQUEST = [
        'CODE'=>400,
        'TEXT'=>'bad request',
    ];

    /**
     * Этот ответ отправляется, если учетные данные клиента не предоставлены или неверны.
     *
     * @const  STATUS_UNAUTHORIZED
     */
    const STATUS_UNAUTHORIZED = [
        'CODE'=>401,
        'TEXT'=>'unauthorized',
    ];

    /**
     * Когда у пользователя нет разрешения на выполнение определенной операции с ресурсом
     * (например, редактирование продукта). Разрешения можно установить
     * через панель управления сайтом.
     *
     * @const  STATUS_FORBIDDEN
     */
    const STATUS_FORBIDDEN = [
        'CODE'=>403,
        'TEXT'=>'forbidden',
    ];

    /**
     * Когда определенный ресурс не существует или не может быть найден.
     *
     * @const  STATUS_NOT_FOUND
     */
    const STATUS_NOT_FOUND = [
        'CODE'=>404,
        'TEXT'=>'not found',
    ];

    /**
     * Ресурс найден, но не поддерживает метод запроса.
     * Выдается, когда конкретный метод еще не реализован в ресурсе или ресурс
     * вообще не поддерживает этот метод (например, PUT для /orders недействителен,
     * но PUT для /orders/{id} действителен).
     *
     * @const  STATUS_METHOD_NOT_ALLOWED
     */
    const STATUS_METHOD_NOT_ALLOWED = [
        'CODE'=>405,
        'TEXT'=>'method not allowed',
    ];

    /**
     * Когда клиент указывает тип содержимого ответа в заголовке Accept,
     * который не поддерживается.
     *
     * @const  STATUS_METHOD_NOT_ACCEPTABLE
     */
    const STATUS_METHOD_NOT_ACCEPTABLE = [
        'CODE'=>406,
        'TEXT'=>'method not acceptable',
    ];

    /**
     * Изменение, запрошенное клиентом, отклоняется из-за условия, наложенного сервером.
     * Точные причины ответа на это будут варьироваться от одного ресурса к другому.
     * Примеры могут включать попытку удалить Категорию, что приведет к тому,
     * что Продукты станут потерянными. Дополнительный информация о конфликте и способах
     * его разрешения может быть доступна в разделе подробностей ответа.
     *
     * @const  STATUS_CONFLICT
     */
    const STATUS_CONFLICT  = [
        'CODE'=>409,
        'TEXT'=>'conflict',
    ];

    /**
     * Когда клиент запрашивает слишком много объектов. например.
     * параметр предела был выше максимально допустимого.
     *
     * @const  STATUS_REQUEST_ENTITY_TOO_LARGE
     */
    const STATUS_REQUEST_ENTITY_TOO_LARGE  = [
        'CODE'=>413,
        'TEXT'=>'request entity too lage',
    ];

    /**
     * Когда произошла ошибка в API.
     *
     * @const  STATUS_INTERNAL_SERVER_ERROR
     */
    const STATUS_INTERNAL_SERVER_ERROR = [
        'CODE'=>500,
        'TEXT'=>'internal server error',
    ];

    /**
     * Когда отправляется метод запроса, который не
     * поддерживается API (например, TRACE, PATCH).
     *
     * @const  STATUS_NOT_IMPLEMENTED
     */
    const STATUS_NOT_IMPLEMENTED = [
        'CODE'=>501,
        'TEXT'=>'not implemented',
    ];

    /**
     * Когда ресурс помечен как «Не работает на техническое обслуживание»
     * или сайт обновляется до новой версии.
     *
     * @const  STATUS_SERVICE_UNAVAILABLE
     */
    const STATUS_SERVICE_UNAVAILABLE = [
        'CODE'=>503,
        'TEXT'=>'service unavailable',
    ];

    /**
     * Когда ресурс помечен как «Не работает на техническое обслуживание»
     * или сайт обновляется до новой версии.
     *
     * @const  STATUS_INSUFFICIENT_STORAGE
     */
    const STATUS_INSUFFICIENT_STORAGE  = [
        'CODE'=>507,
        'TEXT'=>'insufficient storage',
    ];

}
