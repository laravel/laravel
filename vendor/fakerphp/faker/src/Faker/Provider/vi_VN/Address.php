<?php

namespace Faker\Provider\vi_VN;

class Address extends \Faker\Provider\Address
{
    protected static $buildingNumber = ['%###', '%##', '%#', '%'];
    protected static $postcode = ['#####', '######', '#####-####'];
    protected static $province = [
        'An Giang', 'Bà Rịa - Vũng Tàu',
        'Bắc Giang', 'Bắc Kạn', 'Bạc Liêu',
        'Bắc Ninh', 'Bến Tre', 'Bình Định',
        'Bình Dương', 'Bình Phước', 'Bình Thuận',
        'Cà Mau', 'Cao Bằng', 'Đắk Lắk',
        'Đắk Nông', 'Điện Biên', 'Đồng Nai',
        'Đồng Tháp', 'Gia Lai', 'Hà Giang',
        'Hà Nam', 'Hà Tĩnh', 'Hải Dương',
        'Hậu Giang', 'Hòa Bình', 'Hưng Yên',
        'Khánh Hòa', 'Kiên Giang', 'Kon Tum',
        'Lai Châu', 'Lâm Đồng', 'Lạng Sơn',
        'Lào Cai', 'Long An', 'Nam Định',
        'Nghệ An', 'Ninh Bình', 'Ninh Thuận',
        'Phú Thọ', 'Quảng Bình', 'Quảng Nam',
        'Quảng Ngãi', 'Quảng Ninh', 'Quảng Trị',
        'Sóc Trăng', 'Sơn La', 'Tây Ninh',
        'Thái Bình', 'Thái Nguyên', 'Thanh Hóa',
        'Thừa Thiên Huế', 'Tiền Giang', 'Trà Vinh',
        'Tuyên Quang', 'Vĩnh Long', 'Vĩnh Phúc',
        'Yên Bái', 'Phú Yên',
    ];
    protected static $city = [
        'Cần Thơ', 'Đà Nẵng', 'Hải Phòng', 'Hà Nội', 'Hồ Chí Minh',
    ];
    protected static $streetNameFormats = [
        'Phố {{lastName}} {{middleName}} {{firstName}}',
        'Phố {{lastName}} {{middleName}} {{firstName}}',
        'Phố {{lastName}} {{middleName}} {{firstName}}',
        'Phố {{firstName}}',
        'Phố {{lastName}}',
    ];
    protected static $streetAddressFormats = [
        '{{buildingNumber}} {{streetName}}',
    ];
    protected static $hamletNameFormats = [
        '{{hamletPrefix}} {{middleName}} {{firstName}}',
        '{{hamletPrefix}} {{lastName}} {{firstName}}',
        '{{hamletPrefix}} {{firstName}} {{lastName}}',
        '{{hamletPrefix}} {{middleName}} {{firstName}}',
        '{{hamletPrefix}} {{lastName}} {{middleName}} {{firstName}}',
        '{{hamletPrefix}} {{firstName}}',
        '{{hamletPrefix}} {{lastName}}',
        '{{hamletPrefix}} ##',
        '{{hamletPrefix}} #',
    ];
    protected static $hamletPrefix = [
        'Thôn', 'Ấp',
    ];
    protected static $wardNameFormats = [
        '{{hamletPrefix}} {{middleName}} {{firstName}}',
        '{{hamletPrefix}} {{lastName}} {{firstName}}',
        '{{wardPrefix}} {{firstName}} {{lastName}}',
        '{{wardPrefix}} {{middleName}} {{firstName}}',
        '{{wardPrefix}} {{lastName}} {{middleName}} {{firstName}}',
        '{{wardPrefix}} {{firstName}}',
        '{{wardPrefix}} {{lastName}}',
        '{{wardPrefix}} ##',
        '{{wardPrefix}} #',
    ];
    protected static $wardPrefix = [
        'Phường', 'Xã',
    ];
    protected static $districtNameFormats = [
        '{{districtPrefix}} {{middleName}} {{firstName}}',
        '{{districtPrefix}} {{lastName}} {{firstName}}',
        '{{districtPrefix}} {{firstName}} {{lastName}}',
        '{{districtPrefix}} {{middleName}} {{firstName}}',
        '{{districtPrefix}} {{lastName}} {{middleName}} {{firstName}}',
        '{{districtPrefix}} {{firstName}}',
        '{{districtPrefix}} {{lastName}}',
        '{{districtPrefix}} ##',
        '{{districtPrefix}} #',
    ];
    protected static $districtPrefix = [
        'Quận', 'Huyện',
    ];
    protected static $addressFormats = [
        "{{streetAddress}}, {{wardName}}, {{districtName}}\n{{city}}",
        "{{streetAddress}}, {{wardName}}, {{districtName}}\n{{province}}",
        "{{buildingNumber}}, {{hamletName}}, {{wardName}}, {{districtName}}\n{{province}}",
    ];
    protected static $country = [
        'Afghanistan', 'Albania', 'Algeria', 'American Samoa', 'Andorra', 'Angola', 'Anguilla', 'Antarctica (the territory South of 60 deg S)', 'Antigua and Barbuda', 'Argentina', 'Armenia', 'Aruba', 'Australia', 'Austria', 'Azerbaijan',
        'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bermuda', 'Bhutan', 'Bolivia', 'Bosnia and Herzegovina', 'Botswana', 'Bouvet Island (Bouvetoya)', 'Brazil', 'British Indian Ocean Territory (Chagos Archipelago)', 'British Virgin Islands', 'Brunei Darussalam', 'Bulgaria', 'Burkina Faso', 'Burundi',
        'Cambodia', 'Cameroon', 'Canada', 'Cape Verde', 'Cayman Islands', 'Central African Republic', 'Chad', 'Chile', 'China', 'Christmas Island', 'Cocos (Keeling) Islands', 'Colombia', 'Comoros', 'Congo', 'Cook Islands', 'Costa Rica', 'Cote d\'Ivoire', 'Croatia', 'Cuba', 'Cyprus', 'Czech Republic',
        'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic',
        'Ecuador', 'Egypt', 'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Ethiopia',
        'Faroe Islands', 'Falkland Islands (Malvinas)', 'Fiji', 'Finland', 'France', 'French Guiana', 'French Polynesia', 'French Southern Territories',
        'Gabon', 'Gambia', 'Georgia', 'Germany', 'Ghana', 'Gibraltar', 'Greece', 'Greenland', 'Grenada', 'Guadeloupe', 'Guam', 'Guatemala', 'Guernsey', 'Guinea', 'Guinea-Bissau', 'Guyana',
        'Haiti', 'Heard Island and McDonald Islands', 'Holy See (Vatican City State)', 'Honduras', 'Hong Kong', 'Hungary',
        'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq', 'Ireland', 'Isle of Man', 'Israel', 'Italy',
        'Jamaica', 'Japan', 'Jersey', 'Jordan',
        'Kazakhstan', 'Kenya', 'Kiribati', 'Korea', 'Korea', 'Kuwait', 'Kyrgyz Republic',
        'Lao People\'s Democratic Republic', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libyan Arab Jamahiriya', 'Liechtenstein', 'Lithuania', 'Luxembourg',
        'Macao', 'Macedonia', 'Madagascar', 'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Marshall Islands', 'Martinique', 'Mauritania', 'Mauritius', 'Mayotte', 'Mexico', 'Micronesia', 'Moldova', 'Monaco', 'Mongolia', 'Montenegro', 'Montserrat', 'Morocco', 'Mozambique', 'Myanmar',
        'Namibia', 'Nauru', 'Nepal', 'Netherlands Antilles', 'Netherlands', 'New Caledonia', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'Niue', 'Norfolk Island', 'Northern Mariana Islands', 'Norway',
        'Oman',
        'Pakistan', 'Palau', 'Palestinian Territories', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Pitcairn Islands', 'Poland', 'Portugal', 'Puerto Rico',
        'Qatar',
        'Reunion', 'Romania', 'Russian Federation', 'Rwanda',
        'Saint Barthelemy', 'Saint Helena', 'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Martin', 'Saint Pierre and Miquelon', 'Saint Vincent and the Grenadines', 'Samoa', 'San Marino', 'Sao Tome and Principe', 'Saudi Arabia', 'Senegal', 'Serbia', 'Seychelles', 'Sierra Leone', 'Singapore', 'Slovakia (Slovak Republic)', 'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa', 'South Georgia and the South Sandwich Islands', 'Spain', 'Sri Lanka', 'Sudan', 'Suriname', 'Svalbard & Jan Mayen Islands', 'Swaziland', 'Sweden', 'Switzerland', 'Syrian Arab Republic',
        'Taiwan', 'Tajikistan', 'Tanzania', 'Thailand', 'Timor-Leste', 'Togo', 'Tokelau', 'Tonga', 'Trinidad and Tobago', 'Tunisia', 'Turkey', 'Turkmenistan', 'Turks and Caicos Islands', 'Tuvalu',
        'Uganda', 'Ukraine', 'United Arab Emirates', 'United Kingdom', 'United States of America', 'United States Minor Outlying Islands', 'United States Virgin Islands', 'Uruguay', 'Uzbekistan',
        'Vanuatu', 'Venezuela', 'Vietnam',
        'Wallis and Futuna', 'Western Sahara',
        'Yemen',
        'Zambia', 'Zimbabwe',
    ];

    public function hamletName()
    {
        $format = static::randomElement(static::$hamletNameFormats);

        return static::bothify($this->generator->parse($format));
    }

    public function hamletPrefix()
    {
        return static::randomElement(static::$hamletPrefix);
    }

    public function wardName()
    {
        $format = static::randomElement(static::$wardNameFormats);

        return static::bothify($this->generator->parse($format));
    }

    public function wardPrefix()
    {
        return static::randomElement(static::$wardPrefix);
    }

    public function districtName()
    {
        $format = static::randomElement(static::$districtNameFormats);

        return static::bothify($this->generator->parse($format));
    }

    public function districtPrefix()
    {
        return static::randomElement(static::$districtPrefix);
    }

    /**
     * @example 'Hà Nội'
     */
    public function city()
    {
        return static::randomElement(static::$city);
    }

    /**
     * @example 'Bắc Giang'
     */
    public static function province()
    {
        return static::randomElement(static::$province);
    }
}
