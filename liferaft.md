Fixed password reminder functionality

Found and fixed the following auth issues

* Changed default password reminders table name (auth config) to password_reminders
* Added RememberToken field per default to users table
* Added token to GET annotation for password reset view in PasswordController
