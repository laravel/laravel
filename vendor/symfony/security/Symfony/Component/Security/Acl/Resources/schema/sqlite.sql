CREATE TABLE acl_classes (id INTEGER NOT NULL, class_type VARCHAR(200) NOT NULL, PRIMARY KEY("id"))

CREATE UNIQUE INDEX UNIQ_69DD750638A36066 ON acl_classes (class_type)

CREATE TABLE acl_security_identities (id INTEGER NOT NULL, identifier VARCHAR(200) NOT NULL, username BOOLEAN NOT NULL, PRIMARY KEY("id"))

CREATE UNIQUE INDEX UNIQ_8835EE78772E836AF85E0677 ON acl_security_identities (identifier, username)

CREATE TABLE acl_object_identities (id INTEGER NOT NULL, parent_object_identity_id INTEGER DEFAULT NULL, class_id INTEGER NOT NULL, object_identifier VARCHAR(100) NOT NULL, entries_inheriting BOOLEAN NOT NULL, PRIMARY KEY("id"))

CREATE UNIQUE INDEX UNIQ_9407E5494B12AD6EA000B10 ON acl_object_identities (object_identifier, class_id)

CREATE INDEX IDX_9407E54977FA751A ON acl_object_identities (parent_object_identity_id)

CREATE TABLE acl_object_identity_ancestors (object_identity_id INTEGER NOT NULL, ancestor_id INTEGER NOT NULL, PRIMARY KEY("object_identity_id", "ancestor_id"))

CREATE INDEX IDX_825DE2993D9AB4A6 ON acl_object_identity_ancestors (object_identity_id)

CREATE INDEX IDX_825DE299C671CEA1 ON acl_object_identity_ancestors (ancestor_id)

CREATE TABLE acl_entries (id INTEGER NOT NULL, class_id INTEGER NOT NULL, object_identity_id INTEGER DEFAULT NULL, security_identity_id INTEGER NOT NULL, field_name VARCHAR(50) DEFAULT NULL, ace_order INTEGER NOT NULL, mask INTEGER NOT NULL, granting BOOLEAN NOT NULL, granting_strategy VARCHAR(30) NOT NULL, audit_success BOOLEAN NOT NULL, audit_failure BOOLEAN NOT NULL, PRIMARY KEY("id"))

CREATE UNIQUE INDEX UNIQ_46C8B806EA000B103D9AB4A64DEF17BCE4289BF4 ON acl_entries (class_id, object_identity_id, field_name, ace_order)

CREATE INDEX IDX_46C8B806EA000B103D9AB4A6DF9183C9 ON acl_entries (class_id, object_identity_id, security_identity_id)

CREATE INDEX IDX_46C8B806EA000B10 ON acl_entries (class_id)

CREATE INDEX IDX_46C8B8063D9AB4A6 ON acl_entries (object_identity_id)

CREATE INDEX IDX_46C8B806DF9183C9 ON acl_entries (security_identity_id)