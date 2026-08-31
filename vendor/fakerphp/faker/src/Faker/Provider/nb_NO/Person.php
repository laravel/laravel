<?php

namespace Faker\Provider\nb_NO;

class Person extends \Faker\Provider\Person
{
    protected static $formats = [
        '{{firstName}} {{lastName}}',
        '{{firstName}} {{lastName}}',
        '{{firstName}} {{lastName}}',
        '{{firstName}} {{lastName}}',
        '{{firstName}} {{lastName}}',
        '{{firstName}} {{firstName}} {{lastName}}',
        '{{firstName}} {{firstName}} {{lastName}}',
        '{{firstName}} {{firstName}} {{lastName}}',
        '{{firstName}} {{lastName}} {{lastName}}',
        '{{firstName}} {{lastName}}-{{lastName}}',
        '{{firstName}} {{firstName}} {{lastName}}-{{lastName}}',
    ];

    /**
     * @var array Norwegian female first names
     *
     * @see http://spraakbanken.gu.se/statistik/lbfnamnalf.phtml
     */
    protected static $firstNameFemale = [
        'Abida', 'Abigail', 'Abir', 'Ada', 'Adel', 'Adelaine', 'Adele', 'Adelen', 'Adelin', 'Adelina', 'Adeline',
        'Adiba', 'Adila', 'Adile', 'Adina', 'Adine', 'Adisa', 'Admira', 'Adna', 'Adriana', 'Aferdita', 'Afshan',
        'Agata', 'Agate', 'Agathe', 'Agda', 'Agne', 'Agnes', 'Agnete', 'Agnethe', 'Agnieszka', 'Agny', 'Ahlam', 'Aicha',
        'Aida', 'Aija', 'Aileen', 'Aili', 'Ailin', 'Aime', 'Aimée', 'Aina', 'Aino', 'Aisha', 'Aiste', 'Ajna',
        'Albertine', 'Aldona', 'Aleena', 'Aleksandra', 'Alena', 'Alette', 'Alexandra', 'Alfhild', 'Alia', 'Alice',
        'Alida', 'Alina', 'Aline', 'Alis', 'Alise', 'Alison', 'Aliza', 'Allis', 'Alma', 'Almira', 'Alva', 'Alvhild',
        'Alvilde', 'Alyssa', 'Alaa', 'Amabel', 'Amal', 'Amalie', 'Amanda', 'Amber', 'Ambjørg', 'Amelia', 'Amelie',
        'Amie', 'Amila', 'Amina', 'Aminda', 'Amira', 'Amna', 'Amporn', 'Amra', 'Amy', 'An', 'Ana', 'Anab', 'Anabelle',
        'Anastasia', 'Anbjørg', 'Andrea', 'Andrine', 'Ane', 'Aneta', 'Anett', 'Anette', 'Angela', 'Angelica',
        'Angelina', 'Angunn', 'Anh', 'Anikken', 'Anila', 'Anine', 'Anisa', 'Anita', 'Anitra', 'Anja', 'Anke', 'Anlaug',
        'Ann', 'Anna', 'Annabel', 'Annabelle', 'Annbjørg', 'Anne', 'Anneke', 'Anneli', 'Annelise', 'Annemarie',
        'Annette', 'Annfrid', 'Anni', 'Annicken', 'Annie', 'Annika', 'Anniken', 'Annka', 'Annlaug', 'Annveig', 'Anny',
        'Antje', 'Antoinette', 'Anya', 'April', 'Ardita', 'Ariana', 'Ariel', 'Ariela', 'Arina', 'Arja', 'Arlene',
        'Arna', 'Arnbjørg', 'Arnhild', 'Arnlaug', 'Asbjørg', 'Asha', 'Aslaug', 'Asma', 'Asta', 'Astri', 'Astrid',
        'Athene', 'Atina', 'Aud', 'Audhild', 'Audny', 'Audrey', 'Aurora', 'Ayan', 'Ayla', 'Ayleen', 'Aylin', 'Ayse',
        'Azra', 'Babette', 'Barbara', 'Barbro', 'Beate', 'Beatrice', 'Belinda', 'Bella', 'Benedicte', 'Benedikte',
        'Benny', 'Bente', 'Bergdis', 'Bergfrid', 'Bergliot', 'Bergljot', 'Berit', 'Bernadette', 'Berta', 'Berthe',
        'Bertine', 'Beth', 'Betina', 'Betine', 'Betsy', 'Bettina', 'Betty', 'Betzy', 'Bianca', 'Bibbi', 'Bibi',
        'Birgit', 'Birgitta', 'Birgitte', 'Birte', 'Birthe', 'Bitten', 'Bjørg', 'Bjørghild', 'Blanca', 'Bodil',
        'Bolette', 'Bonnie', 'Borghild', 'Borgny', 'Bozena', 'Brigitte', 'Brit', 'Brita', 'Britt', 'Bryngjerd',
        'Brynhild', 'Bushra', 'Caisa', 'Camilla', 'Carina', 'Carita', 'Carla', 'Carlota', 'Carmen', 'Carol', 'Carola',
        'Carolina', 'Caroline', 'Cassandra', 'Catalina', 'Catarina', 'Cate', 'Catherina', 'Cathinka', 'Cathrine',
        'Catrine', 'Cecilia', 'Cecilie', 'Celine', 'Chanette', 'Chantal', 'Charlotte', 'Chi', 'Chloe', 'Christel',
        'Christiane', 'Christin', 'Christina', 'Christine', 'Cicilie', 'Cilje', 'Cindy', 'Clara', 'Claudia', 'Connie',
        'Conny', 'Constance', 'Cora', 'Cordelia', 'Corina', 'Cornelia', 'Cornelie', 'Cristel', 'Cristina', 'Cynthia',
        'Dagfrid', 'Dagmar', 'Dagne', 'Dagny', 'Dagrun', 'Daisy', 'Dana', 'Daniella', 'Danielle', 'Danuta', 'Daria',
        'Dea', 'Debora', 'Denise', 'Derya', 'Desirée', 'Diana', 'Diane', 'Dianne', 'Dilan', 'Dina', 'Dolores', 'Donna',
        'Dora', 'Dordi', 'Doreen', 'Doris', 'Dorit', 'Dorota', 'Dorothea', 'Dorte', 'Dorthe', 'Dorthea', 'Dragana',
        'Drude', 'Dung', 'Dyrhild', 'Dyveke', 'Ea', 'Ebba', 'Ece', 'Edda', 'Edel', 'Edit', 'Edith', 'Edle', 'Edna',
        'Edny', 'Edvarda', 'Edvine', 'Eileen', 'Eilin', 'Einy', 'Eir', 'Eira', 'Eirian', 'Eiril', 'Eirin', 'Eirunn',
        'Eivor', 'Ekaterina', 'Elaine', 'Elbjørg', 'Eldbjørg', 'Eldfrid', 'Eldrid', 'Elea', 'Eleanora', 'Elen', 'Elena',
        'Elenora', 'Elfi', 'Elfrid', 'Elfrida', 'Eli', 'Elia', 'Elida', 'Elin', 'Elina', 'Eline', 'Elinor', 'Elisa',
        'Elisabet', 'Elisabeth', 'Elise', 'Elizabeth', 'Ella', 'Elle', 'Ellen', 'Ellida', 'Ellinor', 'Ellisiv', 'Elma',
        'Elna', 'Elsa', 'Else', 'Elsebeth', 'Elsie', 'Elvine', 'Elvira', 'Elzbieta', 'Eman', 'Embla', 'Emelie', 'Emely',
        'Emilie', 'Emilija', 'Emily', 'Emina', 'Emma', 'Emmy', 'Ena', 'Enid', 'Enya', 'Erica', 'Erika', 'Erle', 'Erna',
        'Esma', 'Ester', 'Esther', 'Ethel', 'Eva', 'Evangeline', 'Evelina', 'Evelyn', 'Evi', 'Evie', 'Evita', 'Evy',
        'Eydis', 'Eyvor', 'Fadumo', 'Faisa', 'Faiza', 'Fanny', 'Farah', 'Farhiya', 'Fariba', 'Farida', 'Farzana',
        'Fatima', 'Fay', 'Felicia', 'Feliza', 'Fernanda', 'Filippa', 'Fiona', 'Florence', 'Fozia', 'Frances',
        'Franciska', 'Franziska', 'Frederika', 'Fredrikke', 'Freja', 'Frid', 'Frida', 'Fride', 'Frigg', 'Frøy', 'Frøya',
        'Frøydis', 'Gabrielle', 'Galina', 'Geirhild', 'Georgine', 'Gerd', 'Gerda', 'Gertrud', 'Ghazala', 'Gidske',
        'Gina', 'Gine', 'Gisela', 'Giske', 'Gisken', 'Gitte', 'Gjerd', 'Gjertine', 'Gjertrud', 'Gjøril', 'Gjørild',
        'Gloria', 'Grace', 'Greta', 'Grete', 'Grethe', 'Gro', 'Gry', 'Gudjørg', 'Gudlaug', 'Gudny', 'Gudrid', 'Gudrun',
        'Gudveig', 'Gul', 'Gulla', 'Gullborg', 'Gun', 'Gunbjørg', 'Gunda', 'Gunhild', 'Gunlaug', 'Gunn', 'Gunnbjørg',
        'Gunnel', 'Gunnhild', 'Gunnlaug', 'Gunnveig', 'Gunnvor', 'Gunnvår', 'Gunvor', 'Guri', 'Gurine', 'Guro', 'Gusta',
        'Gustava', 'Gyda', 'Gyri', 'Gyrid', 'Gøril', 'Hacer', 'Hafsa', 'Haldis', 'Halimo', 'Halina', 'Hallbjørg',
        'Halldis', 'Hallfrid', 'Hamida', 'Hana', 'Hanan', 'Hang', 'Hanna', 'Hanne', 'Hansine', 'Harda', 'Harriet',
        'Hatice', 'Hava', 'Hawa', 'Heather', 'Hedda', 'Hedvig', 'Hege', 'Heidi', 'Heidrun', 'Heike', 'Helen', 'Helena',
        'Helene', 'Helga', 'Helin', 'Hella', 'Helle', 'Helma', 'Hennie', 'Henny', 'Henriette', 'Herbjørg', 'Herborg',
        'Herdis', 'Herlaug', 'Hermine', 'Hiba', 'Hibo', 'Hilary', 'Hild', 'Hilde', 'Hildegunn', 'Hildur', 'Hillevi',
        'Hilma', 'Hina', 'Hjørdis', 'Hoa', 'Hong', 'Huda', 'Hue', 'Hulda', 'Huong', 'Hæge', 'Iben', 'Ida', 'Idun',
        'Idunn', 'Ifrah', 'Ildri', 'Ildrid', 'Ilona', 'Ilse', 'Iman', 'Ina', 'Indira', 'Ine', 'Ines', 'Inga', 'Inge',
        'Ingebjørg', 'Ingeborg', 'Ingegerd', 'Ingelin', 'Inger', 'Inger-Lise', 'Ingerid', 'Ingfrid', 'Inghild',
        'Ingjerd', 'Ingrid', 'Ingrun', 'Ingrunn', 'Ingunn', 'Ingveig', 'Ingvild', 'Irene', 'Iris', 'Irja', 'Irma',
        'Irmelin', 'Isa', 'Isabel', 'Isadora', 'Iselin', 'Ivana', 'Ivarda', 'Iwona', 'Izabela', 'Jacqueline', 'Jamila',
        'Jane', 'Janette', 'Janicke', 'Janken', 'Janne', 'Jarlfrid', 'Jaroslaw', 'Jasmin', 'Jean', 'Jeanette', 'Jeanne',
        'Jelena', 'Jenni', 'Jennifer', 'Jenny', 'Jessica', 'Jill', 'Jo', 'Jocelyn', 'Jofrid', 'Johanna', 'Johanne',
        'Jolanta', 'Jone', 'Jorid', 'Jorun', 'Jorunn', 'Josefine', 'Joyce', 'Judit', 'Judith', 'Julia', 'Julie', 'June',
        'Juni', 'Jytte', 'Jøran', 'Kai', 'Kaia', 'Kaisa', 'Kamila', 'Kamilla', 'Karen', 'Kari', 'Karianne', 'Karin',
        'Karina', 'Karine', 'Karita', 'Karoline', 'Katarina', 'Kate', 'Kathinka', 'Kathleen', 'Kathrine', 'Kaya',
        'Kelly', 'Kerstin', 'Khadija', 'Khadra', 'Khalida', 'Kim', 'Kine', 'Kirsten', 'Kirsti', 'Kitty', 'Kjellaug',
        'Kjellfrid', 'Kjellrun', 'Kjersti', 'Kjerstin', 'Klara', 'Konstanse', 'Kornelia', 'Kristi', 'Kristin',
        'Kristina', 'Kristine', 'Laila', 'Lana', 'Lara', 'Larissa', 'Laura', 'Lea', 'Leah', 'Leia', 'Leikny', 'Leila',
        'Lena', 'Lene', 'Leona', 'Leyla', 'Lidia', 'Lilian', 'Lill', 'Lillian', 'Lilly', 'Lina', 'Linda', 'Line',
        'Linea', 'Linh', 'Linn', 'Linnea', 'Lisa', 'Lisbeth', 'Lise', 'Liss', 'Liv', 'Live', 'Liza', 'Loma', 'Lone',
        'Lotta', 'Lotte', 'Louise', 'Lovise', 'Lucia', 'Ludmila', 'Luna', 'Lydia', 'Lykke', 'Mabel', 'Madeleine',
        'Magda', 'Magdalena', 'Magdalene', 'Magna', 'Magnhild', 'Magni', 'Mai', 'Maia', 'Maiken', 'Mailen', 'Maj',
        'Maja', 'Malene', 'Mali', 'Malin', 'Maren', 'Margareta', 'Margareth', 'Margarita', 'Marge', 'Margit', 'Margot',
        'Margrete', 'Margrethe', 'Marguerite', 'Margy', 'Mari', 'Maria', 'Marianne', 'Marie', 'Mariell', 'Marilyn',
        'Marina', 'Marion', 'Marit', 'Marlene', 'Marta', 'Marte', 'Martha', 'Martine', 'Mary', 'Mathea', 'Mathilde',
        'Maud', 'May', 'Maya', 'Maylen', 'Melanie', 'Melina', 'Melinda', 'Melissa', 'Melita', 'Mercedes', 'Merete',
        'Mette', 'Mia', 'Michaela', 'Mildrid', 'Milena', 'Milla', 'Mille', 'Mina', 'Mira', 'Miranda', 'Miriam', 'Moa',
        'Mona', 'Monica', 'Monika', 'Monja', 'Muna', 'Munira', 'My', 'Märta', 'Märtha', 'Møyfrid', 'Målfrid', 'Nada',
        'Nadia', 'Nadine', 'Nadja', 'Naima', 'Nancy', 'Nanna', 'Naomi', 'Nasreen', 'Nasrin', 'Natalie', 'Nathalie',
        'Nazia', 'Nelly', 'Ngoc', 'Nicole', 'Nikita', 'Niklas', 'Nikoline', 'Nimo', 'Nina', 'Noomi', 'Noor', 'Nor',
        'Nora', 'Norunn', 'Oda', 'Oddbjørg', 'Oddfrid', 'Oddlaug', 'Oddny', 'Oddrun', 'Oddveig', 'Oksana', 'Olaug',
        'Olga', 'Olina', 'Oline', 'Olivia', 'Othilie', 'Otilie', 'Palma', 'Pamela', 'Patricia', 'Paula', 'Pauline',
        'Peggy', 'Perly', 'Pernille', 'Petra', 'Phuong', 'Pia', 'Rachel', 'Ragna', 'Ragne', 'Ragnfrid', 'Ragnhild',
        'Ragni', 'Rahma', 'Rakel', 'Ramona', 'Randi', 'Rania', 'Ranja', 'Ranveig', 'Rebecca', 'Rebekka', 'Regine',
        'Reidun', 'Renate', 'Renée', 'Riborg', 'Rigmor', 'Rina', 'Rine', 'Rita', 'Ronja', 'Rosa', 'Rose', 'Rukhsana',
        'Runa', 'Rut', 'Ruth', 'Rønnaug', 'Saba', 'Sabine', 'Sabrina', 'Sadia', 'Safia', 'Saga', 'Sahra', 'Saima',
        'Sally', 'Salma', 'Samantha', 'Samina', 'Samira', 'Sana', 'Sandra', 'Sanja', 'Sanna', 'Sara', 'Sarah', 'Selina',
        'Selma', 'Serina', 'Shabana', 'Shahnaz', 'Shamim', 'Sharon', 'Shazia', 'Sheila', 'Shirin', 'Shirley', 'Shukri',
        'Sidsel', 'Sigfrid', 'Signe', 'Sigrid', 'Sigrun', 'Silje', 'Silvia', 'Simona', 'Simone', 'Sina', 'Siren',
        'Siri', 'Siril', 'Sissel', 'Siv', 'Snefrid', 'Sofia', 'Sofie', 'Sol', 'Solbjørg', 'Solfrid', 'Solgunn',
        'Sollaug', 'Solrun', 'Solveig', 'Solvor', 'Sonja', 'Sophie', 'Stella', 'Stina', 'Stine', 'Sumaya', 'Sunniva',
        'Susanne', 'Svanaug', 'Svanhild', 'Svetlana', 'Sygni', 'Sylvi', 'Synnøve', 'Sølvi', 'Tahira', 'Tale', 'Tamara',
        'Tania', 'Tanja', 'Tanya', 'Tara', 'Taran', 'Tatiana', 'Tea', 'Terese', 'Thale', 'Thanh', 'Thao', 'Thea',
        'Thelma', 'Theodora', 'Therese', 'Thi', 'Thilde', 'Thina', 'Thine', 'Thora', 'Thorbjørg', 'Thordis', 'Thorild',
        'Thu', 'Thuy', 'Thyra', 'Tia', 'Tiffany', 'Tilde', 'Tina', 'Tindra', 'Tine', 'Tiril', 'Toini', 'Tomine', 'Tone',
        'Tonje', 'Tora', 'Torbjørg', 'Tordis', 'Torgny', 'Torgun', 'Torgunn', 'Torhild', 'Tori', 'Toril', 'Torild',
        'Torlaug', 'Torny', 'Torunn', 'Tove', 'Toya', 'Trine', 'Trude', 'Turid', 'Tuva', 'Tuyet', 'Tyra', 'Ulla',
        'Ulrikke', 'Una', 'Undis', 'Une', 'Unn', 'Unni', 'Ursula', 'Uzma', 'Valentina', 'Vanessa', 'Vanja', 'Vebjørg',
        'Velaug', 'Venche', 'Vendela', 'Vera', 'Veronica', 'Veslemøy', 'Vibeke', 'Victoria', 'Vida', 'Vigdis',
        'Viktoria', 'Vilde', 'Vilhelmina', 'Vilja', 'Villemo', 'Vilma', 'Viola', 'Virginia', 'Vivi', 'Vivian', 'Vår',
        'Vårin', 'Wanda', 'Wanja', 'Wenche', 'Wendy', 'Wera', 'Weronika', 'Wibecke', 'Wibeke', 'Wigdis', 'Wilde',
        'Wilma', 'Winnie', 'Xuan', 'Yasmin', 'Ylva', 'Yngvild', 'Yvonne', 'Zahida', 'Zahra', 'Zainab', 'Zara',
        'Zuzanna', 'Øydis', 'Øyvor', 'Ågot', 'Aasa', 'Aase', 'Åse', 'Åshild', 'Aashild', 'Åslaug', 'Åsne', 'Åsta',
        'Aasta',
    ];

    /**
     * @var array Norwegian male first names
     *
     * @see http://www.mammanett.no/navn/leksikon/alle?field_name_sex_value=m&title=
     */
    protected static $firstNameMale = [
        'Abbas', 'Abdallah', 'Abdelaziz', 'Abdelkader', 'Abdi', 'Abdiasis', 'Abdifatah', 'Abdikadir', 'Abdinasir',
        'Abdirahim', 'Abdirahman', 'Abdirashid', 'Abdirizak', 'Abdul', 'Abdulahi', 'Abdulkadir', 'Abdullah',
        'Abdullahi', 'Abdulqadir', 'Abdurahman', 'Abed', 'Abel', 'Abid', 'Abraham', 'Absalon', 'Abu', 'Abubakar',
        'Adam', 'Adan', 'Adeel', 'Adelheid', 'Adelsten', 'Adem', 'Aden', 'Adham', 'Adi', 'Adil', 'Adis', 'Adler',
        'Admir', 'Adnan', 'Adrian', 'Afanasi', 'Afrim', 'Afshin', 'Agim', 'Agmund', 'Agnar', 'Agvald', 'Ahmad',
        'Ahmed', 'Aiden', 'Ailo', 'Aimar', 'Aime', 'Ajdin', 'Ajmal', 'Akam', 'Akbar', 'Akram', 'Aksel', 'Alain', 'Alan',
        'Alban', 'Albert', 'Alberto', 'Albin', 'Albrecht', 'Alejandro', 'Aleksander', 'Alen', 'Alessandro', 'Alex',
        'Alexander', 'Alexsander', 'Alf', 'Alfred', 'Algirdas', 'Algot', 'Ali', 'Allan', 'Almar', 'Almas', 'Almaz',
        'Almir', 'Altin', 'Alv', 'Alvald', 'Alvar', 'Alvaro', 'Alvfinn', 'Alvgeir', 'Alvin', 'Alvis', 'Alaa', 'Amadeus',
        'Aman', 'Amandus', 'Amanuel', 'Amar', 'Ambjørn', 'Ambros', 'Ambrosius', 'Amel', 'Amer', 'Amin', 'Amir', 'Ammar',
        'Amund', 'An', 'Anas', 'Anbjørn', 'Anders', 'Andi', 'Andor', 'André', 'Andreas', 'Andres', 'Andrew', 'Andris',
        'Andrzej', 'Andy', 'Anh', 'Anil', 'Annar', 'Anselm', 'Ansgar', 'Anskar', 'Anstein', 'Anthon', 'Anthony',
        'Anton', 'Antonio', 'Antonius', 'Anwar', 'Aram', 'Ard', 'Are', 'Arent', 'Ari', 'Arian', 'Ariel', 'Arild',
        'Arkadiusz', 'Armand', 'Armin', 'Arn', 'Arnald', 'Arnar', 'Arnbjørn', 'Arndor', 'Arne', 'Arnfinn', 'Arnfred',
        'Arngrim', 'Arnljot', 'Arnold', 'Arnolf', 'Arnor', 'Arnstein', 'Arnt', 'Arnulf', 'Arnulv', 'Arnvid', 'Aron',
        'Arslan', 'Arthur', 'Artur', 'Arun', 'Arunas', 'Arve', 'Arvid', 'Arvin', 'Asad', 'Asbjørn', 'Asgeir', 'Asif',
        'Ask', 'Askjel', 'Aslak', 'Aslan', 'Asle', 'Asmund', 'Astor', 'Atif', 'Atle', 'Attila', 'Audbjørn', 'Audfinn',
        'Audun', 'Augun', 'August', 'Augustin', 'Axel', 'Aziz', 'Bajram', 'Balder', 'Bao', 'Barry', 'Bart',
        'Bartlomiej', 'Bartol', 'Bastian', 'Bekim', 'Ben', 'Bendik', 'Benedikt', 'Bengt', 'Benjamin', 'Benny', 'Bent',
        'Berent', 'Berge', 'Berger', 'Bergfinn', 'Bergsvein', 'Berhane', 'Bernhard', 'Bernt', 'Bert', 'Bertel',
        'Bertil', 'Bertin', 'Bertold', 'Bertram', 'Bertrand', 'Besim', 'Besnik', 'Bilal', 'Bill', 'Birger', 'Birk',
        'Bjarne', 'Bjart', 'Bjarte', 'Bjartmar', 'Bjørge', 'Bjørk', 'Bjørn', 'Bjørnar', 'Bjørnulv', 'Blerim', 'Bo',
        'Bob', 'Bobby', 'Bodolv', 'Bodvar', 'Bogdan', 'Boguslaw', 'Borgar', 'Borger', 'Boris', 'Bork', 'Bosse',
        'Botolv', 'Boye', 'Brage', 'Brede', 'Bredo', 'Brian', 'Brigt', 'Brikt', 'Broder', 'Bror', 'Bruno', 'Bryan',
        'Brynar', 'Brynjar', 'Brynjulf', 'Brynjulv', 'Bujar', 'Burhan', 'Byrge', 'Børge', 'Børje', 'Børre', 'Bård',
        'Calvin', 'Carl', 'Carsten', 'Caspar', 'Casper', 'Caspian', 'Cato', 'Cay', 'Cecil', 'Cengiz', 'Cesar', 'Chan',
        'Chand', 'Charles', 'Charlie', 'Chi', 'Chris', 'Chrisander', 'Christen', 'Christer', 'Christian', 'Christoffer',
        'Christopher', 'Claes', 'Clas', 'Claude', 'Claudio', 'Claus', 'Clemens', 'Clement', 'Cliff', 'Clive', 'Colin',
        'Cong', 'Conrad', 'Constantin', 'Cornelis', 'Cornelius', 'Craig', 'Cristian', 'Cristoffer', 'Curt', 'Cyril',
        'Czeslaw', 'Dag', 'Dagfinn', 'Dagmøy', 'Damian', 'Damir', 'Dan', 'Dani', 'Danial', 'Daniel', 'Danish',
        'Dankert', 'Danny', 'Dario', 'Dariusz', 'Darko', 'Darren', 'Dat', 'David', 'Davy', 'Dean', 'Dejan', 'Denis',
        'Dennis', 'Derek', 'Derrick', 'Detlef', 'Diako', 'Dick', 'Didrik', 'Diego', 'Dieter', 'Dietmar', 'Dilan',
        'Dimitrios', 'Dines', 'Dino', 'Dirk', 'Ditlev', 'Ditmar', 'Dmitri', 'Dmitry', 'Dominic', 'Dominik', 'Don',
        'Donald', 'Douglas', 'Dragan', 'Dung', 'Dusan', 'Duy', 'Dylan', 'Dyre', 'Earl', 'Ebbe', 'Edd', 'Eddie', 'Eddy',
        'Edgar', 'Edgard', 'Edin', 'Edmond', 'Edmund', 'Edvard', 'Edvin', 'Edward', 'Edwin', 'Ege', 'Egil', 'Egon',
        'Egzon', 'Ehsan', 'Eigil', 'Eilef', 'Eilert', 'Eilev', 'Eilif', 'Eiliv', 'Einar', 'Eindride', 'Einvald',
        'Eirik', 'Eivind', 'Ekrem', 'Eldar', 'Eli', 'Elias', 'Elif', 'Ellef', 'Elleif', 'Elling', 'Elliot', 'Elmar',
        'Elmer', 'Elnar', 'Elton', 'Elvin', 'Elvis', 'Emanuel', 'Embret', 'Embrik', 'Emil', 'Emir', 'Emmanuel', 'Emre',
        'Emrik', 'Endre', 'Ener', 'Enes', 'Enevold', 'Engebret', 'Engel', 'Enis', 'Enok', 'Enrico', 'Enrique', 'Enver',
        'Erik', 'Erland', 'Erlend', 'Erling', 'Ernst', 'Ervin', 'Erwin', 'Esben', 'Eskil', 'Eskild', 'Espen', 'Esten',
        'Eugen', 'Evald', 'Even', 'Evert', 'Eyolf', 'Eystein', 'Eyvind', 'Fabian', 'Fahad', 'Faisal', 'Falk', 'Farah',
        'Farhad', 'Farhan', 'Farid', 'Fartein', 'Faruk', 'Farzan', 'Faste', 'Fastolv', 'Felix', 'Feliz', 'Ferdinand',
        'Filip', 'Finn', 'Finnbjørn', 'Finngard', 'Finngeir', 'Finnvard', 'Flamur', 'Flemming', 'Florian', 'Folke',
        'Francis', 'Frank', 'Frans', 'Frants', 'Frantz', 'Fred', 'Freddie', 'Freddy', 'Frede', 'Frederick', 'Frederik',
        'Fredrick', 'Fredrik', 'Fridleiv', 'Fridtjof', 'Frikk', 'Fritjof', 'Fritjov', 'Frits', 'Fritz', 'Frode',
        'Frøystein', 'Fuad', 'Fuat', 'Gabriel', 'Gard', 'Gardar', 'Gary', 'Gaute', 'Geir', 'Geirmund', 'Geirr',
        'Geirstein', 'Geirulv', 'Geoffrey', 'Georg', 'Gerald', 'Gerard', 'Gerd', 'Gerhard', 'Gerrit', 'Gerry', 'Gert',
        'Gholam', 'Ghulam', 'Gilbert', 'Gintaras', 'Gisle', 'Gjermund', 'Gjert', 'Gjøran', 'Gladys', 'Glen', 'Glenn',
        'Godtfred', 'Goran', 'Gordon', 'Gorm', 'Grant', 'Gregard', 'Greger', 'Gregor', 'Gregorius', 'Gregory', 'Grim',
        'Grimar', 'Grzegorz', 'Gudbrand', 'Gudkjell', 'Gudleiv', 'Gudmund', 'Gudvin', 'Gulbrand', 'Gullik', 'Gunder',
        'Gunleik', 'Gunnar', 'Gunne', 'Gunnerius', 'Gunnleif', 'Gunnleiv', 'Gunnstein', 'Gunnvald', 'Gunstein',
        'Gunvald', 'Gustav', 'Guttorm', 'Guy', 'Gynter', 'Gøran', 'Gösta', 'Hadi', 'Hagbart', 'Hai', 'Hakan', 'Hakon',
        'Haldor', 'Halfdan', 'Halfrid', 'Halgeir', 'Halil', 'Halldor', 'Hallgeir', 'Hallstein', 'Hallvard', 'Halvard',
        'Halvdan', 'Halvor', 'Hamid', 'Hamza', 'Hanad', 'Hans', 'Harald', 'Haroon', 'Harry', 'Hartvig', 'Hasan',
        'Hassan', 'Hasse', 'Hauk', 'Hector', 'Heike', 'Hein', 'Heine', 'Helge', 'Heljar', 'Helmer', 'Heming', 'Henki',
        'Henning', 'Henri', 'Henrik', 'Henry', 'Herbert', 'Herbjørn', 'Herleif', 'Herman', 'Hermann', 'Hermod',
        'Hermund', 'Herstein', 'Hieu', 'Hilbert', 'Hildegard', 'Hilmar', 'Hjalmar', 'Hoang', 'Hogne', 'Holger',
        'Hossein', 'Houssein', 'Hroar', 'Hubert', 'Hugo', 'Hung', 'Hussain', 'Hussein', 'Huu', 'Huy', 'Hågen', 'Håkon',
        'Haakon', 'Hårek', 'Håvald', 'Håvar', 'Håvard', 'Haavard', 'Ian', 'Iben', 'Ibrahim', 'Idar', 'Idris', 'Igor',
        'Ilir', 'Ilyas', 'Iman', 'Imbert', 'Immanuel', 'Imre', 'Ingar', 'Ingard', 'Inge', 'Ingebret', 'Ingebrigt',
        'Ingemar', 'Ingemund', 'Ingmar', 'Ingnar', 'Ingolf', 'Ingolv', 'Ingvald', 'Ingvar', 'Ingvard', 'Ingve',
        'Ioannis', 'Iqra', 'Irfan', 'Isa', 'Isach', 'Isak', 'Ismail', 'Ismet', 'Istvan', 'Ivan', 'Ivar', 'Iver', 'Jack',
        'Jacob', 'Jahn', 'Jakob', 'Jalal', 'Jamal', 'James', 'Jan', 'Jani', 'Jannik', 'Jarand', 'Jardar', 'Jarl',
        'Jarle', 'Jason', 'Jasper', 'Jean', 'Jeffrey', 'Jens', 'Jeppe', 'Jeremias', 'Jermund', 'Jerry', 'Jerzy',
        'Jesper', 'Jesus', 'Jetmund', 'Jim', 'Jimmy', 'Jiri', 'Jo', 'Joachim', 'Joakim', 'Joar', 'Joe', 'Joel',
        'Jogeir', 'Johan', 'Johannes', 'John', 'Johnny', 'Jokum', 'Jomar', 'Jon', 'Jonas', 'Jonatan', 'Jonathan',
        'Jone', 'Jonny', 'Joralf', 'Jorge', 'Jorulf', 'Josef', 'Joshua', 'Jostein', 'Josva', 'Juan', 'Juel', 'Jul',
        'Julian', 'Julius', 'Just', 'Jürgen', 'Jøran', 'Jørg', 'Jørgen', 'Jørn', 'Jørund', 'Kadir', 'Kai', 'Kalle',
        'Kamal', 'Kamran', 'Karel', 'Karelius', 'Karim', 'Karl', 'Karlo', 'Karstein', 'Karsten', 'Kasim', 'Kaspar',
        'Kasper', 'Kato', 'Kay', 'Kazimierz', 'Keith', 'Kemal', 'Ken', 'Kennet', 'Kenneth', 'Kent', 'Ketil', 'Kevin',
        'Khalid', 'Khalil', 'Kian', 'Kim', 'Kimberly', 'Kittil', 'Kjartan', 'Kjell', 'Kjerand', 'Kjetil', 'Kjølv',
        'Klas', 'Klaus', 'Klemet', 'Kleng', 'Knut', 'Kolbein', 'Kolbjørn', 'Kolfinn', 'Konrad', 'Konstantin',
        'Kornelius', 'Kris', 'Kristen', 'Krister', 'Kristian', 'Kristofer', 'Kristoffer', 'Ksenia', 'Kurt', 'Kyrre',
        'Kåre', 'Lage', 'Lambert', 'Lars', 'Lasse', 'Laurent', 'Laurentius', 'Lauri', 'Laurits', 'Lauritz', 'Lavrans',
        'Leander', 'Lech', 'Leidulf', 'Leidulv', 'Leif', 'Leik', 'Leiv', 'Lennart', 'Leo', 'Leon', 'Leonard',
        'Leonhard', 'Leopold', 'Levi', 'Levord', 'Lewis', 'Liam', 'Liban', 'Lidvar', 'Linus', 'Livar', 'Lloyd',
        'Lodvar', 'Lodve', 'Loke', 'Lorents', 'Lorentz', 'Lothar', 'Louis', 'Lucas', 'Ludolf', 'Ludvig', 'Ludvik',
        'Lukas', 'Lyder', 'Maciej', 'Mads', 'Magnar', 'Magne', 'Magnus', 'Mahad', 'Mahamed', 'Majid', 'Malcolm',
        'Malfred', 'Malte', 'Malthe', 'Malvin', 'Manfred', 'Manuel', 'Marc', 'Marcel', 'Marco', 'Marcus', 'Marenius',
        'Margido', 'Marius', 'Mark', 'Markus', 'Martin', 'Martinius', 'Martinus', 'Marvin', 'Mathias', 'Matias',
        'Mats', 'Matteus', 'Mattias', 'Mattis', 'Maurice', 'Maurits', 'Mauritz', 'Max', 'Maximilian', 'Mehmet',
        'Melkior', 'Melvin', 'Michael', 'Michel', 'Mikael', 'Mikkel', 'Mikkjel', 'Milan', 'Milo', 'Mindor', 'Minh',
        'Miroslaw', 'Mirsad', 'Mirza', 'Moa', 'Modolv', 'Modulf', 'Mogens', 'Mohammad', 'Mohamoud', 'Mons', 'Morgan',
        'Morits', 'Moritz', 'Morris', 'Morten', 'Mostafa', 'Muhamed', 'Muhammad', 'Muhammed', 'Murat', 'Mustafa',
        'Narve', 'Nasir', 'Nathaniel', 'Neil', 'Neri', 'Ngoc', 'Nicholas', 'Niclas', 'Nicolai', 'Nicolas', 'Niels',
        'Nikolai', 'Nikolas', 'Nikolaus', 'Nils', 'Njål', 'Noa', 'Noah', 'Noman', 'Noralf', 'Norbert', 'Nordahl',
        'Norma', 'Norman', 'Normann', 'Norodd', 'Norvald', 'Notto', 'Nup', 'Odd', 'Oddbjørn', 'Oddgeir', 'Oddleif',
        'Oddmund', 'Oddvar', 'Oddvin', 'Odin', 'Ola', 'Olaf', 'Olai', 'Olav', 'Ole', 'Oleg', 'Oliver', 'Oluf', 'Olve',
        'Omar', 'Ommund', 'Oscar', 'Oskar', 'Osman', 'Osmund', 'Osvald', 'Ottar', 'Otto', 'Ove', 'Pablo', 'Palle',
        'Palmer', 'Patrick', 'Patrik', 'Paul', 'Paulus', 'Peder', 'Pelle', 'Per', 'Perry', 'Peter', 'Petrus', 'Petter',
        'Philip', 'Piotr', 'Poul', 'Povel', 'Preben', 'Paal', 'Pål', 'Quoc', 'Rachid', 'Radoslaw', 'Rafael', 'Ragnar',
        'Ragnvald', 'Raimond', 'Rainer', 'Ralf', 'Ralph', 'Randolf', 'Randulf', 'Rashid', 'Rasmus', 'Ravn', 'Raymond',
        'Rayner', 'Reidar', 'Reidulf', 'Reidulv', 'Reier', 'Reimar', 'Rein', 'Reinert', 'Reinhard', 'Reinhold',
        'Reiulf', 'Remi', 'Remy', 'René', 'Reza', 'Richard', 'Rikard', 'Rino', 'Roald', 'Roar', 'Robert', 'Robin',
        'Rodney', 'Roger', 'Roland', 'Rolf', 'Rolv', 'Roman', 'Romund', 'Ronald', 'Ronnie', 'Ronny', 'Roy', 'Ruben',
        'Rudi', 'Rudolf', 'Runar', 'Rune', 'Ryan', 'Rådmund', 'Sabah', 'Said', 'Sainab', 'Sakarias', 'Salah', 'Salam',
        'Salmund', 'Salomon', 'Salve', 'Sam', 'Samir', 'Samson', 'Samuel', 'Sander', 'Scott', 'Sean', 'Sebastian',
        'Sebjørn', 'Selmar', 'Selmer', 'Sergio', 'Serkan', 'Seveld', 'Severin', 'Sevrin', 'Shahid', 'Sigbjørn',
        'Sigfred', 'Sigmund', 'Sigurd', 'Sigvald', 'Sigvard', 'Sigvart', 'Sigve', 'Silias', 'Simen', 'Simon', 'Sindre',
        'Sivert', 'Sjur', 'Skage', 'Skjalg', 'Skjold', 'Skule', 'Slawomir', 'Snorre', 'Sofus', 'Sondre', 'Stanislaw',
        'Stanley', 'Stefan', 'Steffen', 'Stein', 'Steinar', 'Steinbjørn', 'Steingrim', 'Steinkjell', 'Steinulv', 'Sten',
        'Stephan', 'Steve', 'Steven', 'Stian', 'Stig', 'Storm', 'Sture', 'Sturla', 'Sturle', 'Styrk', 'Stål', 'Ståle',
        'Sune', 'Svale', 'Svein', 'Sveinar', 'Sveinulf', 'Sveinung', 'Sven', 'Svend', 'Sverre', 'Syed', 'Sylfest',
        'Sylvester', 'Synne', 'Syver', 'Syvert', 'Sæbjørn', 'Sølve', 'Søren', 'Saad', 'Såmund', 'Tadeusz', 'Tage',
        'Tahir', 'Tallak', 'Talleiv', 'Tan', 'Tarald', 'Tariq', 'Tarje', 'Tarjei', 'Ted', 'Tedd', 'Teddy', 'Teis',
        'Tellef', 'Tengel', 'Teo', 'Teodor', 'Terje', 'Terjei', 'Terkel', 'Thai', 'Thanh', 'Theis', 'Theo', 'Theodor',
        'Thien', 'Thom', 'Thomas', 'Thor', 'Thoralf', 'Thorbjørn', 'Thord', 'Thore', 'Thorkild', 'Thorleif', 'Thormod',
        'Thorolf', 'Thorstein', 'Thorvald', 'Tidemann', 'Tim', 'Timothy', 'Tinius', 'Tinus', 'Tjerand', 'Tobben',
        'Tobias', 'Toivo', 'Tollak', 'Tollef', 'Tolleif', 'Tolleiv', 'Tom', 'Tomas', 'Tommy', 'Tony', 'Tor', 'Toralf',
        'Torben', 'Torbjørn', 'Tord', 'Tore', 'Torfinn', 'Torgard', 'Torgeir', 'Torger', 'Torgil', 'Torgils', 'Torgny',
        'Torgrim', 'Torje', 'Torjus', 'Torkel', 'Torkil', 'Torkild', 'Torkjel', 'Torleif', 'Torleik', 'Tormod',
        'Tormund', 'Torodd', 'Torolf', 'Torolv', 'Torry', 'Torstein', 'Torsten', 'Torvald', 'Tov', 'Trang', 'Tristan',
        'Tron', 'Trond', 'Troy', 'Truls', 'Trygg', 'Trygve', 'Trym', 'Tuan', 'Ture', 'Tønnes', 'Tørres', 'Ulf', 'Ulrik',
        'Ulv', 'Ulvar', 'Ulvgeir', 'Umar', 'Une', 'Uno', 'Usman', 'Vagn', 'Valborg', 'Valdemar', 'Valentin', 'Valter',
        'Vebjørn', 'Vegar', 'Vegard', 'Vegeir', 'Vemund', 'Verner', 'Vetle', 'Victor', 'Vidar', 'Vidkunn', 'Viet',
        'Vigbjørn', 'Viggo', 'Vigleik', 'Vigulv', 'Viking', 'Viktor', 'Vilfred', 'Vilgot', 'Vilhelm', 'Viljar',
        'Villads', 'Villum', 'Villy', 'Vincent', 'Vinjar', 'Vladimir', 'Vladislav', 'Vrål', 'Waldemar', 'Waleed',
        'Walid', 'Walter', 'Wayne', 'Werner', 'Wictor', 'Widar', 'Wieslaw', 'Wiggo', 'Wiktor', 'Wilfred', 'Wilhelm',
        'William', 'Willy', 'Wilmar', 'Wojciech', 'Wolfgang', 'Wollert', 'Yasin', 'Yasir', 'Yngvar', 'Yngve', 'Yonas',
        'Younes', 'Yousef', 'Yousuf', 'Yrjan', 'Zahid', 'Zakaria', 'Zbigniew', 'Zdzislaw', 'Zoran', 'Zygmunt',
        'Øistein', 'Øivind', 'Ørjan', 'Ørjar', 'Ørn', 'Ørnulf', 'Ørnulv', 'Ørvar', 'Østen', 'Øyolv', 'Øystein',
        'Øyvind', 'Ådne', 'Aage', 'Åge', 'Aake', 'Åke', 'Åmund', 'Åne', 'Ånen', 'Ånon', 'Ånund', 'Aaron', 'Åskjell',
        'Åsleif', 'Åsleik', 'Åsleiv', 'Åsmund', 'Aasmund', 'Åsulv', 'Åsvald', 'Åvar',
    ];

    /**
     * @var array Norwegian common last names (200 first from the link)
     *
     * @see http://www.ssb.no/befolkning/statistikker/navn/aar/2015-01-27?fane=tabell&sort=nummer&tabell=216066
     */
    protected static $lastName = [
        'Aas', 'Aase', 'Aasen', 'Abrahamsen', 'Ahmed', 'Ali', 'Amundsen', 'Andersen', 'Andersson', 'Andreassen',
        'Andresen', 'Antonsen', 'Arnesen', 'Aune', 'Bakke', 'Bakken', 'Berg', 'Berge', 'Berger', 'Berntsen',
        'Birkeland', 'Bjerke', 'Bjørnstad', 'Borge', 'Borgen', 'Breivik', 'Brekke', 'Bråten', 'Bråthen', 'Bye', 'Bø',
        'Bøe', 'Carlsen', 'Christensen', 'Christiansen', 'Dahl', 'Dahle', 'Dale', 'Dalen', 'Danielsen', 'Edvardsen',
        'Egeland', 'Eide', 'Eikeland', 'Eilertsen', 'Eliassen', 'Ellingsen', 'Engebretsen', 'Engen', 'Enger', 'Eriksen',
        'Evensen', 'Fjeld', 'Foss', 'Fosse', 'Fossum', 'Fredriksen', 'Gabrielsen', 'Gjerde', 'Gulbrandsen', 'Gundersen',
        'Gustavsen', 'Haaland', 'Haga', 'Hagen', 'Halvorsen', 'Hammer', 'Hamre', 'Hansen', 'Hanssen', 'Hassan', 'Haug',
        'Hauge', 'Haugen', 'Haugland', 'Helgesen', 'Helland', 'Helle', 'Henriksen', 'Hermansen', 'Hoel', 'Hoff',
        'Holen', 'Holm', 'Holmen', 'Hovland', 'Håland', 'Ingebrigtsen', 'Isaksen', 'Iversen', 'Jacobsen', 'Jakobsen',
        'Jansen', 'Jensen', 'Jenssen', 'Johannesen', 'Johannessen', 'Johansen', 'Johansson', 'Johnsen', 'Jonassen',
        'Jørgensen', 'Karlsen', 'Khan', 'Knudsen', 'Knutsen', 'Kolstad', 'Kristensen', 'Kristiansen', 'Kristoffersen',
        'Kvam', 'Kvamme', 'Langeland', 'Larsen', 'Lie', 'Lien', 'Lorentzen', 'Ludvigsen', 'Lund', 'Lunde', 'Løken',
        'Madsen', 'Magnussen', 'Martinsen', 'Mathisen', 'Mikalsen', 'Mikkelsen', 'Moe', 'Moen', 'Mohamed', 'Monsen',
        'Mortensen', 'Myhre', 'Myklebust', 'Møller', 'Nguyen', 'Nielsen', 'Nikolaisen', 'Nilsen', 'Nilssen', 'Nordby',
        'Nygaard', 'Nygård', 'Næss', 'Olsen', 'Ottesen', 'Paulsen', 'Pedersen', 'Petersen', 'Pettersen', 'Rasmussen',
        'Ruud', 'Rønning', 'Rønningen', 'Samuelsen', 'Sand', 'Sandberg', 'Sande', 'Sandnes', 'Sandvik', 'Simonsen',
        'Sivertsen', 'Sletten', 'Solbakken', 'Solberg', 'Solheim', 'Solli', 'Solvang', 'Steen', 'Stene', 'Stokke',
        'Strand', 'Strøm', 'Sunde', 'Sveen', 'Svendsen', 'Syversen', 'Sæther', 'Sætre', 'Sørensen', 'Sørlie', 'Tangen',
        'Teigen', 'Thomassen', 'Thoresen', 'Thorsen', 'Tollefsen', 'Torgersen', 'Torp', 'Tran', 'Tveit', 'Vik', 'Viken',
        'Wang', 'Wiik', 'Wilhelmsen', 'Wold', 'Ødegaard', 'Ødegård', 'Øien',
    ];

    /**
     * National Personal Identity number (personnummer)
     *
     * @see https://no.wikipedia.org/wiki/Personnummer
     *
     * @param string $gender Person::GENDER_MALE || Person::GENDER_FEMALE
     *
     * @return string on format DDMMYY#####
     */
    public function personalIdentityNumber(?\DateTime $birthdate = null, $gender = null)
    {
        if (!$birthdate) {
            $birthdate = \Faker\Provider\DateTime::dateTimeThisCentury();
        }
        $datePart = $birthdate->format('dmy');

        /**
         * @todo These number should be random based on birth year
         *
         * @see http://no.wikipedia.org/wiki/F%C3%B8dselsnummer
         */
        $randomDigits = (string) static::numerify('##');

        switch ($gender) {
            case static::GENDER_MALE:
                $genderDigit = static::randomElement([1, 3, 5, 7, 9]);

                break;

            case static::GENDER_FEMALE:
                $genderDigit = static::randomElement([0, 2, 4, 6, 8]);

                break;

            default:
                $genderDigit = (string) static::numerify('#');
        }

        $digits = $datePart . $randomDigits . $genderDigit;

        /**
         * @todo Calculate modulo 11 of $digits
         *
         * @see http://no.wikipedia.org/wiki/F%C3%B8dselsnummer
         */
        $checksum = (string) static::numerify('##');

        return $digits . $checksum;
    }
}
