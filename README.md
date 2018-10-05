# Install
You can use the file *install.sh* or execute the commands/steps follow:

```composer install```

```composer run-script post-root-package-install```

```composer run-script post-create-project-cmd```

Configure database in .env

If you use sqlite, remember execute in your folder:

```touch database/database.sqlite```

After you configure your database run migrations, in production env add the parameter ```--force``` to add yes and run anywhere:

```php artisan migrate```

If you have a any problem clear caches with:

```php artisan cache:clear && php artisan config:clear && php artisan clear-compiled```

# Specification

At Snowman Labs we are developing an application that allows users to view and create __Tourist Spots__ on a map. 
Your job is to create an application to allow users to register __Tourist Spots__ and list them.

## Goal

Develop a __expressive__ RESTFul API to be used by the mobile applications.

## User Stories

* As a user, I want to signup using my facebook account.
* As a user, I want to signin using  my facebook account.
* As a user, I want to view all __Tourist Spots__ in a given radius in kilometers from my actual location.
* As a user, I want to register a Tour Point with a name, geographical coordinates, category and visibiliy (public or private).
* As a user, I want to view all __Tourist Spots__ I registered.
* As a user, I want to delete a tour Point I registered.
* As a user, I want to check-in to a __Tourist Spot__.

## Functional requirements

* Private __Tourist Spots__ are only visible to whom registered them.
* An anonymous user should only see __Tourist Spots__ from Park and Museum categories.
* Users should only be able to check-in to a  __Tourist Spot__ under 1 kilometer from their actual location.
* The allowed categories are:
  * Park
  * Museum
  * Restaurant

## Non-functional requirements

* The application must be deployable.
* You must guarantee data security
* You may choose your prefered programming language.
* You may choose your prefered database.
* You may use frameworks and libraries in their most recent stable versions.
* You may use any infrastructure technology as you judge suitable (E.g. Docker, Ansyble, Redis, etc.).

## Deliverables

The following deliverables are mandatory for the test acceptance:

* Source code in a public git repository (Github or Bitbucket, etc.).
* Instructions for configuring and running the application (The simpler the best).
* A Live Demo (E.g. Heroku, AWS, Digital Ocean).

## Evaluation

The evaluation will follow the criteria as below:

* Application is fully operational by the specs.
* Good development practices.
* Code Maintainability.
* Performance and Scalability.
* Data security.
* Data consistency.
* Robustness.

## Stand Out

* By implementing Tests.
* By documenting the API.
* By implementing Cache.
* By being creative.