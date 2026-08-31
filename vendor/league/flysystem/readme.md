# League\Flysystem

[![Author](https://img.shields.io/badge/author-@frankdejonge-blue.svg)](https://twitter.com/frankdejonge)
[![Source Code](https://img.shields.io/badge/source-thephpleague/flysystem-blue.svg)](https://github.com/thephpleague/flysystem)
[![Latest Version](https://img.shields.io/github/tag/thephpleague/flysystem.svg)](https://github.com/thephpleague/flysystem/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](https://github.com/thephpleague/flysystem/blob/master/LICENSE)
[![Quality Assurance](https://github.com/thephpleague/flysystem/workflows/Quality%20Assurance/badge.svg?branch=2.x)](https://github.com/thephpleague/flysystem/actions?query=workflow%3A%22Quality+Assurance%22)
[![Total Downloads](https://img.shields.io/packagist/dt/league/flysystem.svg)](https://packagist.org/packages/league/flysystem)
![php 7.2+](https://img.shields.io/badge/php-min%208.0.2-red.svg)

## About Flysystem

Flysystem is a file storage library for PHP. It provides one interface to
interact with many types of filesystems. When you use Flysystem, you're
not only protected from vendor lock-in, you'll also have a consistent experience
for which ever storage is right for you. 

## Getting Started

* **[New in V3](https://flysystem.thephpleague.com/docs/what-is-new/)**: What is new in Flysystem V2/V3?
* **[Architecture](https://flysystem.thephpleague.com/docs/architecture/)**: Flysystem's internal architecture
* **[Flysystem API](https://flysystem.thephpleague.com/docs/usage/filesystem-api/)**: How to interact with your Flysystem instance
* **[Upgrade from 1x](https://flysystem.thephpleague.com/docs/upgrade-from-1.x/)**: How to upgrade from 1.x/2.x

### Officially supported adapters

* **[Local](https://flysystem.thephpleague.com/docs/adapter/local/)**
* **[FTP](https://flysystem.thephpleague.com/docs/adapter/ftp/)**
* **[SFTP](https://flysystem.thephpleague.com/docs/adapter/sftp-v3/)**
* **[Memory](https://flysystem.thephpleague.com/docs/adapter/in-memory/)**
* **[AWS S3](https://flysystem.thephpleague.com/docs/adapter/aws-s3-v3/)**
* **[AsyncAws S3](https://flysystem.thephpleague.com/docs/adapter/async-aws-s3/)**
* **[Google Cloud Storage](https://flysystem.thephpleague.com/docs/adapter/google-cloud-storage/)**
* **[MongoDB GridFS](https://flysystem.thephpleague.com/docs/adapter/gridfs/)**
* **[WebDAV](https://flysystem.thephpleague.com/docs/adapter/webdav/)**
* **[ZipArchive](https://flysystem.thephpleague.com/docs/adapter/zip-archive/)**

### Third party Adapters

* **[Azure Blob Storage](https://github.com/Azure-OSS/azure-storage-php-adapter-flysystem)**
* **[Gitlab](https://github.com/RoyVoetman/flysystem-gitlab-storage)**
* **[Google Drive (using regular paths)](https://github.com/masbug/flysystem-google-drive-ext)**
* **[bunny.net / BunnyCDN](https://github.com/PlatformCommunity/flysystem-bunnycdn/tree/v3)**
* **[Sharepoint 365 / One Drive (Using MS Graph)](https://github.com/shitware-ltd/flysystem-msgraph)**
* **[OneDrive](https://github.com/doerffler/flysystem-onedrive)**
* **[Dropbox](https://github.com/spatie/flysystem-dropbox)**
* **[ReplicateAdapter](https://github.com/ajgarlag/flysystem-replicate)**
* **[Uploadcare](https://github.com/vormkracht10/flysystem-uploadcare)**
* **[Useful adapters (FallbackAdapter, LogAdapter, ReadWriteAdapter, RetryAdapter)](https://github.com/ElGigi/FlysystemUsefulAdapters)**
* **[Metadata Cache](https://github.com/jgivoni/flysystem-cache-adapter)**
* **[Migration adapter (lazy)](https://github.com/antonsacred/flysystem-lazy-migration-adapter)**

You can always [create an adapter](https://flysystem.thephpleague.com/docs/advanced/creating-an-adapter/) yourself.

## Security

If you discover any security related issues, please email info@frankdejonge.nl instead of using the issue tracker.

## Enjoy

Oh, and if you've come down this far, you might as well follow me on [twitter](https://twitter.com/frankdejonge).
