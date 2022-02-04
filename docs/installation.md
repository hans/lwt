# LWT Installation
* *Last update*: January 10 2022

## Windows 10
### Possibility (a): Installation EasyPHP / LWT on Win10

Step 1a: Download "vcredist_x86.exe" from https://www.microsoft.com/en-us/download/details.aspx?id=30679 
Step 1b: Choose the x86 version and download.
Step 1c: Run the installer "vcredist_x86.exe" in the Downloads folder.

Step 2a: Go to https://www.easyphp.org/easyphp-devserver.php
Step 2b: Download "EasyPHP DevServer 17.0".
Step 2c: Open your Downloads folder and run the downloaded "EasyPHP-Devserver-17.0-setup.exe". 
Step 2d: Install into "C:\Program Files (x86)\EasyPHP-Devserver-17".

Step 3: [install Composer](https://getcomposer.org/download/)

Step 4: 
* **If you installed composer globally**
  * Run ``composer create-project hugofara/lwt``.
* **If you installed composer locally**
  * Got to the folder where composer.phar is located
  * Run ``php composer.phar create-project hugofara/lwt``

Step 5: Now go into "C:\Program Files (x86)\EasyPHP-Devserver-17\eds-www\lwt". Rename the file "connect_easyphp.inc.php" to "connect.inc.php". Sometimes the "php" extension is hidden, so be careful! You can display file extensions via the Windows Explorer settings and check it.

Step 6a: Start EasyPHP via Desktop Icon (Devserver 17). In the Task Bar near the clock appears the EasyPHP app icon (it may be hidden!).
Step 6b: LWT can now be started. Right-Click on the EasyPHP icon in the taskbar, choose "Servers->Start/Restart all Servers", open a browser, and open http://127.0.0.1/lwt (please bookmark).

Step 7: You may now install the LWT demo database, or define the first language you want to learn. 

If you start up Windows, you must repeat Step 6a+b. 

If you want to start EasyPHP every time you start Windows and avoid Step 6a, put an EasyPHP link into "C:\Users\(YourUID)\AppData\Roaming\Microsoft\Windows\Start Menu\Programs\Startup". 

Now you must only do step 6b to start LWT.

### Possibility (b): Installation XAMPP / LWT on Win10

Step 1a: Go to https://www.apachefriends.org/download.html 
Step 1b: Download "XAMPP for Windows 7.3.30 (64 bit)" (or a higher PHP 7 version).
Step 1c: Open your Downloads folder and run the downloaded "xampp-windows-x64-xxx-installer.exe". Please install the components Apache, MySQL, PHP and phpMyAdmin into the folder C:\xampp.

Step 2: [install Composer](https://getcomposer.org/download/)

Step 3:
* **If you installed composer globally**
  * Run ``composer create-project hugofara/lwt``.
* **If you installed composer locally**
  * Got to the folder where composer.phar is located
  * Run ``php composer.phar create-project hugofara/lwt``

Step 4: Now go into "C:\xampp\htdocs\lwt". Rename the file "connect_xampp.inc.php" to "connect.inc.php". (Sometimes the "php" extension is hidden, so be careful! You can display file extensions via the Windows Explorer settings and check it.)

Step 5a: Start the "XAMPP Control Panel" ("C:\xampp\xampp-control.exe") and start the two modules Apache and MySQL. Now the two module names should have a green background color. 
Step 5b: LWT can now be started. Open a browser, and open http://localhost/lwt (please bookmark).

Step 6: You may now install the LWT demo database, or define the first language you want to learn. 

If you start up Windows, you must repeat Step 5a+b. 

If you want to start "XAMPP Control Panel" every time you start Windows and to avoid Step 5a, put a "XAMPP Control Panel" link to "C:\xampp\xampp-control.exe" into "C:\Users\(YourUID)\AppData\Roaming\Microsoft\Windows\Start Menu\Programs\Startup". To autostart also the Apache and MySQL modules, please open "Config" within the XAMPP Control Panel and check the two checkboxes.

Hint: To fix a "XAMPP Control Panel" error "Xampp-control.ini Access is denied", please read and do the instructions in https://www.codermen.com/fix-xampp-server-error-xampp-control-ini-access-is-denied/

Now you must only do step 5b to start LWT.

## macOS 10.10+

I no longer use Apple hardware. 
Therefore, I cannot test LWT itself and the following installation instructions on a Mac.
Your help is very much appreciated.

Step 1: Go to https://www.mamp.info/en/downloads/

Step 2: Download "MAMP & MAMP PRO" (currently version 6.5).

Step 3: Double-click on the downloaded installation package "MAMP_MAMP_PRO_xxx.pkg", accept the license, click on "Install for all users..." and on "Continue", on the next panel titled "Standard Install on Macintosh HD" click on "Customize", deselect "MAMP PRO", and click Install. You must enter your password. After this step MAMP is installed within a folder named "MAMP" in the Applications folder.

Step 4: [install Composer](https://getcomposer.org/download/)

Step 5: 
* **If you installed composer globally**
  * Run ``composer create-project hugofara/lwt``.
* **If you installed composer locally**
  * Got to the folder where composer.phar is located
  * Run ``php composer.phar create-project hugofara/lwt``

Step 7: Go to /Applications/MAMP/htdocs/lwt. Rename the file connect_mamp.inc.php to connect.inc.php.

Step 8: Open MAMP.app in /Applications/MAMP. Accept the messages from the firewall. Apache and MySQL start automatically.

Step 9: LWT can now be started in your web browser, go to: http://localhost:8888/lwt.

Step 10: You may install the LWT demo database, or define the first language you want to learn. 

If you want to use LWT again, just do step 8 and 9.
The local webserver (MAMP) will be automatically stopped by quitting the MAMP application. 

## Linux (tested Linux Mint 21)

Step 1: Open a Terminal, type and execute the following commands:

Step 1a: Installation of LAMP:

```bash
sudo apt-get update
sudo apt-get install apache2 libapache2-mod-php php php-mbstring php-mysql mysql-server
```

Step 1b: Set MySQL root Password to "abcxyz" 

sudo mysql
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'abcxyz';
FLUSH privileges;
QUIT; 

Step 1c: Check MySQL access:

mysql -u root -p
abcxyz
(if you see the MySQL prompt 'mysql>', everything is OK)
QUIT;

Step 3: [install Composer](https://getcomposer.org/download/)

Step 4: 
* **If you installed composer globally**
  * Run ``composer create-project hugofara/lwt``.
* **If you installed composer locally**
  * Got to the folder where composer.phar is located
  * Run ``php composer.phar create-project hugofara/lwt``

Step 5: Rename the file connect_xampp.inc.php in /[... Path to downloaded LWT ...]/lwt to connect.inc.php.

Step 6: Edit this  file connect.inc.php and set the MySQL password in line 
$passwd = "";
Change it to
$passwd = "abcxyz";
Save the edited file connect.inc.php.

Step 6: Open a Terminal window, type and execute the following commands:

```bash
sudo rm /var/www/html/index.html
sudo mv /[... Path to downloaded LWT ...]/lwt /var/www/html
sudo chmod -R 755 /var/www/html/lwt
sudo service apache2 restart
sudo service mysql restart
```

Step 7: LWT can now be started in your web browser, go to: http://localhost/lwt.

Step 8: You may install the LWT demo database, or define the first language you want to learn. 

If you want to use LWT again, just do step 7. 

## Upgrade LWT

Step 1: Backup the LWT directory. Backup your database (within LWT).

Step 2: go to the LWT folder
* **If you installed composer globally**
  * Run ``composer update``.
* **If you installed composer locally**
  * Got to the folder where composer.phar is located
  * Run ``php composer.phar update``

Step 4: Copy the following (if not already at its place and OK) from your LWT backup into the LWT directory: "connect.inc.php" and the whole "media" sub-directory (if you created one; contains your MP3 audio files).

Step 5: Clear the web browser cache and open LWT as usual. 
