<?php

namespace Faker\Provider\fi_FI;

class Person extends \Faker\Provider\Person
{
    protected static $maleNameFormats = [
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}}',
        '{{titleMale}} {{firstNameMale}} {{lastName}}',
    ];

    protected static $femaleNameFormats = [
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}}',
        '{{titleFemale}} {{firstNameFemale}} {{lastName}}',
    ];

    protected static $firstNameMale = [
        'Aleksi', 'Anssi', 'Antero', 'Antti', 'Ari', 'Arttu', 'Daniel', 'Eero', 'Eetu', 'Elias', 'Elmo', 'Emil', 'Erkki',
        'Hampus', 'Hannu', 'Harri', 'Heikki', 'Helmi', 'Henri', 'Hermanni', 'Ilja', 'Jaakko', 'Jake', 'Jani', 'Janne',
        'Jari', 'Jarno', 'Jere', 'Jeremy', 'Jesper', 'Jesse', 'Jimi', 'Joakim', 'Joel', 'Joona', 'Joonas', 'Juha',
        'Juho', 'Jukka', 'Julius', 'Jussi', 'Justus', 'Juuso', 'Kalle', 'Kasperi', 'Konsta', 'Kristian', 'Lassi', 'Leevi',
        'Leo', 'Levin', 'Luca', 'Lukas', 'Magnus', 'Marko', 'Markus', 'Matias', 'Matti', 'Miika', 'Mika', 'Mikael',
        'Mikko', 'Neo', 'Nico', 'Niklas', 'Niko', 'Oliver', 'Oskari', 'Ossi', 'Otto', 'Paavo', 'Pasi', 'Patrik',
        'Paulus', 'Peetu', 'Pekka', 'Pertti', 'Petri', 'Petteri', 'Pyry', 'Rami', 'Rasmus', 'Riku', 'Risto', 'Roope',
        'Saku', 'Sami', 'Samu', 'Samuel', 'Samuli', 'Santeri', 'Taneli', 'Tatu', 'Teemu', 'Teppo', 'Tero', 'Timo',
        'Tomi', 'Tommi', 'Topi', 'Touko', 'Tuomas', 'Tuomo', 'Tuukka', 'Tuukka', 'Valtteri', 'Veli', 'Viljo', 'Ville',
        'Aake', 'Aapeli', 'Aapo', 'Aappo', 'Aarni', 'Aaro', 'Aatto', 'Aatu', 'Akseli', 'Aku', 'Antton', 'Artturi',
        'Aune', 'Beeda', 'Briitta', 'Eeli', 'Eelis', 'Eemeli', 'Ekku', 'Eljas', 'Erkko', 'Iiro', 'Ilmari', 'Isto',
        'Jirko', 'Joonatan', 'Jore', 'Junnu', 'Jusu', 'Kaste', 'Kauto', 'Luukas', 'Nuutti', 'Onni', 'Osmo', 'Pekko',
        'Sampo', 'Santtu', 'Sauli', 'Simo', 'Sisu', 'Teijo', 'Unto', 'Urho', 'Veeti', 'Veikko', 'Vilho', 'Werneri', 'Wiljami',

    ];

    protected static $firstNameFemale = [
        'Aada', 'Ada', 'Aina', 'Aino', 'Aki', 'Aliisa', 'Amalia', 'Amanda', 'Amelia', 'Amira', 'Anissa', 'Anita', 'Anna',
        'Anne', 'Anni', 'Anniina', 'Annu', 'Anu', 'Asta', 'Atte', 'Atte', 'Aura', 'Aurora', 'Bella', 'Cara',
        'Celina', 'Christa', 'Christel', 'Clara', 'Cornelia', 'Dani', 'Eija', 'Elea', 'Elina', 'Elisa', 'Elise', 'Ella',
        'Ellen', 'Elma', 'Emilia', 'Emma', 'Emmi', 'Enna', 'Erja', 'Esa', 'Essi', 'Eva', 'Eveliina', 'Fanni',
        'Fiona', 'Hanna', 'Heidi', 'Heli', 'Helinä', 'Henna', 'Hilda', 'Hilja', 'Hilla', 'Hilma', 'Iida', 'Iina',
        'Iiris', 'Ilona', 'Inka', 'Inkeri', 'Inna', 'Isabella', 'Jade', 'Jami', 'Janette', 'Janika', 'Janina', 'Janita',
        'Janna', 'Janni', 'Jasmiina', 'Jenna', 'Jenni', 'Jessica', 'Johanna', 'Joni', 'Jonna', 'Julia', 'Juulia', 'Kaija',
        'Karla', 'Karri', 'Kati', 'Katja', 'Katri', 'Kia', 'Kimi', 'Kirsi', 'Krista', 'Lari', 'Laura', 'Lauri',
        'Lea', 'Lila', 'Linnea', 'Lotta', 'Lumina', 'Maarit', 'Maia', 'Maija', 'Maiju', 'Maisa', 'Mari', 'Maria',
        'Meeri', 'Meri', 'Mette', 'Mia', 'Milla', 'Mimi', 'Mimosa', 'Minna', 'Mira', 'Mirella', 'Miska', 'Nadja',
        'Natalia', 'Nea', 'Neea', 'Nella', 'Nia', 'Niina', 'Noora', 'Olga', 'Olivia', 'Oona', 'Outi', 'Paula',
        'Pauliina', 'Petra', 'Pia', 'Piia', 'Pinja', 'Päivi', 'Reeta', 'Reetta', 'Riikka', 'Riina', 'Ritva', 'Roni',
        'Ronja', 'Sanna', 'Sari', 'Satu', 'Seija', 'Sirpa', 'Siru', 'Susanna', 'Tanja', 'Tara', 'Taru', 'Tea',
        'Terhi', 'Tiia', 'Tiina', 'Tiiu', 'Tinja', 'Veera', 'Vili', 'Vilma', 'Wilma', 'Aamu', 'Aliina', 'Annilotta',
        'Eerika', 'Eeva', 'Eevi', 'Eliina', 'Elviira', 'Emmaliina', 'Enni', 'Ennika', 'Helmiina', 'Henniina',
        'Hertta', 'Hilppa', 'Iia', 'Iita', 'Jadessa', 'Jemina', 'Jenika', 'Jermia', 'Jooa', 'Juttamari', 'Kaisla',
        'Kaisu', 'Loviisa', 'Malla', 'Martta', 'Matleena', 'Miina', 'Mimmu', 'Minea', 'Minttu', 'Mirva', 'Nelli', 'Ninni',
        'Oliivia', 'Peppi', 'Pihla', 'Pirkko', 'Riia', 'Roosa', 'Taika', 'Venla', 'Viivi', 'Vilja',
    ];

    protected static $lastName = [
        'Aakula', 'Aalto', 'Aaltonen', 'Aarnio', 'Aaronen', 'Aavikkola', 'Ahmala', 'Aho', 'Ahokas', 'Ahola', 'Ahomaa', 'Ahonen', 'Ahoniemi', 'Ahopelto', 'Ahovaara', 'Ahtila', 'Ahtiluoto', 'Ahtio', 'Ahtisaari', 'Ahto', 'Ahtola', 'Ahtonen', 'Ahtorinne', 'Aija', 'Aijala', 'Ainola', 'Aitio', 'Aitolahti', 'Aitomaa', 'Aittasalmi', 'Akkala', 'Akkanen', 'Alahuhta', 'Alajoki', 'Alajärvi', 'Alanen', 'Alatalo', 'Alasalmi', 'Alapuro', 'Alhola', 'Alijoki', 'Ankkala', 'Ankkuri', 'Annala', 'Annunen', 'Anttila', 'Anttinen', 'Anttonen', 'Ara', 'Arhila', 'Arhinmäki', 'Arhosuo', 'Arinen', 'Arjamaa', 'Arjanen', 'Arkkila', 'Armio', 'Arnio', 'Aronen', 'Arosuo', 'Arponen', 'Arvola', 'Asikainen', 'Astala', 'Attila', 'Aunela', 'Aura', 'Auramies', 'Auranen', 'Autio', 'Auvinen', 'Auvola', 'Avonius', 'Avotie',
        'Bräysy',
        'Davidsainen', 'Dufva',
        'Eerikäinen', 'Eerola', 'Einel', 'Eino', 'Einola', 'Einonen', 'Ekman', 'Ekola', 'Ellilä', 'Ellinen', 'Elomaa', 'Eloharju', 'Eloranta', 'Eno', 'Enola', 'Enäjärvi', 'Erkinjuntti', 'Erkkilä', 'Erkkinen', 'Erkko', 'Erkkola', 'Ernamo', 'Erola', 'Eronen', 'Ervola', 'Eräharju', 'Erämaja', 'Eränen', 'Eskelinen', 'Eskelä', 'Eskola', 'Evelä', 'Evilä',
        'Filppula', 'Finni', 'Frändilä', 'Fränti',
        'Haahka', 'Haahkola', 'Haanpää', 'Haapakorpi', 'Haapala', 'Haapanen', 'Haaparanta', 'Haapasalmi', 'Haapasalo', 'Haapkylä', 'Haapoja', 'Haataja', 'Haavisto', 'Haikala', 'Haikara', 'Hakala', 'Hakkarainen', 'Hakki', 'Hakula', 'Halinen', 'Halkola', 'Halkonen', 'Halla', 'Hallaper', 'Hallapuro', 'Hallikainen', 'Hallila', 'Hallonen', 'Halme', 'Halmela', 'Halmelahti', 'Halonen', 'Halttunen', 'Hammas', 'Hanhela', 'Hanhinen', 'Hannula', 'Hannunen', 'Hapola', 'Harjamäki', 'Harju', 'Harjula', 'Harjunpää', 'Harkimo', 'Hautakangas', 'Hautakoski', 'Hautala', 'Hautamäki', 'Haverinen', 'Havukoski', 'Heikkilä', 'Heikkinen', 'Heimola', 'Heinälä', 'Heiskanen', 'Heiskari', 'Helenius', 'Helinen', 'Helismaa', 'Helmel', 'Helovirta', 'Helppolainen', 'Helstel', 'Hellgren', 'Hentinen', 'Hento', 'Hepomäki', 'Heponen', 'Herranen', 'Hervanta', 'Hervanto', 'Hekkaharju', 'Hiesu', 'Hietala', 'Hietanen', 'Hiltunen', 'Heintikainen', 'Hirvelä', 'Hirvi', 'Hirvikangas', 'Hirvonen', 'Hoikkala', 'Hoikkanen', 'Holappa', 'Holkeri', 'Hongisto', 'Honkanen', 'Hovi', 'Huhta', 'Huhtala', 'Hukkala', 'Huopainen', 'Huotari', 'Huovinen', 'Huttunen', 'Huuhka', 'Huurinainen', 'Huusko', 'Huvinen', 'Hyppölä', 'Hyppönen', 'Hytölä', 'Hyypiä', 'Hyyppä', 'Häkkinen', 'Häkämies', 'Hämäläinen', 'Hänninen', 'Härkönen',
        'Ihalainen', 'Ikola', 'Ikonen', 'Ilmarinen', 'Ilomäki', 'Iloniemi', 'Ilvesniemi', 'Immonen', 'Inkeri', 'Inkinen', 'Isoluoma', 'Isomäki', 'Isotalo', 'Itkonen', 'Itävaara', 'Itävuori',
        'Jaakkola', 'Jaakkonen', 'Jaakonmaa', 'Jaatinen', 'Jakkila', 'Jalonen', 'Jauhiainen', 'Jauho', 'Joenhaara', 'Johto', 'Jokelainen', 'Jokihaara', 'Jokimies', 'Jokinen', 'Jortikka', 'Joru', 'Junkkari', 'Juntti', 'Juppi', 'Jurva', 'Jurvala', 'Jurvanen', 'Jussila', 'Juustinen', 'Juuti', 'Juvanen', 'Juvonen', 'Jylhä', 'Jänis', 'Jäppinen', 'Järvelä', 'Jääskeläinen',
        'Kaakko', 'Kaikkonen', 'Kainulainen', 'Kaista', 'Kaivola', 'Kakkola', 'Kakkonen', 'Kalinainen', 'Kalkkinen', 'Kalliala', 'Kallio', 'Kaillomäki', 'Kalmo', 'Kalvo', 'Kamari', 'Kamppinen', 'Kanala', 'Kangaskorte', 'Kangassalo', 'Kannelmaa', 'Kannelmäki', 'Kantele', 'Kantola', 'Kapanen', 'Karalahti', 'Karhu', 'Karjalainen', 'Karpela', 'Karppinen', 'Karukoski', 'Karvonen', 'Katainen', 'Kataja', 'Kauhala', 'Kaukovaara', 'Kauppala', 'Kauppinen', 'Kaurismäki', 'Kekkonen', 'Kerava', 'Kerttula', 'Keskinen', 'Keskioja', 'Ketola', 'Ketonen', 'Kettula', 'Kieli', 'Kiianen', 'Kiille', 'Kimalainen', 'Kiiski', 'Kinnula', 'Kinnunen', 'Kiskonen', 'Kissala', 'Kivi', 'Kiviniemi', 'Kivistö', 'Koirala', 'Koivisto', 'Koivula', 'Koivulehto', 'Koivuniemi', 'Kokkonen', 'Kolehmainen', 'Komulainen', 'Konttinen', 'Kontunen', 'Korhonen', 'Koriseva', 'Kortesjärvi', 'Koskela', 'Koskelainen', 'Kosonen', 'Kotanen', 'Koukkula', 'Kouvonen', 'Kovalainen', 'Krapu', 'Krekelä', 'Kujala', 'Kujanpää', 'Kukkala', 'Kukkamäki', 'Kukkonen', 'Kultala', 'Kumpula', 'Kumpulainen', 'Kunnas', 'Kuoppala', 'Kuosmanen', 'Kurkela', 'Kurki', 'Kuusijärvi', 'Kyllönen', 'Kynsijärvi', 'Kynsilehto', 'Kärki', 'Kärkkäinen',
        'Laakkola', 'Laakkonen', 'Laakso', 'Laaksonen', 'Laatikainen', 'Lahdenpää', 'Laine', 'Lainela', 'Lakka', 'Lampinen', 'Lappalainen', 'Lassinen', 'Laurila', 'Lauronen', 'Lavola', 'Lehmälä', 'Lehtimäki', 'Lehtinen', 'Lehtisalo', 'Lehto', 'Lehtonen', 'Leino', 'Lepistö', 'Lepomäki', 'Leppilampi', 'Leppäkorpi', 'Leppälä', 'Leppävirta', 'Leskinen', 'Liimatainen', 'Lind', 'Linnala', 'Linnamäki', 'Lippo', 'Litmanen', 'Litvala', 'Liukkonen', 'Loiri', 'Lukkari', 'Lumme', 'Luoma', 'Luukkonen', 'Lyly', 'Lyytikäinen', 'Lähteenmäki', 'Lämsä',
        'Maahinen', 'Made', 'Maijala', 'Makkonen', 'Malmi', 'Malmivaara', 'Mannila', 'Manninen', 'Mannonen', 'Mansikka-aho', 'Mansikkaoja', 'Marila', 'Marjala', 'Marjamäki', 'Marjola', 'Marjomaa', 'Marjonen', 'Markkanen', 'Markkula', 'Markuksela', 'Markus', 'Martikainen', 'Marttinen', 'Masala', 'Masanen', 'Matomäki', 'Mattila', 'Maunula', 'Maunola', 'Melasniemi', 'Merelä', 'Merilä', 'Meriläinen', 'Merimaa', 'Metsoja', 'Metsälampi', 'Metsäoja', 'Mielonen', 'Miettinen', 'Mikkola', 'Mikkonen', 'Muhonen', 'Mujunen', 'Murola', 'Mustapää', 'Mustonen', 'Muurinen', 'Myllymäki', 'Myllypuro', 'Myllys', 'Mylläri', 'Mäenpää', 'Mäkelä', 'Mäki', 'Mäkinen', 'Mäntylä', 'Määttä', 'Möttönen',
        'Naula', 'Naulapää', 'Neuvonen', 'Nevala', 'Niemelä', 'Niemi', 'Nieminen', 'Niemistö', 'Niinimaa', 'Niinistö', 'Niiranen', 'Nikkanen', 'Nikkilä', 'Nikula', 'Nikulainen', 'Niskala', 'Nisukangas', 'Niukkanen', 'Nokelainen', 'Nokkonen', 'Notkonen', 'Nousiainen', 'Nukka', 'Nummelin', 'Nuotio', 'Nurkkala', 'Nurmela', 'Nurmi', 'Nurminiemi', 'Nurminen', 'Nuutti', 'Nykänen', 'Nyman', 'Närvälä', 'Näätänen',
        'Oikkonen', 'Oikonen', 'Oinonen', 'Oja', 'Ojala', 'Ojamäki', 'Ojanen', 'Ojaniemi', 'Oksala', 'Oksanen', 'Ollikainen', 'Ollila', 'Ollinen', 'Oravainen', 'Oravala', 'Otsamo', 'Outinen', 'Ovaska',
        'Paajanen', 'Paakkanen', 'Paananen', 'Paasikivi', 'Paasilinna', 'Paasonen', 'Paavola', 'Pahajoki', 'Pahkasalo', 'Pajumäki', 'Pajunen', 'Pakarinen', 'Pakkala', 'Pakola', 'Pallas', 'Paloheimo', 'Palola', 'Palomäki', 'Parkkonen', 'Pekkala', 'Pekkarinen', 'Pelkonen', 'Peltomaa', 'Pennanen', 'Pennilä', 'Pentikäinen', 'Penttilä', 'Perniö', 'Pesola', 'Pesonen', 'Peuranen', 'Peuraniemi', 'Pietilä', 'Piippola', 'Piirainen', 'Pikkarainen', 'Pirttijärvi', 'Pirttikangas', 'Pitkämäki', 'Pohtamo', 'Porkkala', 'Poronen', 'Poropudas', 'Puhakainenä', 'Puhakka', 'Pukkila', 'Pulli', 'Puolakka', 'Puuperä', 'Pyykkö', 'Pyykkönen', 'Päivälä', 'Päivärinta', 'Pääkkönen', 'Pöllönen', 'Pöntinen', 'Pöysti',
        'Raappana', 'Raatikainen', 'Raatila', 'Rahka', 'Rahkala', 'Raiskio', 'Raitanen', 'Raittila', 'Rajamäki', 'Ramu', 'Ranta', 'Rantamaa', 'Rapala', 'Rasila', 'Rasmus', 'Rauhala', 'Rauhanen', 'Rautaporras', 'Rautavirta', 'Rautio', 'Rehu', 'Reinikainen', 'Reinikka', 'Rekomaa', 'Repo', 'Repola', 'Riihimäki', 'Riikonen', 'Rimmanen', 'Rinne', 'Rinta', 'Rintamäki', 'Ristilä', 'Ritari', 'Rokko', 'Ronkainen', 'Roponen', 'Ruhanen', 'Rumpunen', 'Runtti', 'Ruohoniemi', 'Ruonala', 'Ruonansuu', 'Ruotsalainen', 'Ruuhonen', 'Ruuskari', 'Ruusula', 'Ruutti', 'Ryhänen', 'Ryti', 'Ryysyläinen', 'Räikkönen', 'Räisänen', 'Räsänen',
        'Saanila', 'Saarela', 'Saarenheimo', 'Saari', 'Saarikivi', 'Saarnio', 'Saarnivaara', 'Saastamoinen', 'Saikkonen', 'Saksala', 'Salenius', 'Salmela', 'Salmelainen', 'Salo', 'Salolainen', 'Salonen', 'Saloranta', 'Samulin', 'Sannala', 'Santanen', 'Saraste', 'Sarasvuo', 'Saukko', 'Savioja', 'Savolainen', 'Selänne', 'Seppelin', 'Seppänen', 'Seppälä', 'Servo', 'Setänen', 'Siekkinen', 'Sievinen', 'Sihvonen', 'Siira', 'Siltonen', 'Sikala', 'Silakka', 'Sillanpää', 'Siltala', 'Silvennoinen', 'Simo', 'Simonen', 'Sinnemäki', 'Sipilä', 'Sipola', 'Sirkesalo', 'Sirviö', 'Raiski', 'Soikkeli', 'Soini', 'Sonninen', 'Soppela', 'Sorajoki', 'Sormunen', 'Sorsa', 'Suhonen', 'Suikkala', 'Summanen', 'Suomela', 'Suominen', 'Suosalo', 'Susiluoto', 'Sutinen', 'Suuronen', 'Suutarinen', 'Suvela', 'Sydänmäki', 'Syrjä', 'Syrjälä', 'Säkkinen', 'Särkkä',
        'Taavettila', 'Taavila', 'Taavitsainen', 'Taipale', 'Takkala', 'Takkula', 'Tamminen', 'Tammisto', 'Tanskanen', 'Tapio', 'Tapola', 'Tarvainen', 'Taskinen', 'Tastula', 'Tauriainen', 'Tenkanen', 'Teppo', 'Tervo', 'Tervonen', 'Teräsniska', 'Tiainen', 'Tiilikainen', 'Timonen', 'Toijala', 'Toikkanen', 'Toivanen', 'Tokkola', 'Tolonen', 'Torkkeli', 'Tuisku', 'Tukiainen', 'Tulkki', 'Tuomela', 'Tuominen', 'Tuomisto', 'Tuppurainen', 'Turpeinen', 'Turunen', 'Tuutti', 'Tynkkynen', 'Typpö', 'Tyrninen', 'Törrö', 'Törrönen',
        'Ukkola', 'Ulvila', 'Unhola', 'Uosukainen', 'Urhonen', 'Uronen', 'Urpalainen', 'Urpilainen', 'Utriainen', 'Uusikari', 'Uusikylä', 'Uusisalmi', 'Uusitalo',
        'Vaara', 'Vahala', 'Vahanen', 'Vahvanen', 'Vainio', 'Valjakka', 'Valo', 'Valtanen', 'Vanhanen', 'Vanhoja', 'Varjus', 'Vartiainen', 'Vasala', 'Vauhkonen', 'Veijonen', 'Veini', 'Vennala', 'Vennamo', 'Vepsäläinen', 'Vesa', 'Vesuri', 'Veteläinen', 'Vierikko', 'Vihtanen', 'Viikate', 'Viinanen', 'Viinikka', 'Vilhola', 'Viljanen', 'Vilkkula', 'Vilpas', 'Virkkula', 'Virkkunen', 'Virolainen', 'Virtala', 'Voutilainen', 'Vuokko', 'Vuorenpää', 'Vuorikoski', 'Vuorinen', 'Vähälä', 'Väisälä', 'Väisänen', 'Välimaa', 'Välioja', 'Väyrynen', 'Väätänen',
        'Wettenranta', 'Wiitanen', 'Wirtanen', 'Wiskari',
        'Ylijälä', 'Yliannala', 'Ylijoki', 'Ylikangas', 'Ylioja', 'Ylitalo', 'Ylppö', 'Yläjoki', 'Yrjänen', 'Yrjänä', 'Yrjölä', 'Yrttiaho', 'Yömaa',
        'Äijälä', 'Ämmälä', 'Änäkkälä', 'Äyräs', 'Äärynen',
        'Översti', 'Öysti', 'Öörni',
    ];

    protected static $titleMale = ['Hra.', 'Tri.'];

    protected static $titleFemale = ['Rva.', 'Nti.', 'Tri.'];

    /**
     * National Personal Identity Number (Henkilötunnus)
     *
     * @see http://www.finlex.fi/fi/laki/ajantasa/2010/20100128
     *
     * @param string $gender Person::GENDER_MALE || Person::GENDER_FEMALE
     *
     * @return string on format DDMMYYCZZZQ, where DDMMYY is the date of birth, C the century sign, ZZZ the individual number and Q the control character (checksum)
     */
    public function personalIdentityNumber(?\DateTime $birthdate = null, $gender = null)
    {
        $checksumCharacters = '0123456789ABCDEFHJKLMNPRSTUVWXY';

        if (!$birthdate) {
            $birthdate = \Faker\Provider\DateTime::dateTimeThisCentury();
        }
        $datePart = $birthdate->format('dmy');

        switch ((int) ($birthdate->format('Y') / 100)) {
            case 18:
                $centurySign = '+';

                break;

            case 19:
                $centurySign = '-';

                break;

            case 20:
                $centurySign = 'A';

                break;

            default:
                throw new \InvalidArgumentException('Year must be between 1800 and 2099 inclusive.');
        }

        $randomDigits = self::numberBetween(0, 89);

        if ($gender && $gender == static::GENDER_MALE) {
            if ($randomDigits === 0) {
                $randomDigits .= static::randomElement([3, 5, 7, 9]);
            } else {
                $randomDigits .= static::randomElement([1, 3, 5, 7, 9]);
            }
        } elseif ($gender && $gender == static::GENDER_FEMALE) {
            if ($randomDigits === 0) {
                $randomDigits .= static::randomElement([2, 4, 6, 8]);
            } else {
                $randomDigits .= static::randomElement([0, 2, 4, 6, 8]);
            }
        } else {
            if ($randomDigits === 0) {
                $randomDigits .= self::numberBetween(2, 9);
            } else {
                $randomDigits .= (string) static::numerify('#');
            }
        }
        $randomDigits = str_pad($randomDigits, 3, '0', STR_PAD_LEFT);

        $checksum = $checksumCharacters[(int) ($datePart . $randomDigits) % strlen($checksumCharacters)];

        return $datePart . $centurySign . $randomDigits . $checksum;
    }
}
