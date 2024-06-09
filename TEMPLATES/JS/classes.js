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

    /**
     * Удаляет список параметров, если эти поля разрешены для изменения или
     * есть параметр access_change_profil_sys
     * @param params_array
     */
    static delete(params_array) {
        let params = {};
        for(let i in params_array) {
            params[params_array[i]] = '<-->';
        }
        this.set_list(params, function() {

        });
    }

    /**
     * Передаём объект с парами
     * {
     * ключ: значение,
     * ключ: значение,
     * }
     * если поля закрытые, то система их не установит, если же
     * нужно всёравно установить - в админ-панели нужно установить access_change_profil_sys
     * @param param_argum_list
     * @param call_back
     */
    static set_list(param_argum_list, call_back=function() {}) {
        BACK('profil', 'profil', {'set': param_argum_list}, function(mess) {
            mess_executer(mess, function(mess) {
                call_back(mess.params.response);
                if(mess.params.errors.length > 0) {
                    console.dir(mess.params.errors);
                }
            });
        });
    }

    /**
     * Установит в PROFIL значение
     * 'param', 'argum'
     * @param param
     * @param argum
     * @param call_back
     */
    static set(param, argum, call_back=function() {}) {
        let param_argum_list = {[param]: argum};
        BACK('profil', 'profil', {'set': param_argum_list}, function(mess) {
            mess_executer(mess, function(mess) {
                call_back(mess.params.response);
                if(mess.params.errors.length > 0) {
                    console.dir(mess.params.errors);
                }
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
                    call_back(response.response);
                    if(response.errors.length > 0) {
                        console.dir(response.errors);
                    }
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
                if(response.response !== '') {
                    call_back(response.response[buff_param]);
                } else {
                    call_back(if_not_exists);
                }
                if(response.errors.length > 0) {
                    console.dir(response.errors);
                }
            })
        });
    }
}