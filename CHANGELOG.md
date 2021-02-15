# CHANGELOG

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