DROP DATABASE IF EXISTS clique;

-- INTERNALS DATABASE
CREATE DATABASE clique;

CREATE TABLE clique.MemberStatusLookup (
	StatusId INT NOT NULL AUTO_INCREMENT,
	Description VARCHAR(10) NOT NULL,

	PRIMARY KEY(StatusId)
);

CREATE TABLE clique.Members (
	StudentNo CHAR(10) NOT NULL,
	FirstName VARCHAR(30) NOT NULL,
	MiddleName VARCHAR(20) NOT NULL,
	LastName VARCHAR(20) NOT NULL,
	NickName VARCHAR(10) NULL,
	CurrentCommittee VARCHAR(30) NOT NULL,
	FBUsername VARCHAR(30) NULL,
	BirthDate DATE NOT NULL, 
	StreetNo VARCHAR(10) NULL,
	StreetName VARCHAR(15) NULL,
	Barangay VARCHAR(20) NULL,
	City VARCHAR(15) NULL,
	Region VARCHAR(15) NULL,
	SemAccepted VARCHAR(15) NOT NULL, 
	College VARCHAR(20) NOT NULL, 
	Degree VARCHAR(30) NOT NULL, 
	StatusId INT NOT NULL, 
	-- perhaps we could use StudentNo as username?
	-- account_username VARCHAR(10) NOT NULL, 
	Password CHAR(64) NOT NULL, -- SHA-256  

	PRIMARY KEY(StudentNo),

	-- allow updates, restrict deletes
	FOREIGN KEY (StatusId) 
		REFERENCES MemberStatusLookup(StatusId)
		ON UPDATE CASCADE
);

CREATE TABLE clique.ContactNos (
	StudentNo CHAR(10) NOT NULL, 
	ContactNo VARCHAR(24) NOT NULL, 

	PRIMARY KEY(StudentNo, ContactNo),

	-- cascade all
	FOREIGN KEY (StudentNo)
		REFERENCES Members(StudentNo)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE clique.EmailAddresses (
	StudentNo CHAR(10) NOT NULL, 
	EmailAddress VARCHAR(30) NULL,

	PRIMARY KEY(StudentNo, EmailAddress),

	-- cascade all
	FOREIGN KEY (StudentNo)
		REFERENCES Members(StudentNo)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE clique.Classes (
	ClassCode INT NOT NULL, 
	CourseName VARCHAR(20) NOT NULL, 
	CourseDescription LONGTEXT NOT NULL, 
	StartTime TIME NOT NULL, 
	EndTime TIME NOT NULL, 
	Instructor VARCHAR(60) NOT NULL,

	PRIMARY KEY(ClassCode)
);

CREATE TABLE clique.MemberSchedules (
	StudentNo CHAR(10) NOT NULL, 
	ClassCode INT NOT NULL,

	PRIMARY KEY(StudentNo, ClassCode),

	-- cascade all
	FOREIGN KEY (StudentNo)
		REFERENCES Members(StudentNo)
		ON DELETE CASCADE
		ON UPDATE CASCADE,

	-- restrict all
	FOREIGN KEY (ClassCode)
		REFERENCES Classes(ClassCode)
);

CREATE TABLE clique.MemberEvals ( 
	StudentNoReviewee CHAR(10) NOT NULL,
	EvalDate DATE NOT NULL,
	StudentNoReviewer CHAR(10) NULL,
	EvalText LONGTEXT NOT NULL,

	PRIMARY KEY(StudentNoReviewee, EvalDate),

	-- cascade all for reviewee
	FOREIGN KEY (StudentNoReviewee)
		REFERENCES Members(StudentNo)
		ON DELETE CASCADE
		ON UPDATE CASCADE,

	-- when reviewer is updated, cascade
	-- when reviewer is deleted, just set to null
	FOREIGN KEY (StudentNoReviewer)
		REFERENCES Members(StudentNo)
		ON DELETE SET NULL
		ON UPDATE CASCADE
);

-- --------------------------------------------------------------------------------------------

-- MARKETING CONTACTS

CREATE TABLE clique.Companies (
	Name CHAR(40) NOT NULL,  
	StreetNo VARCHAR(10) NULL, 
	StreetName VARCHAR(15) NULL, 
	Barangay VARCHAR(20) NULL, 
	City VARCHAR(15) NULL, 
	Region VARCHAR(15) NULL,  
	Status VARCHAR(20) NULL, 
	Remark LONGTEXT NULL,

	PRIMARY KEY(Name)
);

CREATE TABLE clique.CompanyContactNos (
	CompanyName CHAR(40) NOT NULL, 
	ContactNo VARCHAR(24) NOT NULL,

	PRIMARY KEY(CompanyName, ContactNo),

	-- cascade all
	FOREIGN KEY (CompanyName)
		REFERENCES Companies(Name)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE clique.CompanyEmails (
	CompanyName CHAR(40) NOT NULL, 
	EmailAddress VARCHAR(30) NOT NULL,

	PRIMARY KEY(CompanyName, EmailAddress),

	-- cascade all
	FOREIGN KEY (CompanyName)
		REFERENCES Companies(Name)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE clique.CompanyFaxNos (
	CompanyName CHAR(40) NOT NULL, 
	FaxNo VARCHAR(24) NOT NULL,

	PRIMARY KEY(CompanyName, FaxNo),

	-- cascade all
	FOREIGN KEY (CompanyName)
		REFERENCES Companies(Name)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE clique.ContactPersons (
	CompanyName CHAR(40) NOT NULL, 
	FirstName VARCHAR(30) NOT NULL, 
	MiddleName VARCHAR(20) NOT NULL, 
	LastName VARCHAR(20) NOT NULL, 
	Position VARCHAR(20) NULL,

	PRIMARY KEY(CompanyName),

	-- cascade all
	FOREIGN KEY (CompanyName)
		REFERENCES Companies(Name)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

-- --------------------------------------------------------------------------------------------

-- ALUMNI RELATIONS DATABASE

CREATE TABLE clique.Alumnus ( 
	StudentNo CHAR(10) NOT NULL, 
	FirstName VARCHAR(30) NOT NULL, 
	MiddleName VARCHAR(20) NOT NULL, 
	LastName VARCHAR(20) NOT NULL,   
	StreetNo VARCHAR(10) NULL, 
	StreetName VARCHAR(15) NULL,  
	Barangay VARCHAR(20) NULL, 
	City VARCHAR(15) NULL, 
	Region VARCHAR(15) NULL, 
	Gender CHAR NOT NULL, 
	Remark LONGTEXT NULL,

	PRIMARY KEY(StudentNo)
);

CREATE TABLE clique.AlumnusDegrees (
	StudentNo CHAR(10) NOT NULL,
	YearGraduated DATE NOT NULL,
	DegreeProgramGraduated VARCHAR(30) NOT NULL,

	PRIMARY KEY(StudentNo, YearGraduated),

	-- cascade all
	FOREIGN KEY (StudentNo)
		REFERENCES Alumnus(StudentNo)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE clique.AlumnusContactNos (
	StudentNo CHAR(10) NOT NULL, 
	ContactNo VARCHAR(24) NOT NULL,

	PRIMARY KEY(StudentNo, ContactNo),

	-- cascade all
	FOREIGN KEY (StudentNo)
		REFERENCES Alumnus(StudentNo)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE clique.AlumnusEmails (
	StudentNo CHAR(10) NOT NULL, 
	EmailAddress VARCHAR(30) NOT NULL,

	PRIMARY KEY(StudentNo, EmailAddress),

	-- cascade all
	FOREIGN KEY (StudentNo)
		REFERENCES Alumnus(StudentNo)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE clique.AlumnusFaxNos (
	StudentNo CHAR(10) NOT NULL, 
	FaxNo VARCHAR(24) NOT NULL,

	PRIMARY KEY(StudentNo, FaxNo),

	-- cascade all
	FOREIGN KEY (StudentNo)
		REFERENCES Alumnus(StudentNo)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE clique.AlumnusWorkInfo (
	StudentNo CHAR(10) NOT NULL, 
	CompanyName CHAR(40) NULL, 
	YearsWorked INT NULL, 
	Position VARCHAR(20) NULL,

	-- most recent work info only
	PRIMARY KEY(StudentNo),

	-- cascade all
	FOREIGN KEY (StudentNo)
		REFERENCES Alumnus(StudentNo)
		ON DELETE CASCADE
		ON UPDATE CASCADE,

	-- cascade all
	FOREIGN KEY (CompanyName)
		REFERENCES Companies(Name)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

-- --------------------------------------------------------------------------------------------

-- PROJECT MANAGEMENT DATABASE

CREATE TABLE clique.Projects (
	ProjectId INT NOT NULL AUTO_INCREMENT, 
	Name VARCHAR(20) NOT NULL, 
	Description LONGTEXT NULL, 
	ApprovalTS TIMESTAMP NULL, 
	StartTS TIMESTAMP NULL, 
	EndTS TIMESTAMP NULL,

	PRIMARY KEY(ProjectId)
);

CREATE TABLE clique.ProjectHeads (
	ProjectId INT NOT NULL,
 	StudentNo CHAR(10) NOT NULL,

 	PRIMARY KEY(ProjectId, StudentNo),

	-- cascade all
	FOREIGN KEY (ProjectId)
		REFERENCES Projects(ProjectId)
		ON DELETE CASCADE
		ON UPDATE CASCADE,

	-- cascade all
	FOREIGN KEY (StudentNo)
		REFERENCES Members(StudentNo)
		ON DELETE CASCADE
		ON UPDATE CASCADE
 );

-- --------------------------------------------------------------------------------------------

-- RECORDS DATABASE

CREATE TABLE clique.Meetings (
	MeetingNo INT NOT NULL AUTO_INCREMENT,
	ProjectId INT NOT NULL,  
	MeetingDate DATE NULL, 
	StartTime TIME NULL, 
	EndTime TIME NULL, 
	Minutes LONGTEXT NULL,

	PRIMARY KEY(MeetingNo),

	FOREIGN KEY (ProjectId)
		REFERENCES Projects(ProjectID)
		ON DELETE CASCADE
		ON UPDATE CASCADE	
);

CREATE TABLE clique.MeetingAttendances (
	MeetingNo INT NOT NULL, 
	StudentNo CHAR(10) NOT NULL, 
	Attendance BOOLEAN NOT NULL,

	PRIMARY KEY(StudentNo, MeetingNo, Attendance),

	-- cascade all
	FOREIGN KEY (StudentNo)
		REFERENCES Members(StudentNo)
		ON DELETE CASCADE
		ON UPDATE CASCADE,

	-- cascade all
	FOREIGN KEY (MeetingNo)
		REFERENCES Meetings(MeetingNo)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

-- --------------------------------------------------------------------------------------------

-- FiNANCE AND LOGISTICS DATABASE
CREATE TABLE clique.MaterialSources (
	Name CHAR(30) NOT NULL, 
	Type VARCHAR(15) NOT NULL, 
	ContactNo VARCHAR(24) NULL, 
	StreetNo VARCHAR(10) NULL, 
	StreetName VARCHAR(15) NULL, 
	Barangay VARCHAR(20) NULL, 
	City VARCHAR(15) NULL, 
	Region VARCHAR(15) NULL,

	PRIMARY KEY(Name)
);

CREATE TABLE clique.Items (
	Name CHAR(20) NOT NULL, 
	Type VARCHAR(15) NOT NULL,

	PRIMARY KEY(Name)
);

CREATE TABLE clique.MaterialProvisions (
	SourceName VARCHAR(30) NOT NULL, -- material source name 
	ItemName VARCHAR(20) NOT NULL, 
	Price DECIMAL(19, 4) NOT NULL,

	PRIMARY KEY(SourceName, ItemName),

	-- cascade all
	FOREIGN KEY (SourceName)
		REFERENCES MaterialSources(Name)
		ON DELETE CASCADE
		ON UPDATE CASCADE,

	-- cascade all
	FOREIGN KEY (ItemName)
		REFERENCES Items(Name)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE clique.Venues (
	Name CHAR(20) NOT NULL,
	ContactNo VARCHAR(24) NULL, 
	StreetNo VARCHAR(10) NULL, 
	StreetName VARCHAR(15) NULL, 
	Barangay VARCHAR(20) NULL, 
	City VARCHAR(15) NULL, 
	Region VARCHAR(15) NULL, 
	EmailAddress VARCHAR(30) NULL, 
	ContactFirstName VARCHAR(30) NULL, 
	ContactLastName VARCHAR(20) NULL,

	PRIMARY KEY(Name)
);

CREATE TABLE clique.VenuePackages (
	VenueName CHAR(20) NOT NULL, -- venue
	PackageNumber INT NOT NULL, 
	Type VARCHAR(20) NOT NULL, 
	Capacity INT NOT NULL, 
	Price DECIMAL(19, 4) NOT NULL, 
	PriceDescription LONGTEXT NULL,

	PRIMARY KEY(VenueName, PackageNumber),

	-- cascade all
	FOREIGN KEY (VenueName)
		REFERENCES Venues(Name)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE clique.Amenities (
	VenueName CHAR(20) NOT NULL, -- venue name 
	Type VARCHAR(20) NOT NULL, 
	Description LONGTEXT NULL, 
	Price DECIMAL(19, 4) NOT NULL, 
	PriceDescription LONGTEXT NULL,

	PRIMARY KEY(VenueName),

	-- cascade all
	FOREIGN KEY (VenueName)
		REFERENCES Venues(Name)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);

CREATE TABLE clique.FinancialStatements (
	StatementId INT NOT NULL, 
	Sem VARCHAR(15) NOT NULL, 
	SchoolYear VARCHAR(15) NOT NULL,

	PRIMARY KEY(StatementId)
); 

CREATE TABLE clique.StatementEntries (
	StatementId INT NOT NULL, -- financial statement id 
	EntryNo INT NOT NULL, 
	EntryDate DATE NOT NULL, 
	Type VARCHAR(20) NOT NULL, 
	Amount INT NOT NULL,
	Description LONGTEXT NULL,

	PRIMARY KEY(StatementID, EntryNo),

	FOREIGN KEY (StatementId)
		REFERENCES FinancialStatements(StatementId)
		ON DELETE CASCADE
		ON UPDATE CASCADE
);