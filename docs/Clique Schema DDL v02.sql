DROP DATABASE IF EXISTS clique;

-- INTERNALS DATABASE
CREATE DATABASE clique;

CREATE TABLE clique.lookup_member_statuses (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	description 		VARCHAR(10) NOT NULL,

	PRIMARY KEY(id)
);

CREATE TABLE clique.members (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	student_no			CHAR(10) 	not NULL,
	first_name 			VARCHAR(30) NOT NULL,
	middle_name 		VARCHAR(20) NOT NULL,
	last_name 			VARCHAR(20) NOT NULL,
	nick_name 			VARCHAR(10) NULL,
	current_committee 	VARCHAR(30) NOT NULL,
	fb_username 		VARCHAR(30) NULL,
	birth_date 			DATE 		NOT NULL, 
	street_no 			VARCHAR(10) NULL,
	street_name 		VARCHAR(15) NULL,
	barangay 			VARCHAR(20) NULL,
	city 				VARCHAR(15) NULL,
	region 				VARCHAR(15) NULL,
	sem_accepted 		VARCHAR(15) NOT NULL, 
	college 			VARCHAR(20) NOT NULL, 
	degree 				VARCHAR(30) NOT NULL, 
	status_id 			INT 		NOT NULL, 
	password 			CHAR(64) 	NOT NULL, -- SHA-256 
	created_at			TIMESTAMP	NULL,
	updated_at			TIMESTAMP	NULL,

	PRIMARY KEY(id),
	UNIQUE(student_no),

	-- allow updates, restrict deletes
	FOREIGN KEY (status_id) 
		REFERENCES lookup_member_statuses(id)
		ON UPDATE CASCADE
);

CREATE TABLE clique.member_contact_nos (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	member_id 			INT 		NOT NULL, 
	contact_no 			VARCHAR(24) NOT NULL, 
	created_at			TIMESTAMP	NULL,
	updated_at			TIMESTAMP	NULL,

	PRIMARY KEY(id),
	UNIQUE(member_id, contact_no),

	-- cascade all
	FOREIGN KEY (member_id)
		REFERENCES members(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE clique.member_emails (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	member_id 			INT 	NOT NULL, 
	email_address 		VARCHAR(30) NULL,
	created_at			TIMESTAMP	NULL,
	updated_at			TIMESTAMP	NULL,

	PRIMARY KEY(id),
	UNIQUE(member_id, email_address),

	-- cascade all
	FOREIGN KEY (member_id)
		REFERENCES members(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE clique.classes (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	class_code 			INT 		NOT NULL, 
	course_name 		VARCHAR(20) NOT NULL, 
	course_description 	LONGTEXT 	NOT NULL, 
	start_time 			TIME 		NOT NULL, 
	end_time 			TIME 		NOT NULL, 
	instructor 			VARCHAR(60) NOT NULL,
	created_at			TIMESTAMP	NULL,
	updated_at			TIMESTAMP	NULL,

	PRIMARY KEY(id),
	UNIQUE(class_code)
);

CREATE TABLE clique.member_schedules (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	member_id 			INT 		NOT NULL, 
	class_code 			INT  		NOT NULL,
	created_at			TIMESTAMP	NULL,
	updated_at			TIMESTAMP	NULL,

	PRIMARY KEY(id),
	UNIQUE(member_id, class_code),

	-- cascade all
	FOREIGN KEY (member_id)
		REFERENCES members(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE,

	-- restrict all
	FOREIGN KEY (class_code)
		REFERENCES classes(class_code)
);

CREATE TABLE clique.member_evals ( 
	id 					INT 		NOT NULL AUTO_INCREMENT,
	student_no_reviewee INT			NOT NULL,
	eval_date 			DATE 		NOT NULL,
	student_no_reviewer INT 		NULL,
	eval_text 			LONGTEXT 	NOT NULL,
	created_at			TIMESTAMP	NULL,
	updated_at			TIMESTAMP	NULL,

	PRIMARY KEY(id),
	UNIQUE(student_no_reviewee, eval_date),

	-- cascade all for reviewee
	FOREIGN KEY (student_no_reviewee)
		REFERENCES members(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE,

	-- when reviewer is updated, cascade
	-- when reviewer is deleted, just set to null
	FOREIGN KEY (student_no_reviewer)
		REFERENCES members(id)
		ON DELETE SET NULL
		ON UPDATE CASCADE
);

-- --------------------------------------------------------------------------------------------

-- MARKETING CONTACTS

CREATE TABLE clique.companies (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	name 				CHAR(40) 	NOT NULL,  
	street_no 			VARCHAR(10) NULL, 
	street_name 		VARCHAR(15) NULL, 
	barangay 			VARCHAR(20) NULL, 
	city 				VARCHAR(15) NULL, 
	region 				VARCHAR(15) NULL,  
	status 				VARCHAR(20) NULL, 
	remark 				LONGTEXT 	NULL,
	created_at			TIMESTAMP	NULL,
	updated_at			TIMESTAMP	NULL,

	PRIMARY KEY(id),
	UNIQUE(name)
);

CREATE TABLE clique.company_contact_nos (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	company_id	 		INT 		NOT NULL, 
	contact_no 			VARCHAR(24) NOT NULL,
	created_at			TIMESTAMP	NULL,
	updated_at			TIMESTAMP	NULL,

	PRIMARY KEY(id),
	UNIQUE(company_id, contact_no),

	-- cascade all
	FOREIGN KEY (company_id)
		REFERENCES companies(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE clique.company_emails (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	company_id 			INT 		NOT NULL, 
	email_address 		VARCHAR(30) NOT NULL,
	created_at			TIMESTAMP	NULL,
	updated_at			TIMESTAMP	NULL,

	PRIMARY KEY(id),
	UNIQUE(company_id, email_address),

	-- cascade all
	FOREIGN KEY (company_id)
		REFERENCES companies(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE clique.company_fax_nos (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	company_id 			INT 		NOT NULL, 
	fax_no 				VARCHAR(24) NOT NULL,
	created_at			TIMESTAMP	NULL,
	updated_at			TIMESTAMP	NULL,

	PRIMARY KEY(id),
	UNIQUE(company_id, fax_no),

	-- cascade all
	FOREIGN KEY (company_id)
		REFERENCES companies(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE clique.contact_persons (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	company_id 			INT 		NOT NULL, 
	first_name 			VARCHAR(30) NOT NULL, 
	middle_name 		VARCHAR(20) NOT NULL, 
	last_name 			VARCHAR(20) NOT NULL, 
	position 			VARCHAR(20) NULL,
	created_at			TIMESTAMP	NULL,
	updated_at			TIMESTAMP	NULL,

	PRIMARY KEY(id),
	UNIQUE(company_id),

	-- cascade all
	FOREIGN KEY (company_id)
		REFERENCES companies(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

-- --------------------------------------------------------------------------------------------

-- ALUMNI RELATIONS DATABASE

CREATE TABLE clique.alumnus ( 
	id 					INT 		NOT NULL AUTO_INCREMENT,
	student_no 			CHAR(10) 	NOT NULL, 
	first_name 			VARCHAR(30) NOT NULL, 
	middle_name 		VARCHAR(20) NOT NULL, 
	last_name 			VARCHAR(20) NOT NULL,   
	street_no 			VARCHAR(10) NULL, 
	street_name 		VARCHAR(15) NULL,  
	barangay 			VARCHAR(20) NULL, 
	city 				VARCHAR(15) NULL, 
	region 				VARCHAR(15) NULL, 
	gender 				CHAR 		NOT NULL, 
	remark 				LONGTEXT 	NULL,
	created_at			TIMESTAMP	NULL,
	updated_at			TIMESTAMP	NULL,

	PRIMARY KEY(id),
	UNIQUE(student_no)
);

CREATE TABLE clique.alumnus_degrees (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	alumnus_id 			INT 		NOT NULL,
	year_graduated 		DATE 		NOT NULL,
	degree_graduated 	VARCHAR(30) NOT NULL,
	created_at			TIMESTAMP	NULL,
	updated_at			TIMESTAMP	NULL,

	PRIMARY KEY(id),
	UNIQUE(alumnus_id, year_graduated),

	-- cascade all
	FOREIGN KEY (alumnus_id)
		REFERENCES alumnus(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE clique.alumnus_contact_nos (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	alumnus_id 			INT 		NOT NULL, 
	contact_no 			VARCHAR(24) NOT NULL,
	created_at			TIMESTAMP	NULL,
	updated_at			TIMESTAMP	NULL,

	PRIMARY KEY(id),
	UNIQUE(alumnus_id, contact_no),

	-- cascade all
	FOREIGN KEY (alumnus_id)
		REFERENCES alumnus(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE clique.alumnus_emails (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	alumnus_id 			INT 		NOT NULL, 
	email_address 		VARCHAR(30) NOT NULL,
	created_at			TIMESTAMP	NULL,
	updated_at			TIMESTAMP	NULL,

	PRIMARY KEY(id),
	UNIQUE(alumnus_id, email_address),

	-- cascade all
	FOREIGN KEY (alumnus_id)
		REFERENCES alumnus(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE clique.alumnus_fax_nos (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	alumnus_id 			INT 		NOT NULL, 
	fax_no 				VARCHAR(24) NOT NULL,
	created_at			TIMESTAMP	NULL,
	updated_at			TIMESTAMP	NULL,

	PRIMARY KEY(id),
	UNIQUE(alumnus_id, fax_no),

	-- cascade all
	FOREIGN KEY (alumnus_id)
		REFERENCES alumnus(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE clique.alumnus_work_info (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	alumnus_id 			INT 		NOT NULL, 
	company_name 		CHAR(40) 	NULL, 
	years_worked 		INT 		NULL, 
	position 			VARCHAR(20) NULL,
	created_at			TIMESTAMP	NULL,
	updated_at			TIMESTAMP	NULL,

	-- most recent work info only
	PRIMARY KEY(id),
	UNIQUE(alumnus_id),

	-- cascade all
	FOREIGN KEY (alumnus_id)
		REFERENCES alumnus(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE,

	-- cascade all
	FOREIGN KEY (company_name)
		REFERENCES companies(name)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

-- --------------------------------------------------------------------------------------------

-- PROJECT MANAGEMENT DATABASE

CREATE TABLE clique.projects (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	name 				VARCHAR(20) NOT NULL, 
	description 		LONGTEXT 	NULL, 
	approval_ts 		TIMESTAMP 	NULL, 
	start_date 			DATE 		NULL, 
	start_time 			TIME 		NULL, 
	end_date 			DATE 		NULL,
	end_time			TIME 		NULL,
	created_at			TIMESTAMP	NULL,
	updated_at			TIMESTAMP	NULL,

	PRIMARY KEY(id)
);

CREATE TABLE clique.project_heads (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	project_id 			INT 		NOT NULL,
 	student_id 			INT 		NOT NULL,
	created_at			TIMESTAMP	NULL,
	updated_at			TIMESTAMP	NULL,

 	PRIMARY KEY(id),
	UNIQUE(project_id, student_id),             

	-- cascade all
	FOREIGN KEY (project_id)
		REFERENCES projects(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE,

	-- cascade all
	FOREIGN KEY (student_id)
		REFERENCES members(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
 );

-- --------------------------------------------------------------------------------------------

-- RECORDS DATABASE

CREATE TABLE clique.meetings (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	project_id 			INT 		NOT NULL,  
	meeting_date 		DATE 		NOT NULL, 
	start_time 			TIME 		NULL, 
	end_time 			TIME 		NULL, 
	minutes 			LONGTEXT 	NULL,
	created_at			TIMESTAMP	NULL,
	updated_at			TIMESTAMP	NULL,

	PRIMARY KEY(id),
	UNIQUE(project_id, meeting_date),

	FOREIGN KEY (project_id)
		REFERENCES projects(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE	
);

CREATE TABLE clique.meeting_attendances (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	student_id 			INT 		NOT NULL, 
	meeting_id 			INT 		NOT NULL, 
	attendance 			BOOLEAN 	NOT NULL,
	created_at			TIMESTAMP	NULL,
	updated_at			TIMESTAMP	NULL,

	PRIMARY KEY(id),
	UNIQUE(student_id, meeting_id),

	-- cascade all
	FOREIGN KEY (student_id)
		REFERENCES members(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE,

	-- cascade all
	FOREIGN KEY (meeting_id)
		REFERENCES meetings(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

-- --------------------------------------------------------------------------------------------

-- FINANCE AND LOGISTICS DATABASE

CREATE TABLE clique.material_sources (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	name 				CHAR(30) 	NOT NULL, 
	type 				VARCHAR(15) NOT NULL, 
	contact_no 			VARCHAR(24) NULL, 
	street_no 			VARCHAR(10) NULL, 
	street_name 		VARCHAR(15) NULL, 
	barangay 			VARCHAR(20) NULL, 
	city 				VARCHAR(15) NULL, 
	region				VARCHAR(15) NULL,
	created_at			TIMESTAMP	NULL,
	updated_at			TIMESTAMP	NULL,

	PRIMARY KEY(id),
	UNIQUE(name)
);

CREATE TABLE clique.items (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	name 				CHAR(20) 	NOT NULL, 
	type 				VARCHAR(15) NOT NULL,

	PRIMARY KEY(id),
	UNIQUE(name)
);

CREATE TABLE clique.material_provisions (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	source_id 			INT 		NOT NULL, -- material source name 
	item_id 			INT 		NOT NULL, 
	price 				DECIMAL(19, 4) NOT NULL,
	created_at			TIMESTAMP	NULL,
	updated_at			TIMESTAMP	NULL,

	PRIMARY KEY(id),
	UNIQUE(source_id, item_id),

	-- cascade all
	FOREIGN KEY (source_id)
		REFERENCES material_sources(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE,

	-- cascade all
	FOREIGN KEY (item_id)
		REFERENCES items(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE clique.venues (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	name 				CHAR(20) 	NOT NULL,
	contact_no 			VARCHAR(24) NULL, 
	street_no 			VARCHAR(10) NULL, 
	street_name 		VARCHAR(15) NULL, 
	barangay 			VARCHAR(20) NULL, 
	city 				VARCHAR(15) NULL, 
	region 				VARCHAR(15) NULL, 
	email_address 		VARCHAR(30) NULL, 
	contact_person 		VARCHAR(50) NULL, 
	created_at			TIMESTAMP	NULL,
	updated_at			TIMESTAMP	NULL,

	PRIMARY KEY(id),
	UNIQUE(name)
);

CREATE TABLE clique.lookup_amenity_types (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	name				CHAR(30)	NOT NULL,

	PRIMARY KEY(id),
	UNIQUE(name)
);

CREATE TABLE clique.amenities (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	venue_id 			INT 		NOT NULL, -- venue name 
	name				CHAR(30)	NOT NULL,
	amenity_type_id 	INT 		NOT NULL, 
	description 		LONGTEXT 	NULL, 
	created_at			TIMESTAMP	NULL,
	updated_at			TIMESTAMP	NULL,

	PRIMARY KEY(id),
	UNIQUE(venue_id, name),

	-- cascade all
	FOREIGN KEY (venue_id)
		REFERENCES venues(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE,

	-- cascade all
	FOREIGN KEY (amenity_type_id)
		REFERENCES lookup_amenity_types(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE clique.venue_packages (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	venue_id 			INT  		NOT NULL, -- venue
	package_number 		INT 		NOT NULL, 
	amenity_id 			INT 		NOT NULL, 
	capacity 			INT 		NOT NULL, 
	price 				DECIMAL(19, 4) NOT NULL, 
	price_description	LONGTEXT 	NULL,
	created_at			TIMESTAMP	NULL,
	updated_at			TIMESTAMP	NULL,

	PRIMARY KEY(id),
	UNIQUE(venue_id, package_number),

	-- cascade all
	FOREIGN KEY (venue_id)
		REFERENCES venues(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE,

	-- cascade all
	FOREIGN KEY (amenity_id)
		REFERENCES amenities(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE clique.financial_statements (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	sem 				VARCHAR(15) NOT NULL, 
	school_year 		VARCHAR(15) NOT NULL,
	closed				BOOLEAN		NOT NULL DEFAULT FALSE,
	created_at			TIMESTAMP	NULL,
	updated_at			TIMESTAMP	NULL,

	PRIMARY KEY(id)
); 

CREATE TABLE clique.lookup_statement_types (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	name				CHAR(30)	NOT NULL, 
	type				TINYINT		NOT NULL, 	-- 	1 IF ASSET,
												-- 	2 IF LIABILITY

	PRIMARY KEY(id),
	UNIQUE(name)
);

CREATE TABLE clique.statement_entries (
	id 					INT 		NOT NULL AUTO_INCREMENT,
	statement_id 		INT 		NOT NULL, -- financial statement id
	project_id			INT			NOT NULL, 
	entry_no 			INT 		NOT NULL, 
	entry_date 			DATE 		NOT NULL, 
	type_id 			INT			NOT NULL, 
	amount 				INT 		NOT NULL,
	creator_id			INT			NULL,
	description 		LONGTEXT 	NULL,
	created_at			TIMESTAMP	NULL,
	updated_at			TIMESTAMP	NULL,

	PRIMARY KEY(id),
	UNIQUE(statement_id, entry_no),

	FOREIGN KEY (statement_id)
		REFERENCES financial_statements(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE,      

	-- cascade all
	FOREIGN KEY (project_id)
		REFERENCES projects(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE,

	FOREIGN KEY (type_id)
		REFERENCES lookup_statement_types(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE,

	-- cascade all for members
	FOREIGN KEY (creator_id)
		REFERENCES members(id)
		ON DELETE SET NULL
		ON UPDATE CASCADE
);