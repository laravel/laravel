# New Project

## Creation and first commit
1. Create the new repo on github
2. Clone the new repo
3. **RUN** `docker run -it --rm -v $(pwd -W &> /dev/null && pwd -W || pwd):/usr/src/myapp -w="/usr/src/<PROJECT-NAME>" digbang/php-dev:7.4 bash`

> Please, change <PROJECT-NAME> with the slug of the namespace you want for your project. This is important, as the composer project creation will rename and rewrite some files using that name.

### Inside the container
1. **RUN** `composer config -g github-oauth.github.com <token>`
(To create the token go to: https://github.com/settings/tokens/new and set the **repo** permissions)
2. **RUN** `composer create-project --prefer-dist digbang/laravel-project . <latest_release>` (Check https://github.com/digbang/laravel-project/releases for the corresponding release number)

### After creating the project
3. Commit and push the new files
4. ...
5. Profit

## Installing an already created project
1. Start the containers
2. Access the PHP container and:
> A. **RUN** `composer config -g github-oauth.github.com <token>`
(To create the token go to: https://github.com/settings/tokens/new and set the **repo** permissions)

> B. **RUN** `composer install`

> C. **RUN** `ln -s /proxies proxies`

> D. **RUN** `composer build`
