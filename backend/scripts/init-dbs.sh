#/bin/bash

mariadb --user='root' --password='root' <'/db/ddl.sql'
mariadb --user='root' --password='root' <'/db/dml.sql'
mariadb --user='root' --password='root' <'/db/ddl-test.sql'
