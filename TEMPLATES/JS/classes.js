class VALUES {
    static email_phone_string(val) {
        // Регулярное выражение для проверки телефонного номера (примеры: 123-456-7890, (123) 456-7890, +1-123-456-7890)
        const phoneRegex = /^(?:\+?\d{1,3}[-.\s]?)?(?:\(\d{1,4}\)|\d{1,4})[-.\s]?\d{1,4}[-.\s]?\d{1,4}[-.\s]?\d{1,9}$/;

        // Регулярное выражение для проверки email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (phoneRegex.test(val)) {
            return 'phone';
        } else if (emailRegex.test(val)) {
            return 'email';
        } else {
            return 'login';
        }
    }
    static clear_phone(str_number) {
        return str_number.replace(/\D/g, '');
    }
}

class PROFIL {
    static errors = [];

    static set_list(param_argum_list, call_back=function() {}) {
        BACK('profil', 'profil', {'set': param_argum_list}, function(mess) {
            mess_executer(mess, function(mess) {
                call_back(mess.params);
            })
        });
    }

    static set(param, argum, call_back=function() {}) {
        BACK('profil', 'profil', {'set': param_argum_list}, function(mess) {
            mess_executer(mess, function(mess) {
                call_back(mess.params);
            })
        });
    }

    /**
     * В param можно передать как одно значение
     * {param: DEFAULT || ''}, - где DEFAULT - это значение
     * если такого параметра найдено не будет
     * можно передавать и массив,
     *   [
     *     { param: DEFAULT || '' },
     *     { param: DEFAULT || '' },
     *   ]
     * на выходе получим
     * объект в виде {params: argum} - если DEFAULT был определён
     * а искомый param не был найден, система вернёт DEFAULT или ''
     * вернёт всё как аргумент метода call_back - заданный 2-ым параметром
     * @param param - обьект пары ключ: значение
     * @param call_back - метод, который будет вызван при возвращении результата
     * @param only_exists - поля, которых нет - будут удалены из ответа
     */
    static get_list(param, call_back=function(){}, only_exists=false) {
        BACK('profil', 'profil', {'get': param, only_exists: only_exists}, function(mess) {
            mess_executer(mess, function(mess) {
                let response = mess.params;
                if(response !== '') {
                    call_back(response);
                }
            })
        });
    }

    /**
     * Получает одно значение param возвращает в call_back функцию с параметром
     * ответа, если ответа нет вернёт или '' или переданное в if_not_exists
     * @param param
     * @param call_back
     * @param if_not_exists
     */
    static get(param, call_back=function(){ say('CALL-BACK'); }, if_not_exists='') {
        let buff_param = param;
        param = {[buff_param]: if_not_exists};
        BACK('profil', 'profil', {'get': param}, function(mess) {
            mess_executer(mess, function(mess) {
                let response = mess.params;
                if(response !== '') {
                    call_back(response[buff_param]);
                }
            })
        });
    }
}