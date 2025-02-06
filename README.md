<div align="center">

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/c053893bdb474fe199b4c9142b91e678)](https://app.codacy.com/gh/AurelienLab/daps-p8-todo/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)
[![Codacy Badge](https://app.codacy.com/project/badge/Coverage/c053893bdb474fe199b4c9142b91e678)](https://app.codacy.com/gh/AurelienLab/daps-p8-todo/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_coverage)

# TodoAndCo

</div>



TodoAndCo is a simple task manager application built with Symfony 7. It allows users to create, edit, and delete tasks, as well as mark them as done.

## Installation

Follow these steps to install and set up the project on your local machine.

### Prerequisites

- PHP 8.3 or higher
- Composer
- Symfony CLI (optional to serve the project)
- Node.js and Yarn

### Clone the Repository

First, clone the repository to your local machine:

```
git clone git@github.com:AurelienLab/daps-p8-todo.git todoandco
cd todoandco
```

### Install PHP Dependencies

Use Composer to install the required PHP dependencies:

```
composer install
```

### Install JavaScript Dependencies

Use Yarn to install the required JavaScript dependencies:

```
yarn install
```

### Set Up Environment Variables

Copy `.env` to `.env.local` file at the root of your project and configure your environment variables.

### Database Migration

Run the following command to create the database schema:

```
php bin/console doctrine:migrations:migrate
```

### Databse seed

You can prepopulate the database with users:

```
php bin/console doctrine:fixtures:load
```

### Default credentials

| login          | password     | role          |
|----------------|--------------|---------------|
| admin@todo.com | TDadmin2k25# | Administrator |
| user@todo.com  | TDuser2k25#  | User          |

### Build Assets

Build the front-end assets using Yarn:

```
yarn dev
```

For production, use:

```
yarn build
```

### Start the Server (optional)

Finally, start the Symfony server:

```
symfony serve
```

Your application should now be running at `http://127.0.0.1:8000`.
