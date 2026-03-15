# What is Simply-Web?
Simply-Web is a lightweight near native PHP framework.
A side project born from boredom. This framework is mainly used for those who
are confused by Laravel, or simply want to learn how PHP works. 
It can also be used for lightweight web applications that does not require
special environments and wants to go the simple route. 

# Why use Simply-Web?
When you are creating a small PHP project, Simply-Web is the way forward.
Utilising the model-view-controller patterns, your main code logic is not exposed to the internet. 
We have also included built-in modules for you to quickly do things without writing it yourself.
From sending emails, user authentications and more quickly. 

# Requirements to run Simply-Web
 - PHP 8.2 and higher (up to 8.5), any PHP version over 8.0 should still work.
 - Ability to make /public the web root folder. 
 - .htaccess compatibility

# Some CLI commands
 - Update core systems (not modules or resources): sw update
 - Install a module from repo: sw install <package>

# Repository Information
The default repository is located at https://repo.surden.me/packages/,
you can change the default repo to a community one or other ones by changing this via the database table: server_configs
or you can update this quickly via CLI: sw change-repo <repo-link e.g. https://repo.somedomain.com/packages/>

# Our framework's goal
 - Lightweight
 - Available on any platform (e.g. HestiaCP, XAMPP, Apache2)
 - Minimal dependencies (PHP 8.0+)
 - Simple Syntax
 - Built-in ready to use modules.
 - Repo to install and/or update modules from our repo server. 

# Disclaimers
This framework is super simple, while not on the same level as Laravel, it is quite educational to
get a deeper understanding of how PHP works, with database handling and much more. Feel free to look around,
edit anything you like and make your own. 

We are not responsible for any data leaks or damages as this is not production ready.