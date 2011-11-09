<?php

/*|

** Note: Please modify the SQL to your needs.

SQL - PostgreSQL:
create table "user" (
    id bigserial not null,
    username varchar(50) not null,
    password varchar(128) not null,
    constraint "pk_user" primary key (id)
);

create table "role" (
    id bigserial not null,
    name varchar(50) not null,
    constraint "pk_role" primary key (id)
);

create table "user_role" (
    user_id bigint not null,
    role_id bigint not null,
    constraint "pk_user_role" primary key (user_id, role_id)
);

create table "resource" (
    id bigserial not null,
    name varchar(128) not null,
    uri varchar(192) not null,
    constraint "pk_resource" primary key (id)
);

create table "role_resource" (
    role_id bigint not null,
    resource_id bigint not null,
    constraint "pk_role_resource" primary key (role_id, resource_id),
    constraint "fk_role_role_resource_role_id"
        foreign key (role_id) references "role" (id),
    constraint "fk_resource_role_resource_resource_id"
        foreign key (resource_id) references "resource" (id)
);

create unique index "idx_role_name" on "role" (name);
create unique index "idx_resource_uri" on "resource" (uri);


SQL - MySQL:
create table `user` (
    id bigint not null auto_increment,
    username varchar(50) not null,
    password varchar(128) not null,
    constraint `pk_user` primary key (id)
) Engine=InnoDB;

create table `role` (
    id bigint not null auto_increment,
    name varchar(50) not null,
    constraint `pk_role` primary key (id)
) Engine=InnoDB;

create table `user_role` (
    user_id bigint not null,
    role_id bigint not null,
    constraint `pk_user_role` primary key (user_id, role_id)
) Engine=InnoDB;

create table `resource` (
    id bigint not null auto_increment,
    name varchar(128) not null,
    uri varchar(192) not null,
    constraint `pk_resource` primary key (id)
) Engine=InnoDB;

create table `role_resource` (
    role_id bigint not null,
    resource_id bigint not null,
    constraint `pk_role_resource` primary key (role_id, resource_id),
    constraint `fk_role_role_resource_role_id`
        foreign key (role_id) references `role` (id),
    constraint `fk_resource_role_resource_resource_id`
        foreign key (resource_id) references `resource` (id)
) Engine=InnoDB;

create unique index `idx_role_name` on `role` (name);
create unique index `idx_resource_uri` on `resource` (uri);

 |*/
return array(
    'table_user' => 'user',

    'table_role' => 'role',

    'table_user_role' => 'user_role',

    'table_resource' => 'resource',

    'table_role_resource' => 'role_resource',
);
