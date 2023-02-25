<?php

namespace Faker\Provider\sv_SE;

class Address extends \Faker\Provider\Address
{
    protected static $buildingNumber = ['%###', '%##', '%#', '%#?', '%', '%?'];

    protected static $streetPrefix = [
        'Stor', 'Små', 'Lill', 'Sjö', 'Kungs', 'Drottning', 'Hamn', 'Brunns', 'Linné', 'Vasa', 'Ring', 'Freds',
    ];

    protected static $streetSuffix = [
        'vägen', 'gatan', 'gränd', 'stigen', 'backen', 'liden',
    ];

    protected static $streetSuffixWord = [
        'Allé', 'Gata', 'Väg', 'Backe',
    ];

    protected static $postcode = ['%####', '%## ##'];

    /**
     * @var array Swedish city names
     *
     * @see http://sv.wikipedia.org/wiki/Lista_%C3%B6ver_Sveriges_t%C3%A4torter
     */
    protected static $cityNames = [
        'Abbekås', 'Abborrberget', 'Agunnaryd', 'Alberga', 'Alby', 'Alfta', 'Algutsrum', 'Alingsås', 'Allerum', 'Almunge', 'Alsike', 'Alstad', 'Alster', 'Alsterbro', 'Alstermo', 'Alunda', 'Alvesta', 'Alvhem', 'Alvik', 'Alvik', 'Ambjörby', 'Ambjörnarp', 'Ammenäs', 'Andalen', 'Anderslöv', 'Anderstorp', 'Aneby', 'Angelstad', 'Angered', 'Ankarsrum', 'Ankarsvik', 'Anneberg', 'Anneberg', 'Annelund', 'Annelöv', 'Antnäs', 'Aplared', 'Arboga', 'Arbrå', 'Ardala', 'Arentorp', 'Arild', 'Arjeplog', 'Arkelstorp', 'Arnäsvall', 'Arnö', 'Arontorp', 'Arvidsjaur', 'Arvika', 'Aröd och Timmervik', 'Askeby', 'Askersby', 'Askersund', 'Asmundtorp', 'Asperö', 'Aspås', 'Avan', 'Avesta', 'Axvall',
        'Backa', 'Backaryd', 'Backberg', 'Backe', 'Baggetorp', 'Ballingslöv', 'Balsby', 'Bammarboda', 'Bankekind', 'Bankeryd', 'Bara', 'Barkarö', 'Barsebäck', 'Barsebäckshamn', 'Bastuträsk', 'Beddingestrand', 'Benareby', 'Bengtsfors', 'Bengtsheden', 'Bensbyn', 'Berg', 'Berg', 'Berg', 'Berga', 'Bergagård', 'Bergby', 'Bergeforsen', 'Berghem', 'Bergkvara', 'Bergnäset', 'Bergsbyn', 'Bergshammar', 'Bergshamra', 'Bergsjö', 'Bergströmshusen', 'Bergsviken', 'Bergvik', 'Bestorp', 'Bettna', 'Bie', 'Billdal', 'Billeberga', 'Billesholm', 'Billinge', 'Billingsfors', 'Billsta', 'Bjurholm', 'Bjursås', 'Bjuv', 'Bjärnum', 'Bjärred', 'Bjärsjölagård', 'Bjästa', 'Björbo', 'Björboholm', 'Björke', 'Björketorp', 'Björklinge', 'Björkvik', 'Björkviken', 'Björkö', 'Björköby', 'Björlanda', 'Björna', 'Björneborg', 'Björnlunda', 'Björnänge', 'Björnö', 'Björnömalmen och Klacknäset', 'Björsäter', 'Blackstalund', 'Bleket', 'Blentarp', 'Blidsberg', 'Blikstorp', 'Blombacka', 'Blomstermåla', 'Blåsmark', 'Blötberget', 'Bockara', 'Boda', 'Bodafors', 'Boden', 'Boholmarna', 'Boliden', 'Bollebygd', 'Bollnäs', 'Bollstabruk', 'Bonäs', 'Boo', 'Bor', 'Borensberg', 'Borggård', 'Borgholm', 'Borgstena', 'Borlänge', 'Borrby', 'Borås', 'Bosnäs', 'Botsmark', 'Bottnaryd', 'Bovallstrand', 'Boxholm', 'Brantevik', 'Brastad', 'Brattås', 'Braås', 'Bredared', 'Bredaryd', 'Bredbyn', 'Bredsand', 'Bredviken', 'Brevik', 'Brevikshalvön', 'Bro', 'Broaryd', 'Broby', 'Brokind', 'Bromölla', 'Brottby', 'Brunflo', 'Brunn', 'Brunna', 'Brunnsberg', 'Bruzaholm', 'Brålanda', 'Bräcke', 'Bräkne-Hoby', 'Brändön', 'Brännland', 'Brännö', 'Brösarp', 'Bua', 'Buerås', 'Bullmark', 'Bunkeflostrand', 'Bureå', 'Burgsvik', 'Burlövs egnahem', 'Burseryd', 'Burträsk', 'Buskhyttan', 'Butbro', 'Bygdeå', 'Bygdsiljum', 'Byske', 'Bålsta', 'Bårslöv', 'Båstad', 'Båtskärsnäs', 'Bäckaskog', 'Bäckebo', 'Bäckefors', 'Bäckhammar', 'Bälgviken', 'Bälinge', 'Bälinge', 'Bärby', 'Bäsna', 'Böle', 'Bönan',
        'Charlottenberg',
        'Dalarö', 'Dalby', 'Dals Långed', 'Dals Rostock', 'Dalsjöfors', 'Dalstorp', 'Dalum', 'Danholn', 'Dannemora', 'Dannike', 'Degeberga', 'Degerfors', 'Degerhamn', 'Deje', 'Delary', 'Delsbo', 'Dingersjö', 'Dingle', 'Dingtuna', 'Diseröd', 'Diö', 'Djulö kvarn', 'Djura', 'Djurmo', 'Djurås', 'Djurö', 'Docksta', 'Domsten', 'Donsö', 'Dorotea', 'Drag', 'Drottningholm', 'Drängsmark', 'Dunö', 'Duved', 'Duvesjön', 'Dvärsätt', 'Dyvelsten', 'Dösjebro',
        'Ed', 'Eda glasbruk', 'Edane', 'Edsbro', 'Edsbruk', 'Edsbyn', 'Edsvalla', 'Eggby', 'Ekeby', 'Ekeby', 'Ekeby', 'Ekeby', 'Ekeby-Almby', 'Ekedalen', 'Ekenässjön', 'Ekerö', 'Ekerö sommarstad', 'Eket', 'Ekshärad', 'Eksjö', 'Eksund', 'Ekängen', 'Eldsberga', 'Ellös', 'Emmaboda', 'Emmaljunga', 'Emsfors', 'Emtunga', 'Eneryda', 'Enhagen-Ekbacken', 'Enköping', 'Ensjön', 'Enstaberga', 'Enviken', 'Enånger', 'Eriksmåla', 'Eringsboda', 'Ersmark', 'Ersmark', 'Ersnäs', 'Eskilsby och Snugga', 'Eskilstuna', 'Eslöv', 'Essvik', 'Evertsberg', 'Everöd',
        'Fagerhult', 'Fagersanna', 'Fagersta', 'Fagerås', 'Falerum', 'Falkenberg', 'Falköping', 'Falla', 'Falun', 'Fanbyn', 'Fellingsbro', 'Fengersfors', 'Figeholm', 'Filipstad', 'Filsbäck', 'Finja', 'Finkarby', 'Finnerödja', 'Finspång', 'Finsta', 'Fiskebäckskil', 'Fisksätra', 'Fjugesta', 'Fjälkinge', 'Fjällbacka', 'Fjärdhundra', 'Fjärås kyrkby', 'Flen', 'Flisby', 'Fliseryd', 'Floby', 'Floda', 'Floda', 'Flurkmark', 'Flygsfors', 'Flyinge', 'Flädie', 'Fornåsa', 'Fors', 'Forsbacka', 'Forsby', 'Forserum', 'Forshaga', 'Forsheda', 'Forssjö', 'Forsvik', 'Fotö', 'Fredrika', 'Fredriksberg', 'Fredriksdal', 'Fridafors', 'Fridlevstad', 'Friggesund', 'Frillesås', 'Frinnaryd', 'Fristad', 'Fritsla', 'Frufällan', 'Frånö', 'Främmestad', 'Frändefors', 'Fränsta', 'Frödinge', 'Frösakull', 'Frövi', 'Funäsdalen', 'Furuby', 'Furudal', 'Furulund', 'Furusjö', 'Furuvik', 'Fyllinge', 'Fågelfors', 'Fågelmara', 'Fågelsta', 'Fågelvikshöjden', 'Fårbo', 'Fårösund', 'Färgelanda', 'Färila', 'Färjestaden', 'Färlöv', 'Färnäs', 'Föllinge', 'Förslöv',
        'Gagnef', 'Gamleby', 'Gammelgården', 'Gammelstad', 'Gantofta', 'Garpenberg', 'Garphyttan', 'Geijersholm', 'Gemla', 'Genarp', 'Genevad', 'Gessie villastad', 'Gesunda', 'Getinge', 'Gideå', 'Gimmersta', 'Gimo', 'Gimåt', 'Gislaved', 'Gistad', 'Gladö kvarn', 'Glanshammar', 'Glemmingebro', 'Glimåkra', 'Glommen', 'Glommersträsk', 'Glumslöv', 'Gnarp', 'Gnesta', 'Gnosjö', 'Godegård', 'Gonäs', 'Gottne', 'Grangärde', 'Granö', 'Graversfors', 'Grebbestad', 'Grebo', 'Grevie', 'Grevie och Beden', 'Grillby', 'Grimslöv', 'Grimstorp', 'Grimsås', 'Gripenberg', 'Grisslehamn', 'Grums', 'Grundsund', 'Grycksbo', 'Grytgöl', 'Grythyttan', 'Gråbo', 'Gräfsnäs', 'Grängesberg', 'Gränna', 'Gränum', 'Grästorp', 'Grödby', 'Gualöv', 'Gubbo', 'Gudhem', 'Gullbrandstorp', 'Gullbranna', 'Gulleråsen', 'Gullringen', 'Gullspång', 'Gundal och Högås', 'Gunnarskog', 'Gunnarstorp', 'Gunnebo', 'Gunsta', 'Gusselby', 'Gustavsberg', 'Gustavsberg', 'Gusum', 'Gyttorp', 'Gånghester', 'Gårdby', 'Gårdskär', 'Gårdstånga', 'Gåvsta', 'Gäddede', 'Gällivare', 'Gällstad', 'Gällö', 'Gängletorp', 'Gärds Köpinge', 'Gärsnäs', 'Gävle', 'Göta', 'Göteborg', 'Götene', 'Götlunda',
        'Habo', 'Hackås', 'Haga', 'Hagby', 'Hagbyhöjden', 'Hagfors', 'Hagge', 'Hagryd-Dala', 'Hakkas', 'Halla Heberg', 'Hallabro', 'Hallen', 'Hallerna', 'Hallsberg', 'Hallstahammar', 'Hallstavik', 'Halltorp', 'Halmstad', 'Halvarsgårdarna', 'Hamburgsund', 'Hammar', 'Hammar', 'Hammarby', 'Hammarslund', 'Hammarstrand', 'Hammenhög', 'Hammerdal', 'Hampetorp', 'Hamrångefjärden', 'Hanaskog', 'Haparanda', 'Harads', 'Harbo', 'Hargshamn', 'Harlösa', 'Harmånger', 'Harplinge', 'Hassela', 'Hasselfors', 'Hasslarp', 'Hasslö', 'Hasslöv', 'Havdhem', 'Haverdal', 'Heberg', 'Heby', 'Hedared', 'Hede', 'Hedekas', 'Hedemora', 'Hedenäset', 'Hedeskoga', 'Hedesunda', 'Hedvigsberg', 'Helsingborg', 'Hemavan/Bierke', 'Hemmesta', 'Hemmingsmark', 'Hemse', 'Henån', 'Herrestad', 'Herrljunga', 'Herräng', 'Herstadberg', 'Hestra', 'Hestra', 'Hillared', 'Hillerstorp', 'Himle', 'Hindås', 'Hishult', 'Hissjön', 'Hittarp', 'Hjo', 'Hjorted', 'Hjortkvarn', 'Hjortsberga', 'Hjuvik', 'Hjälm', 'Hjälmared', 'Hjälmared', 'Hjältevad', 'Hjärnarp', 'Hjärsås', 'Hjärtum', 'Hjärup', 'Hofors', 'Hofterup', 'Hogstad', 'Hogstorp', 'Hok', 'Holm', 'Holmeja', 'Holmsjö', 'Holmsund', 'Holsbybrunn', 'Holsljunga', 'Horda', 'Horn', 'Horndal', 'Horred', 'Hortlax', 'Hoting', 'Hova', 'Hovid', 'Hovmantorp', 'Hovsta', 'Huaröd', 'Hudiksvall', 'Hult', 'Hultafors', 'Hultsfred', 'Hulu', 'Hummelsta', 'Hunnebostrand', 'Hurva', 'Husby', 'Husum', 'Hybo', 'Hyllinge', 'Hyltebruk', 'Hyssna', 'Håbo-Tibble kyrkby', 'Håga', 'Håksberg', 'Hållsta', 'Hålsjö', 'Hånger', 'Häggeby och Vreta', 'Häggenås', 'Häljarp', 'Hällabrottet', 'Hällaryd', 'Hällberga', 'Hällbybrunn', 'Hällefors', 'Hälleforsnäs', 'Hällekis', 'Hällestad', 'Hällesåker', 'Hällevadsholm', 'Hällevik', 'Hälleviksstrand', 'Hällingsjö', 'Hällnäs', 'Hälsö', 'Härad', 'Häradsbygden', 'Härnösand', 'Härryda', 'Härslöv', 'Hässleholm', 'Hästhagen', 'Hästholmen', 'Hästveda', 'Höganäs', 'Högboda', 'Högsby', 'Högsjö', 'Högsäter', 'Höja', 'Hökerum', 'Hökåsen', 'Hököpinge', 'Höllviken', 'Hölö', 'Hönö', 'Hörby', 'Hörnefors', 'Hörvik', 'Höviksnäs', 'Höör',
        'Idala', 'Idkerberget', 'Idre', 'Igelfors', 'Igelstorp', 'Iggesund', 'Ilsbo', 'Immeln', 'Indal', 'Ingared', 'Ingaröstrand', 'Ingatorp', 'Ingelstad', 'Ingelsträde', 'Innertavle', 'Insjön', 'Irsta',
        'Johannedal', 'Johannesudd', 'Johannishus', 'Johansfors', 'Jokkmokk', 'Jonsered', 'Jonslund', 'Jonstorp', 'Jordbro', 'Jukkasjärvi', 'Jung', 'Juniskär', 'Junosuando', 'Junsele', 'Juoksengi', 'Jursla', 'Jäderfors', 'Jädraås', 'Jämjö', 'Jämshög', 'Jämtön', 'Järbo', 'Järlåsa', 'Järna', 'Järna', 'Järnforsen', 'Järpen', 'Järpås', 'Järvsö', 'Jättendal', 'Jävre', 'Jönköping', 'Jönåker', 'Jörlanda', 'Jörn', 'Jössefors',
        'Kalix', 'Kallax', 'Kallinge', 'Kalmar', 'Kalvsund', 'Kangos', 'Karby', 'Kareby', 'Karesuando', 'Karlholmsbruk', 'Karlsborg', 'Karlsborg', 'Karlshamn', 'Karlskoga', 'Karlskrona', 'Karlstad', 'Karlsvik', 'Karungi', 'Karups sommarby', 'Kastlösa', 'Katrinedal', 'Katrineholm', 'Kattarp', 'Kaxholmen', 'Kebal', 'Kil', 'Kil', 'Kilafors', 'Killeberg', 'Kilsmo', 'Kimstad', 'Kinna', 'Kinnared', 'Kinnarp', 'Kinnarumma', 'Kiruna', 'Kisa', 'Kivik', 'Kjulaås', 'Klagstorp', 'Klevshult', 'Klingsta och Allsta', 'Klintehamn', 'Klippan', 'Klippans bruk', 'Klockestrand', 'Klockrike', 'Klågerup', 'Klädesholmen', 'Kläppa', 'Klässbol', 'Klöverträsk', 'Klövsjö', 'Knislinge', 'Knivsta', 'Knutby', 'Knäred', 'Kode', 'Kolbäck', 'Kolsva', 'Konga', 'Kopparberg', 'Kopparmora', 'Koppom', 'Korpilombolo', 'Korsberga', 'Korsberga', 'Korsträsk', 'Koskullskulle', 'Kosta', 'Kovland', 'Kramfors', 'Kristdala', 'Kristianstad', 'Kristineberg', 'Kristinehamn', 'Kristvallabrunn', 'Krokek', 'Krokom', 'Krägga', 'Kulltorp', 'Kullö', 'Kumla', 'Kumla kyrkby', 'Kummelnäs', 'Kungsbacka', 'Kungsberga', 'Kungsgården', 'Kungshamn', 'Kungshult', 'Kungsängen', 'Kungsäter', 'Kungsör', 'Kungälv', 'Kurland', 'Kusmark', 'Kuttainen', 'Kvibille', 'Kvicksund', 'Kvidinge', 'Kvillsfors', 'Kvisljungeby', 'Kvissleby', 'Kvänum', 'Kvärlöv', 'Kyrkheddinge', 'Kyrkhult', 'Kyrksten', 'Kåge', 'Kågeröd', 'Kåhög', 'Kållekärr', 'Kållered', 'Kånna', 'Kårsta', 'Kälarne', 'Källby', 'Källö-Knippla', 'Kärda', 'Kärna', 'Kärsta och Bredsdal', 'Kättilsmåla', 'Kättilstorp', 'Kävlinge', 'Köping', 'Köpingebro', 'Köpingsvik', 'Köpmanholmen',
        'Lagan', 'Laholm', 'Lammhult', 'Landeryd', 'Landfjärden', 'Landsbro', 'Landskrona', 'Landvetter', 'Lanesund och Överby', 'Lanna', 'Lanna', 'Latorpsbruk', 'Laxvik', 'Laxå', 'Lekeryd', 'Leksand', 'Lenhovda', 'Lerdala', 'Lerkil', 'Lerum', 'Lesjöfors', 'Lessebo', 'Liatorp', 'Lidatorp och Klövsta', 'Liden', 'Lidhult', 'Lidingö', 'Lidköping', 'Lilla Edet', 'Lilla Harrie', 'Lilla Stenby', 'Lilla Tjärby', 'Lillhaga', 'Lillhärdal', 'Lillkyrka', 'Lillpite', 'Lima', 'Limedsforsen', 'Limmared', 'Linderöd', 'Lindesberg', 'Lindholmen', 'Lindome', 'Lindsdal', 'Lindö', 'Lingbo', 'Linghed', 'Linghem', 'Linköping', 'Linneryd', 'Listerby', 'Lit', 'Ljugarn', 'Ljung', 'Ljunga', 'Ljungaverk', 'Ljungby', 'Ljungbyhed', 'Ljungbyholm', 'Ljunghusen', 'Ljungsarp', 'Ljungsbro', 'Ljungskile', 'Ljungstorp och Jägersbo', 'Ljusdal', 'Ljusfallshammar', 'Ljusne', 'Loftahammar', 'Lomma', 'Los', 'Lotorp', 'Lottefors', 'Lucksta', 'Ludvigsborg', 'Ludvika', 'Lugnet och Skälsmara', 'Lugnvik', 'Lugnås', 'Luleå', 'Lund', 'Lund', 'Lunde', 'Lundsbrunn', 'Lunnarp', 'Lurudden', 'Lycksele', 'Lyrestad', 'Lysekil', 'Lysvik', 'Långasjö', 'Långsele', 'Långshyttan', 'Långvik', 'Långviksmon', 'Långås', 'Låssby', 'Läby', 'Läckeby', 'Länghem', 'Länna', 'Lärbro', 'Löberöd', 'Löddeköpinge', 'Löderup', 'Lödöse', 'Löftaskog', 'Lögdeå', 'Lönsboda', 'Lörby', 'Löttorp', 'Löwenströmska lasarettet', 'Lövestad', 'Lövstalöt', 'Lövånger',
        'Madängsholm', 'Mala', 'Malmberget', 'Malmbäck', 'Malmköping', 'Malmslätt', 'Malmö', 'Maln', 'Malung', 'Malungsfors', 'Malå', 'Mantorp', 'Marbäck', 'Margretetorp', 'Mariannelund', 'Marieby', 'Mariedal', 'Mariefred', 'Marieholm', 'Marielund', 'Marielund', 'Mariestad', 'Markaryd', 'Marma', 'Marmaskogen', 'Marmaverken', 'Marmorbyn', 'Marstrand', 'Matfors', 'Medle', 'Medåker', 'Mehedeby', 'Mellansel', 'Mellbystrand', 'Mellerud', 'Mellösa', 'Merlänna', 'Misterhult', 'Mjällby', 'Mjällom', 'Mjöbäck', 'Mjöhult', 'Mjölby', 'Mjönäs', 'Mockfjärd', 'Mogata', 'Mohed', 'Moheda', 'Moholm', 'Moliden', 'Molkom', 'Mollösund', 'Mora', 'Mora', 'Morgongåva', 'Morjärv', 'Morup', 'Moskosel', 'Motala', 'Mullhyttan', 'Mullsjö', 'Munga', 'Munka-Ljungby', 'Munkedal', 'Munkfors', 'Munktorp', 'Muskö', 'Myckle', 'Myggenäs', 'Myresjö', 'Myrviken', 'Mysingsö', 'Mysterna', 'Målerås', 'Målilla', 'Målsryd', 'Månkarbo', 'Måttsund', 'Märsta', 'Möklinta', 'Mölle', 'Mölltorp', 'Mölnbo', 'Mölnlycke', 'Mönsterås', 'Mörarp', 'Mörbylånga', 'Mörlunda', 'Mörrum', 'Mörsil', 'Mörtnäs',
        'Naglarby och Enbacka', 'Nedansjö', 'Nedre Gärdsjö', 'Nikkala', 'Nissafors', 'Nitta', 'Njurundabommen', 'Njutånger', 'Nogersund', 'Nolvik', 'Nora', 'Norberg', 'Nordanö', 'Nordingrå', 'Nordkroken', 'Nordmaling', 'Nordmark', 'Nore', 'Norje', 'Norr Amsberg', 'Norra Bro', 'Norra Lagnö', 'Norra Riksten', 'Norra Rörum', 'Norra Visby', 'Norra Åsum', 'Norrfjärden', 'Norr-Hede', 'Norrhult-Klavreström', 'Norrköping', 'Norrlandet', 'Norrskedika', 'Norrsundet', 'Norrtälje', 'Norrö', 'Norsesund', 'Norsholm', 'Norsjö', 'Nossebro', 'Nusnäs', 'Nya Långenäs', 'Nyborg', 'Nybro', 'Nybrostrand', 'Nygård', 'Nygårds hagar', 'Nyhammar', 'Nykil', 'Nykroppa', 'Nykvarn', 'Nykyrka', 'Nyköping', 'Nyland', 'Nymölla', 'Nynäshamn', 'Nås', 'Nälden', 'Näs bruk', 'Nässjö', 'Näsum', 'Näsviken', 'Näsviken', 'Näsåker', 'Nättraby', 'Nävekvarn', 'Nävragöl', 'Nöbbele', 'Nödinge-Nol',
        'Obbola', 'Ockelbo', 'Odensbacken', 'Odensberg', 'Odensjö', 'Oleby', 'Olofstorp', 'Olofström', 'Olsfors', 'Olshammar', 'Olstorp', 'Onsala', 'Onslunda', 'Ope', 'Optand', 'Ormanäs och Stanstorp', 'Ornäs', 'Orrefors', 'Orrviken', 'Orsa', 'Osby', 'Osbyholm', 'Oskar-Fredriksborg', 'Oskarshamn', 'Oskarström', 'Ostvik', 'Otterbäcken', 'Ovanåker', 'Ovesholm', 'Oxelösund', 'Oxie',
        'Pajala', 'Parksidan', 'Pauliström', 'Persberg', 'Persbo', 'Pershagen', 'Perstorp', 'Persön', 'Pilgrimstad', 'Piperskärr', 'Piteå', 'Porjus', 'Pukavik', 'Påarp', 'Pålsboda', 'Påläng', 'Påryd', 'Påskallavik',
        'Rabbalshede', 'Raksta', 'Ramdala', 'Ramnäs', 'Ramsberg', 'Ramsele', 'Ramstalund', 'Ramvik', 'Ransta', 'Rappestad', 'Reftele', 'Rejmyre', 'Rengsjö', 'Repbäcken', 'Resarö', 'Revingeby', 'Riala', 'Riddarhyttan', 'Rimbo', 'Rimforsa', 'Ringarum', 'Ringsegård', 'Rinkaby', 'Rinkabyholm', 'Risögrund', 'Rixö', 'Robertsfors', 'Rockhammar', 'Rockneby', 'Roknäs', 'Rolfhamre och Måga', 'Rolfs', 'Rolfstorp', 'Roma kyrkby (Lövsta)', 'Roma (Romakloster)', 'Ronneby', 'Ronnebyhamn', 'Rosenfors', 'Rosenlund', 'Rosersberg', 'Rossön', 'Rosvik', 'Rot', 'Roteberg', 'Rottne', 'Rottneros', 'Ruda', 'Rundvik', 'Runemo', 'Runhällen', 'Runtuna', 'Rusksele', 'Rutvik', 'Rya', 'Ryd', 'Rydaholm', 'Rydal', 'Rydbo', 'Rydboholm', 'Rydebäck', 'Rydsgård', 'Rydsnäs', 'Rydöbruk', 'Ryssby', 'Råby', 'Råda', 'Råneå', 'Rångedala', 'Rånnaväg', 'Rånäs', 'Rälla', 'Rängs sand', 'Ränneslöv', 'Rättarboda', 'Rättvik', 'Rävemåla', 'Rävlanda', 'Röbäck', 'Röda holme', 'Rödbo', 'Rödeby', 'Röfors', 'Röke', 'Rönneshytta', 'Rönnäng', 'Rörvik', 'Rörö', 'Röstånga',
        'Sala', 'Salbohed', 'Saleby', 'Saltsjöbaden', 'Saltvik', 'Sandared', 'Sandarne', 'Sandhem', 'Sandhult', 'Sandskogen', 'Sandslån', 'Sandviken', 'Sandviken', 'Sangis', 'Sankt Olof', 'Sannahed', 'Saxdalen', 'Saxtorpsskogen', 'Segersta', 'Segersäng', 'Segmon', 'Selja', 'Sennan', 'Seskarö', 'Sexdrega', 'Sibbhult', 'Sibble', 'Sibo', 'Sidensjö', 'Sifferbo', 'Sigtuna', 'Siljansnäs', 'Silverdalen', 'Simlångsdalen', 'Simonstorp', 'Simris', 'Simrishamn', 'Sjuhalla', 'Sjulsmark', 'Sjunnen', 'Sjuntorp', 'Sjöberg', 'Sjöbo', 'Sjöbo sommarby och Svansjö sommarby', 'Sjödiken', 'Sjögestad', 'Sjömarken', 'Sjörröd', 'Sjösa', 'Sjötorp', 'Sjövik', 'Skagersvik', 'Skanör med Falsterbo', 'Skara', 'Skattkärr', 'Skattungbyn', 'Skavkulla och Skillingenäs', 'Skebobruk', 'Skeda udde', 'Skedala', 'Skede', 'Skedvi kyrkby', 'Skee', 'Skegrie', 'Skelleftehamn', 'Skellefteå', 'Skepparkroken', 'Skepplanda', 'Skeppsdalsström', 'Skeppshult', 'Skillingaryd', 'Skillinge', 'Skinnskatteberg', 'Skivarp', 'Skoby', 'Skog', 'Skoghall', 'Skogsby', 'Skogstorp', 'Skogstorp', 'Skottorp', 'Skottsund', 'Skrea', 'Skreanäs', 'Skriketorp', 'Skruv', 'Skultorp', 'Skultuna', 'Skummeslövsstrand', 'Skumparp', 'Skurup', 'Skutskär', 'Skyttorp', 'Skånes-Fagerhult', 'Skåpafors', 'Skåre', 'Skällinge', 'Skänninge', 'Skärblacka', 'Skärgårdsstad', 'Skärhamn', 'Skärplinge', 'Skärstad', 'Sköldinge', 'Sköllersta', 'Skölsta', 'Skövde', 'Slaka', 'Slite', 'Slottsbron', 'Slottsskogen', 'Slöinge', 'Smedby', 'Smedjebacken', 'Smedstorp', 'Smygehamn', 'Smålandsstenar', 'Smögen', 'Snöveltorp', 'Solberga', 'Solberga', 'Sollebrunn', 'Sollefteå', 'Sollerön', 'Solsidan', 'Solvarbo', 'Sommen', 'Sonstorp', 'Sorsele', 'Sorunda', 'Sparreholm', 'Spjutsbygd', 'Spångsholm', 'Staffanstorp', 'Stallarholmen', 'Stamsjö', 'Starrkärr och Näs', 'Stava', 'Stavreviken', 'Stavsjö', 'Stavsnäs', 'Stehag', 'Stenared', 'Stenhamra', 'Steninge', 'Stensele', 'Stensjön', 'Stenstorp', 'Stensund och Krymla', 'Stenungsund', 'Stenungsön', 'Sticklinge udde', 'Stidsvig', 'Stigen', 'Stigtomta', 'Stjärnhov', 'Stoby', 'Stocka', 'Stockamöllan', 'Stockaryd', 'Stockholm', 'Stockvik', 'Stora Bugärde', 'Stora Dyrön', 'Stora Herrestad', 'Stora Höga', 'Stora Levene', 'Stora Mellby', 'Stora Mellösa', 'Stora Vika', 'Storebro', 'Storfors', 'Storuman', 'Storvik', 'Storvreta', 'Storå', 'Strandhugget', 'Strandnorum', 'Striberg', 'Strålsnäs', 'Strångsjö', 'Stråssa', 'Strängnäs', 'Strömma', 'Strömsbruk', 'Strömsfors', 'Strömsholm', 'Strömsnäsbruk', 'Strömstad', 'Strömsund', 'Strövelstorp', 'Stugun', 'Sturefors', 'Sturkö', 'Styrsö', 'Stånga', 'Stångby', 'Ställdalen', 'Stöcke', 'Stöcksjö', 'Stöde', 'Stöllet', 'Stöpen', 'Sulvik', 'Sund', 'Sundborn', 'Sundby', 'Sundbyholm', 'Sundhultsbrunn', 'Sundsbruk', 'Sundsvall', 'Sunnansjö', 'Sunne', 'Sunnemo', 'Sunningen', 'Surahammar', 'Surte', 'Svalsta', 'Svalöv', 'Svanberga', 'Svanesund', 'Svanskog', 'Svanvik', 'Svappavaara', 'Svartbyn', 'Svarte', 'Svartvik', 'Svartå', 'Svedala', 'Sveg', 'Svenljunga', 'Svensbyn', 'Svenshögen', 'Svenstavik', 'Svenstorp', 'Svinninge', 'Svängsta', 'Svärdsjö', 'Svärtinge', 'Sya', 'Sysslebäck', 'Sågmyra', 'Säffle', 'Sälen', 'Sälgsjön', 'Särna', 'Särö', 'Säter', 'Sätila', 'Sätofta', 'Sätra brunn', 'Sävar', 'Sävast', 'Säve', 'Sävja', 'Sävsjö', 'Söderala', 'Söderby', 'Söderby-Karl', 'Söderbärke', 'Söderfors', 'Söderhamn', 'Söderköping', 'Söderskogen', 'Södersvik', 'Södertälje', 'Söderåkra', 'Södra Bergsbyn och Stackgrönnan', 'Södra Klagshamn', 'Södra Näs', 'Södra Sandby', 'Södra Sunderbyn', 'Södra Vi', 'Södra Vrams fälad', 'Sölvesborg', 'Sörfors', 'Sörforsa', 'Sörmjöle', 'Sörstafors', 'Sörvik', 'Söråker', 'Sösdala', 'Sövde', 'Sövestad',
        'Taberg', 'Tahult', 'Tallvik', 'Tallåsen', 'Tandsbyn', 'Tanumshede', 'Tavelsjö', 'Teckomatorp', 'Tenhult', 'Tibro', 'Tidaholm', 'Tidan', 'Tidö-Lindö', 'Tierp', 'Tillberga', 'Timmele', 'Timmernabben', 'Timmersdala', 'Timrå', 'Timsfors', 'Tingsryd', 'Tingstäde', 'Tjautjas/Cavccas', 'Tjuvkil', 'Tjällmo', 'Tjörnarp', 'Toarp', 'Tobo', 'Tofta', 'Toftbyn', 'Tollarp', 'Tollered', 'Tomelilla', 'Torarp', 'Torbjörntorp', 'Torekov', 'Torestorp', 'Torhamn', 'Tormestorp', 'Torna Hällestad', 'Torpsbruk', 'Torpshammar', 'Torreby', 'Torsby', 'Torsby', 'Torsebro', 'Torshälla', 'Torshälla huvud', 'Torsåker', 'Torsång', 'Torsås', 'Tortuna', 'Torup', 'Tosseryd', 'Totebo', 'Totra', 'Tranemo', 'Tranholmen', 'Transtrand', 'Tranås', 'Traryd', 'Trekanten', 'Trelleborg', 'Trollhättan', 'Trosa', 'Trulsegården', 'Trångsviken', 'Tråvad', 'Trädet', 'Träslövsläge', 'Trödje', 'Trönninge', 'Trönninge', 'Tulebo', 'Tumba', 'Tumbo', 'Tumlehed', 'Tun', 'Tuna', 'Tuna', 'Tunadal', 'Tunnerstad', 'Tureholm', 'Tving', 'Tvååker', 'Tvärskog', 'Tvärålund', 'Tygelsjö', 'Tylösand', 'Tyringe', 'Tystberga', 'Tågarp', 'Tånga och Rögle', 'Tångaberg', 'Täby', 'Täfteå', 'Täljö', 'Tällberg', 'Tärnaby', 'Tärnsjö', 'Tävelsås', 'Töcksfors', 'Töllsjö', 'Töre', 'Töreboda', 'Törestorp', 'Tösse',
        'Ucklum', 'Uddebo', 'Uddeholm', 'Uddevalla', 'Uddheden', 'Ullared', 'Ullatti', 'Ullervad', 'Ullånger', 'Ulricehamn', 'Ulrika', 'Ulvkälla', 'Ulvåker', 'Umeå', 'Unbyn', 'Undenäs', 'Undersåker', 'Unnaryd', 'Upphärad', 'Upplanda', 'Upplands Väsby', 'Uppsala', 'Urshult', 'Ursviken', 'Utansjö', 'Utby', 'Utvälinge',
        'Vad', 'Vadstena', 'Vaggeryd', 'Vagnhärad', 'Valbo', 'Valdemarsvik', 'Valje', 'Valla', 'Vallargärdet', 'Vallberga', 'Vallda', 'Vallentuna', 'Vallsta', 'Vallvik', 'Vallåkra', 'Valskog', 'Vankiva', 'Vannsätter', 'Vansbro', 'Vansö kyrkby', 'Vaplan', 'Vara', 'Varberg', 'Varekil', 'Vargön', 'Varnhem', 'Vartofta', 'Vassbäck', 'Vassmolösa', 'Vattholma', 'Vattjom', 'Vattnäs', 'Vattubrinken', 'Vaxholm', 'Veberöd', 'Veddige', 'Vedevåg', 'Vedum', 'Vegby', 'Veinge', 'Vejbystrand', 'Velanda', 'Vellinge', 'Vemdalen', 'Vena', 'Venjan', 'Vessigebro', 'Vetlanda', 'Vi', 'Vibble', 'Viby', 'Vickleby', 'Vidja', 'Vidsel', 'Vidöåsen', 'Vik', 'Vika', 'Vikarbyn', 'Viken', 'Vikingstad', 'Vikmanshyttan', 'Viksjöfors', 'Viksäter', 'Vilhelmina', 'Villshärad', 'Vilshult', 'Vimmerby', 'Vinberg', 'Vinbergs kyrkby', 'Vindeln', 'Vingåker', 'Vinninga', 'Vinnö', 'Vinslöv', 'Vintrie', 'Vintrosa', 'Vinäs', 'Virsbo', 'Virserum', 'Visby', 'Viskafors', 'Vislanda', 'Vissefjärda', 'Vistträsk', 'Vitaby', 'Vittangi', 'Vittaryd', 'Vittinge', 'Vittjärv', 'Vittsjö', 'Vittskövle', 'Vollsjö', 'Vrena', 'Vretstorp', 'Vrigstad', 'Vrångö', 'Vuollerim', 'Vålberg', 'Våmhus', 'Vånga', 'Vårdsätra', 'Vårgårda', 'Vårsta', 'Våxtorp', 'Väckelsång', 'Väderstad', 'Väggarp', 'Väjern', 'Väländan', 'Vänersborg', 'Väne-Åsaka', 'Vänge', 'Vännäs', 'Vännäsby', 'Väring', 'Värmdö-Evlinge', 'Värmlandsbro', 'Värnamo', 'Värsås', 'Väröbacka', 'Väse', 'Väskinde', 'Västanvik', 'Västerberg', 'Västerby', 'Västerfärnebo', 'Västerhaninge', 'Västerhejde', 'Västerhus', 'Västerljung', 'Västerlösa', 'Västermyckeläng', 'Västervik', 'Västerås', 'Västibyn', 'Västra Bispgården', 'Västra Bodarna', 'Västra Hagen', 'Västra Husby', 'Västra Ingelstad', 'Västra Karaby', 'Västra Karup', 'Västra Klagstorp', 'Västra Tommarp', 'Västra Ämtervik', 'Växjö',
        'Yngsjö', 'Ysby', 'Ystad', 'Ytterhogdal', 'Ytternäs och Vreta', 'Yttersjö', 'Ytterån',
        'Zinkgruvan',
        'Åby', 'Åby', 'Åbyggeby', 'Åbytorp', 'Åhus', 'Åkarp', 'Åkers styckebruk', 'Åkersberga', 'Ålberga', 'Åled', 'Ålem', 'Åmmeberg', 'Åmot', 'Åmotfors', 'Åmsele', 'Åmynnet', 'Åmål', 'Ånge', 'Ånäset', 'Åre', 'Årjäng', 'Årstad', 'Årsunda', 'Åryd', 'Åryd', 'Ås', 'Ås', 'Åsa', 'Åsarne', 'Åsarp', 'Åsbro', 'Åsby', 'Åseda', 'Åsele', 'Åselstad', 'Åsen', 'Åsenhöga', 'Åsensbruk', 'Åshammar', 'Åsljunga', 'Åstol', 'Åstorp', 'Återvall', 'Åtorp', 'Åtvidaberg',
        'Älandsbro', 'Älgarås', 'Älghult', 'Älmhult', 'Älmsta', 'Älta', 'Älvdalen', 'Älvkarleby', 'Älvnäs', 'Älvsbyn', 'Älvsered', 'Älvängen', 'Äng', 'Änge', 'Ängelholm', 'Ängsholmen', 'Ängsvik', 'Äppelbo', 'Ärla', 'Äsköping', 'Äspered', 'Äsperöd', 'Ätran',
        'Öbonäs', 'Öckerö', 'Ödeborg', 'Ödeshög', 'Ödsmål', 'Ödåkra', 'Öggestorp', 'Öjersjö', 'Ölmanäs', 'Ölmbrotorp', 'Ölme', 'Ölmstad', 'Ölsta', 'Önneköp', 'Önnestad', 'Örbyhus', 'Örebro', 'Öregrund', 'Örkelljunga', 'Örnsköldsvik', 'Örserum', 'Örsjö', 'Örslösa', 'Örsundsbro', 'Örtagården', 'Örtofta', 'Örviken', 'Ösmo', 'Östadkulle', 'Östansjö', 'Östavall', 'Österbybruk', 'Österbymo', 'Österforse', 'Österfärnebo', 'Österhagen och Bergliden', 'Österslöv', 'Österstad', 'Östersund', 'Östervåla', 'Östhammar', 'Östhamra', 'Östmark', 'Östnor', 'Östorp och Ådran', 'Östra Bispgården', 'Östra Frölunda', 'Östra Grevie', 'Östra Husby', 'Östra Kallfors', 'Östra Karup', 'Östra Ljungby', 'Östra Ryd', 'Östra Sönnarslöv', 'Östra Tommarp', 'Östra Ånneröd', 'Östraby', 'Överboda', 'Överhörnäs', 'Överkalix', 'Överlida', 'Övertorneå', 'Överum', 'Övre Soppero', 'Övre Svartlå', 'Öxabäck', 'Öxeryd',
    ];

    protected static $cityFormats = [
        '{{cityName}}',
    ];

    protected static $state = [];

    protected static $stateAbbr = [];

    protected static $country = [
        'Afghanistan', 'Albanien', 'Algeriet', 'Amerikanska Jungfruöarna', 'Amerikanska Samoa', 'Andorra', 'Angola', 'Anguilla', 'Antarktis', 'Antigua och Barbuda', 'Argentina', 'Armenien', 'Aruba', 'Australien', 'Azerbajdzjan',
        'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belgien', 'Belize', 'Benin', 'Bermuda', 'Bhutan', 'Bolivia', 'Bosnien och Hercegovina', 'Botswana', 'Bouvetön', 'Brasilien', 'Brittiska Indiska oceanöarna', 'Brittiska Jungfruöarna', 'Brunei', 'Bulgarien', 'Burkina Faso', 'Burundi',
        'Caymanöarna', 'Centralafrikanska republiken', 'Chile', 'Colombia', 'Cooköarna', 'Costa Rica', 'Cypern',
        'Danmark', 'Djibouti', 'Dominica', 'Dominikanska republiken',
        'Ecuador', 'Egypten', 'Ekvatorialguinea', 'El Salvador', 'Elfenbenskusten', 'Eritrea', 'Estland', 'Etiopien',
        'Falklandsöarna', 'Fiji', 'Filippinerna', 'Finland', 'Frankrike', 'Franska Guyana', 'Franska Polynesien', 'Franska Sydterritorierna', 'Färöarna', 'Förenade Arabemiraten',
        'Gabon', 'Gambia', 'Georgien', 'Ghana', 'Gibraltar', 'Grekland', 'Grenada', 'Grönland', 'Guadeloupe', 'Guam', 'Guatemala', 'Guernsey', 'Guinea', 'Guinea-Bissau', 'Guyana',
        'Haiti', 'Heard- och McDonaldöarna', 'Honduras', 'Hongkong (S.A.R. Kina)',
        'Indien', 'Indonesien', 'Irak', 'Iran', 'Irland', 'Island', 'Isle of Man', 'Israel', 'Italien',
        'Jamaica', 'Japan', 'Jemen', 'Jersey', 'Jordanien', 'Julön',
        'Kambodja', 'Kamerun', 'Kanada', 'Kap Verde', 'Kazakstan', 'Kenya', 'Kina', 'Kirgizistan', 'Kiribati', 'Kokosöarna', 'Komorerna', 'Kongo-Brazzaville', 'Kongo-Kinshasa', 'Kroatien', 'Kuba', 'Kuwait',
        'Laos', 'Lesotho', 'Lettland', 'Libanon', 'Liberia', 'Libyen', 'Liechtenstein', 'Litauen', 'Luxemburg',
        'Macao (S.A.R. Kina)', 'Madagaskar', 'Makedonien', 'Malawi', 'Malaysia', 'Maldiverna', 'Mali', 'Malta', 'Marocko', 'Marshallöarna', 'Martinique', 'Mauretanien', 'Mauritius', 'Mayotte', 'Mexiko', 'Mikronesien', 'Moldavien', 'Monaco', 'Mongoliet', 'Montenegro', 'Montserrat', 'Moçambique', 'Myanmar',
        'Namibia', 'Nauru', 'Nederländerna', 'Nederländska Antillerna', 'Nepal', 'Nicaragua', 'Niger', 'Nigeria', 'Niue', 'Nordkorea', 'Nordmarianerna', 'Norfolkön', 'Norge', 'Nya Kaledonien', 'Nya Zeeland',
        'Oman',
        'Pakistan', 'Palau', 'Palestinska territoriet', 'Panama', 'Papua Nya Guinea', 'Paraguay', 'Peru', 'Pitcairn', 'Polen', 'Portugal', 'Puerto Rico',
        'Qatar',
        'Rumänien', 'Rwanda', 'Ryssland', 'Réunion',
        'S:t Barthélemy', 'S:t Helena', 'S:t Kitts och Nevis', 'S:t Lucia', 'S:t Martin', 'S:t Pierre och Miquelon', 'S:t Vincent och Grenadinerna', 'Salomonöarna', 'Samoa', 'San Marino', 'Saudiarabien', 'Schweiz', 'Senegal', 'Serbien', 'Serbien och Montenegro', 'Seychellerna', 'Sierra Leone', 'Singapore', 'Slovakien', 'Slovenien', 'Somalia', 'Spanien', 'Sri Lanka', 'Storbritannien', 'Sudan', 'Surinam', 'Svalbard och Jan Mayen', 'Sverige', 'Swaziland', 'Sydafrika', 'Sydgeorgien och Södra Sandwichöarna', 'Sydkorea', 'Syrien', 'São Tomé och Príncipe',
        'Tadzjikistan', 'Taiwan', 'Tanzania', 'Tchad', 'Thailand', 'Tjeckien', 'Togo', 'Tokelau', 'Tonga', 'Trinidad och Tobago', 'Tunisien', 'Turkiet', 'Turkmenistan', 'Turks- och Caicosöarna', 'Tuvalu', 'Tyskland',
        'USA', 'USA:s yttre öar', 'Uganda', 'Ukraina', 'Ungern', 'Uruguay', 'Uzbekistan',
        'Vanuatu', 'Vatikanstaten', 'Venezuela', 'Vietnam', 'Vitryssland', 'Västsahara', 'Wallis- och Futunaöarna',
        'Zambia', 'Zimbabwe',
        'Åland',
        'Österrike', 'Östtimor',
    ];

    /**
     * @var array Swedish street name formats
     */
    protected static $streetNameFormats = [
        '{{lastName}}{{streetSuffix}}',
        '{{lastName}}{{streetSuffix}}',
        '{{firstName}}{{streetSuffix}}',
        '{{firstName}}{{streetSuffix}}',
        '{{streetPrefix}}{{streetSuffix}}',
        '{{streetPrefix}}{{streetSuffix}}',
        '{{streetPrefix}}{{streetSuffix}}',
        '{{streetPrefix}}{{streetSuffix}}',
        '{{lastName}} {{streetSuffixWord}}',
    ];

    /**
     * @var array Swedish street address formats
     */
    protected static $streetAddressFormats = [
        '{{streetName}} {{buildingNumber}}',
    ];

    /**
     * @var array Swedish address formats
     */
    protected static $addressFormats = [
        "{{streetAddress}}\n{{postcode}} {{city}}",
    ];

    /**
     * Randomly return a real city name
     *
     * @return string
     */
    public static function cityName()
    {
        return static::randomElement(static::$cityNames);
    }

    public static function streetSuffixWord()
    {
        return static::randomElement(static::$streetSuffixWord);
    }

    public static function streetPrefix()
    {
        return static::randomElement(static::$streetPrefix);
    }

    /**
     * Randomly return a building number.
     *
     * @return string
     */
    public static function buildingNumber()
    {
        return static::toUpper(static::bothify(static::randomElement(static::$buildingNumber)));
    }
}
