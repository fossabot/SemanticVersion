# SoureCode/SemanticVersion

[![Latest Stable Version](https://poser.pugx.org/sourecode/semantic-version/v)](//packagist.org/packages/sourecode/semantic-version)
[![Total Downloads](https://poser.pugx.org/sourecode/semantic-version/downloads)](//packagist.org/packages/sourecode/semantic-version)
[![Latest Unstable Version](https://poser.pugx.org/sourecode/semantic-version/v/unstable)](//packagist.org/packages/sourecode/semantic-version)
[![CI Status](https://github.com/sourecode/version/workflows/CI/badge.svg)](https://github.com/sourecode/version/actions)
[![Type Coverage](https://shepherd.dev/github/sourecode/version/coverage.svg)](https://shepherd.dev/github/sourecode/version)
[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2FSoureCode%2FSemanticVersion.svg?type=shield)](https://app.fossa.com/projects/git%2Bgithub.com%2FSoureCode%2FSemanticVersion?ref=badge_shield)

A simple wrapper for [SemVer](https://semver.org/).

## Features

- A single [Version](src/Version.php) class.
- Immutable, chainable, unambiguous API.
- Parsing and formatting

## Install

```
composer require sourecode/semantic-version
```

## Tests

```
./vendor/bin/phpunit
```

## Coding Standards

```
php-cs-fixer fix
```

## Static analysis

```
./vendor/bin/psalm
```


## License
[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2FSoureCode%2FSemanticVersion.svg?type=large)](https://app.fossa.com/projects/git%2Bgithub.com%2FSoureCode%2FSemanticVersion?ref=badge_large)