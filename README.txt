Ianus README

--------------------------------------------------------------------------------
0 - Contents
--------------------------------------------------------------------------------
1 - Version
2 - Licensing
3 - Brief Introduction
4 - Features/Change Log
5 - How To Use
6 - Contact 
--------------------------------------------------------------------------------
1 - Version
--------------------------------------------------------------------------------
README file version: 1.0.0
Date: 05/02/2013
Ianus version: 1.0.0
Date: 05/02/2013

--------------------------------------------------------------------------------
2 - Licensing
--------------------------------------------------------------------------------
Copyright (c) 2012 Antony D'Andrea

Permission is hereby granted, free of charge, to any person obtaining a copy of 
this software and associated documentation files (the "Software"), to deal in the 
Software without restriction, including without limitation the rights to use, copy, 
modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, 
and to permit persons to whom the Software is furnished to do so, subject to the 
following conditions:

The above copyright notice and this permission notice shall be included in all 
copies or substantial portions of the Software and the README.txt file.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, 
INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A 
PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT 
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION 
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE 
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
--------------------------------------------------------------------------------
3 - Brief Introduction
--------------------------------------------------------------------------------
Ianus is a PHP class that generates a menu from a table in a database. The menu
is given in the form of an unordered list which can be used by most jQuery plugins
to form a nice looking menu.

Ianus is the Latin name for Janus, who was the Roman god of doors and portals which
I thought would be relevant to a menu system.

Ianus started life as a bespoke piece of code in a website that had a two tier menu.
In the beginning, it wasn't very flexible. You couldn't have links in the top tier,
nor could you easily sort it.

Over the months it evolved and became a class. Version 3 was the most recent version.
By this point, you could sort within the database and have links in the top level 
among other things.

I found myself wanting to create similar menus in other places. So not only was 
it to be updated (to version 4) to become more flexible than ever, it was completely
separated and rewritten to provide a flexible menu system that can have as many 
tiers as you want.

I do have some ideas of what features I would like to add in the future.

--------------------------------------------------------------------------------
4 - Features/Change Log
--------------------------------------------------------------------------------
This is technically version 4.0 of the UniverseSite navigation module which
becomes version 1.0 of Ianus.
The main change from the version 3.2 to this one is the fact that it has been
refactored so that it can be plugged into any project and produce the same 
functionality.
List of changes:
- File name 'navigation' and class name 'module_navigation' both changed to
 'NavigationController'.
- New navigation config file which contains settings. Written in json because
  it is well supported in core PHP.
- Functions to support the reading of Json config file.
- UniverseSite specific components removed.
- No longer assumes that menu category headers are either an header OR a link,
  they can be both.
- Updated the PHP mySQL commands to use mySQLi to conform with future standards
  (mySQL commands are being deprecated as of PHP 5.5.0 and deleted in the future).
- Can have as many levels of menu as you like. The class adapts to your data.
- There is a degree of error handling which gives some meaningful message when 
  hit.
--------------------------------------------------------------------------------
5 - How To Use
--------------------------------------------------------------------------------
1)
Simply include the NavigationController.php file in your project and do:
    $nav = new NavigationController("path/to/config/nav_config.json");  
    print $nav->renderMenu();
You must supply the path of the config file(including the file name) as a parameter when
you create the new object.
You are also free to add an existing mysqli connection as the second parameter,
but I would not recommend this because it could be closed by the class. The class
is capable of making its own one.

2)
The config file, nav_config.json, provides all of the settings required for 
this to work.
You must make a table which has fields that match up to the ones given. 

dbName - the name of your database
tableName - the name of the table
host - the IP address of the server (or localhost if running locally).
username - username to access table. Only read privilege. 
password - this is optional. Only if your username requires one.

fields - here you must put the fields that you created in the table
menu_id - unique id of each menu item
menu_label - the text that appears for each menu item
menu_category_menu_id - if the menu item is part of a subcategory, the menu_id of 
the parent menu should be here. If it is in the top tier, then leave this blank.
menu_sort_order - the sort order of your menu, local to each subcategory/level, i.e. 
if you set this to 3, it will appear beneath 1 and 2, but only of the menus with the
same menu_category_menu_id, 
menu_href - the hyperlink that the menu links to.

Once you have made a table with those fields and have input them into the config file,
your menu should just appear.
--------------------------------------------------------------------------------
6 - Contact
--------------------------------------------------------------------------------
Email: contactme@antonydandrea.com
Twitter: @antonydandrea1