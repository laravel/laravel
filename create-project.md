# New Project

## Creation and first commit
1. Create the new repo on github
2. Clone the new repo
3. **RUN** `docker run -it --rm -v $(pwd -W &> /dev/null && pwd -W || pwd):/usr/src/myapp -w="/usr/src/myapp" digbang/php-dev:7.1 bash`

### Inside the container
1. **RUN** `composer config -g github-oauth.github.com <token>`
(To create the token go to: https://github.com/settings/tokens/new and set the **repo** permissions)
2. **RUN** `composer create-project --prefer-dist digbang/laravel-project . "dev-5.6_digbangs-way" --ignore-platform-reqs`

### After creating the project
3. Commit and push the new files
4. ...
5. Profit
