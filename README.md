# smtpapps

## SMTP mail logging based on Postfix + Fluentd + PostgreSQL

### Install requirements
1. You will need a Postfix server (it should work well with v 2.11 or above)
2. Install the fluentd script (td-agent version)
	1. Download and install fluentd plugin postgres from https://github.com/uken/fluent-plugin-postgres
	2. Put td-agent.conf in /etc/td-agent/
	3. Put out_postgres.rb file in /opt/td-agent/embedded/lib/ruby/gems/2.1.0/gems/fluent-plugin-postgres-0.0.1/lib/fluent/plugin
3. You will need a PostgreSQL server
	1. Install the smtpapps-script.sql

### GUI Setup
1. Please rename and put the config.ini-example file outside your apache document root.
2. Edit and set the path to the config file in session_start.php file.



## Crontab file
The script does a (daily) check of failed mails and then sends a mail with the results.

### Setup
1. Put the file into a directory.
2. Set the path to the GUI config file.
3. sudo crontab -e
4. \# Comprobación diaria de errores en e-mails
0 7 * * * /usr/bin/php /path-to-the-file/resumen-diario.php


fluentd script powered by https://github.com/uken/fluent-plugin-postgres
Form powered by phpFormGenerator http://phpformgen.sourceforge.net