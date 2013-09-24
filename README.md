PingApp
============

### What is PingApp?

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

### Installing

1. Create a database
2. Copy ```application/config/database.template``` to ```application/config/database.php```
3. Edit ```application/config/environments/development/database.php``` and set database, username and password params

	```
	return array
	(
		'default' => array
		(
			'type'       => 'mysql',
			'connection' => array(
				'hostname'   => 'localhost',
				'database'   => 'lamu',
				'username'   => 'lamu',
				'password'   => 'lamu',
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

5. Copy ```application/config/init.template``` to ```application/config/init.php```
6. Edit ```application/config/init.php``` and change base_url to point the the httpdocs directory in your deployment
7. Copy ```application/config/auth.template``` to ```application/config/auth.php```
8. Copy ```application/config/modules.template``` to ```application/config/modules.php```
9. Copy ```httpdocs/template.htaccess``` to ```httpdocs/.htaccess```
10. Edit ```httpdocs/.htaccess``` and change the RewriteBase value to match your deployment url

### Configuration

Base config files are in ```application/config/```.

### Default Login

The default login credentials are admin / westgate