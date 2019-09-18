<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii 2 Advanced Project Template</h1>
    <br>
</p>

Yii 2 Advanced Project Template is a skeleton [Yii 2](http://www.yiiframework.com/) application best for
developing complex Web applications with multiple tiers.

The template includes three tiers: front end, back end, and console, each of which
is a separate Yii application.

The template is designed to work in a team development environment. It supports
deploying the application in different environments.

Documentation is at [docs/guide/README.md](docs/guide/README.md).

[![Latest Stable Version](https://img.shields.io/packagist/v/yiisoft/yii2-app-advanced.svg)](https://packagist.org/packages/yiisoft/yii2-app-advanced)
[![Total Downloads](https://img.shields.io/packagist/dt/yiisoft/yii2-app-advanced.svg)](https://packagist.org/packages/yiisoft/yii2-app-advanced)
[![Build Status](https://travis-ci.org/yiisoft/yii2-app-advanced.svg?branch=master)](https://travis-ci.org/yiisoft/yii2-app-advanced)

DIRECTORY STRUCTURE
-------------------

```
common
    config/              contains shared configurations
    mail/                contains view files for e-mails
    models/              contains model classes used in both backend and frontend
    tests/               contains tests for common classes    
console
    config/              contains console configurations
    controllers/         contains console controllers (commands)
    migrations/          contains database migrations
    models/              contains console-specific model classes
    runtime/             contains files generated during runtime
backend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains backend configurations
    controllers/         contains Web controller classes
    models/              contains backend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for backend application    
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
frontend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains frontend configurations
    controllers/         contains Web controller classes
    models/              contains frontend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for frontend application
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
    widgets/             contains frontend widgets
vendor/                  contains dependent 3rd-party packages
environments/            contains environment-based overrides
```


### Notes: 

- Frontend folder has been deleted cause is not in use

# How to launch the timetable script

```bash
$ php yii main NUMBER_OF_WEEK
```

Example:
```bash
$ php yii main 4
```

This execute the 4th week of the year.

For automatic generation, you have the crontab options here:
```bash
CRONTAB 
30 17 * * 0,1,2,3,4,5 /usr/bin/php /root/timetable/yii mail > /home/logs/mail`date +\%y\%m\%d\%H\%M`.log 2>&1
00 10 * * 2 /usr/bin/php /root/timetable/yii main/generate-next-week  > /home/logs/timetable/main`date +\%y\%m\%d\%H\%M`.log 2>&1
00 8 * * 1 /usr/bin/php /root/timetable/yii mail/holiday-mail  > /home/logs/timetable/mail-holiday`date +\%y\%m\%d\%H\%M`.log 2>&1
00 * * * * /usr/bin/php /root/timetable/yii main/set-number-of-week
00 8 * * 1 /usr/bin/php /root/timetable/yii mail/guards-mail > /home/logs/timetable/mail-guards`date +\%y\%m\%d\%H\%M`.log 2>&1
```

Remember that the hour of the server could be different than yours

## Constants

You have a constants table that can be edited in the frontend. It controls the week generations.

# How to deploy the app.

## Prerequisites

- LAMP.
- Composer.

## Tutorial

The app have to be deployed like a normal Yii2 App.

After you have the code in the repository, you have to do:

```bash
$ php init
```

The frameworks asks you for the enviroment that you are deploying (Development or Production).

After that you have to install all the dependencies:

```bash
$ composer install
```

DB configurations params are in common/config/main-local.php. This file is created by the initialization of the framework the php init command.

You have the structure of the DB in the repo: db.sql

## Common errors

- Ask Sergio and add here


[![License: CC BY-NC-ND 4.0](https://licensebuttons.net/l/by-nc-nd/4.0/80x15.png)](https://creativecommons.org/licenses/by-nc-nd/4.0/) [![License: CC BY-NC-ND 4.0](https://img.shields.io/badge/License-CC%20BY--NC--ND%204.0-lightgrey.svg)](https://creativecommons.org/licenses/by-nc-nd/4.0/)