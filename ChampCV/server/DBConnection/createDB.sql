/*to allow updates*/
SET SQL_SAFE_UPDATES=0;

/*table for both types of users - regular and recruiter*/
CREATE TABLE login(
	member_id INT NOT NULL AUTO_INCREMENT,
	member_name VARCHAR(20) NOT NULL,
	password VARCHAR(20) NOT NULL,
	last_login_time DATETIME NOT NULL, 
	PRIMARY KEY(member_id),
	UNIQUE (member_name)
);

CREATE TABLE users(
	user_id INT NOT NULL,
	user_name VARCHAR(20) NOT NULL,
	first_name VARCHAR(20),
	last_name VARCHAR(20),
	city VARCHAR(20), 
	email VARCHAR(30),
	phone VARCHAR(20), 
	sign_up_time DATETIME NOT NULL, 
	/*which categories can this user rank*/
	fullstack INT(1) NOT NULL, 
	frontend INT(1) NOT NULL, 
	backend INT(1) NOT NULL,  
	UX_UI INT(1) NOT NULL, 
	BI INT(1) NOT NULL, 
	QA INT(1) NOT NULL, 
	DBA INT(1) NOT NULL, 
	IT INT(1) NOT NULL, 
	share_info INT(1) NOT NULL, 
	amount_ranked INT NOT NULL, 
	CV_uploaded_amount INT NOT NULL,
	/*num between 0 to 1*/
	user_reliability FLOAT NOT NULL, 
	PRIMARY KEY(user_id)
);

CREATE TABLE recruiters(
	recruiter_id INT NOT NULL,
	recruiter_name VARCHAR(20) NOT NULL, 
	first_name VARCHAR(20),
	last_name VARCHAR(20),
	/*not null to make sure it's a legit company*/ 
	company_name VARCHAR(30) NOT NULL,
	email VARCHAR(30),
	phone VARCHAR(20), 
	sign_up_time DATETIME NOT NULL, 
	/*which categories can this recruiter rank*/
	fullstack INT(1) NOT NULL, 
	frontend INT(1) NOT NULL, 
	backend INT(1) NOT NULL,  
	UX_UI INT(1) NOT NULL, 
	BI INT(1) NOT NULL, 
	QA INT(1) NOT NULL, 
	DBA INT(1) NOT NULL, 
	IT INT(1) NOT NULL, 
	amount_ranked INT NOT NULL,
	/*num between 0 to 1*/
	recruiter_reliability FLOAT NOT NULL, 
	PRIMARY KEY(recruiter_id)
);

CREATE TABLE cvs(
	user_id INT NOT NULL,
	cv_id INT NOT NULL AUTO_INCREMENT,
	cv_url VARCHAR(100) NOT NULL, 
	/*a string containing tags seprated by ';'*/
	tags_from_cv VARCHAR(255), 
	open_question VARCHAR(255),
	/*categories - who can rank this CV*/
	fullstack INT(1) NOT NULL, 
	frontend INT(1) NOT NULL, 
	backend INT(1) NOT NULL,  
	UX_UI INT(1) NOT NULL, 
	BI INT(1) NOT NULL, 
	QA INT(1) NOT NULL, 
	DBA INT(1) NOT NULL, 
	IT INT(1) NOT NULL, 
	/*amount of times this CV was ranked*/
	amount_ranked INT NOT NULL, 
	PRIMARY KEY(cv_id),
	UNIQUE(user_id)
);

CREATE TABLE points_recruiters(
	recruiter_id INT NOT NULL,
	current_amount INT NOT NULL, 
	total_amount_ever INT NOT NULL, 
	PRIMARY KEY(recruiter_id)
);

CREATE TABLE points_users(
	user_id INT NOT NULL,
	current_amount INT NOT NULL, 
	total_amount_ever INT NOT NULL, 
	PRIMARY KEY(user_id)
);

CREATE TABLE rankings(
	cv_id INT NOT NULL,
	ranked_person_id INT NOT NULL,
	ranking_person_id INT NOT NULL,
	/*num between 0 to 1*/
	rank_reliability FLOAT NOT NULL, 
	/*closed questions - an int between 0 to 5
	  0 value can be given for questions 2-5 which means the ranker chose "irrelevant"*/
	answer_question_1 INT(6) NOT NULL, 
	answer_question_2 INT(6) NOT NULL,
	answer_question_3 INT(6) NOT NULL,
	answer_question_4 INT(6) NOT NULL,
	answer_question_5 INT(6) NOT NULL,
	answer_question_6 INT(6) NOT NULL,
	answer_question_7 INT(6) NOT NULL,
	answer_question_8 INT(6) NOT NULL,
	answer_open_question VARCHAR(255),
	general_remarks VARCHAR(255),
	points_for_rank INT NOT NULL, 
	rank_time INT NOT NULL,
	PRIMARY KEY(cv_id, ranking_person_id)
);

CREATE TABLE reports(
	/*the member reporting the spam*/
	member_id INT NOT NULL,
	/*binary_fields*/
	report_cv INT(1) NOT NULL,
	report_comments INT(1) NOT NULL, 
	report_answer INT(1) NOT NULL, 
	/*cv_id or user_id of the user who caused the report*/
	reported_id INT NOT NULL,
	PRIMARY KEY(member_id, report_cv, report_comments, report_answer, reported_id)
);