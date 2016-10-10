<?xml version="1.0" encoding="UTF-8"?>
<package packagerversion="1.8.0" version="2.0" xmlns="http://pear.php.net/dtd/package-2.0" xmlns:tasks="http://pear.php.net/dtd/tasks-1.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://pear.php.net/dtd/tasks-1.0
    http://pear.php.net/dtd/tasks-1.0.xsd
    http://pear.php.net/dtd/package-2.0
    http://pear.php.net/dtd/package-2.0.xsd">
 <name>Swift</name>
 <channel>pear.swiftmailer.org</channel>
 <summary>Free Feature-rich PHP Mailer.</summary>
 <description>
   Swift Mailer integrates into any web app written in PHP 5, offering a flexible and elegant object-oriented approach to sending emails with a multitude of features. 
 </description>
 <lead>
  <name>Fabien Potencier</name>
  <user>fabpot</user>
  <email>fabien.potencier@symfony-project.org</email>
  <active>yes</active>
 </lead>
 <lead>
  <name>Chris Corbyn</name>
  <user>d11wtq</user>
  <email></email>
  <active>no</active>
 </lead>
 <date>{{ date }}</date>
 <time>{{ time }}</time>
 <version>
  <release>{{ version }}</release>
  <api>{{ api_version }}</api>
 </version>
 <stability>
  <release>{{ stability }}</release>
  <api>{{ stability }}</api>
 </stability>
 <license uri="http://opensource.org/licenses/mit-license.php">MIT</license>
 <notes>-</notes>
 <contents>
   <dir name="/">
     <file name="CHANGES" role="doc" />
     <file name="LICENSE" role="doc" />
     <file name="README" role="doc" />
     <file name="VERSION" role="doc" />
     <dir name="lib">
       <file install-as="mime_types.php" name="mime_types.php" role="php" />
       <file install-as="preferences.php" name="preferences.php" role="php" />
       <file install-as="swift_init.php" name="swift_init.php" role="php" />
       <file install-as="swift_required.php" name="swift_required_pear.php" role="php" />
       <dir name="dependency_maps">
         <file install-as="dependency_maps/cache_deps.php" name="cache_deps.php" role="php" />
         <file install-as="dependency_maps/mime_deps.php" name="mime_deps.php" role="php" />
         <file install-as="dependency_maps/transport_deps.php" name="transport_deps.php" role="php" />
         <file install-as="dependency_maps/message_deps.php" name="message_deps.php" role="php" />
       </dir>
       <dir name="classes">
         <file install-as="Swift.php" name="Swift.php" role="php" />
         <dir name="Swift">
{{ files }}
         </dir>
       </dir>
     </dir>
   </dir>
 </contents>
 <dependencies>
  <required>
   <php>
    <min>5.2.4</min>
   </php>
   <pearinstaller>
    <min>1.4.0</min>
   </pearinstaller>
  </required>
 </dependencies>
 <phprelease />
</package>
