<?php

namespace Faker\Provider\nl_NL;

class Person extends \Faker\Provider\Person
{
    protected static $maleNameFormats = [
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}}',
        '{{title}} {{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}} {{suffix}}',
        '{{title}} {{firstNameMale}} {{lastName}} {{suffix}}',
    ];

    protected static $femaleNameFormats = [
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}}',
        '{{title}} {{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}} {{suffix}}',
        '{{title}} {{firstNameFemale}} {{lastName}} {{suffix}}',
    ];

    protected static $title = [
        'mr.', 'dr.', 'ir.', 'drs', 'bacc.', 'kand.', 'dr.h.c.', 'prof.', 'ds.', 'ing.', 'bc.',
    ];

    protected static $suffix = [
        'BA', 'Bsc', 'LLB', 'LLM', 'MA', 'Msc', 'MPhil', 'D', 'PhD', 'AD', 'B', 'M',
    ];

    protected static $prefix = ["'s", "'t", 'a', 'aan', "aan 't", 'aan de', 'aan den', 'aan der', 'aan het',
        'aan t', 'af', 'al', 'am', 'am de', 'auf', 'auf dem', 'auf den', 'auf der', 'auf ter', 'aus', "aus 'm",
        'aus dem', 'aus den', 'aus der', 'aus m', 'ben', 'bij', "bij 't", 'bij de', 'bij den', 'bij het', 'bij t',
        'bin', 'boven d', "boven d'", 'd', "d'", 'da', 'dal', 'dal’', 'dalla', 'das', 'de', 'de die', 'de die le',
        'de l', 'de l’', 'de la', 'de las', 'de le', 'de van der', 'deca', 'degli', 'dei', 'del', 'della', 'den',
        'der', 'des', 'di', 'die le', 'do', 'don', 'dos', 'du', 'el', 'het', 'i', 'im', 'in', "in 't", 'in de', 'in den',
        'in der', 'in het', 'in t', 'l', 'l’', 'la', 'las', 'le', 'les', 'lo', 'los', 'of', 'onder', "onder 't",
        'onder de', 'onder den', 'onder het', 'onder t', 'op', "op 't", 'op de', 'op den', 'op der', 'op gen', 'op het',
        'op t', 'op ten', 'over', "over 't", 'over de', 'over den', 'over het', 'over t', 's', "s'", 't', 'te', 'ten',
        'ter', 'tho', 'thoe', 'thor', 'to', 'toe', 'tot', 'uijt', "uijt 't", 'uijt de', 'uijt den', 'uijt te de',
        'uijt ten', 'uit', "uit 't", 'uit de', 'uit den', 'uit het', 'uit t', 'uit te de', 'uit ten', 'unter', 'van',
        "van 't", 'van De', 'van de', 'van de l', "van de l'", 'van den', 'van der', 'van gen', 'van het', 'van la',
        'van t', 'van ter', 'van van de', 'ver', 'vom', 'von', "von 't", 'von dem', 'von den', 'von der', 'von t', 'voor',
        "voor 't", 'voor de', 'voor den', "voor in 't", 'voor in t', 'vor', 'vor der', 'zu', 'zum', 'zur',
    ];

    protected static $commonDutchLastNames = [
        'de Jong', 'Jansen', 'de Vries', 'van de Berg', 'van den Berg', 'van der Berg', 'van Dijk', 'Bakker', 'Janssen',
        'Visser', 'Smit', 'Meijer', 'Meyer', 'de Boer', 'Mulder', 'de Groot', 'Bos', 'Vos', 'Peters', 'Hendriks',
        'van Leeuwen', 'Dekker', 'Brouwer', 'de Wit', 'Dijkstra', 'Smits', 'de Graaf', 'van der Meer', 'van der Linden',
        'Kok', 'Jacobs', 'de Haan', 'Vermeulen', 'van den Heuvel', 'van de Veen', 'van der Veen', 'van den Broek',
        'de Bruijn', 'de Bruyn', 'de Bruin', 'van der Heijden', 'van der Heyden', 'Schouten', 'van Beek', 'Willems',
        'van Vliet', 'van de Ven', 'van der Ven', 'Hoekstra', 'Maas', 'Verhoeven', 'Koster', 'van Dam', 'van de Wal',
        'van der Wal', 'Prins', 'Blom', 'Huisman', 'Peeters', 'de Jonge', 'Kuipers', 'van Veen', 'Post', 'Kuiper',
        'Veenstra', 'Kramer', 'van de Brink', 'van den Brink', 'Scholten', 'van Wijk', 'Postma', 'Martens', 'Vink',
        'de Ruiter', 'Timmermans', 'Groen', 'Gerritsen', 'Jonker', 'van Loon', 'Boer', 'van de Velde', 'van den Velde',
        'van der Velde', 'van de Velden', 'van den Velden', 'van der Velden', 'Willemsen', 'Smeets', 'de Lange',
        'de Vos', 'Bosch', 'van Dongen', 'Schipper', 'de Koning', 'van der Laan', 'Koning', 'Driessen', 'van Doorn',
        'Hermans', 'Evers', 'van den Bosch', 'van der Meulen', 'Hofman', 'Bosman', 'Wolters', 'Sanders',
        'van der Horst', 'Mol', 'Kuijpers', 'Molenaar', 'van de Pol', 'van den Pol', 'van der Pol', 'de Leeuw',
        'Verbeek',
    ];

    protected static $dutchLastNames = [
        'Aalts', 'Aarden', 'Aarts', 'Adelaar', 'Adriaansen', 'Adriaensdr', 'Adriaense', 'Adryaens', 'Aeije',
        'Aelftrud van Wessex', 'Aertsz', 'van Alenburg', 'van Allemanië', 'Alpaidis', 'Amalrada', 'van Amstel',
        'Ansems', 'Appelman', 'Arens', 'Arent', 'Ariens', 'Ariens Ansems', 'van Arkel', 'Arnold', 'van Arnsberg',
        'Arts', 'Aschman', 'van den Assem', 'van Asten', 'van der Avoirt', 'Bökenkamp', 'van Baalen', 'Backer',
        'de Backer', 'Barents', 'Bartels', 'Bastiaanse', 'Bastiaense', 'Bave', 'van Beaumont', 'Becht',
        'van Beeck Beeckmans', 'van Beeck', 'van Beek', 'Beekman', 'de Beer', 'Beernink', 'van Beieren', 'Beijring',
        'Bekbergen', 'Bellemans', 'Belpere', 'van Bentheim', 'Beourgeois', 'Berends', 'Berendse', 'van den Berg',
        'van Bergen', 'van den Bergh', 'van Berkel', 'van Berkum', 'Bernaards', 'van Bernicia', 'Bertho', 'Bezemer',
        'Bierstraten', 'van de Biesenbos', 'van de Biezenbos', 'Bijlsma', 'Billung', 'Blaak', 'Blees', 'Bleijenberg',
        'Blewanus', 'Bloemendaal', 'Blokland', 'Blom', 'Blonk', 'de Bock', 'Boddaugh', 'Boer', 'de Boer', 'Boers',
        'Boeser', 'Boetet', 'Bolkesteijn', 'de Bont', 'Booden', 'Boogaerts', 'Borman', 'Bos', 'Bosch', 'Boudewijns',
        'Bouhuizen', 'van Boulogne', 'Bourgondië, van', 'Bouthoorn', 'Bouwhuisen', 'van Boven', 'van Bovene',
        'van Bovenen', 'van den Brand', 'Brandon', 'Brands', 'Brandt', 'van Brenen', 'Bresé', 'Bresse', 'van Breugel',
        'Breugelensis', 'van Breukeleveen', 'van Breukelveen', 'le Briel', 'Briere', 'Brievingh', 'van den Brink',
        'van der Brink', 'Brisee', 'Brizee', 'Broeckx', 'Broeders', 'Broek', 'van den Broek', 'Broekhoven', 'Broeshart',
        'Bronder', 'Brouwer', 'van Bruchem', 'Bruggeman', 'Brugman', 'de Bruijn', 'Bruijne van der Veen', 'de Bruin',
        'Brumleve', 'van Brunswijk', 'Bruynzeels', 'Bud', 'Buijs', 'van Bunschoten', 'Butselaar', 'van Buuren',
        'den Buytelaar', 'Cadefau', 'Cammel', 'Cant', 'Carnotte', 'Charon', 'Chevresson', 'Chotzen', 'Chrodtrud',
        'Claassen', 'Claesdr', 'Claesner', 'van Clootwijck', 'Coenen', 'Coolen', 'Coret', 'Coret-Coredo',
        'Coreth von und zu Coredo und Starkenberg', 'Cornelisse', 'Cornelissen', 'Cornelisz', 'van den Corput',
        'Corstiaens', 'Cosman', 'van de Coterlet', 'Courtier', 'van Cuijck', 'van Daal', 'Dachgelder', 'Dachgeldt',
        'Dachgelt', 'van Dagsburg', 'van Dalem', 'van Dam', 'van de Darnau', 'David', 'Dekker', 'Demmendaal',
        'Dennenberg', 'die Bont', 'Diesbergen', 'van Dijk', 'Dijkman', 'van Dillen', 'Dircken', 'Dirksen', 'Dirven',
        'Doesburg', 'van Dokkum', 'van Dommelen', 'van Dongen', 'van Dooren', 'Doorhof', 'Doornhem', 'Dorsman',
        'Doyle', 'Draaisma', 'van Drenthe', 'Dries', 'Drysdale', 'Dubois', 'van Duivenvoorde', 'Duivenvoorden',
        'van Duvenvoirde', 'van Duyvenvoorde', 'die Bont', 'die Pelser', 'die Witte', 'van Eck', 'Eckhardt', 'Eelman',
        'Eerden', 'van de Eerenbeemt', 'van den Eerenbeemt', 'van Egisheim', 'Ehlert', 'Eijkelboom', 'van den Eijssel',
        'Elberts', 'Elbertse', 'Ellis', 'Elsemulder', 'Elsenaar', 'van de Elzas', 'van Embden', 'van Emmelen', 'Emmen',
        'van Engeland', 'van Engelen', 'Engels', 'van Enschot', 'Erhout', 'Ernst', 'van \'t Erve', 'van Es', 'van Este',
        'Estey', 'van Evelingen', 'Everde', 'Everts', 'Fechant', 'Feenstra', 'Feltzer', 'Ferran', 'Fiere',
        'van der Flaas', 'de la Fleche', 'Flink', 'le Floch', 'van Formbach', 'Fortuyn', 'François', 'Françoise',
        'Frankhuizen', 'Fredriks', 'Fremie', 'Frerichs', 'Freshour', 'Friehus', 'Furda', 'Galenzone', 'Galijn',
        'le Gallen', 'Garret', 'van Gastel', 'van Geenen', 'Geerling', 'Geerts', 'Geertsen', 'van Geest', 'van Geffen',
        'Geldens', 'van Gelder', 'Gellemeyer', 'Gemen', 'van Gemert', 'Geneart', 'Genefaas', 'van Gent',
        'Gepa van Bourgondië', 'Gerrits', 'Gerritse', 'Gervais', 'Ghoerle', 'van Ghoerle', 'van Gils', 'van Ginkel',
        'van Ginneke', 'Giselmeyer', 'Glasses', 'Gnodde', 'Goderts', 'Godfrey van Alemannië', 'Goedhart', 'van Goerle',
        'van Gorp', 'Goudriaan', 'Govarts', 'Goyaerts van Waderle', 'de Graaf', 'de Gratie', '\'s Gravensande',
        'van de Greef', 'Greij', 'van Grinsven', 'Groenendaal', 'Groenestein', 'Grondel', 'van Grondelle', 'de Groot',
        'Groote', 'de Grote', 'Gruijl', 'de Gruijl', 'de Gruijter', 'de Gruil', 'de Grunt', 'de Gruson', 'le Guellec',
        'Guit', 'le Gulcher', 'Höning', 'Haack', 'den Haag', 'van Haarlem', 'de Haas', 'van Haeften', 'Haengreve',
        'van Hagen', 'Hagendoorn', 'Hak', 'Hakker', 'van Ham', 'van Hamaland', 'Haneberg', 'Hanegraaff', 'Haring',
        'Haselaar', 'van Haspengouw', 'Hazenveld', 'de Heer', 'Heere', 'Heerkens',
        'Heerschop', 'Hehl', 'van der Heiden', 'van der Heijden', 'Heijman', 'Heijmans', 'Heijmen', 'Heinrichs',
        'Hekker', 'Hellevoort', 'Helmerhorst', 'van Hemert', 'Hemma', 'Hendricks', 'Hendriks',
        'Hendrikse', 'van Henegouwen',  'van den Henst', 'Heribert van Laon', "d' Heripon",
        'Hermans', 'van Herstal', 'van Heusden', 'Hexspoor', 'Heymans', 'Heyne', 'Hoedemakers', 'van den Hoek', 'Hoeks',
        'Hoelen', 'Hoes', 'van Hoevel en van Zwindrecht', 'van der Hoeven', 'van Holland', 'Hollander', 'Holthuis',
        'Hondeveld', 'Honing', 'de Hoog', 'Hoogers', 'de Hoogh', 'Hoppenbrouwer', 'Horrocks', 'van der Horst',
        'van Hostaden', 'Houdijk', "van 't Houteveen", 'Huberts', 'Huel', 'Huijben', 'Huijbrechts', 'Huijs',
        'Huijzing', 'Huisman', 'Huls', 'Hulshouts', 'Hulskes', 'Hulst', 'van Hulten', 'Huurdeman', 'van het Heerenveen',
        'Jaceps', 'Jacobi', 'Jacobs', 'Jacquot', 'de Jager', 'Jans', 'Jansdr', 'Janse', 'Jansen', 'Jansen', 'Jansse',
        'Janssen', 'Janssens', 'Jasper dr', 'Jdotte', 'Jeggij', 'Jekel', 'Jerusalem', 'Jochems',
        'Jones', 'de Jong', 'Jonkman', 'Joosten', 'Jorlink', 'Jorissen', 'van Jumiège', 'Jurrijens', 'Köster',
        'van der Kaay', 'de Kale', 'Kallen', 'Kalman', 'Kamp', 'Kamper', 'Karels', 'Kas', 'van Kasteelen', 'Kathagen',
        'Keijser', 'de Keijser', 'Keijzer', 'de Keijzer', 'Keltenie', 'van Kempen', 'Kerkhof', 'Ketel', 'Ketting',
        'der Kijnder', 'van der Kint', 'Kirpenstein', 'Kisman', 'van Klaarwater', 'van de Klashorst', 'Kleibrink',
        'Kleijse', 'Klein', 'van der Klein', 'Klerks', 'Kleybrink', 'van der Klijn', 'Klomp Jan', 'Kloppert', 'Knoers',
        'Knuf', 'Koeman', 'Kof', 'Kok', 'de Kok', 'Kolen', 'Kolster', 'de Koning', 'Konings', 'van de Kooij', 'Koret',
        'Korsman', 'Korstman', 'Kort', 'de Korte', 'Kortman', 'Kosten', 'Koster', 'Krabbe', 'Kremer', 'Kriens',
        'Kronenberg', 'Kruns', 'van Kuijc van Malsen', 'van Kuijc', 'Kuijpers', 'Kuilenburg', 'Kuit', 'Kunen',
        'van Kusen', 'Kwaadland', 'van Laar', 'van der Laar', 'van Laarhoven', 'van der Laarse', 'Labado', 'Laffray',
        'Lafleur', 'Lage', 'Lagerweij', 'Lambers', 'Lambregt', 'Lamore', 'Lamotte', 'van Landen', 'Langevoort',
        'Lankle', 'Lansink', 'van Laon', 'Lathrope', 'Latier', 'Le Grand', 'Le Marec', 'van der Lede', 'van der Leek',
        'van de Leemput', 'Leene', 'van Leeuwen', 'Leguit', 'Lelijveld', 'Lemmens', 'Lensen', 'Lether', 'van Leuven',
        'Levesque', 'van Liendert', 'Lieshout', 'Ligtvoet', 'Lijn', 'van Limburg', 'Lind', 'van der Linden',
        'Linschoten', 'Lips', 'Loep', 'Lommert', 'Lonen', 'van der Loo', 'van Loon', 'Loreal', 'Lorreijn', 'Louws',
        'Luboch', 'le Luc', 'Lucas', 'van Lucel', 'van Luin', 'van Luinenburg', 'Luitgardis van Neustrië', 'Luster',
        'Lutterveld', 'van Luxemburg', 'van Luyssel', 'van Maaren', 'Maas', 'van Maasgouw', 'Maaswinkel',
        'van der Maath', 'van der Maes', 'Mahieu', 'Mallien', 'de Man', 'Mangel', 'Manne', 'Mansveld', 'Mansvelt',
        'Marceron', 'Marchal', 'Marchand', 'de Marduras', 'van Mare', 'Martel', 'Martens', 'Massa', 'van der Mast',
        'le Matelot', 'Mater', 'Mathieu', 'Mathol', 'Mathurin', 'Matthews', 'Meeres', 'Meeusen', 'Meijer', 'Meis',
        'Melet', 'Mens', 'Mercks', 'Merckx', 'Merkx', 'van Metz', 'Meyer', 'Michiels', 'Michielsen', 'Middelkoop',
        'Mijsberg', 'van Mil', 'Miltenburg', 'Miner', 'van Mispelen', 'Moenen', 'Moensendijk', 'Moet', 'Mol', 'de Mol',
        'Molegraaf', 'Molen', 'Momberg', 'van Mook', 'Mosley', 'Mudden', 'Muijs', 'Mulder', 'Mulders', 'Muller',
        'van Munster', 'van Nederlotharingen', 'Nedermeijer', 'Nek', 'van Nes', 'Neuteboom', 'Neuzerling', 'Niermann',
        'van den Nieuwenhuijsen', 'Nieuwstraten', 'Nihoe', 'Nijman', 'de Nijs', 'van Nimwegen', 'Nollee',
        'van Noordeloos', 'Noordijk', 'van de Noordmark', 'van Noort', 'van der Noot', 'van Northeim', 'van Nus',
        'van den Nuwenhijsen', 'van den Nuwenhuijzen', 'van den Nuwenhuysen', 'van den Nyeuwenhuysen', 'van Ochten',
        'Oda', 'Oemencs', 'Oennen', 'van den Oever', 'van Oirschot', 'van Olst', 'Olthof', 'Olykan', 'van Ommeren',
        'Ooms', 'van Ooste', 'van Oosten', 'van Oostendorp', 'Oosterhek', 'Oosterhout', 'Oostveen', 'van Ooyen',
        'Opmans', 'van Opper-Lotharingen', 'van Orleans', 'Osterhoudt', 'Otte', 'Otto', 'Oude Heer', 'van Oudewater',
        'Ouwel', 'Ouwerkerk', 'Overdijk', 'Overeem', 'Oversteeg', 'Paillet', 'Palman', 'van Parijs', 'Pasman',
        'Passchiers', 'Pastoors', 'de Pauw', 'Pauwels', 'van de Pavert', 'Perck', 'Perkins', 'Peronne', 'Perrono',
        'Persijn', 'Peterse', 'Phillipsen', 'Pierson', 'Pieters', 'Pieters van der Maes', 'Pison', 'de Plantard',
        'van de Plas', 'van der Plas', 'van der Ploeg', 'van der Pluijm', 'Poncelet', 'Ponci', 'Pons', 'van Poppel',
        'Post', 'Potters', 'van der Pouw', 'van Praagh', 'Pratt', 'Prinsen', 'Puig', 'Rackham', 'Rademaker', 'Ramaker',
        'Recer', 'Recers', 'de Reede', 'Rehorst', 'Reijers', 'Reimes', 'Rek', 'Remmers', 'van Rheineck', 'Ridder',
        'Riem', 'van Riet', "van 't Riet", 'Rietveld', 'Rijcken', 'Rijks', 'Rijn', 'van Rijnsbergen', 'Rijntjes',
        'van Rijthoven', 'Rippey', 'Risma', 'Robbrechts Bruijne', 'Roessink', 'van Roijen', 'Romijn', 'de Roo',
        'Roodesteijn', 'van Rooij', 'Room', 'de Roos', 'Roose', 'Roosenboom', 'van Rossum', 'Rotteveel', 'Roukes',
        'Rousselet', 'Rouwenhorst', 'Rouwhorst', 'Rubben', 'Ruijs', 'Rutten', 'van Saksen', 'Salet', 'van Salm',
        'van Salmen', 'Sam', 'van der Sande', 'Sanders', 'van Santen', 'Sarneel', 'Sas', 'Saxo', 'Scardino', 'Schagen',
        'Schakelaar', 'Scharroo', 'Schatteleijn', 'Scheer', 'Scheffers', 'Schellekens', 'Schelvis', 'Schenk',
        'Schenkel', 'Scherms', 'van Schevinghuizen', 'Schiffer', 'Schilt', 'Schokman', 'Scholten', 'Schotte', 'Schrant',
        'Schrik', 'Schroeff', 'van der Schuijt', 'Schulten', 'Schuurmans', 'Schuylenborch', 'Schwartsbach',
        'van Schweinfurt', 'Scuylenborchs', 'Segerszoen', 'Serra', 'Sestig', 'Shupe', 'Simonis', 'Simons', 'Sire',
        'Sitters', 'Slaetsdochter', 'Slagmolen', 'Slingerland', 'van der Sloot', 'van der Smeede', 'Smit', 'de Smit',
        'Smith', 'Smits', 'van Soest', 'Soos', 'Spaan', 'van der Spaendonc', 'van der Spaendonck', 'Spanhaak',
        'Speijer', 'Spier', 'Spies', 'Spiker', 'Spreeuw', 'van Spreeuwel', 'van Spreuwel', 'Sprong', 'Spruit', 'Spruyt',
        'van der Stael de Jonge', 'van der Stael', 'Stamrood', 'Stange', 'van der Steen', 'Steenbakkers', 'Steenbeek',
        'Steinmeiern', 'Sterkman', 'Stettyn', 'Stichter', 'Stinis', 'Stoffel', 'Stoffelsz', 'Stook', 'van Straaten',
        'van Stralen', 'van der Strigt', 'de Strigter', 'Strijker', 'Strik', 'Stuivenberg', 'Suijker', 'van Suinvorde',
        'van Susa', 'de Swart', 'Symons', 'Takkelenburg', 'Tammerijn', 'Tamsma', 'Terry', 'den Teuling', 'Teunissen',
        'Texier', 'Thatcher', 'The Elder', 'Thomas', 'Thout', 'Tielemans', 'Tillmanno', 'Timmerman', 'Timmermans',
        'Tins', 'Tirie', 'Totwiller', 'van Tours', 'van Tuijl', 'Tuithof', 'Uittenbosch', 'Ulrich',
        'Uphaus', 'Uphuis', 'Uphus', 'VI', 'Vaessen', 'Vallenduuk', 'Van Bragt', 'Vandenbergh',
        'Vastenhouw', 'Veenendaal', 'Vegt', 'van der Veiver', 'Velderman', 'van Velthoven', 'Veltman', 'van Velzen',
        'van de Ven', 'van Venrooy', 'Verbeeck', 'Verbeek', 'Verboom', 'Verbruggen', 'Verda', 'van Verdun', 'Vergeer',
        'Verhaar', 'Verhagen', 'Verharen', 'Verheij', 'Verheuvel', 'Verhoeven', 'Verkade', 'van Vermandois',
        'Vermeulen', 'Verschuere', 'Verschut', 'Versluijs', 'Vertoor', 'Vertooren', 'Vervoort', 'Verwoert', 'Vial',
        'Vierdag', 'Vignon', 'van Vlaanderen', 'Volcke', 'van Voorhout', 'van Voorst', 'Voortman', 'Vos', 'Vrancken',
        'de Vries', 'de Vroege', 'de Vrome', 'ter Waarbeek', 'Waardeloo', 'van Waas', 'Wagenvoort', 'van Wallaert',
        'Walsteijn', 'Walter', 'van Wassenaar', 'van de Water', 'Weeldenburg', 'Weerdenburg',
        'Weijland', 'Weijters', 'van Wel', "van 't Wel", 'Welf', 'Wendt', 'Wensen', 'de Werd', 'Werdes',
        'van Wessex', 'Westerbeek', 'Westerburg', 'Westermann',
        'van Westfalen', 'van de Weterink', 'Wever', 'Weyland', 'Weylant', 'van Wickerode', 'van de Wiel', 'Wigman',
        'Wijland', 'van Wijland', 'Wilcken', 'Wildschut', 'Willems', 'Willems van Lier', 'Willemsen', 'Wilmont',
        'Wilson', 'Winnrich', 'Winters', 'Wipstrik', 'de Wit', 'van den Wittenboer', 'Wolffel',
        'Wolfswinkel', 'Wolters', 'Wolzak', 'Wooning', 'Woudenberg', 'Wouters', 'Wouters van Eijndhoven', 'Woutersz',
        'Wright', 'Wunderink', 'Wutke', 'Zaal', 'Zeemans', 'Zeldenrust', 'Zevenboom', 'van der Zijl', 'Zijlemans',
        'Zijlmans', 'Zuidweg', 'Zuijdveld', 'van Zwaben', 'Zwart', 'Zwijsen',
    ];

    protected static $commonForeignLastNames = [
        'Yilmaz', 'Nguyen', 'Ali', 'Mohamed', 'Yildiz', 'Yildirim', 'Öztürk', 'Demir', 'Hassan', 'Şahin', 'Aydin',
        'Özdemir', 'Çelik', 'Kiliç', 'Arslan', 'Dogan', 'Tran', 'Abdi', 'Aslan', 'Hussein', 'Koç', 'Özcan', 'Hussain',
        'Kurt', 'Pham', 'Autar', 'Polat', 'Korkmaz', 'Le', 'Çetin', 'Koçak', 'Said', 'Ünal', 'Bulut', 'Ramautar',
        'Simsek', 'Ismail', 'Ramcharan', 'Mahabier', 'Kalloe', 'Zhang', 'Özkan', 'Sahin', 'Farah', 'Mohammad', 'Yüksel',
        'Demirci', 'Kanhai', 'Çakir', 'Karaca', 'Can', 'Keskin', 'dos Santos', 'Uzun', 'Winklaar', 'Sardjoe', 'Lopes',
        'Erdoğan', 'Loukili', 'Tekin', 'Ramlal', 'Yavuz', 'Sambo', 'Coşkun', 'Yalçin', 'Biharie', 'Köse', 'Dogan',
        'Aktaş', 'Avci', 'Uysal', 'Badal', 'Bozkurt', 'Ramos', 'Moussaoui', 'Akin', 'Özer', 'Malik', 'Sital',
        'El Idrissi', 'Aziz', 'Demirel', 'Henriquez', 'Janga', 'Hooi', 'Geerman', 'Güler', 'Aksoy', 'Soekhoe', 'Turan',
        'Güneş', 'Narain', 'Ahmadi', 'Esajas', 'Zhou', 'Tahiri', 'Çiçek', 'Mohan', 'Cicilia', 'Mangal',
    ];

    protected static $longLastNames = [
        'Albinus genaamd Weiss von Weissenlöw', "van Bol'es Rijnbende", 'Doris Bin Sijlvanus',
        'Douglas tot Springwoodpark', 'Dubbeldemuts van der Sluys', 'Duhme auf der Heide sive Heydahrens',
        'Elsjan of Wipper', "de la Fontaine und d'Harnoncourt Unverzagt", 'Franse Storm', 'von Frijtag Drabbe Künzel',
        'Gansneb genaamd Tengnagel tot Bonkenhave', 'Grinwis Plaat Stuitjes', "von Heinrich d'Omóróvicza",
        'van Hugenpoth tot den Berenclauw', 'Jansz Muskus te Pasque', 'Kijk in de Vegte', 'Kleine Pier', 'Koning Knol',
        'Martena van Burmania Vegilin van Claerbergen', 'Paspoort van Grijpskerke en Poppendamme',
        'de Pruyssenaere de la Woestijne', 'Rahajoe genaamd en geschreven ten Kate', 'de la Rive Box',
        'Spiegelmaker Spanjaard', 'Spring in ‘t Veld', 'Vos Specht', 'Vroeg in de Wei', 'Zowran von Ranzow',
        'Zuérius Boxhorn van Miggrode', 'Zum Vörde Sive Vörding',
    ];

    protected static $firstNameFemale = [
        'Emma', 'Sophie', 'Julia', 'Anna', 'Lisa', 'Isa', 'Eva', 'Saar', 'Lotte', 'Tess', 'Lynn', 'Fleur', 'Sara',
        'Lieke', 'Noa', 'Fenna', 'Sarah', 'Mila', 'Sanne', 'Roos', 'Elin', 'Zoë', 'Evi', 'Maud', 'Jasmijn', 'Femke',
        'Nina', 'Anne', 'Noor', 'Amy', 'Sofie', 'Olivia', 'Feline', 'Liv', 'Esmee', 'Nora', 'Iris', 'Lina', 'Luna',
        'Naomi', 'Elise', 'Amber', 'Yara', 'Charlotte', 'Lana', 'Milou', 'Isabel', 'Isabella', 'Eline', 'Floor', 'Lara',
        'Anouk', 'Fenne', 'Vera', 'Nikki', 'Loïs', 'Liz', 'Maria', 'Tessa', 'Jill', 'Laura', 'Puck', 'Sophia', 'Hannah',
        'Evy', 'Lizzy', 'Fay', 'Veerle', 'Bente', 'Nienke', 'Linde', 'Romy', 'Senna', 'Isis', 'Bo', 'Sterre', 'Benthe',
        'Lauren', 'Julie', 'Norah', 'Merel', 'Ilse', 'Marit', 'Nova', 'Rosalie', 'Lena', 'Fiene', 'Lise', 'Demi',
        'Johanna', 'Suze', 'Vajèn', 'Ella', 'Mirthe', 'Lola', 'Indy', 'Emily', 'Kiki', 'Sofia', 'Isabelle', 'Myrthe',
        'Yfke', 'Jade', 'Cato', 'Lize', 'Danique', 'Guusje', 'Elisa', 'Esmée', 'Elena', 'Rosa', 'Suus', 'Fien', 'Britt',
        'Quinty', 'Robin', 'Hanna', 'Elisabeth', 'Silke', 'Pien', 'Amira', 'Elize', 'Faye', 'Hailey', 'Madelief', 'Aya',
        'Louise', 'Meike', 'Elif', 'Jaylinn', 'Daphne', 'Lily', 'Liza', 'Juul', 'Lieve', 'Valerie', 'Josephine', 'Mara',
        'Sam', 'Kate', 'Jolie', 'Phileine', 'Ise', 'Amélie', 'Cornelia', 'Dewi', 'Livia', 'Stella', 'Mia', 'Noortje',
        'Ashley', 'Janne', 'Alicia', 'Ivy', 'Janna', 'Nynke', 'Kaylee', 'Lisanne', 'Azra', 'Maartje', 'Megan', 'Jet',
        'Victoria', 'Kayleigh', 'Floortje', 'Chloë', 'Pleun', 'Alyssa', 'Jennifer', 'Mare', 'Renske', 'Aimée',
        'Juliette', 'Kim', 'Fem', 'Mette', 'Dina', 'Tara', 'Michelle', 'Esther', 'Jenna', 'Lot', 'Elizabeth', 'Merle',
        'Dana', 'Eliza', 'Karlijn', 'Bibi', 'Melissa', 'Yasmin', 'Annabel', 'Carlijn', 'Imke', 'Evie', 'Fabiënne',
        'Linn', 'Zeynep', 'Kyra', 'Aylin', 'Zara', 'Lois', 'Zoey', 'Ceylin', 'Chloé', 'Joëlle', 'Joy', 'Noëlle',
        'Féline', 'Yasmine', 'Evelien', 'Ize', 'Mirte', 'Ninthe', 'Ecrin', 'Kyara', 'Maya', 'Nisa', 'Leah', 'Maryam',
        'Angelina', 'Catharina', 'Lindsey', 'Loes', 'Yinthe', 'Sienna', 'Adriana', 'Esila', 'Jente', 'Lizz', 'Lucy',
        'Nadine', 'Selina', 'Fatima', 'Maaike', 'Aaliyah', 'Amina', 'Inaya', 'Selena', 'Frederique', 'Pippa', 'Puk',
        'Sylvie', 'Annemijn', 'Helena', 'Jayda', 'Nadia', 'Amelia', 'Jinthe', 'Jolijn', 'Maja', 'Tirza',
    ];

    protected static $firstNameMale = [
        'Daan', 'Bram', 'Sem', 'Lucas', 'Milan', 'Levi', 'Luuk', 'Thijs', 'Jayden', 'Tim', 'Finn', 'Stijn', 'Thomas',
        'Lars', 'Ruben', 'Jesse', 'Noah', 'Julian', 'Max', 'Liam', 'Mees', 'Sam', 'Sven', 'Gijs', 'Luca', 'Teun',
        'Tijn', 'Siem', 'Mats', 'Jens', 'Benjamin', 'Adam', 'Ryan', 'Jan', 'Floris', 'David', 'Olivier', 'Cas', 'Tygo',
        'Dylan', 'Ties', 'Tom', 'Pepijn', 'Daniël', 'Hugo', 'Thijmen', 'Dean', 'Boaz', 'Jasper', 'Nick', 'Willem',
        'Roan', 'Dex', 'Niels', 'Guus', 'Stan', 'Koen', 'Mohamed', 'Joep', 'Johannes', 'Jurre', 'Pim', 'Niek', 'Robin',
        'Bas', 'Rayan', 'Damian', 'Jelle', 'Noud', 'Pieter', 'Vince', 'Dani', 'Joris', 'Jason', 'Timo', 'Mick',
        'Quinten', 'Joshua', 'Simon', 'Tobias', 'Kyan', 'Hidde', 'Mohammed', 'Jack', 'Quinn', 'Rens', 'Samuel',
        'Alexander', 'Hendrik', 'Xavi', 'Joey', 'Fabian', 'Justin', 'Keano', 'Cornelis', 'Fedde', 'Casper', 'Morris',
        'Mike', 'Nathan', 'Jacob', 'Mika', 'Owen', 'Abel', 'Emir', 'Sepp', 'Twan', 'Aiden', 'Jonathan', 'Muhammed',
        'Job', 'Mason', 'Stef', 'Chris', 'Gerrit', 'Jesper', 'Lukas', 'Valentijn', 'Melle', 'Wessel', 'Jip', 'Luc',
        'Rick', 'Sil', 'Loek', 'Dylano', 'Florian', 'Kevin', 'Jort', 'Julius', 'Daniel', 'Maarten', 'Matthijs', 'Jamie',
        'Jelte', 'Tycho', 'Amir', 'Boris', 'Thijn', 'Sep', 'Wout', 'Sjoerd', 'Joël', 'Aron', 'Bart', 'James', 'Kai',
        'Lorenzo', 'Raf', 'Lenn', 'Marijn', 'Sebastiaan', 'Senn', 'Jim', 'Brent', 'Rafael', 'Tijs', 'Imran', 'Nout',
        'Thom', 'Aaron', 'Dirk', 'Oscar', 'Jay', 'Ravi', 'Ali', 'Sami', 'Kian', 'Wouter', 'Giovanni', 'Ian', 'Laurens',
        'Leon', 'Milo', 'Kay', 'Alex', 'Amin', 'Jayson', 'Berat', 'Jules', 'Sander', 'Seth', 'Ben', 'Jonas', 'Jordy',
        'Mathijs', 'Colin', 'Tijmen', 'Marinus', 'Wesley', 'Yusuf', 'Maurits', 'Bjorn', 'Bryan', 'Joost', 'Riley',
        'Victor', 'Felix', 'Ibrahim', 'Luka', 'Bastiaan', 'Hamza', 'Mark', 'Arthur', 'Bradley', 'Dave', 'Rowan',
        'Collin', 'Luke', 'Merijn', 'Vigo', 'Beau', 'Bilal', 'Jorn', 'Vincent', 'Matthias', 'Ayden', 'Maxim', 'Yassin',
        'Dion', 'Jake', 'Kyano', 'Kick', 'Mustafa', 'Michael', 'Youssef', 'Elias', 'Naud', 'Senna', 'Brian', 'Jari',
        'Mehmet', 'Micha', 'Stefan', 'Arie', 'Duuk', 'Zakaria', 'Ayoub', 'Faas', 'Olaf', 'Tristan', 'Mads', 'Berend',
        'Mart', 'Sten', 'Ivan', 'Philip', 'Giel', 'Lex', 'Rik', 'Tyler',
    ];

    /**
     * @example 'Doe'
     */
    public function lastName()
    {
        $determinator = self::numberBetween(0, 25);

        if ($determinator === 0) {
            $lastName = static::randomElement(static::$longLastNames);
        } elseif ($determinator <= 10) {
            $lastName = static::randomElement(static::$commonDutchLastNames);
        } elseif ($determinator <= 15) {
            $lastName = static::randomElement(static::$commonForeignLastNames);
        } else {
            $lastName = static::randomElement(static::$dutchLastNames);
        }

        return $lastName;
    }

    public function title($gender = null)
    {
        return static::randomElement(static::$title);
    }

    /**
     * replaced by specific unisex dutch title
     */
    public static function titleMale()
    {
        return static::randomElement(static::$title);
    }

    /**
     * replaced by specific unisex dutch title
     */
    public static function titleFemale()
    {
        return static::randomElement(static::$title);
    }

    /**
     * @example 'BA'
     */
    public static function suffix()
    {
        return static::randomElement(static::$suffix);
    }

    /**
     * @example 'van der'
     */
    public static function prefix()
    {
        return static::randomElement(static::$prefix);
    }

    /**
     * @see https://nl.wikipedia.org/wiki/Burgerservicenummer#11-proef
     *
     * @return string
     */
    public function idNumber()
    {
        $nr = [];
        $nr[] = 0;

        while (count($nr) < 8) {
            $nr[] = static::randomDigit();
        }
        $nr[] = self::numberBetween(0, 6);

        if ($nr[7] == 0 && $nr[8] == 0) {
            $nr[7] = 0;
        }

        $bsn = (9 * $nr[8]) + (8 * $nr[7]) + (7 * $nr[6]) + (6 * $nr[5]) + (5 * $nr[4]) + (4 * $nr[3]) + (3 * $nr[2]) + (2 * $nr[1]);
        $nr[0] = floor($bsn - floor($bsn / 11) * 11);

        if ($nr[0] > 9) {
            if ($nr[1] > 0) {
                $nr[0] = 8;
                --$nr[1];
            } else {
                $nr[0] = 1;
                ++$nr[1];
            }
        }

        return implode('', array_reverse($nr));
    }
}
