CREATE DATABASE IF NOT EXISTS studenthub_db;
USE studenthub_db;

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mobile VARCHAR(15) NOT NULL,
    course VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO students (student_id, name, email, mobile, course, year) VALUES
('25DCE025', 'Bhavya Patel', '25dce025@charusat.edu.in', '9876543210', 'BCA', 2),
('25DCE001', 'Aarav Shah', '25dce001@charusat.edu.in', '9825012345', 'B.Tech CSE', 1),
('25DCE002', 'Diya Joshi', '25dce002@charusat.edu.in', '9825012346', 'B.Tech IT', 1),
('25DCE003', 'Kavya Sharma', '25dce003@charusat.edu.in', '9825012347', 'BCA', 1),
('25DCE004', 'Rohan Mehta', '25dce004@charusat.edu.in', '9825012348', 'BBA', 2),
('25DCE005', 'Ananya Desai', '25dce005@charusat.edu.in', '9825012349', 'B.Tech CSE', 2),
('25DCE006', 'Dev Patel', '25dce006@charusat.edu.in', '9825012350', 'B.Tech IT', 2),
('25DCE007', 'Isha Trivedi', '25dce007@charusat.edu.in', '9825012351', 'B.Com', 1),
('25DCE008', 'Manan Dave', '25dce008@charusat.edu.in', '9825012352', 'BCA', 3),
('25DCE009', 'Pooja Vyas', '25dce009@charusat.edu.in', '9825012353', 'B.Tech CSE', 3),
('25DCE010', 'Siddharth Rao', '25dce010@charusat.edu.in', '9825012354', 'B.Tech IT', 3),
('25DCE011', 'Tanvi Soni', '25dce011@charusat.edu.in', '9825012355', 'BBA', 3),
('25DCE012', 'Yash Parmar', '25dce012@charusat.edu.in', '9825012356', 'B.Tech CSE', 4),
('25DCE013', 'Sneha Vaghela', '25dce013@charusat.edu.in', '9825012357', 'B.Tech IT', 4),
('25DCE014', 'Aditya Chauhan', '25dce014@charusat.edu.in', '9825012358', 'BCA', 2),
('25DCE015', 'Riya Makwana', '25dce015@charusat.edu.in', '9825012359', 'B.Com', 2),
('25DCE016', 'Parth Barot', '25dce016@charusat.edu.in', '9825012360', 'BBA', 1),
('25DCE017', 'Khushi Prajapati', '25dce017@charusat.edu.in', '9825012361', 'B.Tech CSE', 2),
('25DCE018', 'Karan Solanki', '25dce018@charusat.edu.in', '9825012362', 'B.Tech IT', 1),
('25DCE019', 'Nidhi Bhatt', '25dce019@charusat.edu.in', '9825012363', 'BCA', 4),
('25DCE020', 'Harshil Gohil', '25dce020@charusat.edu.in', '9825012364', 'B.Com', 3);
