<?php
enum LINE_TYPE:string {
    case SOLID = 'solid';
    case DASHED = 'dashed';
    case DOTTED = 'dotted';
}

enum PROFIL_TYPE {
    case login;
    case id;
    case email;
}

enum TYPE_INTERVAL {
    case seconds;
    case minutes;
    case hours;
    case days;
}

enum mess_type {
    case text;
    case image;
    case video;
    case audio;
    case file;
    case pdf;
}

enum ALERT_TYPE:string {
    case MESSAGE = 'message';
    case ATTANTION = 'attantion';
    case WARNING = 'warning';
    case DANGER = 'danger';
    case ERROR = 'error';
}

enum ORDER_STATUS:string {
    case EMPTY = 'готовится';
    case WAITING = 'в ожидании';
    case WAITING_SENDER = 'ожидает отправки';
    case CREATED = 'создан';
    case PAYED = 'оплачен';
    case SENDED = 'отправлен';
    case TRACKED = 'в пути (отслеживается)';
    case RECEIVED = 'получен';
    case CLOSED = 'закрыт';
    case ABORTED = 'отменён';
}

enum CDEK_ORDER_STATUS:string {
    case EMPTY = 'ожидает подтверждения';
    case READY = 'подготовлен';
    case CREATED = 'создан';
    case FORMED = 'сформирован';
    case IN_START_POINT = 'в точке отправки';
    case TRACKED = 'в пути (отслеживается)';
    case IN_FINISH_POINT = 'в точке прибытия';
}

enum MODALITY:string {
    case empty = 'empty';
    case user = 'user';
    case operator = 'operator';
    case easy_seller = 'easy-seller';
    case admin = 'admin';
    case super_admin = 'super-admin';
    case seo_operator = 'seo-operator';
}

enum PRODUCT_GROUP:string {
    case MAIN_CAT = 'shops_categorys';
    case UNDER_CAT = 'shops_undercats';
    case ACTION_LIST = 'shops_lists';
}

enum TARIF_CDEK:string {
    case EXPRESS_D_D = 'Экспресс дверь-дверь';
    case EXPRESS_D_W = 'Экспресс дверь-склад';
    case EXPRESS_W_D = 'Экспресс склад-дверь';
    case EXPRESS_W_W = 'Экспресс склад-склад';
    case EXPRESS_D_P = 'Экспресс дверь-постамат';
    case EXPRESS_W_P = 'Экспресс склад-постамат';
    case EXPRESS_P_D = 'Экспресс постамат-дверь';
    case EXPRESS_P_W = 'Экспресс постамат-склад';
    case EXPRESS_P_P = 'Экспресс постамат-постамат';
    case M_EXPRESS_D_D = 'Магистральный экспресс дверь-дверь';
    case M_EXPRESS_D_W = 'Магистральный экспресс дверь-склад';
    case M_EXPRESS_W_D = 'Магистральный экспресс склад-дверь';
    case M_EXPRESS_W_W = 'Магистральный экспресс склад-склад';
    case M_EXPRESS_D_P = 'Магистральный экспресс дверь-постамат';
    case M_EXPRESS_W_P = 'Магистральный экспресс склад-постамат';
}
