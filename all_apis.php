✅ Auth APIs
POST /auth/admin/login - Admin login
http://127.0.0.1:8000/api/auth/admin/login
{
    "id": 1,
    "name": "Super Admin",
    "email": "admin@example.com",
    "created_at": "2025-07-06T11:33:26.000000Z",
    "updated_at": "2025-07-06T11:33:26.000000Z"
}
POST /auth/student/login - Student login
http://127.0.0.1:8000/api/auth/student/login
{
    "token": "81|PBmmFDcXyusAA5UyBDacfvs9p6c9oGFxGwBgqLMVa5753a2d",
    "user": {
        "id": 13,
        "name": "Jiss Doe",
        "phone": "232323232",
        "email": null,
        "institute_id": "1",
        "department_id": "1",
        "registration_date": "2025-07-07 11:23:28",
        "last_login": "2025-07-12T06:49:39.099551Z",
        "test_status": "active",
        "created_at": "2025-07-07T11:23:28.000000Z",
        "updated_at": "2025-07-12T06:49:39.000000Z"
    },
    "expires_at": "2025-07-13T06:49:39.136942Z"
}
GET /auth/profile - Get user profile
http://127.0.0.1:8000/api/auth/profile
admin profile:
{
    "id": 1,
    "name": "Super Admin",
    "email": "admin@example.com",
    "created_at": "2025-07-06T11:33:26.000000Z",
    "updated_at": "2025-07-06T11:33:26.000000Z"
}
student portal
{
    "token": "81|PBmmFDcXyusAA5UyBDacfvs9p6c9oGFxGwBgqLMVa5753a2d",
    "user": {
        "id": 13,
        "name": "Jiss Doe",
        "phone": "232323232",
        "email": null,
        "institute_id": "1",
        "department_id": "1",
        "registration_date": "2025-07-07 11:23:28",
        "last_login": "2025-07-12T06:49:39.099551Z",
        "test_status": "active",
        "created_at": "2025-07-07T11:23:28.000000Z",
        "updated_at": "2025-07-12T06:49:39.000000Z"
    },
    "expires_at": "2025-07-13T06:49:39.136942Z"
}
POST /auth/logout - Logout
http://127.0.0.1:8000/api/auth/logout

✅ Institution Management APIs

GET /institutions?page={page} - Get paginated institutions
http://127.0.0.1:8000/api/institutions?page=1
response:
{"current_page":1,"data":[{"id":8,"name":"Calicut University 2","code":"CU","address":"Malapuram University","department_id":5,"department":{"id":5,"name":"Chemical Engineering","code":"CHE"}},{"id":3,"name":"California Institute of Technology","code":"CALTECH","address":"1200 E California Blvd, Pasadena, CA 91125, USA","department_id":1,"department":{"id":1,"name":"Computer Science","code":"CSE"}},{"id":5,"name":"Carnegie Mellon University","code":"CMU","address":"5000 Forbes Ave, Pittsburgh, PA 15213, USA","department_id":1,"department":{"id":1,"name":"Computer Science","code":"CSE"}},{"id":1,"name":"Massachusetts Institute of Technology","code":"MIT","address":"77 Massachusetts Ave, Cambridge, MA 02139, USA","department_id":1,"department":{"id":1,"name":"Computer Science","code":"CSE"}},{"id":2,"name":"Stanford University","code":"STAN","address":"450 Serra Mall, Stanford, CA 94305, USA","department_id":4,"department":{"id":4,"name":"Civil Engineering","code":"CE"}}],"first_page_url":"http:\/\/127.0.0.1:8000\/api\/institutions?page=1","from":1,"last_page":2,"last_page_url":"http:\/\/127.0.0.1:8000\/api\/institutions?page=2","links":[{"url":null,"label":"&laquo; Previous","active":false},{"url":"http:\/\/127.0.0.1:8000\/api\/institutions?page=1","label":"1","active":true},{"url":"http:\/\/127.0.0.1:8000\/api\/institutions?page=2","label":"2","active":false},{"url":"http:\/\/127.0.0.1:8000\/api\/institutions?page=2","label":"Next &raquo;","active":false}],"next_page_url":"http:\/\/127.0.0.1:8000\/api\/institutions?page=2","path":"http:\/\/127.0.0.1:8000\/api\/institutions","per_page":5,"prev_page_url":null,"to":5,"total":6}
GET /institutions/{id} - Get institution by ID
http://127.0.0.1:8000/api/institutions/1
response:
{"name":"Massachusetts Institute of University","code":"MIU","address":"77 Massachusetts Ave, Cambridge, MA 02139, USA","department_id":1,"updated_at":"2025-07-09T13:02:22.000000Z","created_at":"2025-07-09T13:02:22.000000Z","id":10}
POST /institutions - Create institution
http://127.0.0.1:8000/api/institutions
{"name":"Massachusetts Institute of University","code":"MIU","address":"77 Massachusetts Ave, Cambridge, MA 02139, USA","department_id":1}
response:
{"name":"Massachusetts Institute of University","code":"MIU","address":"77 Massachusetts Ave, Cambridge, MA 02139, USA","department_id":1,"updated_at":"2025-07-09T13:02:22.000000Z","created_at":"2025-07-09T13:02:22.000000Z","id":10}
PUT /institutions/{id} - Update institution
http://127.0.0.1:8000/api/institutions/1
input:
{"name":"Manchester Institute of Technology","code":"MITY","address":"77 Massachusetts Ave, Cambridge, MA 02139, USA","department_id":1}
response:
{"id":10,"name":"Manchester Institute of Technology","code":"MITY","address":"77 Massachusetts Ave, Cambridge, MA 02139, USA","department_id":1,"created_at":"2025-07-09T13:02:22.000000Z","updated_at":"2025-07-09T13:07:07.000000Z"}
DELETE /institutions/{id} - Delete institution
http://127.0.0.1:8000/api/institutions/1
{
    "message": "Institution deleted successfully"
}
http://127.0.0.1:8000/api/institutions/all
[
    {
        "id": 11,
        "name": "Amma University",
        "code": "AU",
        "address": "Amma's place",
        "department_id": 5,
        "department": {
            "id": 5,
            "name": "Chemical Engineering",
            "code": "CHE"
        }
    },
    {
        "id": 8,
        "name": "Calicut University 2",
        "code": "CU",
        "address": "Malapuram University",
        "department_id": 5,
        "department": {
            "id": 5,
            "name": "Chemical Engineering",
            "code": "CHE"
        }
    },
    {
        "id": 3,
        "name": "California Institute of Technology",
        "code": "CALTECH",
        "address": "1200 E California Blvd, Pasadena, CA 91125, USA",
        "department_id": 1,
        "department": {
            "id": 1,
            "name": "Computer Science",
            "code": "CSE"
        }
    },
    {
        "id": 5,
        "name": "Carnegie Mellon University",
        "code": "CMU",
        "address": "5000 Forbes Ave, Pittsburgh, PA 15213, USA",
        "department_id": 1,
        "department": {
            "id": 1,
            "name": "Computer Science",
            "code": "CSE"
        }
    },
    {
        "id": 10,
        "name": "Manchester Institute of Technology",
        "code": "MITY",
        "address": "77 Massachusetts Ave, Cambridge, MA 02139, USA",
        "department_id": 1,
        "department": {
            "id": 1,
            "name": "Computer Science",
            "code": "CSE"
        }
    },
    {
        "id": 1,
        "name": "Massachusetts Institute of Technology",
        "code": "MIT",
        "address": "77 Massachusetts Ave, Cambridge, MA 02139, USA",
        "department_id": 1,
        "department": {
            "id": 1,
            "name": "Computer Science",
            "code": "CSE"
        }
    },
    {
        "id": 2,
        "name": "Stanford University",
        "code": "STAN",
        "address": "450 Serra Mall, Stanford, CA 94305, USA",
        "department_id": 4,
        "department": {
            "id": 4,
            "name": "Civil Engineering",
            "code": "CE"
        }
    },
    {
        "id": 4,
        "name": "University of California, Berkeley Fort",
        "code": "UCB",
        "address": "Berkeley, CA 94720, USA",
        "department_id": 1,
        "department": {
            "id": 1,
            "name": "Computer Science",
            "code": "CSE"
        }
    }
]

✅ Student Management APIs
GET /students?page={page} - Get paginated students
http://127.0.0.1:8000/api/students?page=1
response:
{"data":{"current_page":1,"data":[{"id":1,"name":"Alice Johnson","phone":"1234567890","email":"alice@example.com","institute_id":1,"department_id":1,"registration_date":"2025-07-06 11:33:25","last_login":"2025-07-06 11:40:08","test_status":"active","created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-06T11:40:08.000000Z","department":{"id":1,"name":"Computer Science","code":"CSE","created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-06T11:33:25.000000Z"},"institute":{"id":1,"name":"Massachusetts Institute of Technology","code":"MIT","address":"77 Massachusetts Ave, Cambridge, MA 02139, USA","department_id":1,"created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-06T11:33:25.000000Z"}},{"id":2,"name":"Bob Williams","phone":"2345678901","email":"bob@example.com","institute_id":2,"department_id":2,"registration_date":"2025-06-21 11:33:25","last_login":"2025-07-04 11:33:25","test_status":"active","created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-06T11:33:25.000000Z","department":{"id":2,"name":"Electrical Engineering","code":"EE","created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-06T11:33:25.000000Z"},"institute":{"id":2,"name":"Stanford University","code":"STAN","address":"450 Serra Mall, Stanford, CA 94305, USA","department_id":4,"created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-07T12:13:27.000000Z"}},{"id":3,"name":"Charlie Brown","phone":"3456789012","email":"charlie@example.com","institute_id":3,"department_id":3,"registration_date":"2025-06-06 11:33:25","last_login":"2025-06-29 11:33:25","test_status":"inactive","created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-06T11:33:25.000000Z","department":{"id":3,"name":"Mechanical Engineering","code":"ME","created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-06T11:33:25.000000Z"},"institute":{"id":3,"name":"California Institute of Technology","code":"CALTECH","address":"1200 E California Blvd, Pasadena, CA 91125, USA","department_id":1,"created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-06T11:33:25.000000Z"}},{"id":4,"name":"Diana Prince","phone":"4567890123","email":"diana@example.com","institute_id":4,"department_id":4,"registration_date":"2025-06-26 11:33:25","last_login":"2025-07-05 11:33:25","test_status":"active","created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-06T11:33:25.000000Z","department":{"id":4,"name":"Civil Engineering","code":"CE","created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-06T11:33:25.000000Z"},"institute":{"id":4,"name":"University of California, Berkeley","code":"UCB","address":"Berkeley, CA 94720, USA","department_id":1,"created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-06T11:33:25.000000Z"}},{"id":5,"name":"Ethan Hunt","phone":"5678901234","email":"ethan@example.com","institute_id":5,"department_id":5,"registration_date":"2025-07-01 11:33:25","last_login":"2025-07-06 11:33:25","test_status":"active","created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-06T11:33:25.000000Z","department":{"id":5,"name":"Chemical Engineering","code":"CHE","created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-06T11:33:25.000000Z"},"institute":{"id":5,"name":"Carnegie Mellon University","code":"CMU","address":"5000 Forbes Ave, Pittsburgh, PA 15213, USA","department_id":1,"created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-06T11:33:25.000000Z"}}],"first_page_url":"http:\/\/127.0.0.1:8000\/api\/students?page=1","from":1,"last_page":3,"last_page_url":"http:\/\/127.0.0.1:8000\/api\/students?page=3","links":[{"url":null,"label":"&laquo; Previous","active":false},{"url":"http:\/\/127.0.0.1:8000\/api\/students?page=1","label":"1","active":true},{"url":"http:\/\/127.0.0.1:8000\/api\/students?page=2","label":"2","active":false},{"url":"http:\/\/127.0.0.1:8000\/api\/students?page=3","label":"3","active":false},{"url":"http:\/\/127.0.0.1:8000\/api\/students?page=2","label":"Next &raquo;","active":false}],"next_page_url":"http:\/\/127.0.0.1:8000\/api\/students?page=2","path":"http:\/\/127.0.0.1:8000\/api\/students","per_page":5,"prev_page_url":null,"to":5,"total":11}}
GET /students/{id} - Get student by ID
http://127.0.0.1:8000/api/students/1
response:
{"data":{"id":1,"name":"Alice Johnson","phone":"1234567890","email":"alice@example.com","institute_id":1,"department_id":1,"registration_date":"2025-07-06 11:33:25","last_login":"2025-07-06 11:40:08","test_status":"active","created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-06T11:40:08.000000Z"}}
POST /students - Create student
http://127.0.0.1:8000/api/students
input:
{"name":"Capton America","phone":"1238967890","email": "alexaa@example.com", "institute_id":3,"department_id":2}
response:
{"message":"Student created successfully","data":{"name":"Capton America","phone":"1238967890","email":"alexaa@example.com","institute_id":3,"department_id":2,"registration_date":"2025-07-09T13:24:12.754073Z","last_login":"2025-07-09T13:24:12.754087Z","updated_at":"2025-07-09T13:24:12.000000Z","created_at":"2025-07-09T13:24:12.000000Z","id":17,"institute":{"id":3,"name":"California Institute of Technology","code":"CALTECH","address":"1200 E California Blvd, Pasadena, CA 91125, USA","department_id":1,"created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-06T11:33:25.000000Z"},"department":{"id":2,"name":"Electrical Engineering","code":"EE","created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-06T11:33:25.000000Z"}}}
PUT /students/{id} - Update student
http://127.0.0.1:8000/api/students/1
input:
{"name":"Capton Marvel","phone":"1238967890","email": "alexaa@example.com", "institute_id":3,"department_id":2}
response:
{"data":{"id":17,"name":"Capton Marvel","phone":"1238967890","email":"alexaa@example.com","institute_id":3,"department_id":2,"registration_date":"2025-07-09 13:24:12","last_login":"2025-07-09T13:24:59.641695Z","test_status":null,"created_at":"2025-07-09T13:24:12.000000Z","updated_at":"2025-07-09T13:24:59.000000Z"}}
DELETE /students/{id} - Delete student
http://127.0.0.1:8000/api/students/1
response:
{"message":"Student deleted successfully."}

✅ Department APIs
GET /departments - Get all departments
http://127.0.0.1:8000/api/departments
[{"id":5,"name":"Chemical Engineering","code":"CHE"},{"id":4,"name":"Civil Engineering","code":"CE"},{"id":1,"name":"Computer Science","code":"CSE"},{"id":2,"name":"Electrical Engineering","code":"EE"},{"id":3,"name":"Mechanical Engineering","code":"ME"}]

✅ Test Management APIs

GET /tests - Get all tests
http://127.0.0.1:8000/api/tests?page=1
response:
{"data":{"current_page":1,"data":[{"id":1,"title":"Main Aptitude Test","duration":60,"total_questions":25,"status":"active","description":"Test your general knowledge and aptitude skills","passing_marks":50,"created_at":"2025-07-06T11:33:26.000000Z","updated_at":"2025-07-06T11:33:26.000000Z","questions":[{"id":1,"test_id":1,"question":"What is the next number in the sequence: 2, 4, 8, 16, ___?","options":"[\"24\",\"32\",\"30\",\"28\"]","correct_answer":"32","difficulty":"easy","category":null,"category_id":1,"created_at":"2025-07-06T11:33:26.000000Z","updated_at":"2025-07-06T11:33:26.000000Z"},{"id":2,"test_id":1,"question":"What is the value of \u03c0 (pi) to two decimal places?","options":"[\"3.14\",\"3.16\",\"3.12\",\"3.18\"]","correct_answer":"3.14","difficulty":"easy","category":null,"category_id":1,"created_at":"2025-07-06T11:33:26.000000Z","updated_at":"2025-07-06T11:33:26.000000Z"},{"id":3,"test_id":1,"question":"If all Bloops are Razzies and all Razzies are Lazzies, then all Bloops are definitely Lazzies.","options":"[\"True\",\"False\",\"Uncertain\",\"None of the above\"]","correct_answer":"True","difficulty":"medium","category":null,"category_id":2,"created_at":"2025-07-06T11:33:26.000000Z","updated_at":"2025-07-06T11:33:26.000000Z"},{"id":4,"test_id":1,"question":"Choose the correct sentence:","options":"[\"She don't like apples\",\"She doesn't likes apples\",\"She doesn't like apples\",\"She not like apples\"]","correct_answer":"She doesn't like apples","difficulty":"easy","category":null,"category_id":3,"created_at":"2025-07-06T11:33:26.000000Z","updated_at":"2025-07-06T11:33:26.000000Z"}]},{"id":2,"title":"Technical Assessment - Programming","duration":90,"total_questions":30,"status":"active","description":"Test your programming knowledge and problem-solving skills","passing_marks":60,"created_at":"2025-07-06T11:33:26.000000Z","updated_at":"2025-07-06T11:33:26.000000Z","questions":[{"id":5,"test_id":2,"question":"What is the time complexity of accessing an element in an array by index?","options":"[\"O(1)\",\"O(n)\",\"O(log n)\",\"O(n\\u00b2)\"]","correct_answer":"O(1)","difficulty":"medium","category":null,"category_id":4,"created_at":"2025-07-06T11:33:26.000000Z","updated_at":"2025-07-06T11:33:26.000000Z"},{"id":6,"test_id":2,"question":"Which of the following is NOT a CSS framework?","options":"[\"Bootstrap\",\"Tailwind\",\"React\",\"Foundation\"]","correct_answer":"React","difficulty":"easy","category":null,"category_id":5,"created_at":"2025-07-06T11:33:26.000000Z","updated_at":"2025-07-06T11:33:26.000000Z"},{"id":7,"test_id":2,"question":"What does the \"===\" operator do in JavaScript?","options":"[\"Compares values for equality with type conversion\",\"Assigns a value to a variable\",\"Compares values for equality without type conversion\",\"Checks if a variable is defined\"]","correct_answer":"Compares values for equality without type conversion","difficulty":"medium","category":null,"category_id":7,"created_at":"2025-07-06T11:33:26.000000Z","updated_at":"2025-07-06T11:33:26.000000Z"},{"id":8,"test_id":2,"question":"Which of the following is the correct way to start a PHP session?","options":"[\"start_session()\",\"session_begin()\",\"session_start()\",\"begin_session()\"]","correct_answer":"session_start()","difficulty":"easy","category":null,"category_id":8,"created_at":"2025-07-06T11:33:26.000000Z","updated_at":"2025-07-06T11:33:26.000000Z"},{"id":9,"test_id":2,"question":"Which SQL statement is used to update data in a database?","options":"[\"MODIFY\",\"UPDATE\",\"CHANGE\",\"ALTER\"]","correct_answer":"UPDATE","difficulty":"easy","category":null,"category_id":9,"created_at":"2025-07-06T11:33:26.000000Z","updated_at":"2025-07-06T11:33:26.000000Z"},{"id":10,"test_id":2,"question":"Which SQL keyword is used to sort the result set?","options":"[\"ORDER BY\",\"SORT BY\",\"GROUP BY\",\"ARRANGE BY\"]","correct_answer":"ORDER BY","difficulty":"easy","category":null,"category_id":9,"created_at":"2025-07-06T11:33:26.000000Z","updated_at":"2025-07-06T11:33:26.000000Z"}]},{"id":3,"title":"Logical Reasoning Test","duration":45,"total_questions":20,"status":"active","description":"Evaluate your logical thinking and problem-solving abilities","passing_marks":55,"created_at":"2025-07-06T11:33:26.000000Z","updated_at":"2025-07-06T11:33:26.000000Z","questions":[]},{"id":4,"title":"Mathematics Challenge","duration":75,"total_questions":25,"status":"active","description":"Test your mathematical skills and numerical ability","passing_marks":50,"created_at":"2025-07-06T11:33:26.000000Z","updated_at":"2025-07-06T11:33:26.000000Z","questions":[]},{"id":5,"title":"Web Development Quiz","duration":60,"total_questions":20,"status":"draft","description":"Coming soon: Test your web development knowledge","passing_marks":60,"created_at":"2025-07-06T11:33:26.000000Z","updated_at":"2025-07-06T11:33:26.000000Z","questions":[]}],"first_page_url":"http:\/\/127.0.0.1:8000\/api\/tests?page=1","from":1,"last_page":2,"last_page_url":"http:\/\/127.0.0.1:8000\/api\/tests?page=2","links":[{"url":null,"label":"&laquo; Previous","active":false},{"url":"http:\/\/127.0.0.1:8000\/api\/tests?page=1","label":"1","active":true},{"url":"http:\/\/127.0.0.1:8000\/api\/tests?page=2","label":"2","active":false},{"url":"http:\/\/127.0.0.1:8000\/api\/tests?page=2","label":"Next &raquo;","active":false}],"next_page_url":"http:\/\/127.0.0.1:8000\/api\/tests?page=2","path":"http:\/\/127.0.0.1:8000\/api\/tests","per_page":5,"prev_page_url":null,"to":5,"total":9}}
GET /student/tests - Get all active tests with attempted status for current student
http://127.0.0.1:8000/api/student/tests?page=1
response:
{"data":{"current_page":1,"data":[{"id":1,"title":"Main Aptitude Test","duration":60,"total_questions":25,"status":"active","attempted":true,"attempt_status":"in_progress"},{"id":2,"title":"Technical Assessment - Programming","duration":90,"total_questions":30,"status":"active","attempted":false,"attempt_status":null},{"id":3,"title":"Logical Reasoning Test","duration":45,"total_questions":20,"status":"active","attempted":false,"attempt_status":null},{"id":4,"title":"Mathematics Challenge","duration":75,"total_questions":25,"status":"active","attempted":false,"attempt_status":null},{"id":7,"title":"Language Proficiency - English","duration":40,"total_questions":30,"status":"active","attempted":false,"attempt_status":null}],"first_page_url":"http:\/\/127.0.0.1:8000\/api\/student\/tests?page=1","from":1,"last_page":2,"last_page_url":"http:\/\/127.0.0.1:8000\/api\/student\/tests?page=2","links":[{"url":null,"label":"&laquo; Previous","active":false},{"url":"http:\/\/127.0.0.1:8000\/api\/student\/tests?page=1","label":"1","active":true},{"url":"http:\/\/127.0.0.1:8000\/api\/student\/tests?page=2","label":"2","active":false},{"url":"http:\/\/127.0.0.1:8000\/api\/student\/tests?page=2","label":"Next &raquo;","active":false}],"next_page_url":"http:\/\/127.0.0.1:8000\/api\/student\/tests?page=2","path":"http:\/\/127.0.0.1:8000\/api\/student\/tests","per_page":5,"prev_page_url":null,"to":5,"total":7}}
GET /tests/{id} - Get test by ID
http://127.0.0.1:8000/api/tests/1
response:
{"id":1,"title":"Main Aptitude Test","duration":60,"total_questions":25,"status":"active","description":"Test your general knowledge and aptitude skills","passing_marks":50,"created_at":"2025-07-06T11:33:26.000000Z","updated_at":"2025-07-06T11:33:26.000000Z"}
POST /tests - Create test
http://127.0.0.1:8000/api/tests
input:
{"title":"Test 3","total_questions":10,"duration":60, "status": "active", "description": "Test examm", "passing_marks": 30}
response:
{"title":"Test 3","duration":60,"total_questions":10,"status":"active","description":"Test examm","passing_marks":30,"updated_at":"2025-07-09T13:39:11.000000Z","created_at":"2025-07-09T13:39:11.000000Z","id":9}
PUT /tests/{id} - Update test
http://127.0.0.1:8000/api/tests/1
input:
{"title":"Test 3","total_questions":12,"duration":60, "status": "active", "description": "Test examm", "passing_marks": 30}
response:
{"id":10,"title":"Test 3","duration":60,"total_questions":12,"status":"active","description":"Test examm","passing_marks":30,"created_at":"2025-07-09T13:39:46.000000Z","updated_at":"2025-07-09T13:40:13.000000Z"}
PUT /tests/{id} - Update test
http://127.0.0.1:8000/api/tests/1
input:
{"title":"Test 3","total_questions":12,"duration":60, "status": "active", "description": "Test examm", "passing_marks": 30}
response:
{"id":10,"title":"Test 3","duration":60,"total_questions":12,"status":"active","description":"Test examm","passing_marks":30,"created_at":"2025-07-09T13:39:46.000000Z","updated_at":"2025-07-09T13:40:13.000000Z"}
DELETE /tests/{id} - Delete test
http://127.0.0.1:8000/api/tests/1
response:
{"message":"Test deleted."}
POST /tests/{testId}/start - Start test
http://127.0.0.1:8000/api/tests/1/start
response:
{"data":{"id":9,"test_id":"2","started_at":"2025-07-10T12:03:26.000000Z","duration":90}}
POST /tests/{testId}/submit - Submit test
http://127.0.0.1:8000/api/tests/1/submit


✅ Question Management APIs
POST /questions/upload - Upload questions (file upload)
input:
{"file": "questions.pdf","test_id": 8}
response:
{"message":"Questions uploaded successfully","count":20,"questions":[{"test_id":"8","question":"2, 4, 6, 8, 10, __","options":{"A":"11","B":"12","C":"13","D":"14"},"correct_answer":"B","updated_at":"2025-07-09T14:58:46.000000Z","created_at":"2025-07-09T14:58:46.000000Z","id":31},{"test_id":"8","question":"58, 52, 46, 40, 34, __","options":{"A":"26","B":"28","C":"30","D":"32"},"correct_answer":"B","updated_at":"2025-07-09T14:58:46.000000Z","created_at":"2025-07-09T14:58:46.000000Z","id":32},{"test_id":"8","question":"40, 40, 47, 47, 54, __","options":{"A":"54","B":"59","C":"60","D":"61"},"correct_answer":"D","updated_at":"2025-07-09T14:58:46.000000Z","created_at":"2025-07-09T14:58:46.000000Z","id":33},{"test_id":"8","question":"544, 509, 474, 439, __","options":{"A":"404","B":"414","C":"420","D":"445"},"correct_answer":"A","updated_at":"2025-07-09T14:58:46.000000Z","created_at":"2025-07-09T14:58:46.000000Z","id":34},{"test_id":"8","question":"201, 202, 204, 207, __","options":{"A":"208","B":"209","C":"210","D":"211"},"correct_answer":"B","updated_at":"2025-07-09T14:58:46.000000Z","created_at":"2025-07-09T14:58:46.000000Z","id":35},{"test_id":"8","question":"8, 22, 8, 28, 8, __","options":{"A":"9","B":"32","C":"36","D":"40"},"correct_answer":"C","updated_at":"2025-07-09T14:58:46.000000Z","created_at":"2025-07-09T14:58:46.000000Z","id":36},{"test_id":"8","question":"80, 10, 70, 15, 60, __","options":{"A":"20","B":"25","C":"30","D":"35"},"correct_answer":"A","updated_at":"2025-07-09T14:58:46.000000Z","created_at":"2025-07-09T14:58:46.000000Z","id":37},{"test_id":"8","question":"36, 34, 30, 28, 24, __","options":{"A":"20","B":"22","C":"23","D":"26"},"correct_answer":"B","updated_at":"2025-07-09T14:58:46.000000Z","created_at":"2025-07-09T14:58:46.000000Z","id":38},{"test_id":"8","question":"22, 21, 23, 22, 24, 23, __","options":{"A":"22","B":"25","C":"26","D":"27"},"correct_answer":"A","updated_at":"2025-07-09T14:58:46.000000Z","created_at":"2025-07-09T14:58:46.000000Z","id":39},{"test_id":"8","question":"3, 4, 7, 8, 11, 12, __","options":{"A":"13","B":"15","C":"14","D":"16"},"correct_answer":"C","updated_at":"2025-07-09T14:58:46.000000Z","created_at":"2025-07-09T14:58:46.000000Z","id":40},{"test_id":"8","question":"31, 29, 24, 22, 17, __","options":{"A":"15","B":"14","C":"13","D":"12"},"correct_answer":"B","updated_at":"2025-07-09T14:58:46.000000Z","created_at":"2025-07-09T14:58:46.000000Z","id":41},{"test_id":"8","question":"21, 9, 21, 11, 21, 13, __","options":{"A":"14","B":"15","C":"16","D":"17"},"correct_answer":"A","updated_at":"2025-07-09T14:58:46.000000Z","created_at":"2025-07-09T14:58:46.000000Z","id":42},{"test_id":"8","question":"53, 53, 40, 40, 27, 27, __","options":{"A":"13","B":"14","C":"15","D":"16"},"correct_answer":"B","updated_at":"2025-07-09T14:58:46.000000Z","created_at":"2025-07-09T14:58:46.000000Z","id":43},{"test_id":"8","question":"2, 6, 18, 54, __","options":{"A":"108","B":"162","C":"216","D":"324"},"correct_answer":"C","updated_at":"2025-07-09T14:58:46.000000Z","created_at":"2025-07-09T14:58:46.000000Z","id":44},{"test_id":"8","question":"1,000, 200, 40, __","options":{"A":"8","B":"16","C":"24","D":"32"},"correct_answer":"A","updated_at":"2025-07-09T14:58:46.000000Z","created_at":"2025-07-09T14:58:46.000000Z","id":45},{"test_id":"8","question":"7, 10, 8, 11, 9, 12, __","options":{"A":"11","B":"10","C":"13","D":"14"},"correct_answer":"B","updated_at":"2025-07-09T14:58:46.000000Z","created_at":"2025-07-09T14:58:46.000000Z","id":46},{"test_id":"8","question":"14, 28, 20, 40, 32, 64, __","options":{"A":"48","B":"56","C":"72","D":"80"},"correct_answer":"B","updated_at":"2025-07-09T14:58:46.000000Z","created_at":"2025-07-09T14:58:46.000000Z","id":47},{"test_id":"8","question":"1.5, 2.3, 3.1, 3.9, __","options":{"A":"4.3","B":"4.7","C":"5.1","D":"5.5"},"correct_answer":"B","updated_at":"2025-07-09T14:58:46.000000Z","created_at":"2025-07-09T14:58:46.000000Z","id":48},{"test_id":"8","question":"5.2, 4.8, 4.4, 4, __","options":{"A":"3.2","B":"3.4","C":"3.6","D":"3.8"},"correct_answer":"C","updated_at":"2025-07-09T14:58:46.000000Z","created_at":"2025-07-09T14:58:46.000000Z","id":49},{"test_id":"8","question":"2, 1, 1\/2, 1\/4, __","options":{"A":"1\/6","B":"1\/8","C":"1\/10","D":"1\/12"},"correct_answer":"B","updated_at":"2025-07-09T14:58:46.000000Z","created_at":"2025-07-09T14:58:46.000000Z","id":50}]}
PUT /questions/{id} - Update question
http://127.0.0.1:8000/api/questions/30
input:
{"question": "What is the correct answer?","options": {"A": "1/6","B": "1/8", "C": "1/10","D": "1/12"},"correct_answer": "A"  // or whichever option is correct}
response:
{"id":29,"test_id":8,"question":"What is the correct answer?","options":{"A":"1\/6","B":"1\/8","C":"1\/10","D":"1\/12"},"correct_answer":"A","difficulty":null,"category":null,"category_id":null,"created_at":"2025-07-09T14:58:20.000000Z","updated_at":"2025-07-09T15:07:09.000000Z"}
DELETE /questions/{id} - Delete question
http://127.0.0.1:8000/api/questions/30
{"message":"Question deleted."}

✅ Analytics APIs
GET /analytics/results - Get analytics results
http://127.0.0.1:8000/api/analytics/results
response:
{"data":{"summary":{"total_attempts":6,"passed_students":0,"failed_students":6,"average_score":null},"results":[{"student_name":"Alice Johnson","institute":{"id":1,"name":"Massachusetts Institute of Technology","code":"MIT","address":"77 Massachusetts Ave, Cambridge, MA 02139, USA","department_id":1,"created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-06T11:33:25.000000Z"},"department":{"id":1,"name":"Computer Science","code":"CSE","created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-06T11:33:25.000000Z"},"score":null,"total_questions":30,"percentage":null,"test_date":"2025-07-03T12:48:26.000000Z","duration":75,"status":"failed"},{"student_name":"Bob Williams","institute":{"id":2,"name":"Stanford University","code":"STAN","address":"450 Serra Mall, Stanford, CA 94305, USA","department_id":4,"created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-07T12:13:27.000000Z"},"department":{"id":2,"name":"Electrical Engineering","code":"EE","created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-06T11:33:25.000000Z"},"score":null,"total_questions":25,"percentage":null,"test_date":"2025-06-29T12:23:26.000000Z","duration":50,"status":"failed"},{"student_name":"Bob Williams","institute":{"id":2,"name":"Stanford University","code":"STAN","address":"450 Serra Mall, Stanford, CA 94305, USA","department_id":4,"created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-07T12:13:27.000000Z"},"department":{"id":2,"name":"Electrical Engineering","code":"EE","created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-06T11:33:25.000000Z"},"score":null,"total_questions":20,"percentage":null,"test_date":"2025-07-04T12:08:26.000000Z","duration":35,"status":"failed"},{"student_name":"Charlie Brown","institute":{"id":3,"name":"California Institute of Technology","code":"CALTECH","address":"1200 E California Blvd, Pasadena, CA 91125, USA","department_id":1,"created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-06T11:33:25.000000Z"},"department":{"id":3,"name":"Mechanical Engineering","code":"ME","created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-06T11:33:25.000000Z"},"score":null,"total_questions":25,"percentage":null,"test_date":"2025-06-26T12:38:26.000000Z","duration":65,"status":"failed"},{"student_name":"Diana Prince","institute":{"id":4,"name":"University of California, Berkeley","code":"UCB","address":"Berkeley, CA 94720, USA","department_id":1,"created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-06T11:33:25.000000Z"},"department":{"id":4,"name":"Civil Engineering","code":"CE","created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-06T11:33:25.000000Z"},"score":null,"total_questions":25,"percentage":null,"test_date":"2025-07-05T12:13:26.000000Z","duration":40,"status":"failed"},{"student_name":"Ethan Hunt","institute":{"id":5,"name":"Carnegie Mellon University","code":"CMU","address":"5000 Forbes Ave, Pittsburgh, PA 15213, USA","department_id":1,"created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-06T11:33:25.000000Z"},"department":{"id":5,"name":"Chemical Engineering","code":"CHE","created_at":"2025-07-06T11:33:25.000000Z","updated_at":"2025-07-06T11:33:25.000000Z"},"score":null,"total_questions":30,"percentage":null,"test_date":"2025-07-04T12:28:26.000000Z","duration":55,"status":"failed"}]}}

✅ Test Results APIs
GET /test/result/{attemptId} - Get test result by attempt ID
http://127.0.0.1:8000/api/test/result/1
response:
{"id":1,"attempt_id":1,"score":18,"total_questions":20,"percentage":90,"status":"passed","duration":"45m","answers":"{\"1\":\"32\",\"2\":\"True\"}","created_at":"2025-07-06T11:33:26.000000Z","updated_at":"2025-07-06T11:33:26.000000Z"}