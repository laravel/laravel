# [Project devs to add: Project name]

## Project background
[Project devs to add: Add a short description about what this project codebase is.]

## Project architecture
[Project devs to add: Add a short description, or any notes on the codebase's architecture.]

| Project element | Software |
| --------------- |---------------|
| **Backend**        | PHP 7.3 |
| **Database**       | MariaDB |
| **Framework**      | Laravel 5.8 |
| **Build system**   | Laravel Mix / Webpack |
| **JS Framework**   | VueJS |

## Running a local version
The application can be accessed locally via the endpoints below:

| Endpoint | URI |
| --------------- |---------------|
| Web application | http://localhost:8000 |
| Database        | http://localhost:8080 |

### Prerequisites
1) [Download](https://docs.docker.com/docker-for-mac/install/) and install Docker Desktop for Mac.
2) Ensure no other applications are using the same ports as the Docker containers in this projects use. e.g. Ports: 8000, 5757, 13306, 8080.

### Establish a running environment

##### 1. Starting up service containers:
To start the project, run `scripts/bootstrap`.
If this is the first time checking out the project. The script will create an environment file from the example file, and run any application initialistion scripts.

##### 2. Building assets:
To build any compiled assets, run `scripts/build`.

[Project devs to add: Add any additional information here]

### Scripts reference
##### `scripts/bootstrap`
Use the bootstrap script to start a project in an initialised state.
**Usage:**: `$ scripts/bootstrap [args]`

| Argument        | Description   |
| --------------- |---------------|
| **--no-update** | Makes the script skip application dependency installation |

This script creates a .env file if one isn’t present, builds and starts the Docker service containers, installs applications dependencies (Composer packages, Node modules, Database schema, and seeding data), and generates an application key.

##### `scripts/update`
**Usage:**: `$ scripts/update`
Update installs application dependencies inside app container. For example Composer packages, Node modules, Database schema, and seeding data

##### `scripts/build`
**Usage:** `$ scripts/build [command]`
Build is used for building front-end assets. If no command is passed, the script will run yarn’s development build task.

##### `scripts/watch`
**Usage:** `$ scripts/watch`
Shortcut to Yarn's watch task

##### `scripts/test`
**Usage:** `$ scripts/test`
Test runs the applications testing suite. It will run a php static analyser, followed by PHPunit.

##### `scripts/console`
**Usage:** `$ scripts/console [args]`
Console runs the given arguments in the applications command line interface.
