<?php

namespace Faker\Provider\en_UG;

class Person extends \Faker\Provider\Person
{
    /**
     * It is very common in Uganda for people to arrange their names as
     * lastname(surname) firstname
     */
    protected static $maleNameFormats = [
        '{{firstNameMale}} {{lastName}}',
        '{{lastName}} {{firstNameMale}}',
        '{{firstNameMale}} {{lastNameMale}}',
        '{{lastNameMale}} {{firstNameMale}}',
    ];

    /**
     * It is very common in Uganda for people to arrange their names as
     * lastname(surname) firstname
     */
    protected static $femaleNameFormats = [
        '{{firstNameFemale}} {{lastName}}',
        '{{lastName}} {{firstNameFemale}}',
        '{{firstNameFemale}} {{lastNameFemale}}',
        '{{lastNameFemale}} {{firstNameFemale}}',
    ];

    protected static $firstNameMale = [
        'Aaron', 'Abdul', 'Abdullah', 'Abraham', 'Adam', 'Agustin', 'Ahmad', 'Ahmed', 'Akeem', 'Albert', 'Alex', 'Alfred', 'Ali', 'Allan', 'Allen', 'Alvin', 'Amani', 'Ambrose', 'Amos', 'Anderson', 'Andrew', 'Angel', 'Anthony', 'Arnold', 'Arthur', 'Austin',
        'Barnet', 'Barry', 'Ben', 'Benjamin', 'Bennie', 'Benny', 'Bernard', 'Berry', 'Berta', 'Bertha', 'Bill', 'Billy', 'Bobby', 'Boyd', 'Bradley', 'Brian', 'Bruce',
        'Caesar', 'Caleb', 'Carol', 'Cecil', 'Charles', 'Charlie', 'Chris', 'Christian', 'Christopher', 'Cleveland', 'Clifford', 'Clinton', 'Collin', 'Conrad',
        'Dan', 'Daren', 'Dave', 'David', 'Dax', 'Denis', 'Dennis', 'Derek', 'Derick', 'Derrick', 'Don', 'Donald', 'Douglas', 'Dylan',
        'Earnest', 'Eddie', 'Edgar', 'Edison', 'Edmond', 'Edmund', 'Edward', 'Edwin', 'Elias', 'Elijah', 'Elliot', 'Emanuel', 'Emmanuel', 'Eric', 'Ernest', 'Ethan', 'Eugene', 'Ezra',
        'Felix', 'Francis', 'Frank', 'Frankie', 'Fred',
        'Gaetano', 'Gaston', 'Gavin', 'Geoffrey', 'George', 'Gerald', 'Gideon', 'Gilbert', 'Glen', 'Godfrey', 'Graham', 'Gregory',
        'Hans', 'Harold', 'Henry', 'Herbert', 'Herman', 'Hillary', 'Howard',
        'Ian', 'Isaac', 'Isaiah', 'Ismael',
        'Jabari', 'Jack', 'Jackson', 'Jacob', 'Jamaal', 'Jamal', 'Jasper', 'Jayson', 'Jeff', 'Jeffery', 'Jeremy', 'Jimmy', 'Joe', 'Joel', 'Joesph', 'Johathan', 'John', 'Johnathan', 'Johnny', 'Johnson', 'Jonathan', 'Jordan', 'Joseph', 'Joshua', 'Julian', 'Julio', 'Julius', 'Junior',
        'Kaleb', 'Keith', 'Kelly', 'Kelvin', 'Ken', 'Kennedy', 'Kenneth', 'Kevin', 'Kim',
        'Lawrence', 'Lewis', 'Lincoln', 'Lloyd', 'Luis', 'Luther',
        'Mackenzie', 'Martin', 'Marvin', 'Mathew', 'Mathias', 'Matt', 'Maurice', 'Max', 'Maxwell', 'Mckenzie', 'Micheal', 'Mike', 'Milton', 'Mitchel', 'Mitchell', 'Mohamed', 'Mohammad', 'Mohammed', 'Morris', 'Moses', 'Muhammad', 'Myles',
        'Nasir', 'Nat', 'Nathan', 'Newton', 'Nicholas', 'Nick', 'Nicklaus', 'Nickolas', 'Nicolas', 'Noah', 'Norbert',
        'Oscar', 'Owen',
        'Patrick', 'Paul', 'Peter', 'Philip',
        'Rashad', 'Rasheed', 'Raul', 'Ray', 'Raymond', 'Reagan', 'Regan', 'Richard', 'Richie', 'Rick', 'Robb', 'Robbie', 'Robert', 'Robin', 'Roger', 'Rogers', 'Ronald', 'Rowland', 'Royal', 'Ryan',
        'Sam', 'Samson', 'Sean', 'Shawn', 'Sid', 'Sidney', 'Solomon', 'Steve', 'Stevie', 'Stewart', 'Stuart',
        'Taylor', 'Theodore', 'Thomas', 'Timmy', 'Timothy', 'Titus', 'Tom', 'Tony', 'Travis', 'Trevor', 'Troy', 'Trystan', 'Tyler', 'Tyson',
        'Victor', 'Vince', 'Vincent', 'Vinnie',
        'Walter', 'Warren', 'Wilford', 'Wilfred', 'Will', 'William', 'Willis', 'Willy', 'Wilson',
    ];

    protected static $firstNameFemale = [
        'Abigail', 'Adela', 'Adrianna', 'Adrienne', 'Aisha', 'Alice', 'Alisha', 'Alison', 'Amanda', 'Amelia', 'Amina', 'Amy', 'Anabel', 'Anabelle', 'Angela', 'Angelina', 'Angie', 'Anita', 'Anna', 'Annamarie', 'Anne', 'Annette', 'April', 'Arianna', 'Ariela', 'Asha', 'Ashley', 'Ashly', 'Audrey', 'Aurelia',
        'Barbara', 'Beatrice', 'Bella', 'Bernadette', 'Beth', 'Bethany', 'Bethel', 'Betsy', 'Bette', 'Bettie', 'Betty', 'Blanche', 'Bonita', 'Bonnie', 'Brenda', 'Bridget', 'Bridgette', 'Carissa', 'Carol', 'Carole', 'Carolina', 'Caroline', 'Carolyn', 'Carolyne', 'Catharine', 'Catherine', 'Cathrine', 'Cathryn', 'Cathy', 'Cecelia', 'Cecile', 'Cecilia', 'Charity', 'Charlotte', 'Chloe', 'Christina', 'Christine', 'Cindy', 'Claire', 'Clara', 'Clarissa', 'Claudine', 'Cristal', 'Crystal', 'Cynthia',
        'Dahlia', 'Daisy', 'Daniela', 'Daniella', 'Danielle', 'Daphne', 'Daphnee', 'Daphney', 'Darlene', 'Deborah', 'Destiny', 'Diana', 'Dianna', 'Dina', 'Dolly', 'Dolores', 'Donna', 'Dora', 'Dorothy', 'Dorris',
        'Edna', 'Edwina', 'Edyth', 'Elizabeth', 'Ella', 'Ellen', 'Elsa', 'Elsie', 'Emelia', 'Emilia', 'Emilie', 'Emily', 'Emma', 'Emmanuelle', 'Erica', 'Esta', 'Esther', 'Estella', 'Eunice', 'Eva', 'Eve', 'Eveline', 'Evelyn',
        'Fabiola', 'Fatima', 'Fiona', 'Flavia', 'Flo', 'Florence', 'Frances', 'Francesca', 'Francisca', 'Frida',
        'Gabriella', 'Gabrielle', 'Genevieve', 'Georgiana', 'Geraldine', 'Gertrude', 'Gladys', 'Gloria', 'Grace', 'Gracie',
        'Helen', 'Hellen', 'Hilda', 'Hillary', 'Hope',
        'Imelda', 'Isabel', 'Isabell', 'Isabella', 'Isabelle',
        'Jackie', 'Jacklyn', 'Jacky', 'Jaclyn', 'Jacquelyn', 'Jane', 'Janelle', 'Janet', 'Jaquelin', 'Jaqueline', 'Jenifer', 'Jennifer', 'Jessica', 'Joan', 'Josephine', 'Joy', 'Joyce', 'Juanita', 'Julia', 'Juliana', 'Julie', 'Juliet', 'Justine',
        'Katarina', 'Katherine', 'Katheryn', 'Katrina',
        'Laura', 'Leah', 'Leila', 'Lilian', 'Lillian', 'Lilly', 'Lina', 'Linda', 'Lisa', 'Lora', 'Loraine', 'Lucie', 'Lucy', 'Lulu', 'Lydia',
        'Mabel', 'Maggie', 'Mandy', 'Margaret', 'Margarete', 'Margret', 'Maria', 'Mariah', 'Mariam', 'Marian', 'Mariana', 'Mariane', 'Marianna', 'Marianne', 'Marie', 'Marilyne', 'Marina', 'Marion', 'Marjorie', 'Marjory', 'Marlene', 'Mary', 'Matilda', 'Maudie', 'Maureen', 'Maya', 'Meagan', 'Melisa', 'Melissa', 'Melody', 'Michele', 'Michelle', 'Minerva', 'Minnie', 'Miracle', 'Monica',
        'Nadia', 'Naomi', 'Naomie', 'Natalia', 'Natalie', 'Natasha', 'Nichole', 'Nicole', 'Nina', 'Nora',
        'Pamela', 'Patience', 'Patricia', 'Pauline', 'Pearl', 'Phoebe', 'Phyllis', 'Pink', 'Pinkie', 'Priscilla', 'Prudence',
        'Rachael', 'Rachel', 'Rebeca', 'Rebecca', 'Rhoda', 'Rita', 'Robyn', 'Rose', 'Rosemary', 'Ruth', 'Ruthe', 'Ruthie',
        'Sabina', 'Sabrina', 'Salma', 'Samantha', 'Sandra', 'Sandy', 'Sarah', 'Serena', 'Shakira', 'Sharon', 'Sheila', 'Sierra', 'Sonia', 'Sonya', 'Sophia', 'Sophie', 'Stacey', 'Stacy', 'Stella', 'Susan', 'Susana', 'Susanna', 'Susie', 'Suzanne', 'Sylvia',
        'Tabitha', 'Teresa', 'Tess', 'Theresa', 'Tia', 'Tiffany', 'Tina', 'Tracy', 'Trinity', 'Trisha', 'Trudie', 'Trycia',
        'Ursula',
        'Valentine', 'Valerie', 'Vanessa', 'Veronica', 'Vickie', 'Vicky', 'Victoria', 'Viola', 'Violet', 'Violette', 'Viva', 'Vivian', 'Viviane', 'Vivianne', 'Vivien', 'Vivienne',
        'Wanda', 'Wendy', 'Whitney', 'Wilma', 'Winifred',
        'Yvette', 'Yvonne',
        'Zita', 'Zoe',
    ];

    protected static $lastNameMale = [
        'Mubiru', 'Muwanguzi', 'Muwonge',
        'Nsamba',
        'Obol', 'Odeke', 'Okumu', 'Okumuringa', 'Opega', 'Opio', 'Orishaba', 'Osiki', 'Ouma',
        'Sekandi', 'Semande', 'Serwanga', 'Ssebatta', 'Ssebugulu', 'Ssebunya', 'Ssebuuma', 'Ssebyala', 'Ssegawa', 'Ssekabira', 'Ssekanjako', 'Ssekate', 'Ssekibuule', 'Ssekidde', 'Ssekiranda', 'Ssekitooleko', 'Ssekubulwa', 'Ssempija', 'Ssempungu', 'Ssemwezi', 'Ssendege', 'Ssenjovu', 'Ssenkaali', 'Ssentezza', 'Ssentongo', 'Sserubiri', 'Sseruyinda', 'Ssettende',
    ];

    protected static $lastNameFemale = [
        'Abol', 'Adeke', 'Aketch', 'Akoth', 'Akumu', 'Aol', 'Apega', 'Apio', 'Auma', 'Awori', 'Ayo',
        'Babirye',
        'Chandiru',
        'Dushime',
        'Kabatesi', 'Kabonesa', 'Kaitesi', 'Kakiiza', 'Kakuze', 'Kaliisa', 'Karungi', 'Katusiime', 'Kebirungi', 'Kyomi', 'Kyoshabire',
        'Mahoro', 'Murungi',
        'Nabaale', 'Nabaggala', 'Nabakooza', 'Nabaloga', 'Nabankema', 'Nabasirye', 'Nabaweesi', 'Nabayunga', 'Nabbona', 'Nabise', 'Nabukeera', 'Nabunya', 'Nabuufu', 'Nabuuso', 'Nabwami', 'Nakaayi', 'Nakabugo', 'Nakabuye', 'Nakafeero', 'Nakalanzi', 'Nakalunda', 'Nakasinde', 'Nakasolya', 'Nakasumba', 'Nakato', 'Nakaweesa', 'Nakazibwe', 'Nakiboneka', 'Nakidde', 'Nakigozi', 'Nakiguli', 'Nakimbugwe', 'Nakimuli', 'Nakinobe', 'Nakiridde', 'Nakisige', 'Nakitende', 'Nakiyemba', 'Nakku', 'Nakyagaba', 'Nakyanzi', 'Nalubuga', 'Nalubwama', 'Nalukwago', 'Naluyima', 'Nalweyiso', 'Nalwoga', 'Namaganda', 'Namagembe', 'Namatovu', 'Nambi', 'Nambogo', 'Nambooze', 'Nambuusi', 'Namenya', 'Namiiro', 'Namirembe', 'Nampemba', 'Nampijja', 'Namubiru', 'Namuddu', 'Namugenyi', 'Namugwanya', 'Namukwaya', 'Namuleme', 'Namulindwa', 'Namutebi', 'Nankindu', 'Nankinga', 'Nanteeza', 'Nantongo', 'Nanvule', 'Nanyanzi', 'Nanyombi', 'Nanyondo', 'Nanyonjo', 'Nassimwba', 'Nazziwa', 'Ndagire',
    ];

    protected static $lastName = [
        'Abayisenga', 'Agaba', 'Ahebwe', 'Aisu', 'Akankunda', 'Akankwasa', 'Akashaba', 'Akashabe', 'Ampumuza', 'Ankunda', 'Asasira', 'Asiimwe', 'Atuhe', 'Atuhire', 'Atukunda', 'Atukwase', 'Atwine', 'Aurishaba',
        'Badru', 'Baguma', 'Bakabulindi', 'Bamwiine', 'Barigye', 'Bbosa', 'Bisheko', 'Biyinzika', 'Bugala', 'Bukenya', 'Buyinza', 'Bwana', 'Byanyima', 'Byaruhanga',
        'Ddamulira',
        'Gamwera',
        'Ijaga', 'Isyagi',
        'Kaaya', 'Kabanda', 'Kabuubi', 'Kabuye', 'Kafeero', 'Kagambira', 'Kakooza', 'Kalumba', 'Kanshabe', 'Kansiime', 'Kanyesigye', 'Kareiga', 'Kasekende', 'Kasumba', 'Kateregga', 'Katusiime', 'Kawooya', 'Kawuki', 'Kayemba', 'Kazibwe', 'Kibirige', 'Kiconco', 'Kiganda', 'Kijjoba', 'Kirabira', 'Kirabo', 'Kirigwajjo', 'Kisitu', 'Kitovu', 'Kityamuwesi', 'Kivumbi', 'Kiwanuka', 'Kyambadde',
        'Lunyoro',
        'Mbabazi', 'Migisha', 'Mugisa', 'Mugisha', 'Muhwezi', 'Mukalazi', 'Mulalira', 'Munyagwa', 'Murungi', 'Mushabe', 'Musinguzi', 'Mutabuza', 'Muyambi', 'Mwesige', 'Mwesigye',
        'Nabasa', 'Nabimanya', 'Nankunda', 'Natukunda', 'Nayebare', 'Nimukunda', 'Ninsiima', 'Nkoojo', 'Nkurunungi', 'Nuwagaba', 'Nuwamanya', 'Nyeko',
        'Obol', 'Odeke', 'Okumu', 'Okumuringa', 'Opega', 'Orishaba', 'Osiki', 'Ouma',
        'Rubalema', 'Rusiimwa', 'Rwabyoma',
        'Tamale', 'Tendo', 'Tizikara', 'Tuhame', 'Tumusiime', 'Tumwebaze', 'Tumwesigye', 'Tumwiine', 'Turyasingura', 'Tusiime', 'Twasiima', 'Twesigomwe',
        'Wasswa', 'Wavamuno', 'Were',
    ];

    public function lastName($gender = null)
    {
        if ($gender === static::GENDER_MALE) {
            return static::lastNameMale();
        }

        if ($gender === static::GENDER_FEMALE) {
            return static::lastNameFemale();
        }

        return static::randomElement(static::$lastName);
    }

    public static function lastNameMale()
    {
        return static::randomElement(static::$lastNameMale);
    }

    public static function lastNameFemale()
    {
        return static::randomElement(static::$lastNameFemale);
    }
}
