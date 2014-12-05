## Clique

Clique is an online org-tracking web tool, currently built on Laravel. 

## Documentation

## Dev Setup

Follow these steps to get a working local copy of Clique on your local (Windows with XAMPP) machine, along with some GitHub basics:

- Install [GitHub] (https://github-windows.s3.amazonaws.com/GitHubSetup.exe) 
  - afterwards please give me your GitHub usernames so I can set you as collaborators
- Install [Composer] (https://getcomposer.org/Composer-Setup.exe)
  - during installation, point the PHP executable to `C:\xampp\php` (or wherever it is)
  - while you're at it, make sure to set PHP in your PATH environment variable too
- Open GitHub in your machine, then clone this repo into your `C:\xampp\htdocs` folder (or whatever web root you have set)
  - if I am still not available by this time, then fork (make your own branch) this repo instead and clone your fork into your machine. During collaboration, you'll have to send a pull request for every commit that you would like to make (if the terms are still vague, you can read this [guide](https://help.github.com/categories/collaborating/) for an overview).
- Set Up Clique
  - set up a proxy url
    - open `C:\Windows\System32\drivers\etc\hosts`, then add this line: ``127.0.0.1             www.clique.dev```
    - open `C:\xampp\apache\conf\extra\httpd-vhosts.conf`, then add this block:
    ```
    <VirtualHost *:80>
        DocumentRoot "C:/xampp/htdocs/clique/public"
        ServerName www.clique.dev
        ServerAlias www.clique.dev
        ErrorLog "logs/laravel.log"
        CustomLog "logs/custom.laravel.log" combined
        <Directory "C:/xampp/htdocs/clique/public">
            AllowOverride All
            Order Allow,Deny
            Allow from all
            Require all granted
        </Directory>
    </VirtualHost>
    ```
    - restart Apache server, then go to [https://clique.dev]. You should now see the Laravel Welcome Screen.
  - double-check config files in `clique\app\config` for any unique settings that you may have for your machine, such as database connection settings (just add a .gitignore file for it afterwards)
- Whenever you make changes to the application:
  - if you are already a collaborator, just sync your changes, then commit
  - if not, then sync your changes to your fork and create a pull request for that fork (you can look at this
  

## Team Comments

###### Jeric what have you done - dafuq is Clique? eww ######

I just thought that cliques have vertices all connected to each other (much like how you we want to have the org connected), plus Clique and CURSOR jives together (get it? HAHA) , I thought why not. 

## Freedom Board

Sory uli sa Sat work guys unavoidable @_@
So san overnight sa Sat? Haha

