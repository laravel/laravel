CHANGELOG
=========

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
