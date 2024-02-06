# CHANGELOG
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [v3.0.0] - 2024-02-06
This version drops support for older PHPs, adds stricter types internally, and fixes some bugs.

### Changed
- Support for PHP 7.4 and 8.0 has been dropped. Please use v2.0.0 if you need to use this with an older version of PHP.
- Types have been added for all properties, method parameters, and method returns.
- The `DeadLetterQueue::readOldest` method will now make requests to EventHub without an `Accepts: application/json` header, which can force an XML-to-JSON conversion. This allows consumers to receive the message in its intended content type.

### Fixed
- Fixed an exception in the `RetryClient` that prevented it from dealing with `ConnectException`s.

## v2.0.0
There are **no breaking changes** to the library, but the minimum PHP version is now 7.4.

- Drop support for older versions of PHP.
- Update dependencies.

## v1.0.2
- EventHub changed one of the DLQ calls, so the library has been updated to reflect the new URL
- Two methods were added to DeadLetterQueue: readOldest() and moveFromDLQ(). These are necessary to pull your messages out of purgatory.

The first change is technically a breaking change (the method signature has been updated), but I am not incrementing the major version since your app is broken anyway -- it'll always 404 and throw an exception.

## v1.0.1
Add minimum PHP version & update dependencies

## v1.0.0
Initial release