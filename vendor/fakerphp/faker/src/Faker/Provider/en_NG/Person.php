<?php

namespace Faker\Provider\en_NG;

class Person extends \Faker\Provider\Person
{
    protected static $maleNameFormats = [
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{firstNameMale}} {{lastName}}',
    ];

    protected static $femaleNameFormats = [
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{firstNameFemale}} {{lastName}}',
    ];

    /**
     * An array of typical Nigerian male firstnames.
     *
     * @see https://andela-celisha-wigwe.github.io/names.html
     */
    protected static $firstNameMale = [
        'Abimbola', 'Abisola', 'Abisoye', 'Adeboye', 'Adedayo', 'Adegoke', 'Akande', 'Akanni', 'Alade', 'Ayinde', 'Azubuike',
        'Banji', 'Bankole', 'Buchi', 'Bukola',
        'Chinedu', 'Chisom', 'Chukwu',
        'Damilare', 'Damilola', 'Danjuma',
        'Ebiowei', 'Emeka', 'Emmanuel', 'Esse',
        'Funmilade', 'Funmilayo',
        'Gbeminiyi', 'Gbemisola',
        'Habiba', 'Ifeanyichukwu',
        'Ikenna', 'Ikhidie', 'Ireti',
        'Jadesola', 'Johnson',
        'Kayode', 'Kemi', 'Kubra', 'Kubura',
        'Lolade',
        'Makinwa', 'Mohammed', 'Musa', 'Muyiwa',
        'Nnamdi',
        'Olaide', 'Olufunmi', 'Olumide', 'Oluwunmi', 'Onoriode',
        'Remilekun', 'Rotimi',
        'Shade', 'Shalewa', 'Sname',
        'Tari', 'Temitope', 'Titilope', 'Tobiloba', 'Toke', 'Tomiloba', 'Tope',
        'Uzodimma',
        'Wale',
        'Yakubu', 'Yusuf', 'Yusuf',
    ];

    /**
     * @see https://andela-celisha-wigwe.github.io/names.html
     */
    protected static $firstNameFemale = [
        'Adaugo', 'Akunna', 'Aminat', 'Aminu', 'Augustina', 'Ayebatari',
        'Cherechi', 'Chiamaka', 'Chimamanda', 'Chinyere', 'Chizoba',
        'Ebiere', 'Efe',
        'Fatima', 'Ifeoma',
        'Ifunanya', 'Isioma',
        'Jolayemi',
        'Lola',
        'Obioma', 'Omawunmi', 'Omolara', 'Onome',
        'Rasheedah',
        'Sekinat', 'Simisola', 'Sumayyah',
        'Titi', 'Titilayo', 'Toluwani',
        'Zainab',
    ];

    /**
     * @see https://andela-celisha-wigwe.github.io/names.html
     */
    protected static $lastName = [
        'Abiodun', 'Abiola', 'Abodunrin', 'Abosede', 'Adaobi', 'Adebayo', 'Adegboye', 'Adegoke', 'Ademayowa', 'Ademola', 'Adeniyan', 'Adeoluwa', 'Aderinsola', 'Aderonke', 'Adesina', 'Adewale', 'Adewale', 'Adewale', 'Adewunmi', 'Adewura', 'Adeyemo', 'Afolabi', 'Afunku', 'Agboola', 'Agboola', 'Agnes', 'Aigbiniode', 'Ajakaiye', 'Ajose-adeogun', 'Akeem-omosanya', 'Akerele', 'Akintade', 'Aligbe', 'Amaechi', 'Aminat', 'Aremu', 'Atanda', 'Ayisat', 'Ayobami', 'Ayomide', 'Ayomide',
        'Babalola', 'Babatunde', 'Balogun', 'Bamisebi', 'Bello', 'Busari',
        'Chibike', 'Chibuike', 'Chidinma', 'Chidozie', 'Christian', 'Clare',
        'David', 'David',
        'Ebubechukwu', 'Egbochukwu', 'Ehigiator', 'Ekwueme', 'Elebiyo', 'Elizabeth', 'Elizabeth', 'Elizabeth', 'Emmanuel', 'Emmanuel', 'Esther',
        'Funmilayo',
        'Gbadamosi', 'Gbogboade', 'Grace',
        'Habeeb', 'Hanifat', 'Isaac',
        'Ismail', 'Isokun', 'Israel', 'Iyalla',
        'Jamiu', 'Jimoh', 'Joshua', 'Justina',
        'Katherine', 'Kayode', 'Kayode', 'Kimberly',
        'Ladega', 'Latifat', 'Lawal', 'Leonard',
        'Makuachukwu', 'Maryam', 'Maryjane', 'Mayowa', 'Miracle', 'Mobolaji', 'Mogbadunade', 'Motalo', 'Muinat', 'Mukaram', 'Mustapha', 'Mutiat',
        'Ndukwu', 'Ngozi', 'Nojeem', 'Nwachukwu', 'Nwogu', 'Nwuzor',
        'Obiageli', 'Obianuju', 'Odunayo', 'Odunayo', 'Ogunbanwo', 'Ogunwande', 'Okonkwo', 'Okunola', 'Oladeji', 'Oladimeji', 'Olaoluwa', 'Olasunkanmi', 'Olasunkanmi-fasayo', 'Olawale', 'Olubukola', 'Olubunmi', 'Olufeyikemi', 'Olumide', 'Olutola', 'Oluwakemi', 'Oluwanisola', 'Oluwaseun', 'Oluwaseyi', 'Oluwashina', 'Oluwatosin', 'Omobolaji', 'Omobolanle', 'Omolara', 'Omowale', 'Onohinosen', 'Onose', 'Onyinyechukwu', 'Opeyemi', 'Osuagwu', 'Oyebola', 'Oyelude', 'Oyinkansola',
        'Peter',
        'Sabdat', 'Saheed', 'Salami', 'Samuel', 'Sanusi', 'Sarah', 'Segunmaru', 'Sekinat', 'Sulaimon', 'Sylvester',
        'Taiwo', 'Tamunoemi', 'Tella', 'Temitope', 'Tolulope',
        'Uchechi',
        'Wasiu', 'Wilcox', 'Wuraola',
        'Yaqub', 'Yussuf',
    ];
}
