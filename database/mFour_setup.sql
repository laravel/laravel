drop schema "public" cascade;

create schema "public";

CREATE TABLE "public"."users" (
	"first_name" VARCHAR(255) NOT NULL,
	"last_name" VARCHAR(255) NOT NULL,
	"email" VARCHAR(255) NOT NULL UNIQUE
) WITH (
  OIDS=FALSE
);

insert into "user" ("first_name", "last_name", "email")
values
    ('first1', 'last1', '1@example.com'),
    ('first2', 'last2', '2@example.com'),
    ('first3', 'last3', '3@example.com'),
    ('first4', 'last4', '4@example.com'),
    ('first5', 'last5', '5@example.com');
