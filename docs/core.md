Basics
======

The application is laid out in a typical model-view-controller configuration.  All of the framework logic is handled in the core libraries, the root index.php and any rules scripts act as the controllers, and all of the html and scaffolding resides in the /html folder.  See below for a quick start guide.

API documentation can be found in /docs/api

Structure
---------

---
### core files
#### base 
##### ObjFactory
[/core/base/ObjFactory.php]

Since this application makes use of the magic method __autoload, this acts as a dynamic object loader, searching the /core directory tree for classes referenced.

##### OverLoader
[/core/base/OverLoader.php]
The parent class of all domain objects.  Includes methods for dynamically accessing properties and hooks to manage data persistence with data access objects.


#### config
##### config.xml
[/core/config/config.xml]
database, error handling, logging, and general application settings can be managed in this file

##### Configuration
[/core/config/Configuration.php]
manages internal xml files

##### Constants
[/core/config/Constants.php]
additional configuration settings.  set the email notification address here.

##### Definitions
[/core/config/definitions.php]
defines the internal structure of the app

##### Domain Mapper
[/core/config/domainMapper.php]
Setup getters and setters here for domain objects

##### Messages
[/core/config/message.php]
define system wide messages here

##### Page
[/core/config/pages.xml]
The page objects in the site.  Any html page entities must be defined here.  The application routing takes the format of http://yourdomain.com/display/pagename.  pagename is defined here and in the database (see the database section).

##### Templates
[/core/config/templates.xml]
Defined the actual template files associate with pages and page components.  You also register javascript dependencies here.



#### conversion

##### xmlToArray
[/core/conversion/xmlToArray.xml]
for internal conversion of xml files


#### db
##### mysql
[/core/db/mysql/mysql.php]
mysql wrapper implemented from the DBWrapper interface.  Add additional database connectors here from the same interface.

##### DBConn
[/core/mysql/DBConn.xml]
Singleton database connection handler

##### DBWrapper
[/core/mysql/DBWrapper.php]
Interface for defining db connectors

##### QueryBuilder
[/core/mysql/QueryBuilder.php]
sql query generator

##### domain
[/core/domain/[domain object].php]
Create your domain objects here.  The must extend the Overloader class.  Then you can add all your domain business rules code here.  The steps to creating a usable domain object that has persistence is as follows:
1. Create a domain object here that extends Overloader:

class User extends Overloader
{

	private function userMethods(){ /.../ }

	public function doSomething(){ /.../ }

}

2. Add a userdao object to the service directory (/core/service) that extends the Basedao class:

class Userdao extends Basedao

{

	protected $dataClass = 'User'; //this is important - refers to the domain name you chose for this entity

	public function __construct($id=0,$keyName = 'id',$table='users'){ //you can rewrite $table if you want a different table name (such as testing) in the db

        if($id){

                parent::__construct($id, $keyName, $table);

        }

    }
}

3. For persistence to work you must also create the table (next version should allow you to do that on the fly)
4. To control which properties you want to allow getters and setters, you must edit the /core/config/domainMapper.xml file

#### Exception
##### coreException
[/core/exception/coreException.php]
App specific exception extended from the PHP Exception class.

#### Func
##### autoload
[/core/func/autoload.php]
Responsible for dynamically loading classes

##### Common Functions
[/core/func/common.functions.php]
Add any global functions you wish to use here.

##### Header Preamble
[/core/func/header.preamble.php]
add any header() directives here

#### Logger
##### autoload
[/core/logger/Logger.php]
Handles creating logs

#### Service
##### BaseDAO
[/core/service/Basedao.php]
The parent class for handling all db persistence.  Add your Data Access Objects here, extending the Basedao class.  See domain section on tips for doing this.

#### Web
##### EmailAction
[/core/web/EmailAction.php]
Handles all phpmailer transactions.  

##### Page
[/core/web/Page.php]
Responsible for rendering and constructing all page entities in the app

##### Request
[/core/web/Request.php]
Provides an object wrapper to the Server Request Object

##### Session
[/core/web/Session.php]
Provides an object wrapper to the Server Session Object

##### Templator
[/core/web/Templator.php]
Responsible for constructing pages and components




---
### html / interface files

#### main.tpl
[/html/main.tpl]

This is the main html container for the application.  Right now it's mono-themed, but you can create seperate sub directories under html and point to them in the config.xml file.  All of the sub components in main load to the $oei_content placeholder in the body section.

#### Components
[/html/components]
Invididual pages and any other html snippets reside here.  You must register them to be used as page objects (in core/config/templates.xml)

#### Client Side Dependencies
The suggested structure is to place your javascripts, css, images, below the html directory. You can use the {$html_path} global keyword to refer to the src directory rather than hardcoding it 
e.g: <script type="text/javascript" src="${html_path}/js/myplugins.js"></script>

---
### Rules
There are function hooks you can utilize to load and execute methods during page loads and page actions.  They have a specific file naming scheme and are as follows:

#### Loaders
place any scripts you wish to run when a page generates in the /rules/loaders directory.  You must also register it in the /config/templates.xml file:
under the <template></template> node, add an entry like <initializeRule>mainpageLoader.php</initializeRule>
and add the script to the loaders directory.

#### Methods
Rules run as methods and are executed by a specific url pattern or query string.

First, save your script in /rules/methods in the following format:

run.[methodName].php 

and it will execute before a page loads when you call either:

http://myapp.com/?method=methodName

or

http://myapp.com/m/methodName

---
### API
[/api/index.php]
There is an internal api gateway for ajax calls.  

You can call it with a url such as http://myapp.com/api/?m=findUser

where m = the method name.  

Parse any other query data and use the apiRun class to parse out the details.  See the /api/index.php for details.

---
### Tests
A number of test cases are included in the /test directory.

---
### The Database
There are two types of tables in the database.  The pages table and any other domain objects you define in the application.


the pages table has the following fields:


* id  = autoincremented number
* pageID = this id needs to match the domain object id in the domainMapper.xml file
* title = page title
* RequireLogin = set to Y and the page loader will check for a loggedin user session object and forward to a login page if it doesn't exist
* pagetype = Deprecated

The other types of tables (domain object persistence tables) just need an id and whatever fields you choose to build them with.

---
### Quick Start
####Getting a Page to Work
1. Config the database settings in /core/config/config.xml
2. Create a main.tpl and place in /html
3. Create a hello world component called hello.tpl and save it in /html/components
4. By default, the main index.php file calls a page named 'landing', so open the /core/config/pages.xml file and make sure the landing entry looks like:
<page><pageID>landing</pageID><pageTemplate>main</pageTemplate>
            <componentmapper>
                    <component><componentID>helloworld</componentID><htmltag>oei_content</htmltag></component>
			</componentmapper>
		</page>	
5. In templates.xml, create an entry like this:
<component>
			<componentID>helloWorld</componentID>
			<fileName>hello.tpl</fileName>
			<location>oei_Components</location>			
		</component>
6. As of the current release, you must also create an entry for this page in the database.  So in the pages table you would add:
INSERT INTO `pages` VALUES(40, 'helloWorld', 'HelloWorld Test', 'N', NULL);

####Creating a persistent domain object and manipulating it
1. Create a class that extends the Overloader.php class in /core/domain (e.g. User)
2. Create a class that extends the Basedao.php class in /core/service/ (e.g. Userdao.php)
3. Set the default $table in the dao class constructor
4. Add any setters and getters to the domainMapper.xml file
5. You can test in the index.php file or make a script that runs as a method (see Rules)
6. Create, modify, and save your object

$user = new User;

$user->setFname('John');

$user->setLname('Smithington');

$user->setEmail('john@test.com');

$user->save();

$user2 = new User;

$user2->loadbyField('email','john@test.com');

echo $user2->getLname(); //should say 'Smithington'