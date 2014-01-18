set session foreign_key_checks=0;


/* create tables */

create table account
(
	msisdn varchar(30) not null,
	service_id int not null,
	telco_id int not null,
	register_time datetime not null,
	primary key (msisdn, service_id)
);


create table campaign_promote
(
	pid int not null auto_increment,
	publisher_id int not null,
	name varchar(100) not null,
	-- 0-100
	dial_config int not null comment '0-100',
	dial_count int not null,
	dial_shoot int not null,
	primary key (pid)
);


create table campaign_promote_service_map
(
	pid int not null,
	service_id int not null
);


create table partner_gateway
(
	partner_gw_id int not null auto_increment,
	name varchar(100) not null,
	username varchar(100) not null,
	password varchar(100) not null,
	primary key (partner_gw_id)
);


create table partner_gw_servcie_map
(
	partner_gw_id int not null,
	partner_service_id varchar(50) not null,
	service_id int not null,
	primary key (partner_gw_id, partner_service_id)
);


create table partner_gw_telco_map
(
	partner_gw_id int not null,
	partner_telco_id varchar(50) not null,
	telco_id int not null,
	primary key (partner_gw_id, partner_telco_id)
);


create table publisher
(
	publisher_id int not null auto_increment,
	name varchar(100),
	primary key (publisher_id)
);


create table quit_log
(
	msisdn varchar(30) not null,
	service_id int not null,
	telco_id int not null,
	register_time datetime not null,
	quit_time datetime not null
);


create table ringtone_params
(
	track_id bigint not null,
	primary key (track_id)
);


create table service
(
	service_id int not null auto_increment,
	shortcode varchar(20) not null,
	keyword varchar(10) not null,
	primary key (service_id)
);


create table subscriber_dn
(
	sub_dn_id varchar(50) not null,
	partner_gw_id int not null,
	partner_gw_txid varchar(100) not null,
	cmd varchar(100),
	shortcode varchar(20),
	keyword varchar(10),
	msisdn varchar(30) not null,
	telco_id int,
	response_data text not null,
	sys_time datetime not null,
	response_time datetime not null,
	primary key (sub_dn_id),
	unique (partner_gw_txid, partner_gw_id)
);


create table telco
(
	telco_id int not null,
	name varchar(100) not null,
	primary key (telco_id)
);


create table track_enty
(
	track_id bigint not null,
	parent_id bigint,
	pid int,
	shortcode varchar(20) not null,
	keyword varchar(10) not null,
	msisdn varchar(20) not null,
	telco_id int not null,
	sys_time datetime not null,
	-- 0:expried (not register)
	-- 1:register
	-- 2:shoot pexel
	status tinyint(3) comment '0:expried (not register)
1:register
2:shoot pexel',
	primary key (track_id)
);


create table track_enty_pending
(
	track_id bigint not null auto_increment,
	parent_id bigint,
	pid int,
	shortcode varchar(20) not null,
	keyword varchar(10) not null,
	msisdn varchar(20) not null,
	telco_id int,
	sys_time datetime not null,
	primary key (track_id)
);



/* create foreign keys */

alter table campaign_promote_service_map
	add foreign key (pid)
	references campaign_promote (pid)
	on update restrict
	on delete restrict
;


alter table track_enty
	add foreign key (pid)
	references campaign_promote (pid)
	on update restrict
	on delete restrict
;


alter table track_enty_pending
	add foreign key (pid)
	references campaign_promote (pid)
	on update restrict
	on delete restrict
;


alter table partner_gw_servcie_map
	add foreign key (partner_gw_id)
	references partner_gateway (partner_gw_id)
	on update restrict
	on delete restrict
;


alter table partner_gw_telco_map
	add foreign key (partner_gw_id)
	references partner_gateway (partner_gw_id)
	on update restrict
	on delete restrict
;


alter table subscriber_dn
	add foreign key (partner_gw_id)
	references partner_gateway (partner_gw_id)
	on update restrict
	on delete restrict
;


alter table campaign_promote
	add foreign key (publisher_id)
	references publisher (publisher_id)
	on update restrict
	on delete restrict
;


alter table account
	add foreign key (service_id)
	references service (service_id)
	on update restrict
	on delete restrict
;


alter table campaign_promote_service_map
	add foreign key (service_id)
	references service (service_id)
	on update restrict
	on delete restrict
;


alter table partner_gw_servcie_map
	add foreign key (service_id)
	references service (service_id)
	on update restrict
	on delete restrict
;


alter table quit_log
	add foreign key (service_id)
	references service (service_id)
	on update restrict
	on delete restrict
;


alter table account
	add foreign key (telco_id)
	references telco (telco_id)
	on update restrict
	on delete restrict
;


alter table partner_gw_telco_map
	add foreign key (telco_id)
	references telco (telco_id)
	on update restrict
	on delete restrict
;


alter table quit_log
	add foreign key (telco_id)
	references telco (telco_id)
	on update restrict
	on delete restrict
;


alter table subscriber_dn
	add foreign key (telco_id)
	references telco (telco_id)
	on update restrict
	on delete restrict
;


alter table track_enty
	add foreign key (telco_id)
	references telco (telco_id)
	on update restrict
	on delete restrict
;


alter table track_enty_pending
	add foreign key (telco_id)
	references telco (telco_id)
	on update restrict
	on delete restrict
;



