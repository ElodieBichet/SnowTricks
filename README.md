# SnowTricks

Snowboard community site made with Symfony 5.
Work carried out as part of the training course "Application Developer - PHP / Symfony" on OpenClassrooms.

[![Maintainability](https://api.codeclimate.com/v1/badges/5f37b4e2d0ca53a4a7b5/maintainability)](https://codeclimate.com/github/ElodieBichet/SnowTricks/maintainability)

## Table of Contents
1.  __[Prerequisite and technologies](#prerequisite-and-technologies)__
  * [Server](#server)
  * [Framework and libraries](#framework-and-libraries)
2.  __[Installation](#installation)__
  * [Download or clone](#download-or-clone)
  * [Configure environment variables](#configure-environment-variables)
  * [Install the project](#install-the-project)
  * [Create the database](#create-the-database)
3.  __[Usage](#usage)__

---
## PREREQUISITE AND TECHNOLOGIES

### __Server__
You need a web server with PHP7 (> 7.2.5) and MySQL DBMS.  
Versions used in this project:
* Apache 2.4.46
* PHP 7.3.21
* MySQL 5.7.31

You also need an access to a SMTP server.

See more information on technical requirements in the [Symfony official documentation](https://symfony.com/doc/5.2/setup.html#technical-requirements).

### __Framework and libraries__
Framework: __Symfony ^5.2.9__
Dependencies manager: __Composer ^1.9.3__  

Libraries included via Composer (used in fixtures):
* Cocur/Slugify ^4.0 ([more info](https://github.com/cocur/slugify))
* FakerPHP/Faker ^1.14 ([more info](https://fakerphp.github.io/))

CSS/JS libraries, included via CDN links:
* Bootstrap ^5.0.1 ([more info](https://getbootstrap.com/docs/5.0/getting-started/introduction/))
* Yeti bootswatch theme ([more info](https://bootswatch.com/yeti/))
* Font-awesome ^5.15.3 ([more info](https://fontawesome.com/))
* Axios ^0.21.1 ([more info](https://axios-http.com/))

---
## INSTALLATION

### __Download or clone__
Download zip files or clone the project repository with github ([see GitHub documentation](https://docs.github.com/en/github/creating-cloning-and-archiving-repositories/cloning-a-repository)).

### __Configure environment variables__
You need to configure at least these lines in ___.env___ file:
```env
###> symfony/mailer ###
# MAILER_DSN=smtp://localhost
# MAILER_USER=smtp-user-email-address@domain.com
###< symfony/mailer ###
...
###> doctrine/doctrine-bundle ###
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"
...
###> super admin configuration, used in fixtures loading ###
SUPER_ADMIN_EMAIL=your@email.com
SUPER_ADMIN_NAME='FirstName LastName'
SUPER_ADMIN_PASSWORD=yourSecretPassword
```

### __Install the project__
1.  If needed, install __Composer__ by following [the official instructions](https://getcomposer.org/download/).
2.  In your cmd, go to the directory where you want to install the project and install dependencies with composer:
```
$ cd some\directory
$ composer install
```
Dependencies should be installed in your project (check _vendor_ directory).

### __Create the database__
1. If the database does not exist, create it with the following command in the project directory:
```
$ php bin/console doctrine:database:create
```
2. Create database structure thanks to migrations:
```
$ php bin/console doctrine:migrations:migrate
```
Here is the database model:
  ![DB Model](UML/Data-model.png) 
  
3. Install fixtures to have first contents and your super admin account (update the fixtures files before if needed):
```
$ php bin/console doctrine:fixtures:load
```
Your database should be updated with contents.  
Images files in scr/DataFixtures/images repertory should be copied with a new name in public/uploads/pictures directory.

#### __You are ready to use your app!__  
If you login with your admin username and password, you can access all public pages of the app, and manage registered users or any content.  
Loggued in as a registered user (with verified email address), you can manage all tricks and your own messages.  
As an simple visitor, you can just see the content.  