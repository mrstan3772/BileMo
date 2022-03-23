[![Codacy Badge](https://app.codacy.com/project/badge/Grade/50d912de6aa94bd9841937617989154d)](https://www.codacy.com/gh/mrstan3772/BileMo/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=mrstan3772/BileMo&amp;utm_campaign=Badge_Grade)

# BileMo

The BileMo API purpose is to make a selection of mobile devices accessible to third party companies.

It was entirely designed in PHP with Symfony framework.
It's provided with a set of resources including this one:
- Installation guidelines
- Documentation
- Third packages libraries and extensions
- Etc.


## Context

BileMo is a company offering a wide selection of high quality cell phones.

I'm in the lead for developing BileMo's cell phone showcase.  BileMo's business model is not to sell its products directly on the website, but to provide all the platforms that are interested in having access to the catalog via an API (Application Programming Interface). It' s about selling exclusively in B2B (business to business).


We will have to set up a certain quantity of APIs so that the applications of other web platforms can carry out operations.


## Prerequisites:

In order to make this project work, you must:
- Use **PHP 8.0.X | 8.1.X**
- [Download composer](https://getcomposer.org/) to install PHP dependencies
- Extensions (which are installed and enabled by default in most PHP 8 installations): [Ctype](https://www.php.net/book.ctype), [iconv](https://www.php.net/book.iconv), [Session](https://www.php.net/book.session), [SimlpleXML](https://www.php.net/book.simplexml), [Tokenizer](https://www.php.net/book.tokenizer), [PCRE](https://www.php.net/book.pcre)

Optional : [Install Symfony CLI](https://symfony.com/download)

The symfony binary also provides a tool to check if your computer meets all requirements. Open your console terminal and run this command:

`symfony check:requirements`

Without this tool you have to replace in the terminal `symfony` with `php bin/console` and always at the root of the project.

## Deployment


### Application Environment

Edit the `.env` or `.env.local` file on the root of the directory. On the example below adapt the configuration according to your credentials to `DATABASE_URL` values which concerns the SQL database.

```env
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=x.x.x"
```
Found this example in the root folder under the file name [".env.example"](https://github.com/mrstan3772/BileMo/blob/master/.env.example)

### Dependencies

Use the command `composer install` **[AFTER EDITING THE .ENV FILE](https://github.com/mrstan3772/BileMo#application-configuration)** from the project root directory(BileMo). Do not answer questions if you see any during the installation (press enter to skip). Once this step is done you will have all the necessary dependencies for the main project.

### Generate SSL Keys for JWT

Type this command to get a new JWT `private.pem` and `public.pem` key file in JWT configuration folder : 

`symfony console lexik:jwt:generate-keypair`


## Installation

### Creating tables in the database (MySQL)

From now on, we will focus on creating the tables required to save phones, users and clients information. All we have to do is type this command and follow :  

```bash
#Same name in your .env or .env.local file to replace "db_name" 
symfony console doctrine:database:create
symfony console make:migration
#IF ERROR THEN REMOVE DATABASE AND REMOVE ALL MIGRATION FILES IN "migrations" FOLDER AND START AGAIN
symfony console doctrine:migrations:migrate

#OPTIONAL
symfony console doctrine:migrations:diff
symfony console doctrine:schema:update --force
```

And load fixtures data with this command : 

`symfony console doctrine:fixtures:load`

### Run Server

Type this command inside the root folder(BileMo) to start running web server :

`symfony serve`

An address in the format 127.0.0.1:<port> is shown on the terminal.

Copy and paste this address in the navigation bar of your browser.

That's all !


## Version

Version : 1.0.0

We use FOSRestBundle for versioning. For more details, see [link](https://fosrestbundle.readthedocs.io/en/3.x/versioning.html).


## Authors

**Stanley LOUIS JEAN** - *Web Dev* - [MrStan](https://github.com/mrstan3772)


## License

![GPL-v3](https://zupimages.net/up/21/46/iarl.png)


## Thanks
API documentation template made by : [NelmioApiDocBundle](https://github.com/nelmio/NelmioApiDocBundle)