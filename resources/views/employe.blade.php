<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="date"],
        select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 25px;
            font-size: 14px;
            box-sizing: border-box;
            background-color: #f9f9f9;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus,
        select:focus {
            outline: none;
            border-color: #4CAF50;
            background-color: white;
        }
        
        .btn-container {
            text-align: center;
            margin-top: 30px;
        }
        
        .btn-add {
            background-color: #4CAF50;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn-add:hover {
            background-color: #45a049;
        }
        
        .employee-list {
            margin-top: 40px;
        }
        
        .employee-item {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .employee-info {
            flex: 1;
        }
        
        .employee-name {
            font-weight: bold;
            color: #333;
        }
        
        .employee-details {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .btn-edit,
        .btn-delete {
            padding: 8px 15px;
            border: none;
            border-radius: 15px;
            margin-left: 5px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .btn-edit {
            background-color: #2196F3;
            color: white;
        }
        
        .btn-delete {
            background-color: #f44336;
            color: white;
        }
        
        .btn-edit:hover {
            background-color: #0b7dda;
        }
        
        .btn-delete:hover {
            background-color: #da190b;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Employee Management</h1>
        
        <form id="employeeForm" method="POST" action="{{ route('employee.store') }}">
            @csrf
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" placeholder="Enter employee name" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter email address" required>
            </div>
            
            <div class="form-group">
                <label for="position">Position:</label>
                <input type="text" id="position" name="position" placeholder="Enter job position" required>
            </div>
            
            <div class="form-group">
                <label for="department">Department:</label>
                <select id="department" name="department" required>
                    <option value="">Select Department</option>
                    <option value="IT">IT</option>
                    <option value="HR">Human Resources</option>
                    <option value="Finance">Finance</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Operations">Operations</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="salary">Salary:</label>
                <input type="number" id="salary" name="salary" placeholder="Enter salary amount" required>
            </div>
            
            <div class="form-group">
                <label for="hire_date">Hire Date:</label>
                <input type="date" id="hire_date" name="hire_date" required>
            </div>
            
            <div class="btn-container">
                <button type="submit" class="btn-add">Add Employee</button>
            </div>
        </form>
        
        <div class="employee-list">
            <h2>Employee List</h2>
            
            @if(isset($employees) && count($employees) > 0)
                @foreach($employees as $employee)
                <div class="employee-item">
                    <div class="employee-info">
                        <div class="employee-name">{{ $employee->name }}</div>
                        <div class="employee-details">
                            {{ $employee->position }} - {{ $employee->department }} | 
                            Salary: Rp {{ number_format($employee->salary, 0, ',', '.') }} | 
                            Hired: {{ $employee->hire_date }}
                        </div>
                    </div>
                    <div>
                        <button class="btn-edit" onclick="editEmployee({{ $employee->id }})">Edit</button>
                        <button class="btn-delete" onclick="deleteEmployee({{ $employee->id }})">Delete</button>
                    </div>
                </div>
                @endforeach
            @else
                <div class="employee-item">
                    <div class="employee-info">
                        <div class="employee-name">No employees found</div>
                        <div class="employee-details">Start by adding your first employee using the form above.</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <script>
        function editEmployee(id) {
            // Redirect to edit page or show edit modal
            window.location.href = '/employee/' + id + '/edit';
        }
        
        function deleteEmployee(id) {
            if (confirm('Are you sure you want to delete this employee?')) {
                // Create a form to submit DELETE request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/employee/' + id;
                
                // Add CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken.getAttribute('content');
                    form.appendChild(csrfInput);
                }
                
                // Add method override for DELETE
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // Add form validation
        document.getElementById('employeeForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const position = document.getElementById('position').value.trim();
            const department = document.getElementById('department').value;
            const salary = document.getElementById('salary').value;
            
            if (!name || !email || !position || !department || !salary) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }
            
            // Basic email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return false;
            }
            
            // Salary validation
            if (salary <= 0) {
                e.preventDefault();
                alert('Please enter a valid salary amount.');
                return false;
            }
        });
    </script>
</body>
</html>