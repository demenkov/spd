<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "operator".
 *
 * @property integer $id
 * @property string $name
 * @property string $country
 *
 * @property Click[] $clicks
 */
class Operator extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'operator';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'country'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['country'], 'string', 'max' => 2]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'country' => Yii::t('app', 'Country'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClicks()
    {
        return $this->hasMany(Click::className(), ['operator_id' => 'id']);
    }
    /**
     * Get operator identifier by ip address.
     * @param string $ip 
     * @return int
     */
    public static function getOperatorId($ip) {
        $map = static::getMap();
        foreach ($map as $key => $id) {
            list($range, $netmask) = explode('/', $key, 2);
            if (static::ipInRange($ip, $range, $netmask)) {
                return $id;
            }
        }
        return FALSE;
    }
    /**
     * Save operators in database and map it into cache.
     * @param bool $update 
     * @return array
     */
    public static function getMap($update = FALSE) {
        $key = 'operators';
        //try to load fromcache if it isn't update
        if ($update || ($map = Yii::$app->cache->get($key)) === FALSE) {
            //replace with real data loading
            $loadedJsonOperators = '[{"name": "Operator name","country_code": "RU","subnets": ["5.44.32.0/21","176.28.80.0/21","77.244.112.0/20","5.191.0.0/16"]},{"name": "Internal","country_code": "RU","subnets": ["127.0.0.1/32"]}]';
            $operators = json_decode($loadedJsonOperators);
            $map = [];
            foreach ($operators as $operator) {
                $params = [
                    'name' => $operator->name,
                    'country' => $operator->country_code,
                ];
                //save new operators into db
                if (empty($existsOperator = Operator::findOne($params))) {
                    $existsOperator = new Operator($params);
                    $existsOperator->save();
                }
                //fill map with operator subnets
                foreach ($operator->subnets as $subnet) {
                    $map[$subnet] = $existsOperator->id;
                }
            }
            Yii::$app->cache->set($key, $map);
        }
        return $map;
    }
    /**
     * Check ip in network range.
     * @param string $ip 
     * @param string $range 
     * @param string $mask 
     * @return bool
     */
    public static function ipInRange($ip, $range, $mask) {
        $wildcard_decimal = pow(2,(32 - $mask)) - 1;
        $netmask_decimal = ~ $wildcard_decimal;
        return ((ip2long($ip) & $netmask_decimal) == (ip2long($range) & $netmask_decimal));
    }
    /**
     * Country list ISO 3166-2 officially assigned
     * @return array
     */
    public static function countryList() {
        $countries = [
            'AD' => Yii::t('app', "Andorra"),
            'AE' => Yii::t('app', "United Arab Emirates"),
            'AF' => Yii::t('app', "Afghanistan"),
            'AG' => Yii::t('app', "Antigua and Barbuda"),
            'AI' => Yii::t('app', "Anguilla"),
            'AL' => Yii::t('app', "Albania"),
            'AM' => Yii::t('app', "Armenia"),
            'AO' => Yii::t('app', "Angola"),
            'AR' => Yii::t('app', "Argentina"),
            'AS' => Yii::t('app', "American Samoa"),
            'AT' => Yii::t('app', "Austria"),
            'AU' => Yii::t('app', "Australia"),
            'AW' => Yii::t('app', "Aruba"),
            'AX' => Yii::t('app', "Aland Islands"),
            'AZ' => Yii::t('app', "Azerbaijan"),
            'BA' => Yii::t('app', "Bosnia and Herzegovina"),
            'BB' => Yii::t('app', "Barbados"),
            'BD' => Yii::t('app', "Bangladesh"),
            'BE' => Yii::t('app', "Belgium"),
            'BF' => Yii::t('app', "Burkina Faso"),
            'BG' => Yii::t('app', "Bulgaria"),
            'BH' => Yii::t('app', "Bahrain"),
            'BI' => Yii::t('app', "Burundi"),
            'BJ' => Yii::t('app', "Benin"),
            'BL' => Yii::t('app', "Saint Bartelemey"),
            'BM' => Yii::t('app', "Bermuda"),
            'BN' => Yii::t('app', "Brunei Darussalam"),
            'BO' => Yii::t('app', "Bolivia"),
            'BQ' => Yii::t('app', "Bonaire, Saint Eustatius and Saba"),
            'BR' => Yii::t('app', "Brazil"),
            'BS' => Yii::t('app', "Bahamas"),
            'BT' => Yii::t('app', "Bhutan"),
            'BV' => Yii::t('app', "Bouvet Island"),
            'BW' => Yii::t('app', "Botswana"),
            'BY' => Yii::t('app', "Belarus"),
            'BZ' => Yii::t('app', "Belize"),
            'CA' => Yii::t('app', "Canada"),
            'CC' => Yii::t('app', "Cocos (Keeling) Islands"),
            'CD' => Yii::t('app', "Congo, The Democratic Republic of the"),
            'CF' => Yii::t('app', "Central African Republic"),
            'CG' => Yii::t('app', "Congo"),
            'CH' => Yii::t('app', "Switzerland"),
            'CI' => Yii::t('app', "Cote d'Ivoire"),
            'CK' => Yii::t('app', "Cook Islands"),
            'CL' => Yii::t('app', "Chile"),
            'CM' => Yii::t('app', "Cameroon"),
            'CN' => Yii::t('app', "China"),
            'CO' => Yii::t('app', "Colombia"),
            'CR' => Yii::t('app', "Costa Rica"),
            'CU' => Yii::t('app', "Cuba"),
            'CV' => Yii::t('app', "Cape Verde"),
            'CW' => Yii::t('app', "Curacao"),
            'CX' => Yii::t('app', "Christmas Island"),
            'CY' => Yii::t('app', "Cyprus"),
            'CZ' => Yii::t('app', "Czech Republic"),
            'DE' => Yii::t('app', "Germany"),
            'DJ' => Yii::t('app', "Djibouti"),
            'DK' => Yii::t('app', "Denmark"),
            'DM' => Yii::t('app', "Dominica"),
            'DO' => Yii::t('app', "Dominican Republic"),
            'DZ' => Yii::t('app', "Algeria"),
            'EC' => Yii::t('app', "Ecuador"),
            'EE' => Yii::t('app', "Estonia"),
            'EG' => Yii::t('app', "Egypt"),
            'EH' => Yii::t('app', "Western Sahara"),
            'ER' => Yii::t('app', "Eritrea"),
            'ES' => Yii::t('app', "Spain"),
            'ET' => Yii::t('app', "Ethiopia"),
            'FI' => Yii::t('app', "Finland"),
            'FJ' => Yii::t('app', "Fiji"),
            'FK' => Yii::t('app', "Falkland Islands (Malvinas)"),
            'FM' => Yii::t('app', "Micronesia, Federated States of"),
            'FO' => Yii::t('app', "Faroe Islands"),
            'FR' => Yii::t('app', "France"),
            'GA' => Yii::t('app', "Gabon"),
            'GB' => Yii::t('app', "United Kingdom"),
            'GD' => Yii::t('app', "Grenada"),
            'GE' => Yii::t('app', "Georgia"),
            'GF' => Yii::t('app', "French Guiana"),
            'GG' => Yii::t('app', "Guernsey"),
            'GH' => Yii::t('app', "Ghana"),
            'GI' => Yii::t('app', "Gibraltar"),
            'GL' => Yii::t('app', "Greenland"),
            'GM' => Yii::t('app', "Gambia"),
            'GN' => Yii::t('app', "Guinea"),
            'GP' => Yii::t('app', "Guadeloupe"),
            'GQ' => Yii::t('app', "Equatorial Guinea"),
            'GR' => Yii::t('app', "Greece"),
            'GS' => Yii::t('app', "South Georgia and the South Sandwich Islands"),
            'GT' => Yii::t('app', "Guatemala"),
            'GU' => Yii::t('app', "Guam"),
            'GW' => Yii::t('app', "Guinea-Bissau"),
            'GY' => Yii::t('app', "Guyana"),
            'HK' => Yii::t('app', "Hong Kong"),
            'HM' => Yii::t('app', "Heard Island and McDonald Islands"),
            'HN' => Yii::t('app', "Honduras"),
            'HR' => Yii::t('app', "Croatia"),
            'HT' => Yii::t('app', "Haiti"),
            'HU' => Yii::t('app', "Hungary"),
            'ID' => Yii::t('app', "Indonesia"),
            'IE' => Yii::t('app', "Ireland"),
            'IL' => Yii::t('app', "Israel"),
            'IM' => Yii::t('app', "Isle of Man"),
            'IN' => Yii::t('app', "India"),
            'IO' => Yii::t('app', "British Indian Ocean Territory"),
            'IQ' => Yii::t('app', "Iraq"),
            'IR' => Yii::t('app', "Iran"),
            'IS' => Yii::t('app', "Iceland"),
            'IT' => Yii::t('app', "Italy"),
            'JE' => Yii::t('app', "Jersey"),
            'JM' => Yii::t('app', "Jamaica"),
            'JO' => Yii::t('app', "Jordan"),
            'JP' => Yii::t('app', "Japan"),
            'KE' => Yii::t('app', "Kenya"),
            'KG' => Yii::t('app', "Kyrgyzstan"),
            'KH' => Yii::t('app', "Cambodia"),
            'KI' => Yii::t('app', "Kiribati"),
            'KM' => Yii::t('app', "Comoros"),
            'KN' => Yii::t('app', "Saint Kitts and Nevis"),
            'KR' => Yii::t('app', "Korea, Republic of"),
            'KW' => Yii::t('app', "Kuwait"),
            'KY' => Yii::t('app', "Cayman Islands"),
            'KZ' => Yii::t('app', "Kazakhstan"),
            'LA' => Yii::t('app', "Laos"),
            'LB' => Yii::t('app', "Lebanon"),
            'LC' => Yii::t('app', "Saint Lucia"),
            'LI' => Yii::t('app', "Liechtenstein"),
            'LK' => Yii::t('app', "Sri Lanka"),
            'LR' => Yii::t('app', "Liberia"),
            'LS' => Yii::t('app', "Lesotho"),
            'LT' => Yii::t('app', "Lithuania"),
            'LU' => Yii::t('app', "Luxembourg"),
            'LV' => Yii::t('app', "Latvia"),
            'MA' => Yii::t('app', "Morocco"),
            'MC' => Yii::t('app', "Monaco"),
            'MD' => Yii::t('app', "Moldova, Republic of"),
            'ME' => Yii::t('app', "Montenegro"),
            'MF' => Yii::t('app', "Saint Martin"),
            'MG' => Yii::t('app', "Madagascar"),
            'MH' => Yii::t('app', "Marshall Islands"),
            'MK' => Yii::t('app', "Macedonia"),
            'ML' => Yii::t('app', "Mali"),
            'MM' => Yii::t('app', "Myanmar"),
            'MN' => Yii::t('app', "Mongolia"),
            'MO' => Yii::t('app', "Macao"),
            'MP' => Yii::t('app', "Northern Mariana Islands"),
            'MQ' => Yii::t('app', "Martinique"),
            'MR' => Yii::t('app', "Mauritania"),
            'MS' => Yii::t('app', "Montserrat"),
            'MT' => Yii::t('app', "Malta"),
            'MU' => Yii::t('app', "Mauritius"),
            'MV' => Yii::t('app', "Maldives"),
            'MW' => Yii::t('app', "Malawi"),
            'MX' => Yii::t('app', "Mexico"),
            'MY' => Yii::t('app', "Malaysia"),
            'MZ' => Yii::t('app', "Mozambique"),
            'NA' => Yii::t('app', "Namibia"),
            'NC' => Yii::t('app', "New Caledonia"),
            'NE' => Yii::t('app', "Niger"),
            'NF' => Yii::t('app', "Norfolk Island"),
            'NG' => Yii::t('app', "Nigeria"),
            'NI' => Yii::t('app', "Nicaragua"),
            'NL' => Yii::t('app', "Netherlands"),
            'NO' => Yii::t('app', "Norway"),
            'NP' => Yii::t('app', "Nepal"),
            'NR' => Yii::t('app', "Nauru"),
            'NU' => Yii::t('app', "Niue"),
            'NZ' => Yii::t('app', "New Zealand"),
            'OM' => Yii::t('app', "Oman"),
            'PA' => Yii::t('app', "Panama"),
            'PE' => Yii::t('app', "Peru"),
            'PF' => Yii::t('app', "French Polynesia"),
            'PG' => Yii::t('app', "Papua New Guinea"),
            'PH' => Yii::t('app', "Philippines"),
            'PK' => Yii::t('app', "Pakistan"),
            'PL' => Yii::t('app', "Poland"),
            'PM' => Yii::t('app', "Saint Pierre and Miquelon"),
            'PN' => Yii::t('app', "Pitcairn"),
            'PR' => Yii::t('app', "Puerto Rico"),
            'PT' => Yii::t('app', "Portugal"),
            'PW' => Yii::t('app', "Palau"),
            'PY' => Yii::t('app', "Paraguay"),
            'QA' => Yii::t('app', "Qatar"),
            'RE' => Yii::t('app', "Reunion"),
            'RO' => Yii::t('app', "Romania"),
            'RS' => Yii::t('app', "Serbia"),
            'RU' => Yii::t('app', "Russian Federation"),
            'RW' => Yii::t('app', "Rwanda"),
            'SA' => Yii::t('app', "Saudi Arabia"),
            'SB' => Yii::t('app', "Solomon Islands"),
            'SC' => Yii::t('app', "Seychelles"),
            'SD' => Yii::t('app', "Sudan"),
            'SE' => Yii::t('app', "Sweden"),
            'SG' => Yii::t('app', "Singapore"),
            'SI' => Yii::t('app', "Slovenia"),
            'SJ' => Yii::t('app', "Svalbard and Jan Mayen"),
            'SK' => Yii::t('app', "Slovakia"),
            'SL' => Yii::t('app', "Sierra Leone"),
            'SM' => Yii::t('app', "San Marino"),
            'SN' => Yii::t('app', "Senegal"),
            'SO' => Yii::t('app', "Somalia"),
            'SR' => Yii::t('app', "Suriname"),
            'SS' => Yii::t('app', "South Sudan"),
            'ST' => Yii::t('app', "Sao Tome and Principe"),
            'SV' => Yii::t('app', "El Salvador"),
            'SX' => Yii::t('app', "Sint Maarten"),
            'SY' => Yii::t('app', "Syrian Arab Republic"),
            'SZ' => Yii::t('app', "Swaziland"),
            'TC' => Yii::t('app', "Turks and Caicos Islands"),
            'TD' => Yii::t('app', "Chad"),
            'TF' => Yii::t('app', "French Southern Territories"),
            'TG' => Yii::t('app', "Togo"),
            'TH' => Yii::t('app', "Thailand"),
            'TJ' => Yii::t('app', "Tajikistan"),
            'TK' => Yii::t('app', "Tokelau"),
            'TL' => Yii::t('app', "Timor-Leste"),
            'TM' => Yii::t('app', "Turkmenistan"),
            'TN' => Yii::t('app', "Tunisia"),
            'TO' => Yii::t('app', "Tonga"),
            'TR' => Yii::t('app', "Turkey"),
            'TT' => Yii::t('app', "Trinidad and Tobago"),
            'TV' => Yii::t('app', "Tuvalu"),
            'TW' => Yii::t('app', "Taiwan"),
            'TZ' => Yii::t('app', "Tanzania"),
            'UA' => Yii::t('app', "Ukraine"),
            'UG' => Yii::t('app', "Uganda"),
            'UM' => Yii::t('app', "United States Minor Outlying Islands"),
            'US' => Yii::t('app', "United States"),
            'UY' => Yii::t('app', "Uruguay"),
            'UZ' => Yii::t('app', "Uzbekistan"),
            'VC' => Yii::t('app', "Saint Vincent and the Grenadines"),
            'VE' => Yii::t('app', "Venezuela"),
            'VG' => Yii::t('app', "Virgin Islands, British"),
            'VI' => Yii::t('app', "Virgin Islands, U.S."),
            'VN' => Yii::t('app', "Vietnam"),
            'VU' => Yii::t('app', "Vanuatu"),
            'WF' => Yii::t('app', "Wallis and Futuna"),
            'WS' => Yii::t('app', "Samoa"),
            'YE' => Yii::t('app', "Yemen"),
            'YT' => Yii::t('app', "Mayotte"),
            'ZA' => Yii::t('app', "South Africa"),
            'ZM' => Yii::t('app', "Zambia"),
            'ZW' => Yii::t('app', "Zimbabwe"),
        ];

        asort($countries);

        return $countries;
    }
}
