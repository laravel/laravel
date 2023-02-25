<?php

namespace Faker\Provider\en_IN;

class Person extends \Faker\Provider\Person
{
    protected static $maleNameFormats = [
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{middleNameMale}} {{lastName}}',
        '{{firstNameMale}} {{middleNameMale}} {{lastName}}',
        '{{firstNameMale}} {{firstNameMale}} {{lastName}}',
    ];

    protected static $femaleNameFormats = [
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{firstNameMale}} {{lastName}}',
    ];

    /**
     * @see http://www.indiaonlinepages.com/babynames/
     */
    protected static $firstNameMale = [
        'Aayushman', 'Amrit', 'Anand', 'Abhinav', 'Anil', 'Animesh', 'Arpit', 'Akhil', 'Ajinkya', 'Aniruddh', 'Arun', 'Atul', 'Ajay', 'Abhishek', 'Aditya', 'Ajeet', 'Akshay', 'Arjun', 'Arvind', 'Aadil', 'Aadish', 'Amir', 'Aarif', 'Aatif', 'Abbas', 'Abdul', 'Aslam', 'Azhar', 'Anees', 'Alex', 'Albert',
        'Bahadur', 'Baldev', 'Baalkrishan', 'Balaji', 'Bharat', 'Bhola', 'Bijoy', 'Binod', 'Biren', 'Bishnu', 'Baber', 'Binoya', 'Brock',
        'Chitranjan', 'Chirag', 'Chinmay', 'Charandeep', 'Chand', 'Charlie',
        'Deep', 'Dinesh', 'Devendra', 'Deepesh', 'Dhiraj', 'Darpan', 'Dhanush', 'Daanish', 'David',
        'Ekbal', 'Ehsaan', 'Elias', 'Emran', 'Eddie',
        'Fakaruddin', 'Faisal', 'Faraz', 'Fardeen', 'Feroz',
        'Ganesh', 'Govind', 'Giaan', 'Ghanshyam', 'Gaurav', 'Gauransh', 'Gajendra', 'Gulzar', 'Ghalib',
        'Hari', 'Himesh', 'Hemendra', 'Hanuman', 'Hetan', 'Hrishikesh', 'Himanshu', 'Habib', 'Hassan', 'Harbhajan', 'Harpreet',
        'Ibrahim', 'Iqbal', 'Ishat',
        'Jatin', 'Jagat', 'Jagdish', 'Jaswant', 'Jawahar', 'Jamshed', 'Javed', 'Jobin', 'Jack', 'John',
        'Kartik', 'Koushtubh', 'Kirti', 'Kushal', 'Kailash', 'Kalyan', 'Krishna', 'Kamlesh', 'Kalpit', 'Kabeer', 'Karim',
        'Lalit', 'Lakshmi', 'Labeen',
        'Mohan', 'Mukund', 'Mohan', 'Mohit', 'Manish', 'Moti', 'Mowgli', 'Mohanlal', 'Mitesh', 'Manoj', 'Monin', 'Mahmood', 'Malik', 'Mehul', 'Mustafa', 'Manpreet', 'Mukul', 'Munaf', 'Marlo',
        'Nitin', 'Nayan', 'Naresh', 'Neerendra', 'Nirmal', 'Narayan', 'Nakul', 'Naval', 'Natwar', 'Naseer', 'Nazir', 'Nawab',
        'Parveen', 'Pravin', 'Pranab', 'Prabhat', 'Pradeep', 'Prasoon', 'Preet', 'Pranay', 'Parvez', 'Pirzada', 'Peter',
        'Omar', 'Obaid', 'Owais',
        'Qabeel', 'Qabool', 'Qadim',
        'Radhe', 'Radheshyam', 'Raj', 'Raju', 'Rajendra', 'Rajesh', 'Ram', 'Ratan', 'Ram Gopal', 'Rupesh', 'Rupal', 'Ramesh', 'Ricky', 'Rehman', 'Rahim', 'Rashid', 'Raghavan',
        'Somnath', 'Sushant', 'Samir', 'Sumit', 'Shashank', 'Sirish', 'Satish', 'Saurabh', 'Subhash', 'Suraj', 'Surya', 'Sahil', 'Sohail', 'Satishwar', 'Srinivasan', 'Sharad', 'Sai', 'Siddharth', 'Sid', 'Suresh',
        'Tarun', 'Tanay', 'Tushar', 'Tabeed', 'Taahid',
        'Umesh', 'Uday', 'Ujwal', 'Umar', 'Usman',
        'Vivek', 'Vijay', 'Vikrant', 'Vijayent', 'Vicky', 'Varun', 'Virat', 'Venkat',
        'Wahid', 'Wafiq', 'Wafa',
        'Yadu', 'Yadunandan', 'Yash', 'Yogesh',
        'Zaad', 'Zahir', 'Zeeshan',
    ];

    protected static $firstNameFemale = [
        'Aabha', 'Aarti', 'Aarushi', 'Aastha', 'Aayushi', 'Aditi', 'Afreen', 'Aisha', 'Aishwarya', 'Akanksha', 'Akhila', 'Alaknanda', 'Alka', 'Alpa', 'Anshu', 'Ambika', 'Ananya', 'Amrita', 'Amolika', 'Anjana', 'Ankita', 'Anshula', 'Anusha', 'Aruna', 'Astha', 'Avantika',
        'Babita', 'Basanti', 'Bagwati', 'Bhaagyasree', 'Bhairavi', 'Bhanupriya', 'Binita', 'Bimla',
        'Chameli', 'Charu', 'Chhavi', 'Chitra', 'Chhaya', 'Chandni',
        'Damini', 'Devika', 'Dipti', 'Divya', 'Drishti', 'Diya',
        'Esha',
        'Falguni', 'Farah', 'Fatima',
        'Gayatri', 'Geetanjali', 'Gowri', 'Gulab', 'Gunjan',
        'Heena', 'Heer', 'Hema', 'Himani', 'Hira', 'Hina',
        'Indrani', 'Isha', 'Indira',
        'Jagruti', 'Jasmin', 'Jayshree', 'Jiya', 'Juhi', 'Jyoti', 'Julie',
        'Kalpana', 'Kalyani', 'Kamini', 'Kasturi', 'Kiran', 'Komal', 'Kusum', 'Krishna', 'Kanika', 'Kasturba', 'Kunti', 'Kajal', 'Kajol', 'Kirti', 'Kim',
        'Lata', 'Lalita', 'Leela', 'Leelawati', 'Lakshmi', 'Laveena',
        'Madhu', 'Madhavi', 'Maya', 'Mayawati', 'Megha', 'Mona', 'Mridula', 'Mukti', 'Meghana', 'Manjari', 'Mukti', 'Mini', 'Munni', 'Monica',
        'Nagma', 'Naina', 'Nalini', 'Namita', 'Nancy', 'Nandini', 'Namita', 'Narmada', 'Neela', 'Neha', 'Nidhi', 'Nikita', 'Nilam', 'Nilima', 'Nishi', 'Nishita', 'Nupoor', 'Nutan', 'Nitika', 'Niyati', 'Nupur', 'Navami', 'Nishtha',
        'Padama', 'Padmini', 'Payal', 'Poonam', 'Prabha', 'Priyanka', 'Pushpa', 'Pooja', 'Prerna', 'Pamela', 'Pinky', 'Parminder', 'Preshita',
        'Radha', 'Radhika', 'Ragini', 'Rakhi', 'Richa', 'Riddhi', 'Ritika', 'Riya', 'Rohini', 'Roma', 'Ruchi', 'Rachel', 'Rita', 'Rosey', 'Rimi', 'Runjhun',
        'Sabina', 'Sameera', 'Sameedha', 'Sapna', 'Sara', 'Seema', 'Shanti', 'Sheetal', 'Shobha', 'Savita', 'Smriti', 'Sneha', 'Sona', 'Sunita', 'Supriya', 'Sushmita', 'Swati', 'Sweta', 'Shweta', 'Sukriti',
        'Tanuja', 'Tejaswani', 'Tulsi', 'Trishana', 'Teena',
        'Uma', 'Urmi', 'Urmila', 'Urvashi', 'Usha', 'Upasana',
        'Vineeta', 'Vimala', 'Veena', 'Vaishali',
        'Yamini', 'Yasmin',
        'Zeenat', 'Zara',
    ];

    /**
     * @see http://genealogy.familyeducation.com/browse/origin/indian
     */
    protected static $lastName = [
        'Acharya', 'Agarwal', 'Agate', 'Aggarwal', 'Agrawal', 'Ahluwalia', 'Ahuja', 'Amble', 'Amin', 'Anand', 'Andra', 'Anne', 'Anthony', 'Apte', 'Arora', 'Arya', 'Atwal', 'Aurora',
        'Babu', 'Badal', 'Badami', 'Bahl', 'Bahri', 'Bail', 'Bains', 'Bajaj', 'Bajwa', 'Bakshi', 'Bal', 'Bala', 'Balakrishnan', 'Balan', 'Balasubramanian', 'Balay', 'Bali', 'Bandi', 'Banerjee', 'Banik', 'Bansal', 'Barad', 'Baral', 'Baria', 'Barman', 'Basak', 'Bassi', 'Basu', 'Bath', 'Batra', 'Batta', 'Bava', 'Bawa', 'Bedi', 'Beharry', 'Behl', 'Ben', 'Bera', 'Bhagat', 'Bhakta', 'Bhalla', 'Bhandari', 'Bhardwaj', 'Bhargava', 'Bhasin', 'Bhat', 'Bhatia', 'Bhatnagar', 'Bhatt', 'Bhattacharyya', 'Bhatti', 'Bhavsar', 'Bir', 'Biswas', 'Biyani', 'Binnani', 'Boase', 'Bobal', 'Bora', 'Borah', 'Borde', 'Borra', 'Bose', 'Brahmbhatt', 'Brar', 'Buch', 'Bumb', 'Butala',
        'Chacko', 'Chad', 'Chada', 'Chadha', 'Chahal', 'Chakrabarti', 'Chakraborty', 'Chana', 'Chand', 'Chanda', 'Chander', 'Chandra', 'Chandran', 'Char', 'Chatterjee', 'Chaudhari', 'Chaudhary', 'Chaudhry', 'Chaudhuri', 'Chaudry', 'Chauhan', 'Chawla', 'Cheema', 'Cherian', 'Chhabra', 'Chia', 'Chohan', 'Chokshi', 'Chopra', 'Choudhary', 'Choudhry', 'Choudhury', 'Chowdhury', 'Comar', 'Contractor',
        'Dad', 'Dada', 'Dalal', 'Dani', 'Dar', 'Dara', 'Das', 'Dasgupta', 'Dash', 'Dass', 'Date', 'Datta', 'Dave', 'Dayal', 'De', 'Deep', 'Deo', 'Deol', 'Desai', 'Deshmukh', 'Deshpande', 'Devan', 'Devi', 'Dewan', 'Dey', 'Dhaliwal', 'Dhar', 'Dhawan', 'Dhillon', 'Dhingra', 'Dial', 'Din', 'Divan', 'Dixit', 'Doctor', 'Dodiya', 'Dora', 'Doshi', 'Dua', 'Dube', 'Dubey', 'Dugal', 'Dugar', 'Dutt', 'Dutta', 'D’Alia', 'Dyal',
        'Edwin',
        'Gaba', 'Gade', 'Gagrani', 'Gala', 'Gandhi', 'Ganesan', 'Ganesh', 'Ganguly', 'Gara', 'Garde', 'Garg', 'George', 'Gera', 'Ghose', 'Ghosh', 'Gill', 'Gobin', 'Goda', 'Goel', 'Gokhale', 'Gola', 'Gole', 'Golla', 'Gopal', 'Goswami', 'Gour', 'Goyal', 'Grewal', 'Grover', 'Guha', 'Gulati', 'Gupta',
        'Halder', 'Handa', 'Hans', 'Hari', 'Harjo', 'Hayer', 'Hayre', 'Hegde', 'Hora',
        'Inani', 'Issac', 'Iyengar', 'Iyer',
        'Jacob', 'Jaggi', 'Jain', 'Jani', 'Jayaraman', 'Jha', 'Jhaveri', 'Johal', 'Joshi',
        'Kabra', 'Kadakia', 'Kade', 'Kakar', 'Kala', 'Kale', 'Kalita', 'Kalla', 'Kamdar', 'Kanda', 'Kannan', 'Kant', 'Kapadia', 'Kapoor', 'Kapur', 'Kar', 'Kara', 'Karan', 'Kari', 'Karnik', 'Karpe', 'Kashyap', 'Kata', 'Kaul', 'Kaur', 'Keer', 'Khalsa', 'Khan', 'Khanna', 'Khare', 'Khatri', 'Khosla', 'Khurana', 'Kibe', 'Kohli', 'Konda', 'Korpal', 'Koshy', 'Kota', 'Kothari', 'Krish', 'Krishna', 'Krishnamurthy', 'Krishnan', 'Kulkarni', 'Kumar', 'Kumer', 'Kunda', 'Kurian', 'Kuruvilla',
        'Lachman', 'Lad', 'Lal', 'Lala', 'Lall', 'Lalla', 'Lanka', 'Lata', 'Lodi', 'Loke', 'Loyal', 'Luthra',
        'Madan', 'Magar', 'Mahabir', 'Mahadeo', 'Mahajan', 'Mahal', 'Maharaj', 'Maheshwari', 'Majumdar', 'Malhotra', 'Mall', 'Mallick', 'Malpani', 'Mammen', 'Mand', 'Manda', 'Mandal', 'Mander', 'Mane', 'Mangal', 'Mangat', 'Mani', 'Mann', 'Mannan', 'Manne', 'Maraj', 'Masih', 'Master', 'Mathai', 'Mathew', 'Mathur', 'Matthai', 'Meda', 'Mehan', 'Mehra', 'Mehrotra', 'Mehta', 'Meka', 'Memon', 'Menon', 'Merchant', 'Minhas', 'Mishra', 'Misra', 'Mistry', 'Mital', 'Mitra', 'Mittal', 'Mitter', 'Modi', 'Mody', 'Mogul', 'Mohabir', 'Mohan', 'Mohanty', 'Morar', 'More', 'Mukherjee', 'Mukhopadhyay', 'Muni', 'Munshi', 'Murthy', 'Murty', 'Mutti',
        'Nadig', 'Nadkarni', 'Nagar', 'Nagarajan', 'Nagi', 'Nagy', 'Naidu', 'Naik', 'Nair', 'Nanda', 'Narain', 'Narang', 'Narasimhan', 'Narayan', 'Narayanan', 'Narine', 'Naruka', 'Narula', 'Natarajan', 'Nath', 'Natt', 'Nawal', 'Nayak', 'Nayar', 'Nazareth', 'Nigam', 'Nori',
        'Oak', 'Om', 'Oommen', 'Oza',
        'Padmanabhan', 'Pai', 'Pal', 'Palan', 'Pall', 'Palla', 'Panchal', 'Pandey', 'Pandit', 'Pandya', 'Pant', 'Pardeshi', 'Parekh', 'Parikh', 'Parmar', 'Parmer', 'Parsa', 'Patel', 'Pathak', 'Patil', 'Patla', 'Pau', 'Peri', 'Persad', 'Persaud', 'Philip', 'Pillai', 'Pillay', 'Pingle', 'Prabhakar', 'Prabhu', 'Pradhan', 'Prakash', 'Prasad', 'Prashad', 'Puri', 'Purohit',
        'Radhakrishnan', 'Raghavan', 'Rai', 'Raj', 'Raja', 'Rajagopal', 'Rajagopalan', 'Rajan', 'Raju', 'Ram', 'Rama', 'Ramachandran', 'Ramakrishnan', 'Raman', 'Ramanathan', 'Ramaswamy', 'Ramesh', 'Ramkissoon', 'Ramnarine', 'Rampersad', 'Rampersaud', 'Ramroop', 'Ramson', 'Rana', 'Randhawa', 'Ranganathan', 'Rao', 'Rastogi', 'Ratta', 'Rattan', 'Ratti', 'Rau', 'Raval', 'Ravel', 'Ravi', 'Ray', 'Reddy', 'Rege', 'Rout', 'Roy',
        'Sabharwal', 'Sachar', 'Sachdev', 'Sachdeva', 'Sagar', 'Saha', 'Sahni', 'Sahota', 'Saini', 'Salvi', 'Sama', 'Sami', 'Sampath', 'Samra', 'Samuel', 'Sandal', 'Sandhu', 'Sane', 'Sangha', 'Sanghvi', 'Sani', 'Sankar', 'Sankaran', 'Sant', 'Saraf', 'Saran', 'Sarin', 'Sarkar', 'Sarma', 'Sarna', 'Sarraf', 'Sastry', 'Sathe', 'Savant', 'Sawhney', 'Saxena', 'Sehgal', 'Sekhon', 'Sem', 'Sen', 'Sengupta', 'Seshadri', 'Seth', 'Sethi', 'Setty', 'Sha', 'Shah', 'Shan', 'Shankar', 'Shanker', 'Sharaf', 'Sharma', 'Shenoy', 'Shere', 'Sheth', 'Shetty', 'Shroff', 'Shukla', 'Sibal', 'Sidhu', 'Sing', 'Singh', 'Singhal', 'Sinha', 'Sodhi', 'Solanki', 'Som', 'Soman', 'Somani', 'Sodhani', 'Soni', 'Sood', 'Sridhar', 'Srinivas', 'Srinivasan', 'Srivastava', 'Subramaniam', 'Subramanian', 'Sule', 'Sundaram', 'Sunder', 'Sur', 'Sura', 'Surana', 'Suresh', 'Suri', 'Swaminathan', 'Swamy',
        'Tailor', 'Tak', 'Talwar', 'Tandon', 'Taneja', 'Tank', 'Tara', 'Tata', 'Tella', 'Thaker', 'Thakkar', 'Thakur', 'Thaman', 'Thomas', 'Tiwari', 'Toor', 'Tripathi', 'Trivedi',
        'Upadhyay', 'Uppal', 'Usman',
        'Vaidya', 'Vala', 'Varghese', 'Varkey', 'Varma', 'Varty', 'Varughese', 'Vasa', 'Venkataraman', 'Venkatesh', 'Verma', 'Vig', 'Virk', 'Viswanathan', 'Vohra', 'Vora', 'Vyas',
        'Wable', 'Wadhwa', 'Wagle', 'Wali', 'Walia', 'Walla', 'Warrior', 'Wason',
        'Yadav', 'Yogi', 'Yohannan',
        'Zacharia', 'Zachariah',
    ];

    /**
     * @see http://www.indianchild.com/indian_middle_names.htm
     */
    protected static $middleNameMale = [
        'Dev', 'Chandra', 'Kumar', 'Lal', 'Prasad', 'Raj', 'Singh', 'Rao', 'Ram', 'Pratap', 'Bhai',
    ];

    /**
     * Return male middle name
     *
     * @example 'Kumar'
     *
     * @return string Middle name
     */
    public function middleNameMale()
    {
        return static::randomElement(static::$middleNameMale);
    }
}
