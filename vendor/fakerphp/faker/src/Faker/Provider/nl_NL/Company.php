<?php

namespace Faker\Provider\nl_NL;

use Faker\Provider\Miscellaneous;

class Company extends \Faker\Provider\Company
{
    /**
     * @see https://nl.wikipedia.org/wiki/Lijst_van_beroepen
     */
    protected static $jobTitleFormat = [
        'Aankondiger', 'Acceptant', 'Accountant', 'Accountmanager', 'Acrobaat', 'Acteur', 'Activiteitenbegeleider', 'Actuaris', 'Acupuncturist', 'Adjudant', 'Administrateur', 'Advertentiezetter', 'Adviseur', 'Advocaat', 'Agent', 'Agrariër', 'Akoepedist', 'Akoesticus', 'Alchemist', 'Allergoloog', 'Altist', 'Amanuensis', 'Ambtenaar', 'Ambulancebegeleider', 'Ambulancechauffeur', 'Ambulanceverpleegkundige', 'Analist', 'Anatoom', 'Andragoog', 'Androloog', 'Anesthesist', 'Anesthesiemedewerker', 'Animeermeisje', 'Antiquaar', 'Antiquair', 'Apotheker', 'Apothekersassistent', 'Applicatieontwikkelaar', 'Arbeidsanalist', 'Arbeidsbemiddelaar', 'Arbeidsdeskundige', 'Arbeidsfysioloog', 'Arbeidsgeneesheer', 'Arbeidshygiënist', 'Archeoloog', 'Architect', 'Archivaris', 'Archivist', 'Arrangeur', 'Artdirector', 'Artiest', 'Arts', 'Assuradeur', 'Astrofysicus', 'Astroloog', 'Astronaut', 'Astronoom', 'Audioloog', 'Audiometrist', 'Audiotherapeut', 'Auditor', 'Autohandelaar', 'Automonteur', 'Autoplaatwerker', 'Autospuiter',
        'Bacterioloog', 'Badmeester', 'Baggermachinist', 'Baggermolenarbeider', 'Baker', 'Bakker', 'Baliemedewerker', 'Balletdanser', 'Ballroomdanser', 'Bandagist', 'Bandenmonteur', 'Bankbediende', 'Bankdirecteur', 'Banketbakker', 'Bankmakelaar', 'Bankwerker', 'Barbediende', 'Barhouder', 'Barman', 'Basketballer', 'Bassist', 'Beademingsassistent', 'Bedienaar', 'Bediener', 'Bedrijfsbrandweer', 'Bedrijfseconoom', 'Bedrijfshoofd', 'Bedrijfsjurist', 'Bedrijfskassier', 'Bedrijfskundige', 'Bedrijfsleermeester', 'Bedrijfsleider', 'Bedrijfsorganisatiedeskundige', 'Bedrijfspolitieagent', 'Bedrijfsrecherche', 'Bedrijfsverpleegkundige', 'Beeldapparatuurbediener', 'Beeldhouwer', 'Beenhouwer', 'Begeleider', 'Begrafenispersoneel', 'Begrotingscalculator', 'Behanger', 'Beheerder', 'Beiaardier', 'Bejaardenverzorgende', 'Belastingambtenaar', 'Belastingconsulent', 'Beleidsambtenaar', 'Beleidsmedewerker', 'Belichter', 'Bergingsduiker', 'Beroepskeuzeadviseur', 'Beroepsmilitair', 'Beroepssporter', 'Bestekschrijver', 'Besteksorteerder', 'Bestekzoeker', 'Bestuurder', 'Bestuurskundige', 'Betonmolenbaas', 'Betontimmerman', 'Betonstaalvlechter', 'Betonwerker', 'Beul', 'Beveiligingsapparatuur', 'Beveiligingsbeambte', 'Bewaarder', 'Bewaker', 'Bewegingstherapeut', 'Bezorger', 'Bibliothecaris', 'Bibliotheekassistent', 'Bierbrouwer', 'Bijenkorfvlechter', 'Bijenkweker', 'Bijkantoorhouder', 'Binderijpersoneel', 'Binnenhuisarchitect', 'Biochemicus', 'Biograaf', 'Bioloog', 'Bioscoopoperateur', 'Bitumineerder', 'Bloemist', 'Bloemkweker', 'Bloemschikker', 'Bloemsierkunstenaar', 'Bode', 'Boekbinder', 'Boekhouder', 'Boekillustrator', 'Boer', 'Bontkleermaker', 'Bontsnijder', 'Bookmaker', 'Boomchirurg', 'Boomkweker', 'Boomverzorger', 'Boordwerktuigkundige', 'Boormachinist', 'Boorpersoneel', 'Bootsman', 'Bosbaas', 'Bosbouwkundige', 'Boswachter', 'Botenbouwer', 'Bouwcalculator', 'Bouwhistoricus', 'Bouwkundig tekenaar', 'Bouwliftbediener', 'Bouwpromotor', 'Bouwopzichter', 'Bouwvakker', 'Bouwvaktimmerman', 'Brandmeester', 'Brandveiligheidsdeskundige', 'Brandwacht', 'Brandweerman', 'Brandweercommandant', 'Brandweeronderofficier', 'Breimachinesteller', 'Bromfietshersteller', 'Bronboorder', 'Buffetbediende', 'Buikspreker', 'Buitenbandenvulkaniseur', 'Buitendienstmedewerker', 'Burgemeester', 'Buschauffeur', 'Budgetcoach', 'Butler',
        'Cabaretier', 'Caféhouder', 'Cafetariamedewerker', 'Caissière', 'Calculator', 'Callgirl', 'Cameraman', 'Cardioloog', 'Cargadoor', 'Carrosseriebouwer', 'Cartograaf', 'Cellist', 'Chauffeur', 'Chef', 'Chemicus', 'Chiropodist', 'Chirurg', 'Chocolademaker', 'Chocolatier', 'Choreograaf', 'Cilindermaker', 'Cineast', 'Cipier', 'Circusartiest', 'Circusdirecteur', 'Civiel ingenieur', 'Classicus', 'Clown', 'Coach', 'Codeur', 'Collationist', 'Colporteur', 'Columnist', 'Combinatiefunctionaris', 'Commentator', 'Commissaris', 'Commissionair', 'Completeerder', 'Compliance officer', 'Componist', 'Computeroperator', 'Computerprogrammeur', 'Conciërge', 'Conducteur', 'Conservator', 'Constructeur', 'Constructiebankwerker', 'Constructiesamenbouwer', 'Constructieschilder', 'Consulent', 'Contactlensspecialist', 'Controleur', 'Controller', 'Coördinator', 'Copywriter', 'Counselor', 'Corrector', 'Correpetitor', 'Correspondent', 'Creatief therapeut', 'Crècheleidster', 'Criminoloog', 'Criticus', 'Croupeur', 'Croupier', 'Cultuurtechnicus', 'Curator', 'Cursuscoördinator', 'Cursusleider',
        'Dakdekker', 'Dakpannenvormer', 'Danser', 'Dansleraar', 'Database administrator', 'Debitant', 'Decaan', 'Declarant', 'Decoratieschilder', 'Decorschilder', 'Degelpersdrukker', 'Dekkledenmaker', 'Dekpersoneel', 'Delfstoffenbewerker', 'Demonstrateur', 'Dermatoloog', 'Deskundige', 'Detailhandelaar', 'Detective', 'Deurenzetter', 'Deurwaarder', 'Dichter', 'Dieetkok', 'Dienstbode', 'Dienstleider', 'Diepdrukgraveur', 'Dierenarts', 'Dierenartsassistent', 'Dierenasielhouder', 'Dierentrainer', 'Dierenverzorger', 'Diëtist', 'Diplomaat', 'Directeur', 'Directieassistent', 'Directiesecretaresse', 'Dirigent', 'Diskjockey', 'Districtschef', 'Districtsverpleegkundige', 'Docent', 'Documentalist', 'Documentencontroleur', 'Dokmeester', 'Doktersassistent', 'Dominee', 'Doodgraver', 'Douaneambtenaar', 'Dozenmaker', 'Draaier', 'Dramadocent', 'Dramatherapeut', 'Drogist', 'Drukker', 'Drukkerijbinder', 'Drukwerkvoorbereiders', 'Drummer', 'Duiker',
        'Econoom', 'Ecotechnisch manager', 'Edelmetaalbewerker', 'Edelsmid', 'Editor', 'EDP-auditor', 'Egyptoloog', 'Eindredacteur', 'Elektricien', 'Elektromonteur', 'Elektronicamonteur', 'Elektronicus', 'Elektrotechnicus', 'Encyclopedist', 'Enquêteur', 'Ergonoom', 'Ergotherapeut', 'Ertskundige', 'Essayeur', 'Essayist', 'Etaleur', 'Etnograaf', 'Etnoloog', 'Etymoloog', 'Evangelist', 'Examinator', 'Expediteur', 'Explantatiemedewerker',
        'Fabrikant', 'Facilitair Manager', 'Facturist', 'Farmacoloog', 'Fietsenmaker', 'Fijnbankwerker', 'Filiaalhouder', 'Filmer', 'Filmregisseur', 'Filosoof', 'Filterreiniger', 'Financieel analist', 'Fluitenbouwer', 'Fotograaf', 'Fotograveur', 'Fotolaborant', 'Fotolaboratoriumbediende', 'Fotolithograaf', 'Fotoredacteur', 'Framebouwer', 'Frezer', 'Fruitteler', 'Fysicus', 'Fysioloog', 'Fysiotherapeut',
        'Galvaniseur', 'Game Designer', 'Garagehouder', 'Garderobejuffrouw', 'Garnalenpeller', 'Gasleidinglegger', 'Gastvrouw', 'Gecommitteerde', 'Gedeputeerde', 'Gemeentesecretaris', 'Geneeskundige', 'Generaal', 'Geodeet', 'Geograaf', 'Geoloog', 'Gerant', 'Gerechtsdeurwaarder', 'Gereedschapsmaker', 'Gereedschapssmid', 'Geschiedkundige', 'Gevangenbewaarder', 'Gezaghebber', 'Gezagvoerder', 'Gezondheidsbegeleider', 'Gezondheidsfysicus', 'Gezondheidstechnicus', 'Gidsenschrijver', 'Gieterijtechnicus', 'Gietmachinebediener', 'Gigolo', 'Gipsverbandmeester', 'Gitarist', 'Glasblazer', 'Glasgraveur', 'Glasslijper', 'Glaszetter', 'Glazenhaler', 'Glazenmaker', 'Glazenwasser', 'Goochelaar', 'Goudsmid', 'Goudzoeker', 'Grafdelver', 'Graficus', 'Grafisch ontwerper', 'Grafoloog', 'Graveur', 'Griendwerker', 'Griffier', 'Grimeur', 'Groenteteler', 'Groepsleider', 'Groepsvervoer', 'Grondsteward', 'Grondstewardess', 'Grondwerker', 'Groothandelaar', 'Gymleraar', 'Gynaecoloog',
        'Handelaar', 'Handelscorrespondent', 'Handwever', 'Havenarbeider', 'Havenmeester', 'Heemraad', 'Heftruckchauffeur', 'Heibaas', 'Heier', 'Heilpedagoog', 'Heilsoldaat', 'Helpdeskmedewerker', 'Herbergier', 'Hijsmachinist', 'Historicus', 'Hoefsmid', 'Hoekman', 'Hofmeester', 'Homeopaat', 'Hondenfokker', 'Hondentoiletteerder', 'Hondentrimmer', 'Hoofd', 'Hoofdambtenaar', 'Hoofdcontroleur', 'Hoofdredacteur', 'Hoofduitvoerder', 'Hoofdverpleegkundige', 'Hoofdwerktuigkundige', 'Hoogleraar', 'Hoornist', 'Hoorspelregisseur', 'Horlogemaker', 'Hostess', 'Hotelier', 'Hotelmanager', 'Hotelportier', 'Houtbewerker', 'Houtmodelmaker', 'Houtsnijder', 'Houtvester', 'Houtwarensamensteller', 'Hovenier', 'Huidtherapeut', 'Huisarts', 'Huisbaas', 'Huisbewaarder', 'Huishoudhulp', 'Huishoudster', 'Huisschilder', 'Hulparbeider', 'Hulpautomonteur', 'Hulpkok', 'Hulpverkoper', 'Huurmoordenaar', 'Hydroloog',
        'IJscoman', 'IJzervlechter', 'Illusionist', 'Illustrator', 'Imam', 'Imker', 'Importeur', 'Impresario', 'Industrieel ontwerper', 'Ingenieur', 'Inkoper', 'Inrijger', 'Inseminator', 'Inspecteur', 'Installateur', 'Instructeur', 'Instrumentalist', 'Instrumentmaker', 'Interieurarchitect', 'Interieurverzorger', 'Interne accountant', 'Internist',
        'Jachtopzichter', 'Jager', 'Jongleur', 'Journalist', 'Justitieel Aanklager', 'Juwelier', 'Judoleraar',
        'Kaartenzetter', 'Kaasmaker', 'Kabelsplitser', 'Kabelwerker', 'Kanaalmeester', 'Kantonnier', 'Kantoorhulp', 'Kapitein', 'Kapper', 'Kappershulp', 'Kardinaal', 'Karteerder', 'Kartonnagewerker', 'Kassamedewerker', 'Kassier', 'Kelner', 'Keizer', 'Keramist', 'Kermisexploitant', 'Kernmaker', 'Kerstman', 'Ketelmetselaar', 'Keukenassistent', 'Keukenknecht', 'Keurder', 'Keuringsambtenaar', 'Keurmeester', 'Kinderverzorgende', 'Kleermaker', 'Kleidelver', 'Kleinhandelaar', 'Klerk', 'Kleuterleider', 'Klokkenmaker', 'Klompenmaker', 'Kloosterling', 'Kno-arts', 'Koerier', 'Koetsier', 'Kok', 'Komiek', 'Kompel', 'Kooiker', 'Kooiman', 'Koordirigent', 'Koperslager', 'Kostendeskundige', 'Koster', 'Kostprijscalculator', 'Kozijnenmaker', 'Kraamverzorgende', 'Kraamhulp', 'Kraanmachinist', 'Kredietanalist', 'Kredietbeoordelaar', 'Kruidendokter', 'Kruier', 'Kuiper', 'Kunstcriticus', 'Kunstenaar', 'Kunstschilder', 'Kustlichtwachter', 'Kwitantieloper',
        'Laadschopbestuurder', 'Laborant', 'Laboratoriumbediende', 'Lader', 'Ladingmeester', 'Lakei', 'Landarbeider', 'Landbouwer', 'Landbouwkundige', 'Landbouwmachinebestuurder', 'Landbouwmilieubeheer', 'Landbouwwerktuigenhersteller', 'Landmeetkundige', 'Landmeettechnicus', 'Landmeter', 'Landschapsarchitect', 'Landschapsbeheer', 'Lasinspecteur', 'Lasser', 'Lastechnicus', 'Lector', 'Ledertechnoloog', 'Lederwarenmaker', 'Leerbewerker', 'Leerkracht', 'Leeuwentemmer', 'Legionair', 'Leidekker', 'Leidinggevende', 'Leraar', 'Letterkundige', 'Leurder', 'Lichtdrukker', 'Lichtmatroos', 'Lijstenmaker', 'Linktrainer', 'Literator', 'Literatuurcriticus', 'Literatuuronderzoeker', 'Logopedist', 'Logotherapeut', 'Lokettist', 'Longfunctieassistent', 'Loodgieter', 'Loods', 'Loodschef', 'Loonadministrateur', 'Loopbaancoach', 'Losser', 'Luchtverkeersleider',
        'Maatnemer', 'Maatschappelijk medewerker', 'Maatschappelijk werker', 'Maatschoenmaker', 'Machine vouwer', 'Machinebankwerker', 'Machinebediende', 'Machinesteller', 'Manegehouder', 'Machinist', 'Magazijnbediende', 'Magazijnbeheerder', 'Magazijnknecht', 'Magnetiseur', 'Makelaar', 'Managementassistent', 'Manager', 'Mandenmaker', 'Mannequin', 'Manueel therapeut', 'Marconist', 'Marinier', 'Maritiem Officier', 'Marechaussee', 'Marketingadviseur', 'Marketingassistent', 'Marktkoopman', 'Masseur', 'Mathematicus', 'Matroos', 'Mattenmaker', 'Medewerker', 'Mediatrainer', 'Meester restauratiestukadoor', 'Meettechnicus', 'Melkboer', 'Metaalbewerker', 'Metaalbrander', 'Metaalbuiger', 'Metaalfrezer', 'Metaalgieter', 'Metaalkundige', 'Meteoroloog', 'Meteropnemer', 'Metselaar', 'Meubelbeeldhouwer', 'Meubelmaker', 'Meubelstoffeerder', 'Meubelstoffennaaister', 'Meubeltekenaar', 'Mijnbouwkundige', 'Middenstander', 'Mijnwerker', 'Milieudeskundige', 'Milieuhygiënist', 'Militair', 'Mimespeler', 'Min', 'Mineralenbewerker', 'Minister', 'Minister-president', 'Model', 'Modelmaker', 'Modelnaaister', 'Molenaar', 'Modeontwerper', 'Mondhygiënist', 'Monnik', 'Monteur', 'Mosselman', 'Motordemonteur', 'Motordrijver', 'Motormonteur', 'Mouldroomtechnicus', 'Munter', 'Muntmeester', 'Museumconservator', 'Museumgids', 'Museumhouder', 'Museummedewerker', 'Musicus', 'Muziekinstrumentenmaker', 'Muziekprogrammeur',
        'Naaister', 'Nachtwaker', 'Nagelstyliste', 'Nasynchronisatieregisseur', 'Natuurkundeleraar', 'Natuurkundige', 'Natuurwetenschapper', 'Navigator', 'Neonatoloog', 'Nettenboeter', 'Netwerkbeheerder', 'Neurochirurg', 'Neuroloog', 'Neurofysioloog', 'Nieuwslezer', 'Nijverheidsconsulent', 'Nko-arts', 'Nopster', 'Notaris', 'Nucleair geneeskundige',
        'Ober', 'Oberkelner', 'Objectleider', 'Oceanoloog', 'Octrooigemachtigde', 'Officier', 'Officier van justitie', 'Olieslager', 'Omroeper', 'Omsteller', 'Oncoloog', 'Onderhoudsloodgieter', 'Onderhoudsman', 'Onderhoudsmedewerker', 'Onderhoudsmonteur', 'Ondernemer', 'Onderofficier', 'Ondersteunende', 'Onderwaterwerker', 'Onderwijsassistent', 'Onderwijstechnicus', 'Onderwijzer', 'Onderzoeker', 'Onderzoeker in opleiding', 'Ontdekkingsreiziger', 'Ontmijner', 'Ontvlekker', 'Ontwerper', 'Oogarts', 'Operateur', 'Operatieassistent', 'Operational auditor', 'Operator', 'Opkoper', 'Opperman', 'Opsporingsambtenaar', 'Opsporingsingenieur', 'Opticien', 'Optometrist', 'Opvoedingsconsulent', 'Opvoedingsvoorlichter', 'Opzichter', 'Organist', 'Organizer', 'Ornitholoog', 'Orthodontist', 'Orthopedagoog', 'Orthopeed', 'Orthoptist', 'ORL-arts', 'Osteopaat', 'Ouvreuse', 'Ovenman',
        'Paardenfokker', 'Pakhuischef', 'Paleontoloog', 'Palfrenier', 'Pandjesbaas', 'Papierschepper', 'Papiervernisser', 'Parkeerwachter', 'Parketvloerenlegger', 'Parketwacht', 'Pastoor', 'Paswerker', 'Patholoog', 'Patholoog-anatoom', 'Patissier', 'Patroonmaker', 'Patroontekenaar', 'Pedagoog', 'Pedicure', 'Perronopzichter', 'Perser', 'Personeelsfunctionaris', 'Peuterwerker', 'Pianist', 'Pianostemmer', 'Piccolo', 'Pijpfitter', 'Pikeur', 'Piloot', 'Plaatwerker', 'Planner', 'Plantenteeltdeskundige', 'Plantsoenmedewerker', 'Plasticvormer', 'Pleitbezorger', 'Poelier', 'Poepruimer', 'Poetser', 'Podiatrist', 'Podoloog', 'Poffertjesbakker', 'Polisopmaker', 'Politicus', 'Politieagent', 'Politiecommissaris', 'Politie-inspecteur', 'Politiek analist', 'Pontschipper', 'Porder', 'Portier', 'Portretfotograaf', 'Postbediende', 'Postbesteller', 'Postbode', 'Postcommandant', 'Postexpediteur', 'Postsorteerder', 'Pottenbakker', 'Predikant', 'Premier', 'Presentator', 'President', 'Priester', 'Probleemanalist', 'Procesmanager', 'Procesoperator', 'Procureur', 'Procureur des Konings', 'Producer', 'Productenmaker', 'Productensorteerder', 'Productiebegeleider', 'Productieleider', 'Productiemedewerker', 'Productieplanner', 'Professor', 'Professioneel worstelaar', 'Programmamaker', 'Programmeur', 'Projectadviseur', 'Projectleider', 'Projectmanager', 'Projectontwikkelaar', 'Promovendus', 'Pruikenmaker', 'Psychiater', 'Psychologisch assistent', 'Psycholoog', 'Psychotherapeut', 'Psychomotorisch kindertherapeut', 'Purser', 'Putjesschepper',
        'Quarantaine-beambte', 'Quizmaster', 'Quantity surveyor',
        'Raadsman', 'Radarwaarnemer', 'Radiotherapeutisch laborant', 'Radiograaf', 'Radiolaborant', 'Radiotechnicus', 'Radiotelegrafist', 'Rangeerder', 'Recensent', 'Receptionist', 'Recherchekundige', 'Rechercheur', 'Rechtbanktekenaar', 'Rechter', 'Reclame-ontwerper', 'Reclameacquisiteur', 'Reclamedeskundige', 'Reclametekenaar', 'Redacteur', 'Redactiechef', 'Regisseur', 'Registeraccountant', 'Reiniger', 'Reinigingsdienstarbeider', 'Reisleider', 'Reisprogrammeur', 'Reisverkoper', 'Rekenaar', 'Rekwisietenmaker', 'Rentmeester', 'Reparateur', 'Ridder', 'Repetitor', 'Reproductietekenaar', 'Restauranthouder', 'Rietmeubelmaker', 'Rietwerker', 'Rijtuigspuiter', 'Rijwielhersteller', 'Rolluikentimmerman', 'Rondvaartgids', 'Röntgenoloog', 'Ruimtevaarder',
        'Samensteller', 'Saunahouder', 'Scenarioschrijver', 'Schaaldierenkweker', 'Schaaldierenpeller', 'Schaapherder', 'Schadecorrespondent', 'Schadetaxateur', 'Schakelbordwachter', 'Schaker', 'Schapenscheerder', 'Scharensliep', 'Scheepskapitein', 'Scheepskok', 'Scheepspurser', 'Scheepsschilder', 'Scheepstimmerman', 'Scheidsrechter', 'Scheikundige', 'Schillenboer', 'Schipper', 'Schoenfabrieksarbeider', 'Schoenhersteller', 'Schoenmaker', 'Schoolbegeleider', 'Schooldecaan', 'Schooldirecteur', 'Schoolinspecteur', 'Schoonheidsmasseur', 'Schoonheidsspecialiste', 'Schoonmaker', 'Schoorsteenveger', 'Schotter', 'Schrijftolk', 'Schrijver', 'Schuurder', 'Secretaresse', 'Secretariaatsmedewerker', 'Secretaris', 'Seismoloog', 'Seizoenarbeider', 'Seksuoloog', 'Selecteur', 'Sergeant', 'Seroloog', 'Serveerster', 'Setdresser', 'Sigarenmaker', 'Sinoloog', 'Sjorder', 'Sjouwer', 'Slachter', 'Slager', 'Slagwerker', 'Slijter', 'Sloper', 'Sluiswachter', 'Smeerder', 'Smelter', 'Smid', 'Snackbarbediende', 'Snackbarhouder', 'Snijder', 'Sociotherapeut', 'Softwareontwikkelaar', 'Soldaat', 'Soldeerder', 'Sommelier', 'Sondeerder', 'Songwriter', 'Souschef', 'Spoeler', 'Souffleur', 'Specialist', 'Spelersmakelaar', 'Speltherapeut', 'Spindoppenmonteur', 'Spion', 'Sportinstructeur', 'Stadsomroeper', 'Stadstimmerman', 'Stanser', 'Stationschef', 'Statisticus', 'Stedenbouwkundige', 'Steenbewerker', 'Steenfabrikant', 'Steenhouwer', 'Steenzetter', 'Steigerbouwer', 'Steigermaker', 'Stenotypist', 'Stereotypeur', 'Sterilisatieassistent', 'Stewardess', 'Stoelenmatter', 'Stoffeerder', 'Storingsmonteur', 'Straatverkoper', 'Strandjutter', 'Stratenmaker', 'Stripper', 'Stucwerker', 'Stukadoor', 'Stuurman', 'Stuwadoor', 'Stylist', 'Stypengalvaniseur', 'Surinamist', 'Systeemanalist', 'Systeembeheerder', 'Systeemontwerper', 'Systeemprogrammeur',
        'Takelaar', 'Tandarts', 'Tandartsassistente', 'Tandtechnicus', 'Tapper', 'Taxichauffeur', 'Taxidermist', 'Technicus', 'Technisch Oogheelkundig Assistent', 'Technisch tekenaar', 'Tegelzetter', 'Tekenaar', 'Tekstschrijver', 'Telecommunicatiemonteur', 'Telefoniste', 'Telegrafist', 'Televisieregisseur', 'Televisietechnicus', 'Telexist', 'Tennisser', 'Terrazzovloerenlegger', 'Terreinchef', 'Tester', 'Textieldrukker', 'Textiellaborant', 'Textielopmaker', 'Textielproductenmaker', 'Theateragent', 'Theatertechnicus', 'Therapeut', 'Timmerman', 'Tingieter', 'Toetsenist', 'Tolk', 'Toneelfigurant', 'Toneelmeester', 'Toneelregisseur', 'Toneelschrijver', 'Toneelspeler', 'Torenkraanmonteur', 'Totalisatormedewerker', 'Touringcarchauffeur', 'Touwslager', 'Traceur', 'Trainingsacteur', 'Traiteur', 'Trambestuurder', 'Transportplanner', 'Treinbestuurder', 'Treinconducteur', 'Treindienstleider', 'Treinduwer', 'Treinmachinist', 'Trekkerchauffeur', 'Tuiger', 'Tuinarchitect', 'Tuinder', 'Tuinman', 'Typiste',
        'Uitgever', 'Uitsmijter', 'Uitvaartbegeleider', 'Uitvinder', 'Uitvoerder', 'Uroloog', 'Uurwerkmaker',
        'Vakkenvuller', 'Valet', 'Veearts', 'Veehouder', 'Veeverloskundige', 'Veiligheidsbeambte', 'Veilinghouder', 'Verfspuiter', 'Vergaderstenograaf', 'Verhuizer', 'Verhuurder', 'Verkeersdienstsimulator', 'Verkeersinspecteur', 'Verkeerskundige', 'Verkeersleider', 'Verkeersonderzoeker', 'Verkeersplanoloog', 'Verkoopchef', 'Verkoopstyliste', 'Verkoper', 'Verloskundige', 'Verpleeghulp', 'Verpleegkundige', 'Verslaggever', 'Verspaner', 'Vertaler', 'Vertegenwoordiger', 'Vervoer', 'Vervoersinspecteur', 'Verwarmingsinstallateur', 'Verwarmingsmonteur', 'Verzekeringsagent', 'Verzekeringsdeskundige', 'Verzekeringsinspecteur', 'Verzorgende', 'Vicaris', 'Videoclipregisseur', 'Videojockey', 'Vioolbouwer', 'Violist', 'Vinoloog', 'Viroloog', 'Visagiste', 'Visfileerder', 'Visser', 'Vj', 'Vleeswarenmaker', 'Vlieger', 'Vliegtuigplaatwerker', 'Vliegtuigtimmerman', 'Vloerlegger', 'Voedingsmiddelentechnoloog', 'Voedingsvoorlichter', 'Voeger', 'Voertuigbekleder', 'Voetballer', 'Volder of Voller', 'Voorganger', 'Voorlichter', 'Voorlichtingsfunctionaris', 'Voorraadadministrateur', 'Voorzitter', 'Vormende', 'Vormenmaker', 'Vormer', 'Vormgever', 'Vrachtwagenchauffeur', 'Vuilnisman', 'Vulkanoloog', 'Vuurspuwer', 'Vuurtorenwachter', 'Vroedvrouw',
        'Waard', 'Waardijn', 'Waarzegger', 'Wachtcommandant', 'Wachter', 'Wachtmeester', 'Wagenmaker', 'Wasser', 'Wasserettehouder', 'Waterbouwkundige', 'Webdesigner', 'Weefmachinesteller', 'Weerkundige', 'Weerpresentator', 'Wegenbouwarbeider', 'Wegenbouwmachinist', 'Wegmarkeerder', 'Werkleider-dokmeester', 'Werktuigbouwkundige', 'Werktuigkundige', 'Werkvoorbereider', 'Wethouder', 'Wijkmeester', 'Wijnboer', 'Winkelbediende', 'Winkelier', 'Wiskundige', 'Wisselkassier', 'Wisselmaker', 'Woonbegeleider',
        'Xylofonist',
        'Yogaleraar',
        'Zaakwaarnemer', 'Zakenman', 'Zanger', 'Zeefdrukker', 'Zeeman', 'Zeepzieder', 'Zeilmaker', 'Zelfstandig ondernemer', 'Zetter', 'Ziekenhuisapotheker', 'Ziekenhuishygiënist', 'Ziekenverzorgende', 'Zilversmid', 'Zweminstructeur', 'Zoöloog',
    ];

    protected static $companySuffix = [
        'VOF', 'CV', 'LLP', 'BV', 'NV', 'IBC', 'CSL', 'EESV', 'SE', 'CV', 'Stichting', '& Zonen', '& Zn',
    ];

    protected static $product = [
        'Keuken', 'Media', 'Meubel', 'Sanitair', 'Elektronica', 'Schoenen',
        'Zorg', 'Muziek', 'Audio', 'Televisie', 'Pasta', 'Lunch', 'Boeken', 'Cadeau', 'Kunst', 'Tuin', 'Klus',
        'Video', 'Sieraden', 'Kook', 'Woon', 'Pizza', 'Mode', 'Haar', 'Kleding', 'Antiek', 'Interieur', 'Gadget',
        'Foto', 'Computer', 'Witgoed', 'Bruingoed', 'Broeken', 'Pakken', 'Maatpak', 'Fietsen', 'Speelgoed',
        'Barbecue', 'Sport', 'Fitness', 'Brillen', 'Bakkers', 'Drank', 'Zuivel', 'Pret', 'Vis', 'Wijn', 'Salade',
        'Terras', 'Borrel', 'Dieren', 'Aquaria', 'Verf', 'Behang', 'Tegel', 'Badkamer', 'Decoratie',
    ];

    protected static $type = [
        'Markt', 'Kampioen', 'Expert', 'Concurrent', 'Shop', 'Expert', 'Magazijn',
        'Dump', 'Store', 'Studio', 'Boulevard', 'Fabriek', 'Groep', 'Huis', 'Salon', 'Vakhuis', 'Winkel', 'Gigant',
        'Reus', 'Plaza', 'Park', 'Tuin',
    ];

    protected static $store = [
        'Boekhandel', 'Super', 'Tabakzaak', 'Schoenmaker', 'Kaashandel', 'Slagerij',
        'Smederij', 'Bakkerij', 'Bierbrouwer', 'Kapperszaak', 'Groenteboer', 'Bioboer', 'Fietsenmaker', 'Opticien',
        'Café', 'Garage',
    ];

    /**
     * @example 'Fietsenmaker Zijlemans'
     *
     * @return string
     */
    public function company()
    {
        $determinator = self::numberBetween(0, 2);

        switch ($determinator) {
            case 0:
                $companyName = static::randomElement(static::$product) . ' ' . static::randomElement(static::$type);

                break;

            case 1:
                $companyName = static::randomElement(static::$product) . strtolower(static::randomElement(static::$type));

                break;

            case 2:
                $companyName = static::randomElement(static::$store) . ' ' . $this->generator->lastName();

                break;
        }

        if (Miscellaneous::boolean()) {
            return $companyName . ' ' . static::randomElement(static::$companySuffix);
        }

        return $companyName;
    }

    /**
     * Belasting Toegevoegde Waarde (BTW) = VAT
     *
     * @example 'NL123456789B01'
     *
     * @see https://www.belastingdienst.nl/wps/wcm/connect/bldcontentnl/belastingdienst/zakelijk/btw/administratie_bijhouden/btw_nummers_controleren/uw_btw_nummer
     *
     * @return string VAT Number
     */
    public static function vat()
    {
        return sprintf('%s%d%s%d', 'NL', self::randomNumber(9, true), 'B', self::randomNumber(2, true));
    }

    /**
     * Alias dutch vat number format
     *
     * @return string
     */
    public static function btw()
    {
        return self::vat();
    }
}
