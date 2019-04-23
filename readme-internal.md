# Engage Laravel Framework

## Prerequisites

1) Download and install Docker (https://www.docker.com/)

2) Ensure MAMP/Homestead/Other Docker containers or any other environment that's using ports 8000, 5757, 13306, or 8080 are not running

---

## Setup: When creating a new repo

Note: All of the following commands are run from the root of the project.*

1. Clone Laravel repo, removing the git repository:
   ```
   git clone https://github.com/engageinteractive/laravel --depth new-folder-name
   cd new-folder-name
   rm -rf .git
   ```
2. Fill out an APP_NAME and a COMPOSE_PROJECT_NAME to the `.env.example`.
3. Run `scripts/setup`

## Setup: When working with an existing repo

Note: All of the following commands are run from the root of the project.*

1. Run `scripts/setup`

---

### Building the frontend assets

To run the watch task you can run run `scripts/watch` from the root of the project.

If you want to access the node container directly you can use the following:

 - docker-compose run --rm node [your shell command]

Alternatively you can use the shorthand:

 - scripts/node [your shell command]
 - scripts/node bash            # Enter the container, or
 - scripts/node yarn run dev    # Build assets in dev mode
 - scripts/node yarn run prod   # Build assets in prod mode
