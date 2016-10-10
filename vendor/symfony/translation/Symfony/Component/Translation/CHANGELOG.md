CHANGELOG
=========

2.3.0
-----

 * added classes to make operations on catalogues (like making a diff or a merge on 2 catalogues)
 * added Translator::getFallbackLocales()
 * deprecated Translator::setFallbackLocale() in favor of the new Translator::setFallbackLocales() method

2.2.0
-----

 * QtTranslationsLoader class renamed to QtFileLoader. QtTranslationsLoader is deprecated and will be removed in 2.3.
 * [BC BREAK] uniformized the exception thrown by the load() method when an error occurs. The load() method now
   throws Symfony\Component\Translation\Exception\NotFoundResourceException when a resource cannot be found
   and Symfony\Component\Translation\Exception\InvalidResourceException when a resource is invalid.
 * changed the exception class thrown by some load() methods from \RuntimeException to \InvalidArgumentException
   (IcuDatFileLoader, IcuResFileLoader and QtFileLoader)

2.1.0
-----

 * added support for more than one fallback locale
 * added support for extracting translation messages from templates (Twig and PHP)
 * added dumpers for translation catalogs
 * added support for QT, gettext, and ResourceBundles
