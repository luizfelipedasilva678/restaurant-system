CREATE DATABASE IF NOT EXISTS test;

USE test;

CREATE TABLE IF NOT EXISTS Employee(
  id int not null auto_increment,
  name varchar(128) not null,
  login varchar(128) not null unique,
  password varchar(128) not null,
  salt varchar(128) not null,
  type ENUM('attendant', 'manager') not null,
  constraint employee_pk PRIMARY KEY (id)
)ENGINE=INNODB;


CREATE TABLE IF NOT EXISTS RestaurantTable(
  id int not null auto_increment,
  number int not null unique,
  constraint restaurant_table_pk PRIMARY KEY (id)
)ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS Client(
  id int not null auto_increment,
  name varchar(128) not null,
  constraint client_pk PRIMARY KEY (id)
)ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS ClientPhone(
  id int not null auto_increment,
  phone varchar(128) not null,
  client_id int not null,
  constraint client_phone_pk PRIMARY KEY(id),
  constraint client_phone_client_id_fk FOREIGN KEY(client_id) REFERENCES Client(id) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS Category(
  id int not null auto_increment,
  name varchar(128) not null,
  constraint category_fk PRIMARY KEY(id)
)ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS Item(
  id int not null auto_increment,
  description varchar(128) not null,
  code varchar(128) not null unique,
  price double not null,
  category_id int not null,
  constraint item_pk PRIMARY KEY(id),
  constraint item_category_id_fk FOREIGN KEY(category_id) REFERENCES Category(id) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS PaymentMethod(
  id int not null auto_increment,
  name varchar(128) not null,
  constraint payment_method_pk PRIMARY KEY(id)
)ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS Reservation(
  id int not null auto_increment,
  start_time datetime not null,
  end_time datetime not null,
  client_id int not null,
  employee_id int not null,
  restaurant_table_id int not null,
  status ENUM('active', 'inactive', 'completed') not null,
  constraint reservation_pk PRIMARY KEY (id),
  constraint reservation_client_id_fk FOREIGN KEY (client_id) REFERENCES Client (id) ON UPDATE CASCADE ON DELETE RESTRICT,
  constraint reservation_employee_id_fk FOREIGN KEY (employee_id) REFERENCES Employee (id) ON UPDATE CASCADE ON DELETE RESTRICT,
  constraint reservation_restaurant_table_id_fk FOREIGN KEY (restaurant_table_id) REFERENCES RestaurantTable (id) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS TableOrder(
  id int not null auto_increment,
  table_id int not null,
  client_id int not null,
  status ENUM('open', 'completed') not null,
  constraint table_order_pk PRIMARY KEY(id),
  constraint table_order_client_id_fk FOREIGN KEY (client_id) REFERENCES Client (id) ON UPDATE CASCADE ON DELETE RESTRICT,
  constraint table_order_table_id_fk FOREIGN KEY (table_id) REFERENCES RestaurantTable (id) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS OrderItem(
  id int not null auto_increment,
  quantity int not null,
  table_order_id int not null,
  item_id int not null,
  constraint order_item_pk PRIMARY KEY(id),
  constraint order_item_table_order_id_fk FOREIGN KEY (table_order_id) REFERENCES TableOrder (id) ON UPDATE CASCADE ON DELETE RESTRICT,
  constraint order_item_item_id_fk FOREIGN KEY (item_id) REFERENCES Item (id) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS Bill(
  id int not null auto_increment,
  total double not null,
  discount double not null,
  creation_date datetime not null,
  employee_id int not null,
  payment_method_id int not null,
  table_order_id int not null,
  constraint bill_pk PRIMARY KEY(id),
  constraint bill_employee_id_fk FOREIGN KEY(employee_id) REFERENCES Employee(id) ON UPDATE CASCADE ON DELETE RESTRICT, 
  constraint bill_table_order_id_fk FOREIGN KEY(table_order_id) REFERENCES TableOrder(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  constraint bill_payment_method_id_fk FOREIGN KEY(payment_method_id) REFERENCES PaymentMethod(id) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS Day (
  id int not null auto_increment,
  name varchar(30) not null,
  constraint day_pk PRIMARY KEY(id)
)ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS Schedule(
  id int not null auto_increment,
  start_time TIME not null,
  end_time TIME not null,
  schedule_type ENUM('reservation', 'working_hours') not null,
  day_id int not null,
  constraint schedule_pk PRIMARY KEY(id),
  constraint schedule_day_id_fk FOREIGN KEY (day_id) REFERENCES Day (id) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=INNODB;