PingApp
============

### What is PingApp?

A personal SMS check-in app - check if your friends respond.

### System Requirements

To install the platform on your computer/server, the target system must meet the following requirements:

* PHP version 5.3.0 or greater
* Database Server
    - MySQL version 5.5 or greater
    - PostgreSQL support is coming
* An HTTP Server. PingApp is known to work with the following web servers:
    - Apache 2.2+
    - nginx
* Unicode support in the operating system

### Downloading

1. Open a terminal
2. Clone the project ```git clone https://github.com/ushahidi/pingapp.git```
3. Update the submodules ```git submodule update --init```
4. Install the Twilio packages using [Composer](http://getcomposer.org) by running ```composer install```.


### Installing

1. Create a database
2. ```cp application/config/database.template application/config/database.php```


3. Edit ```application/config/database.php``` and set database, username and password params

	```
	return array
	(
		'default' => array
		(
			'type'       => 'mysql',
			'connection' => array(
				'hostname'   => 'localhost',
				'database'   => 'pingapp',
				'username'   => 'pingapp',
				'password'   => 'pingapp',
				'persistent' => FALSE,
			),
			'table_prefix' => '',
			'charset'      => 'utf8',
			'caching'      => TRUE,
			'profiling'    => TRUE,
		)
	);
	```

4. Install the database schema using migrations

	```./minion --task=migrations:run --up```

5. ```cp application/config/init.template application/config/init.php```
6. Edit ```application/config/init.php``` and change base_url to point the the httpdocs directory in your deployment
7. ```cp application/config/auth.template application/config/auth.php```
8. ```cp application/config/modules.template application/config/modules.php```
9. ```cp httpdocs/template.htaccess httpdocs/.htaccess```
10. Edit ```httpdocs/.htaccess``` and change the RewriteBase value to match your deployment url
11. Create directories ```application/cache``` and ```application/logs``` and make them writable

### Configuration

Base config files are in ```application/config/```.

### Twilio

1. Get a free account at https://www.twilio.com/.
2. ```cp application/config/sms.template application/config/sms.php```
3. set `account_sid` and `auth_token`

  ```
  'options' => array(
      'account_sid' => 'TwilioAccountSID20130924',
      'auth_token'  => 'TwilioAuthToken20130924'
    )
  ```


### Default Login

The default login credentials are admin / westgate
