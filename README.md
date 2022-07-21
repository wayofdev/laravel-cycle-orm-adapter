

<br>

<div align="center">
<img width="456" src="https://raw.githubusercontent.com/wayofdev/laravel-cycle-orm-adapter/master/assets/logo.gh-light-mode-only.png#gh-light-mode-only">
<img width="456" src="https://raw.githubusercontent.com/wayofdev/laravel-cycle-orm-adapter/master/assets/logo.gh-dark-mode-only.png#gh-dark-mode-only">
</div>


<br>

<br>

<div align="center">
<a href="https://github.com/wayofdev/laravel-cycle-orm-adapter/actions"><img alt="Build Status" src="https://img.shields.io/endpoint.svg?url=https%3A%2F%2Factions-badge.atrox.dev%2Fwayofdev%2Flaravel-cycle-orm-adapter%2Fbadge&style=flat-square"/></a>
<a href="https://packagist.org/packages/wayofdev/laravel-cycle-orm-adapter"><img src="https://img.shields.io/packagist/dt/wayofdev/laravel-cycle-orm-adapter?&style=flat-square" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/wayofdev/laravel-cycle-orm-adapter"><img src="https://img.shields.io/packagist/v/wayofdev/laravel-cycle-orm-adapter?&style=flat-square" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/wayofdev/laravel-cycle-orm-adapter"><img src="https://img.shields.io/packagist/l/wayofdev/laravel-cycle-orm-adapter?style=flat-square&color=blue" alt="Software License"/></a>
<a href="https://packagist.org/packages/wayofdev/laravel-cycle-orm-adapter"><img alt="Commits since latest release" src="https://img.shields.io/github/commits-since/wayofdev/laravel-cycle-orm-adapter/latest?style=flat-square"></a>
<a href="https://app.codecov.io/gh/wayofdev/laravel-cycle-orm-adapter"><img alt="Codecov" src="https://img.shields.io/codecov/c/github/wayofdev/laravel-cycle-orm-adapter?style=flat-square"></a>
<a href="https://scrutinizer-ci.com/g/wayofdev/laravel-cycle-orm-adapter"><img alt="Scrutinizer code quality (GitHub)" src="https://img.shields.io/scrutinizer/quality/g/wayofdev/laravel-cycle-orm-adapter?style=flat-square"></a>
</div>

<br>

# Laravel Cycle ORM Adapter

Package fully integrates [Cycle ORM](https://cycle-orm.dev) into [Laravel](https://laravel.com) framework.

* [Laravel Octane](https://laravel.com/docs/9.x/octane) ready!
* [Laravel Telescope](https://laravel.com/docs/9.x/telescope) compatible

If you **like/use** this package, please consider **starring** it. Thanks!

<br>

## 💿 Installation

### → Using Composer

Require as dependency:

```bash
$ composer req wayofdev/laravel-cycle-orm-adapter
```

<br>

## 💻 Usage

### → Console Commands

#### Migrations:

| Command            | Description                                                  |
| ------------------ | ------------------------------------------------------------ |
| `migrate`          | Perform one or all outstanding migrations.<br />`--one` Execute only one (first) migration. |
| `migrate:replay`   | Replay (down, up) one or multiple migrations.<br />`--all` Replay all migrations. |
| `migrate:rollback` | Rollback one (default) or multiple migrations.<br />`--all` Rollback all executed migrations. |
| `migrate:init`     | Init migrations component (create migrations table).         |
| `migrate:status`   | Get list of all available migrations and their statuses.     |

#### Database:

| Command            | Description                                                  |
| ------------------ | ------------------------------------------------------------ |
| `db:list [db]`     | Get list of available databases, their tables and records count.<br/>`db` database name. |
| `db:table <table>` | Describe table schema of specific database.<br/>`table` Table name (required).<br/>`--database` Source database. |

#### ORM and Schema:

| Command         | Description                                                  |
| --------------- | ------------------------------------------------------------ |
| `cycle`         | Update (init) cycle schema from database and annotated classes. |
| `cycle:migrate` | Generate ORM schema migrations. `--run` Automatically run generated migration. |
| `cycle:render`  | Render available CycleORM schemas. `--no-color` Display output without colors. |
| `cycle:sync`    | Sync Cycle ORM schema with database without intermediate migration (risk operation). |

<br>

## 🧪 Running Tests

### → PHPUnit Tests

To run tests, run the following command:

```bash
$ make test
```

### → Static Analysis

Code quality using PHPStan:

```bash
$ make stan
```

### → Coding Standards Fixing

Fix code using The PHP Coding Standards Fixer (PHP CS Fixer) to follow our standards:

```bash
$ make cs-fix
```

<br>

## 🤝 License

[![Licence](https://img.shields.io/github/license/wayofdev/laravel-cycle-orm-adapter?style=for-the-badge&color=blue)](./LICENSE)

<br>

## 🙆🏼‍♂️ Author Information

Created in **2022** by [lotyp / wayofdev](https://github.com/wayofdev)

<br>

## 🧱 Resources and Credits

* Inspired by [butschster's](https://github.com/butschster/LaravelCycleORM) package

* The official [spiral/cycle-bridge](https://github.com/spiral/cycle-bridge) for Spiral Framework

<br>
