<?php declare(strict_types=1);

namespace PHPSTORM_META;

override(\Nette\Utils\Arrays::get(0), elementType(0));
override(\Nette\Utils\Arrays::getRef(0), elementType(0));
override(\Nette\Utils\Arrays::grep(0), type(0));
override(\Nette\Utils\Arrays::toObject(0), type(1));

expectedArguments(\Nette\Utils\Image::resize(), 2, \Nette\Utils\Image::ShrinkOnly, \Nette\Utils\Image::Stretch, \Nette\Utils\Image::OrSmaller, \Nette\Utils\Image::OrBigger, \Nette\Utils\Image::Cover);
expectedArguments(\Nette\Utils\Image::calculateSize(), 4, \Nette\Utils\Image::ShrinkOnly, \Nette\Utils\Image::Stretch, \Nette\Utils\Image::OrSmaller, \Nette\Utils\Image::OrBigger, \Nette\Utils\Image::Cover);
