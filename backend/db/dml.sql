USE app;

INSERT INTO Employee (name, login, password, type, salt) VALUES ('Rozella Cejka', 'user1', '7564657a09d204f3aff4dede432e7a6a5ded0ff7144b28a23c746727e27bc35da9f3ca22f06bdfb2a043b602f13ccd996448243072897da15da08f2461618705', 'attendant', '65b4976ddb349c56555e833aae5a2b70cec0a524');
INSERT INTO Employee (name, login, password, type, salt) VALUES ('Tamara Reidshaw', 'user2', '5ac61b5b450c308c059b3fad1b004d77b3b2db515eaa9cb20b750e02e694c34b7e2eebf9f53af7fda31e8ee9ad9f476d0267b10696ac50fcc048d2949210d709', 'attendant', '74c94b6d3f309ebe20877163e696845a4cd9ae20');
INSERT INTO Employee (name, login, password, type, salt) VALUES ('Latrena Laughren', 'user3', '76ff28bd4bed2f6a8ad3b607b091ecfdcdc0f7a5bae8e53084ce74181b2898bab112c559aaa67a8d473862f69b18bcf7e9d134a645968856bf69a4779365f7f1', 'attendant', 'de05607c6aa3877cb8983de0cf6c6ae1fd1b1ae9');
INSERT INTO Employee (name, login, password, type, salt) VALUES ('Luiz Henrique', 'user4', 'b6a7166b8c2e8933755eab1f775ca3174ef27fe95223d910198a28f679c6753596dac2402ebc17acbe5019f4819c9b4a7349584672774af0b9ffe504e0e0a64c', 'manager', 'e29370d66ffe094c25eec9b6c3faa2a1a0b7f20d');

insert into RestaurantTable (number) values (1);
insert into RestaurantTable (number) values (2);
insert into RestaurantTable (number) values (3);
insert into RestaurantTable (number) values (4);
insert into RestaurantTable (number) values (5);
insert into RestaurantTable (number) values (6);
insert into RestaurantTable (number) values (7);
insert into RestaurantTable (number) values (8);
insert into RestaurantTable (number) values (9);
insert into RestaurantTable (number) values (10);

insert into Day (name) values ('Monday');
insert into Day (name) values ('Tuesday');
insert into Day (name) values ('Wednesday');
insert into Day (name) values ('Thursday');
insert into Day (name) values ('Friday');
insert into Day (name) values ('Saturday');
insert into Day (name) values ('Sunday');

insert into Schedule (start_time, end_time, schedule_type, day_id) values ('11:00:00', '15:00:00', 'working_hours', 1);
insert into Schedule (start_time, end_time, schedule_type, day_id) values ('11:00:00', '15:00:00', 'working_hours', 2);
insert into Schedule (start_time, end_time, schedule_type, day_id) values ('11:00:00', '15:00:00', 'working_hours', 3);
insert into Schedule (start_time, end_time, schedule_type, day_id) values ('11:00:00', '22:00:00', 'working_hours', 4);
insert into Schedule (start_time, end_time, schedule_type, day_id) values ('11:00:00', '22:00:00', 'working_hours', 5);
insert into Schedule (start_time, end_time, schedule_type, day_id) values ('11:00:00', '22:00:00', 'working_hours', 6);
insert into Schedule (start_time, end_time, schedule_type, day_id) values ('11:00:00', '22:00:00', 'working_hours', 7);

insert into Schedule (start_time, end_time, schedule_type, day_id) values ('11:00:00', '20:00:00', 'reservation', 4);
insert into Schedule (start_time, end_time, schedule_type, day_id) values ('11:00:00', '20:00:00', 'reservation', 5);
insert into Schedule (start_time, end_time, schedule_type, day_id) values ('11:00:00', '20:00:00', 'reservation', 6);
insert into Schedule (start_time, end_time, schedule_type, day_id) values ('11:00:00', '20:00:00', 'reservation', 7);

INSERT INTO Category (name) 
VALUES 
('Entrada'),
('Prato Principal'),
('Bebida'),
('Sobremesa');

INSERT INTO Item(description, code, price, category_id)
VALUES 
("Crostini", "E1", 25.00, 1),
("Carpaccio de salmão defumado", "E2", 20.00, 1),
("Espaguete ao frutos do mar", "PP1", 30.00, 2),
("Lula grelhada com arroz negro", "PP2", 35.00, 2),
("Negroni", "B1", 15.00, 3),
("Mojito tradicional", "B2", 12.00, 3),
("Pudim de leite condensado", "S1", 23.00, 4),
("Torta de limão", "S2", 35.00, 4);

INSERT INTO PaymentMethod(name)
VALUES 
('Pix'),
('Dinheiro'),
('Cartão de crédito'),
('Cartão de débito');