Visual SCRUM
========================

This project is an application that automates UML diagram generation
from the user stories of user SCRUM projects.

## Installation:

In order to install the project please run these commands:

```bash
    composer install
    npm install
    gulp
    bin/console assets:install --symlink web
    bin/console doctrine:database:create
    bin/console doctrine:schema:update --force
    bin/console server:run
```

If all these are successful, you should be able to reach the project
via your browser at `127.0.0.1:8000`
