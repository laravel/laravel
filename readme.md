## Clique

Clique is an online org-tracking web tool, currently built on Laravel. 

## Documentation

All project documentation is under the `/docs` folder, listed below:

#### mockups/

Load all of these `.bmml` files into Balsamiq. The links should work as long as they're in the same root.

*could some one (with free time) please be nice and handle these screens (at least for me)? We need to have screenies for each functional story in the final paper(???), which we'll use to show the queries to be used in our project. I should start with the back end implementation right away and I'm afraid I won't be able to continue maintaining the Balsamiq screens myself, sorry (and to the kind soul that will do this, thank you!!! ehem McGriddle ehem XD). You can access my notes on the lacking parts on** `/docs/Implementation Notes.txt` 
*and with*
`/docs/cs165 final - implemented parts highlighted.docx`
*...but before you do this, please discuss Team Questions #2 first haha*

#### Clique Schema DDL v02

The Schema DDL for the database in this implementation.

```
But Jeric - this is different from the original DDL Schema - stahp making changes plz
```

Yes I think I should explain the changes that I did:
- Composite keys were turned to Surrogate keys.
  - in the original model, composite primary keys were used. However the new model only uses one auto-incrementing id key for each table, and a unique constraint on the original primary keys. This decision was more implementation-influenced than relational-model-influenced:
    - Eloqent, Laravel's ORM does not support composite primary keys (which was horrific for me after discovering it this late). Hence, using surrogate keys (and unique constraints) is just a workaround so that we can easily leverage Eloquent models with our database tables. 
    - Some PHP ORMs support composite primary keys (such as Doctrine), but in the span of 1 week, speed is paramount. It's too risky for me to experiment with another ORM (with the fear of encountering implementation issues that take up time) rather than use the out-of-the-box Eloquent.
    -  the workaround is theoretically equivalent to the original model. The only drawback is that another index would have to be made for new unique constraint (in the original, they were primary keys - thus already indexed by nature), which will cause performance issues. 
  - Feel free to change the direction of the implementation (composite primary keys or surrogate keys) if you feel that this is uncomfortable. I will go forward with what your decision. This is partly my mistake.
-  Timestamp fields were added to some tables
  - so that we can clear some data entry inquiries in the future

In the future (after the project), I will eventually transform this MySQL DDL into separate Laravel Migration objects; this should produce a much more cleaner migration-based setup, plus our data model will be database-agnostic.

#### Clique Schema DDL

the original one (Romeo this is what we ran on your machine)

#### CS 165 final

my most recent copy of the final paper

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
    - open `C:\Windows\System32\drivers\etc\hosts`, then add this line: 
    ```
    127.0.0.1             www.clique.dev
    ```
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
  - set up Laravel dependencies 
    - open the command line and in the `clique\` root, type: `composer install`
    - what this is doing is: Composer is checking the contents of `composer.json` and updating/managing all our imported components for us. 
    - if you need to include an external module into the project, you just have to update `composer.json` and run `composer install` again.
  - test the URL
    - restart Apache server, then go to http://www.clique.dev. You should now see the Laravel Welcome Screen.
  - double-check config files in `clique\app\config` for any unique settings that you may have for your machine, such as database connection settings (just add a .gitignore file for it afterwards)
- Whenever you make changes to the application:
  - if you are already a collaborator, just sync your changes, then commit
  - if not, then sync your changes to your fork and create a pull request for that fork (you can look at this [guide](https://help.github.com/categories/collaborating/) for details).

If yor're still confused / can't get Laravel to work / would like to point out a fundamental mistake (hey I'm just learning this too), send a message. 

## Team Questions

#### Jeric what have you done - dafuq is Clique? eww

I just remembered that cliques have vertices all connected to each other (much like how you want everyone in the org to be connected), plus Clique and CURSOR jives together (get it? HAHA) so I thought why not. 

#### Assuming there is a final paper submission and an implementation submission, what is the team's direction? Should there be a difference between the two (the final paper will have complete user stories / cases [relative to the last paper we submitted], but only a select few will be implemented)...or should the two be parallel (paper == implementation)?

*Please answer me haha*

## Freedom Board

Sory uli sa Sat work guys unavoidable @_@
So san overnight sa Sat? Haha

