create database if not exists wesweb_forum;
use wesweb_forum;

create table if not exists user (
	id          bigint(11) unsigned primary key AUTO_INCREMENT,
	email       varchar(128) NOT NULL unique,
	username    varchar(45) NOT NULL unique,
	password    varchar(128) NOT NULL,
	firstname   varchar(45) NOT NULL,
	lastname    varchar(45) NOT NULL,
	admin       tinyint NOT NULL,
	created_at  datetime NOT NULL,
	updated_at  datetime NOT NULL
);

create table if not exists category (
	id                  bigint(11) unsigned AUTO_INCREMENT primary key,
	title               varchar(45) NOT NULL,
	description         varchar(128) NULL,
	show_in_navigation  tinyint NOT NULL,
	created_at          datetime NOT NULL,
	updated_at          datetime NOT NULL 
 );

select * from user;