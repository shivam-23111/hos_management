	-- Create the database
	CREATE DATABASE hos_management;

	-- Use the database
	USE hos_management;

	-- Create the admins table
	CREATE TABLE admins (
		id INT AUTO_INCREMENT PRIMARY KEY,
		username VARCHAR(50) NOT NULL UNIQUE,
		password VARCHAR(255) NOT NULL
	);

	-- Insert default admin credentials
	INSERT INTO admins (username, password) VALUES 
	('admin', SHA2('admin123', 256));

	CREATE TABLE doctors (
		id INT AUTO_INCREMENT PRIMARY KEY,
		full_name VARCHAR(255) NOT NULL,
		email VARCHAR(255) NOT NULL UNIQUE,
		department VARCHAR(255) NOT NULL,
		category VARCHAR(255) NOT NULL,
		created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
	);
	CREATE TABLE beds (
		bed_id INT AUTO_INCREMENT PRIMARY KEY,
		status ENUM('Available', 'Occupied', 'Under Maintenance') DEFAULT 'Available',
		department VARCHAR(100) NOT NULL,
		ward VARCHAR(100) NOT NULL,
		created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
	);
	alter table beds drop department


	CREATE TABLE patients (
		patient_id INT AUTO_INCREMENT PRIMARY KEY,
		name VARCHAR(255) NOT NULL,
		age INT NOT NULL,
		gender ENUM('Male', 'Female', 'Other') NOT NULL,
		email VARCHAR(255) UNIQUE NOT NULL,
		mobile_number VARCHAR(15) NOT NULL,
		password VARCHAR(255) NOT NULL,
		department VARCHAR(100) NOT NULL,
		doctor_assigned INT,
		bed_id INT NULL,
		FOREIGN KEY (doctor_assigned) REFERENCES doctors(id),
		FOREIGN KEY (bed_id) REFERENCES beds(bed_id)
	);
	ALTER TABLE patients
    add column registration_time timestamp default current_timestamp
	ADD COLUMN category ENUM('Inpatient', 'Outpatient') NOT NULL DEFAULT 'Outpatient';


	CREATE TABLE opd_registration (
		id INT AUTO_INCREMENT PRIMARY KEY,
		name VARCHAR(255) NOT NULL,
		age INT NOT NULL,
		gender ENUM('Male', 'Female', 'Other') NOT NULL,
		email VARCHAR(255) UNIQUE NOT NULL,
		mobile_number VARCHAR(15) NOT NULL,
		department VARCHAR(100) NOT NULL,
		queue_number INT NOT NULL UNIQUE,
		registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
	);
	ALTER TABLE opd_registration
	ADD COLUMN doctor_assigned INT;



	-- Change the column name from registered_at to registration_time
	ALTER TABLE opd_registration
	CHANGE COLUMN registered_at registration_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

	-- Add the foreign key constraint for doctor_assigned
	ALTER TABLE opd_registration
	ADD CONSTRAINT fk_doctor
	FOREIGN KEY (doctor_assigned) REFERENCES doctors(id);
	RENAME TABLE opd_registration TO opd_registrations;

	ALTER TABLE doctors ADD COLUMN password VARCHAR(255) NOT NULL;

	alter table opd_registrations
	add column pat_status enum('waiting','in-consultation','completed') not null default 'waiting';
	alter table opd_registrations
	add column problem varchar(255) default null
  
    ALTER TABLE opd_registrations
modify queue_time TIMESTAMP NULL DEFAULT NULL AFTER pat_status,
ADD COLUMN consultation_time TIMESTAMP NULL DEFAULT NULL AFTER queue_time;


