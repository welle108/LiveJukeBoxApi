# LiveJukeBox
An Android App for Live Music. To best view this file, go to

## Setup Instructions
##### Warning: This app was developed in Windows and has never been tested on a MacOS machine. You can install xampp/composer on either so it will probably work regardless, but if you have any issues try on a Windows machine

#### 1) Install xampp on your machine
* [https://www.apachefriends.org/download.html]()

#### 2) Install the PHP dependency manager Composer: 
* [Windows](https://getcomposer.org/download/)
* [MacOS](https://pilsniak.com/install-composer-mac-os/)

To verify that Composer has been installed, open a terminal and run the command
    
	composer --version

If installation was succesful you will see a message like

    C:\Users\test>composer --version
    Composer version 1.8.5 2019-04-09 17:46:47
	


#### 3)  Find the root directory for xxamp on your machine and navigate to the htdocs folder.
In Windows, the default location for this is 

>C:/xampp/htdocs
	
If you have trouble finding the root directory on MacOS, use these [suggestions](https://stackoverflow.com/questions/44989243/unable-to-find-htdocs-on-xampp/54727322#54727322)

Once inside this folder, open a terminal and run the command

    composer create-project slim/slim-skeleton LiveJukeBoxApi

This might take a few minutes

#### 4) Open project directory in xampp/htdocs/LiveJukeBoxApi 
Navigate to the public folder. Replace the index.php file with the one in the PHP_Backend folder of my submitted code, then create a directory called "includes" and copy the contents of the includes folder in my source code into this folder. Once properly set up it should like this:

##### htdocs/LiveJukeBoxApi
![](https://i.imgur.com/x2DGScp.png)
##### LiveJukeBoxApi/public
![](https://imgur.com/HAZ3UYZ.png)
##### LiveJukeBoxApi/includes
![](https://imgur.com/Mc9AeUp.png)

#### 5) Configure php to send email
Go back to the xampp root directory and navigate to the php folder. From there open the php.ini folder, press ctrl+f, find the sendmail function and replace what is there with

    SMTP=smtp.gmail.com
    smtp_port=587
    sendmail_from = livejukeboxalerts@gmail.com
    sendmail_path = "\"D:\xampp\sendmail\sendmail.exe\" -t"

The last line for you will be different based on where xammp is installed on your machine and what your os is. Now go to the sendmail directory in xammp and open sendmail.ini. Delete everything and replace it with

    [sendmail]

    smtp_server=smtp.gmail.com
    smtp_port=587
    error_logfile=error.log
    debug_logfile=debug.log
    auth_username=livejukeboxalerts@gmail.com
    auth_password=Avenue22
    force_sender=livejukeboxalerts@gmail.com

#### 6) Open Xampp and run the Apache and MySQL local servers
![](https://imgur.com/viCbQvL.png)

#### 7) Create Database
Open a browser and go to http://localhost/phpmyadmin/

Once inside of phpmyadmin, open an SQL terminal then copy and run the contents of LiveJukeBox.sql. This will create the database along with some preexisting data. 

#### 6) Install Android Studio on your machine

##### * [Download Link](https://developer.android.com/studio/?gclid=EAIaIQobChMItqC5vaSj4gIVCcRkCh0_rAENEAAYASAAEgJZHfD_BwE)
##### * [Installation Instructions](https://developer.android.com/studio/install)

You should now be able to unzip and run the project in the Android directory of the LiveJukeBox source code

#### 7) Create an emulator device
![](https://imgur.com/A8ILfbb.png)

From here you will be able to run and test LiveJukeBox
If you would like to log in to a previously made test account, use the credentials:

##### Email: scott<span>@</span>gmail.com
##### Password: password
