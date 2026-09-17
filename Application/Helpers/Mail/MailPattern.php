<?php

namespace Application\Helpers\Mail;

use \Modules\Base\Models\DbTables as db;
use \Config\CC as C;
use Modules\Base\Models\PersonAlert;

class MailPattern
{

    /** @var null|\Modules\Base\Models\Person $person */
    public static $_person  = null;
    public static $_private = null;
    public static $_email   = null;
    public static $_data    = [];
    const MAIL_SYSTEM_CONFIRM  = 'confirm';
    const MAIL_SYSTEM_ADD_USER = 'add';

    /**
     * @param string $template
     * @return string
     */
    public static function getSubjects($template) {
        return (db\MailTemplate::getRow($template))->getMailTemplateSubject();
    }

    const PATTERN_USER_LOGIN    = 'LOGIN';
    const PATTERN_USER_PASSWORD = 'PASSWORD';
    const PATTERN_PIXEL = 'PIXEL';
    const PATTERN_CONFIRM_EMAIL = 'CONFIRM';
    const PATTERN_LINK          = 'LINK';
    const PATTERN_STATS_PERIOD  = 'STATS_PERIOD';
    const PATTERN_HOUSE_NAME    = 'HOUSE_NAME';
    const PATTERN_AUCTION_NAME  = 'AUCTION_NAME';
    const PATTERN_TEXT          = 'TEXT';
    const PATTERN_SUBJECT          = 'SUBJECT';

    public static $list = [
        self::PATTERN_USER_LOGIN    => 'getParticipantLogin',
        self::PATTERN_USER_PASSWORD => 'getParticipantPassword',
        self::PATTERN_LINK          => 'getLink',
        self::PATTERN_STATS_PERIOD  => 'getStatsPeriod',
        self::PATTERN_HOUSE_NAME    => 'getHouseName',
        self::PATTERN_AUCTION_NAME  => 'getAuctionName',
        self::PATTERN_TEXT          => 'getText',
        self::PATTERN_PIXEL         => 'getPixel',
        self::PATTERN_SUBJECT       => 'getSubject',
    ];

    /**
     * @return array
     */
    public static function getPatternList() {
        return [
            self::PATTERN_USER_LOGIN    => C::locale('Add login'),
            self::PATTERN_USER_PASSWORD => C::locale('Add password'),
        ];
    }

    /**
     * @return string
     */
    public static function getStatsPeriod() {
        return (int)(C::constant(db\Constant::QUEUE_STATISTIC_PERIOD) / 3600);
    }

    /**
     * @return string
     */
    public static function getParticipantLogin() {
        $person = static::$_person;
        if (is_null($person)) {
            return '';
        }
        /** @var string|int|\Modules\Base\Models\Person $person */
        return $person->lotsPersonPrivateInBaseByPersonId()->getPersonPrivateLogin();
    }

    /**
     * @return mixed|null|string
     */
    public static function getParticipantPassword() {
        return isset(self::$_data['vars'][self::PATTERN_USER_PASSWORD]) ? self::$_data['vars'][self::PATTERN_USER_PASSWORD] : '';
    }

    const MAIL_LIST_ELEMENT_MEMBERS_NEW         = 1;
    const MAIL_LIST_ELEMENT_MEMBERS_TOTAL       = 2;
    const MAIL_LIST_ELEMENT_MEMBERS_REMOVED     = 3;
    const MAIL_LIST_ELEMENT_AUCTIONEERS_NEW     = 4;
    const MAIL_LIST_ELEMENT_AUCTIONEERS_TOTAL   = 5;
    const MAIL_LIST_ELEMENT_AUCTIONEERS_REMOVED = 6;
    const MAIL_LIST_ELEMENT_HOUSE_CONFIRMED     = 7;
    const MAIL_LIST_ELEMENT_HOUSE_NOT_CONFIRMED = 8;
    const MAIL_LIST_ELEMENT_HOUSE_DRAFT         = 9;
    const MAIL_LIST_ELEMENT_HOUSE_MODERATION    = 10;
    const MAIL_LIST_ELEMENT_HOUSE_APPROVED      = 11;
    const MAIL_LIST_ELEMENT_HOUSE_DECLINED      = 12;
    const MAIL_LIST_ELEMENT_HOUSE_FREE          = 13;
    const MAIL_LIST_ELEMENT_AUCTIONS_DRAFT      = 14;
    const MAIL_LIST_ELEMENT_AUCTIONS_MODERATION = 15;
    const MAIL_LIST_ELEMENT_AUCTIONS_APPROVED   = 16;
    const MAIL_LIST_ELEMENT_AUCTIONS_DECLINED   = 17;

    public static function getElement($element) {
        $list = [
            self::MAIL_LIST_ELEMENT_MEMBERS_NEW         => C::locale('Members registered'),
            self::MAIL_LIST_ELEMENT_MEMBERS_TOTAL       => C::locale('Total members'),
            self::MAIL_LIST_ELEMENT_MEMBERS_REMOVED     => C::locale('Members deleted')
        ];
        return isset($list[$element]) ? $list[$element] : $list;
    }

    /**
     * @return mixed|null|string
     */
    public static function getText() {
        $response = '';
        if (isset(self::$_data['vars'][self::PATTERN_TEXT])) {
            if (is_array(self::$_data['vars'][self::PATTERN_TEXT])) {
                if (dfCount(self::$_data['vars'][self::PATTERN_TEXT]) > 0) {
                    foreach (self::$_data['vars'][self::PATTERN_TEXT] as $k => $v) {
                        $response .= '<li>' . self::getElement($k) . ': ' . $v . '</li>';
                    }
                }
            } else {
                $response = self::$_data['vars'][self::PATTERN_TEXT];
            }
        }
        return $response;
    }

    /**
     * @return string
     */
    public static function getPixel() {
        return isset(self::$_data['vars']) && isset(self::$_data['vars'][self::PATTERN_PIXEL]) ? self::$_data['vars'][self::PATTERN_PIXEL] : '#';
    }

    /**
     * @return string
     */
    public static function getSubject() {
        return isset(self::$_data['vars']) && isset(self::$_data['vars'][self::PATTERN_SUBJECT]) ? self::$_data['vars'][self::PATTERN_SUBJECT] : '';
    }

    /**
     * @return string
     */
    public static function getLink() {
        return isset(self::$_data['vars']) && isset(self::$_data['vars'][self::PATTERN_LINK]) ? self::$_data['vars'][self::PATTERN_LINK] : '#';
    }

    /**
     * @return string
     */
    public static function getHouseName() {
        return isset(self::$_data['vars']) && isset(self::$_data['vars'][self::PATTERN_HOUSE_NAME]) ? self::$_data['vars'][self::PATTERN_HOUSE_NAME] : '';
    }

    /**
     * @return string
     */
    public static function getAuctionName() {
        return isset(self::$_data['vars']) && isset(self::$_data['vars'][self::PATTERN_AUCTION_NAME]) ? self::$_data['vars'][self::PATTERN_AUCTION_NAME] : '';
    }
}