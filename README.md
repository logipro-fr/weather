# Weather

Weather mutualisation service.

# Install

```console
git clone git@github.com:logipro-fr/weather.git
```

# To Contribute to Weather

## Requirements

* docker
* git


## Unit test

```console
bin/phpunit
```

Using Test-Driven Development (TDD) principles (thanks to Kent Beck and others), following good practices (thanks to Uncle Bob and others).

## Manual tests

```console
./start
```
have a local look at http://127.0.0.1:10290/ in your navigator

```console
./stop
```

## Quality

Some indicators that seem interesting.

* phpcs PSR12
* phpstan level 9
* coverage >=100%
* infection MSI >=100%

Quick check with:
```console
./codecheck
```

Check coverage with:
```console
bin/phpunit --coverage-html var
```
and view 'var/index.html' with your browser

Check infection with:
```console
bin/infection
```
and view 'var/infection.html' with your browser