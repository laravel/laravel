CHANGELOG
=========

6.4
---

 * Add early directory pruning to `Finder::filter()`

6.2
---

 * Add `Finder::sortByExtension()` and `Finder::sortBySize()`
 * Add `Finder::sortByCaseInsensitiveName()` to sort by name with case insensitive sorting methods

6.0
---

 * Remove `Comparator::setTarget()` and `Comparator::setOperator()`

5.4.0
-----

 * Deprecate `Comparator::setTarget()` and `Comparator::setOperator()`
 * Add a constructor to `Comparator` that allows setting target and operator
 * Finder's iterator has now `Symfony\Component\Finder\SplFileInfo` inner type specified
 * Add recursive .gitignore files support

5.0.0
-----

 * added `$useNaturalSort` argument to `Finder::sortByName()`

4.3.0
-----

 * added Finder::ignoreVCSIgnored() to ignore files based on rules listed in .gitignore

4.2.0
-----

 * added $useNaturalSort option to Finder::sortByName() method
 * the `Finder::sortByName()` method will have a new `$useNaturalSort`
   argument in version 5.0, not defining it is deprecated
 * added `Finder::reverseSorting()` to reverse the sorting

4.0.0
-----

 * removed `ExceptionInterface`
 * removed `Symfony\Component\Finder\Iterator\FilterIterator`

3.4.0
-----

 * deprecated `Symfony\Component\Finder\Iterator\FilterIterator`
 * added Finder::hasResults() method to check if any results were found

3.3.0
-----

 * added double-star matching to Glob::toRegex()

3.0.0
-----

 * removed deprecated classes

2.8.0
-----

 * deprecated adapters and related classes

2.5.0
-----
 * added support for GLOB_BRACE in the paths passed to Finder::in()

2.3.0
-----

 * added a way to ignore unreadable directories (via Finder::ignoreUnreadableDirs())
 * unified the way subfolders that are not executable are handled by always throwing an AccessDeniedException exception

2.2.0
-----

 * added Finder::path() and Finder::notPath() methods
 * added finder adapters to improve performance on specific platforms
 * added support for wildcard characters (glob patterns) in the paths passed
   to Finder::in()

2.1.0
-----

 * added Finder::sortByAccessedTime(), Finder::sortByChangedTime(), and
   Finder::sortByModifiedTime()
 * added Countable to Finder
 * added support for an array of directories as an argument to
   Finder::exclude()
 * added searching based on the file content via Finder::contains() and
   Finder::notContains()
 * added support for the != operator in the Comparator
 * [BC BREAK] filter expressions (used for file name and content) are no more
   considered as regexps but glob patterns when they are enclosed in '*' or '?'
