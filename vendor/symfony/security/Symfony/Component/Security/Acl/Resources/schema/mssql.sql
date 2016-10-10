CREATE TABLE acl_classes (id INT UNSIGNED IDENTITY NOT NULL, class_type NVARCHAR(200) NOT NULL, PRIMARY KEY (id))

CREATE UNIQUE INDEX UNIQ_69DD750638A36066 ON acl_classes (class_type) WHERE class_type IS NOT NULL

CREATE TABLE acl_security_identities (id INT UNSIGNED IDENTITY NOT NULL, identifier NVARCHAR(200) NOT NULL, username BIT NOT NULL, PRIMARY KEY (id))

CREATE UNIQUE INDEX UNIQ_8835EE78772E836AF85E0677 ON acl_security_identities (identifier, username) WHERE identifier IS NOT NULL AND username IS NOT NULL

CREATE TABLE acl_object_identities (id INT UNSIGNED IDENTITY NOT NULL, parent_object_identity_id INT UNSIGNED DEFAULT NULL, class_id INT UNSIGNED NOT NULL, object_identifier NVARCHAR(100) NOT NULL, entries_inheriting BIT NOT NULL, PRIMARY KEY (id))

CREATE UNIQUE INDEX UNIQ_9407E5494B12AD6EA000B10 ON acl_object_identities (object_identifier, class_id) WHERE object_identifier IS NOT NULL AND class_id IS NOT NULL

CREATE INDEX IDX_9407E54977FA751A ON acl_object_identities (parent_object_identity_id)

CREATE TABLE acl_object_identity_ancestors (object_identity_id INT UNSIGNED NOT NULL, ancestor_id INT UNSIGNED NOT NULL, PRIMARY KEY (object_identity_id, ancestor_id))

CREATE INDEX IDX_825DE2993D9AB4A6 ON acl_object_identity_ancestors (object_identity_id)

CREATE INDEX IDX_825DE299C671CEA1 ON acl_object_identity_ancestors (ancestor_id)

CREATE TABLE acl_entries (id INT UNSIGNED IDENTITY NOT NULL, class_id INT UNSIGNED NOT NULL, object_identity_id INT UNSIGNED DEFAULT NULL, security_identity_id INT UNSIGNED NOT NULL, field_name NVARCHAR(50) DEFAULT NULL, ace_order SMALLINT UNSIGNED NOT NULL, mask INT NOT NULL, granting BIT NOT NULL, granting_strategy NVARCHAR(30) NOT NULL, audit_success BIT NOT NULL, audit_failure BIT NOT NULL, PRIMARY KEY (id))

CREATE UNIQUE INDEX UNIQ_46C8B806EA000B103D9AB4A64DEF17BCE4289BF4 ON acl_entries (class_id, object_identity_id, field_name, ace_order) WHERE class_id IS NOT NULL AND object_identity_id IS NOT NULL AND field_name IS NOT NULL AND ace_order IS NOT NULL

CREATE INDEX IDX_46C8B806EA000B103D9AB4A6DF9183C9 ON acl_entries (class_id, object_identity_id, security_identity_id)

CREATE INDEX IDX_46C8B806EA000B10 ON acl_entries (class_id)

CREATE INDEX IDX_46C8B8063D9AB4A6 ON acl_entries (object_identity_id)

CREATE INDEX IDX_46C8B806DF9183C9 ON acl_entries (security_identity_id)

ALTER TABLE acl_object_identities ADD CONSTRAINT FK_9407E54977FA751A FOREIGN KEY (parent_object_identity_id) REFERENCES acl_object_identities (id)

ALTER TABLE acl_object_identity_ancestors ADD CONSTRAINT FK_825DE2993D9AB4A6 FOREIGN KEY (object_identity_id) REFERENCES acl_object_identities (id) ON UPDATE CASCADE ON DELETE CASCADE

ALTER TABLE acl_object_identity_ancestors ADD CONSTRAINT FK_825DE299C671CEA1 FOREIGN KEY (ancestor_id) REFERENCES acl_object_identities (id) ON UPDATE CASCADE ON DELETE CASCADE

ALTER TABLE acl_entries ADD CONSTRAINT FK_46C8B806EA000B10 FOREIGN KEY (class_id) REFERENCES acl_classes (id) ON UPDATE CASCADE ON DELETE CASCADE

ALTER TABLE acl_entries ADD CONSTRAINT FK_46C8B8063D9AB4A6 FOREIGN KEY (object_identity_id) REFERENCES acl_object_identities (id) ON UPDATE CASCADE ON DELETE CASCADE

ALTER TABLE acl_entries ADD CONSTRAINT FK_46C8B806DF9183C9 FOREIGN KEY (security_identity_id) REFERENCES acl_security_identities (id) ON UPDATE CASCADE ON DELETE CASCADE