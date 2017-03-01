INSERT INTO grr_setting VALUES ('grr_mail_port', '25');
INSERT INTO grr_setting VALUES ('grr_mail_encrypt', '');
INSERT INTO grr_setting VALUES ('grr_print_auto', '1');
update grr_setting set value = "3.6" where name = "version";
drop table if exists grr_files;
Create table grr_files(id int not null auto_increment, id_entry int, file_name varchar(50), public_name varchar(50),Primary key (id), constraint fk_idEntry foreign key (id_entry) references resatest.grr_entry(id));
