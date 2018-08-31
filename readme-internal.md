# Engage Laravel Framework

## Prerequisites

1) Download and install Docker (https://www.docker.com/)

2) Ensure MAMP/Homestead/Other Docker containers or any other environment that's using ports 8000, 5757, 13306, or 8080 are not running

---

## Setup

Note: All of the following commands are run from the root of the project.*

1. Clone Laravel repo, removing the git repository:
   ```
   git clone https://github.com/engageinteractive/laravel --depth new-folder-name
   cd new-folder-name
   rm -rf .git
   ```
2. Fill out an APP_NAME and a COMPOSE_PROJECT_NAME to the `.env.example`.
3. Run `scripts/init`

---

### Watching/building

Run `scripts/watch`

