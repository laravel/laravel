<?php

namespace Faker\Provider\en_GB;

class Person extends \Faker\Provider\Person
{
    protected static $maleNameFormats = [
        '{{firstNameMale}} {{lastName}}',
    ];

    protected static $femaleNameFormats = [
        '{{firstNameFemale}} {{lastName}}',
    ];

    /**
     * @see http://www.ons.gov.uk/ons/rel/vsob1/baby-names--england-and-wales/2013/index.html
     */
    protected static $firstNameMale = [
        'Aaron', 'Adam', 'Adrian', 'Aiden', 'Alan', 'Alex', 'Alexander', 'Alfie', 'Andrew', 'Andy', 'Anthony', 'Archie', 'Arthur',
        'Barry', 'Ben', 'Benjamin', 'Bradley', 'Brandon', 'Bruce',
        'Callum', 'Cameron', 'Charles', 'Charlie', 'Chris', 'Christian', 'Christopher', 'Colin', 'Connor', 'Craig',
        'Dale', 'Damien', 'Dan', 'Daniel', 'Darren', 'Dave', 'David', 'Dean', 'Dennis', 'Dominic', 'Duncan', 'Dylan',
        'Edward', 'Elliot', 'Elliott', 'Ethan',
        'Finley', 'Frank', 'Fred', 'Freddie',
        'Gary', 'Gavin', 'George', 'Gordon', 'Graham', 'Grant', 'Greg',
        'Harley', 'Harrison', 'Harry', 'Harvey', 'Henry',
        'Ian', 'Isaac',
        'Jack', 'Jackson', 'Jacob', 'Jake', 'James', 'Jamie', 'Jason', 'Jayden', 'Jeremy', 'Jim', 'Joe', 'Joel', 'John', 'Jonathan', 'Jordan', 'Joseph', 'Joshua',
        'Karl', 'Keith', 'Ken', 'Kevin', 'Kieran', 'Kyle',
        'Lee', 'Leo', 'Lewis', 'Liam', 'Logan', 'Louis', 'Lucas', 'Luke',
        'Mark', 'Martin', 'Mason', 'Matthew', 'Max', 'Michael', 'Mike', 'Mohammed', 'Muhammad',
        'Nathan', 'Neil', 'Nick', 'Noah',
        'Oliver', 'Oscar', 'Owen',
        'Patrick', 'Paul', 'Pete', 'Peter', 'Philip',
        'Quentin',
        'Ray', 'Reece', 'Riley', 'Rob', 'Ross', 'Ryan',
        'Samuel', 'Scott', 'Sean', 'Sebastian', 'Stefan', 'Stephen', 'Steve',
        'Theo', 'Thomas', 'Tim', 'Toby', 'Tom', 'Tony', 'Tyler',
        'Wayne', 'Will', 'William',
        'Zachary', 'Zach',
    ];

    protected static $firstNameFemale = [
        'Abbie', 'Abigail', 'Adele', 'Alexa', 'Alexandra', 'Alice', 'Alison', 'Amanda', 'Amber', 'Amelia', 'Amy', 'Anna', 'Ashley', 'Ava',
        'Beth', 'Bethany', 'Becky',
        'Caitlin', 'Candice', 'Carlie', 'Carmen', 'Carole', 'Caroline', 'Carrie', 'Charlotte', 'Chelsea', 'Chloe', 'Claire', 'Courtney',
        'Daisy', 'Danielle', 'Donna',
        'Eden', 'Eileen', 'Eleanor', 'Elizabeth', 'Ella', 'Ellie', 'Elsie', 'Emily', 'Emma', 'Erin', 'Eva', 'Evelyn', 'Evie',
        'Faye', 'Fiona', 'Florence', 'Francesca', 'Freya',
        'Georgia', 'Grace',
        'Hannah', 'Heather', 'Helen', 'Helena', 'Hollie', 'Holly',
        'Imogen', 'Isabel', 'Isabella', 'Isabelle', 'Isla', 'Isobel',
        'Jade', 'Jane', 'Jasmine', 'Jennifer', 'Jessica', 'Joanne', 'Jodie', 'Julia', 'Julie', 'Justine',
        'Karen', 'Karlie', 'Katie', 'Keeley', 'Kelly', 'Kimberly', 'Kirsten', 'Kirsty',
        'Laura', 'Lauren', 'Layla', 'Leah', 'Leanne', 'Lexi', 'Lilly', 'Lily', 'Linda', 'Lindsay', 'Lisa', 'Lizzie', 'Lola', 'Lucy',
        'Maisie', 'Mandy', 'Maria', 'Mary', 'Matilda', 'Megan', 'Melissa', 'Mia', 'Millie', 'Molly',
        'Naomi', 'Natalie', 'Natasha', 'Nicole', 'Nikki',
        'Olivia',
        'Patricia', 'Paula', 'Pauline', 'Phoebe', 'Poppy',
        'Rachel', 'Rebecca', 'Rosie', 'Rowena', 'Roxanne', 'Ruby', 'Ruth',
        'Sabrina', 'Sally', 'Samantha', 'Sarah', 'Sasha', 'Scarlett', 'Selina', 'Shannon', 'Sienna', 'Sofia', 'Sonia', 'Sophia', 'Sophie', 'Stacey', 'Stephanie', 'Suzanne', 'Summer',
        'Tanya', 'Tara', 'Teagan', 'Theresa', 'Tiffany', 'Tina', 'Tracy',
        'Vanessa', 'Vicky', 'Victoria',
        'Wendy',
        'Yasmine', 'Yvette', 'Yvonne',
        'Zoe',
    ];

    /**
     * @see http://surname.sofeminine.co.uk/w/surnames/most-common-surnames-in-great-britain.html
     */
    protected static $lastName = [
        'Adams', 'Allen', 'Anderson',
        'Bailey', 'Baker', 'Bell', 'Bennett', 'Brown', 'Butler',
        'Campbell', 'Carter', 'Chapman', 'Clark', 'Clarke', 'Collins', 'Cook', 'Cooper', 'Cox',
        'Davies', 'Davis',
        'Edwards', 'Ellis', 'Evans',
        'Fox',
        'Graham', 'Gray', 'Green', 'Griffiths',
        'Hall', 'Harris', 'Harrison', 'Hill', 'Holmes', 'Hughes', 'Hunt', 'Hunter',
        'Jackson', 'James', 'Johnson', 'Jones',
        'Kelly', 'Kennedy', 'Khan', 'King', 'Knight',
        'Lee', 'Lewis', 'Lloyd',
        'Marshall', 'Martin', 'Mason', 'Matthews', 'Miller', 'Mitchell', 'Moore', 'Morgan', 'Morris', 'Murphy', 'Murray',
        'Owen',
        'Palmer', 'Parker', 'Patel', 'Phillips', 'Powell', 'Price',
        'Reid', 'Reynolds', 'Richards', 'Richardson', 'Roberts', 'Robertson', 'Robinson', 'Rogers', 'Rose', 'Ross', 'Russell',
        'Saunders', 'Scott', 'Shaw', 'Simpson', 'Smith', 'Stevens', 'Stewart',
        'Taylor', 'Thomas', 'Thompson', 'Turner',
        'Walker', 'Walsh', 'Ward', 'Watson', 'White', 'Wilkinson', 'Williams', 'Wilson', 'Wood', 'Wright',
        'Young',
    ];

    /**
     * Generates a random National Insurance number.
     *
     * @see https://www.gov.uk/hmrc-internal-manuals/national-insurance-manual/nim39110
     */
    public function nino(): string
    {
        $prefixAllowList = ['A', 'B', 'C', 'E', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'W', 'X', 'Y', 'Z'];
        $prefixBanList = ['BG', 'GB', 'KN', 'NK', 'NT', 'TN', 'ZZ'];

        do {
            $prefix = implode('', self::randomElements($prefixAllowList, 2, true));
        } while (in_array($prefix, $prefixBanList, false) || $prefix[1] == 'O');

        $digits = static::numerify('######');
        $suffix = static::randomElement(['A', 'B', 'C', 'D']);

        return sprintf('%s%s%s', $prefix, $digits, $suffix);
    }
}
