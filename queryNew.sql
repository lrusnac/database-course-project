/************************* CLEAN  ****************************/
DROP TABLE IF EXISTS films CASCADE;
DROP TABLE IF EXISTS shows CASCADE;
DROP TABLE IF EXISTS rooms CASCADE;
DROP TABLE IF EXISTS books CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS users_session CASCADE;

DROP SEQUENCE IF EXISTS idbook_seq;
DROP SEQUENCE IF EXISTS idfilm_seq;
DROP SEQUENCE IF EXISTS idroom_seq;
DROP SEQUENCE IF EXISTS idshow_seq;

DROP FUNCTION IF EXISTS new_user(text, text, text, text, text);
DROP FUNCTION IF EXISTS new_film(text, text, text, text, integer);
DROP FUNCTION IF EXISTS new_show(date, integer, real, integer, integer);
DROP FUNCTION IF EXISTS new_book(integer, integer, text);
DROP FUNCTION IF EXISTS new_room(text, integer);

/******************** TABLES and SEQ ***********************/
CREATE TABLE films (
	idfilm			INTEGER PRIMARY KEY,
	title			TEXT NOT NULL,
	genre			TEXT,
	castmembers		TEXT,
	directedby		TEXT,
	runningtime		INTEGER
);

CREATE TABLE shows (
	idshow			INTEGER PRIMARY KEY,
	day			DATE,
	hour			INTEGER,
	price			REAL,
	idfilm			INTEGER NOT NULL,
	idroom			INTEGER NOT NULL
);

CREATE TABLE rooms (
	idroom			INTEGER PRIMARY KEY,
	name			TEXT,
	seats			INTEGER NOT NULL
);

CREATE TABLE bookings (
	idbook			INTEGER PRIMARY KEY,
	seatsnumber		INTEGER NOT NULL,
	idshow			INTEGER NOT NULL,
	userlogin		TEXT NOT NULL
);

CREATE TABLE users (
	login			TEXT PRIMARY KEY,
	fullname		TEXT NOT NULL,
	address			TEXT NOT NULL,
	password		TEXT NOT NULL,
	salt			TEXT NOT NULL,
	"group"			INTEGER DEFAULT 1
);

CREATE TABLE users_session (
	login			TEXT PRIMARY KEY references users(login),
	hash			TEXT NOT NULL
);


ALTER TABLE shows 
ADD FOREIGN KEY (idfilm) REFERENCES films(idfilm) ON UPDATE CASCADE ON DELETE CASCADE,
ADD FOREIGN KEY (idroom) REFERENCES rooms(idroom) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE bookings 
ADD FOREIGN KEY (idshow) REFERENCES shows(idshow) ON UPDATE CASCADE ON DELETE CASCADE,
ADD FOREIGN KEY (userlogin) REFERENCES users(login) ON UPDATE CASCADE ON DELETE CASCADE;

CREATE SEQUENCE idbook_seq START 1;
CREATE SEQUENCE idfilm_seq START 1;
CREATE SEQUENCE idshow_seq START 1;
CREATE SEQUENCE idroom_seq START 1;

/**************************** FUNCTIONS ****************************/
CREATE FUNCTION new_user(text, text, text, text, text) RETURNS VOID AS $$
	INSERT INTO users (login, password, salt, fullname, address) VALUES ($1, $2, $3, $4, $5)
$$ LANGUAGE SQL;

CREATE FUNCTION new_film(text, text, text, text, integer) RETURNS VOID AS $$
	INSERT INTO films (idfilm, title, genre, castmembers, directedby, runningtime) VALUES (nextval('idfilm_seq'::regclass),$1, $2, $3, $4, $5);
$$ LANGUAGE SQL;

CREATE FUNCTION new_show(date, integer, real, integer, integer) RETURNS VOID AS $$
	INSERT INTO shows (idshow, day, hour, price, idfilm, idroom) VALUES (nextval('idshow_seq'::regclass), $1, $2, $3, $4, $5);
$$ LANGUAGE SQL;

CREATE FUNCTION new_book(integer, integer, text) RETURNS VOID AS $$
	INSERT INTO bookings (idbook, seatsnumber, idshow, userlogin) VALUES (nextval('idbook_seq'::regclass), $1, $2, $3);
$$ LANGUAGE SQL;

CREATE FUNCTION new_room(text, integer) RETURNS VOID AS $$
	INSERT INTO rooms (idroom, name, seats) VALUES (nextval('idroom_seq'::regclass), $1, $2);
$$ LANGUAGE SQL;

/**************************** QUERY ****************************/

/*tutti i film che sono proietatti oggi*/
SELECT DISTINCT f.* FROM shows s, films f WHERE s.idfilm = f.idfilm AND s.day=current_date
/*tutti gli orari di oggi di un certo film*/
SELECT s.day, s.hour FROM shows s WHERE s.day=current_date AND s.idfilm=:idfilm ORDER BY s.hour

/*conta il numero di posti occupati*/
SELECT r.seats, sum(b.seatsnumber), s.idshow
FROM (rooms r LEFT JOIN shows s ON r.idroom=s.idroom LEFT JOIN bookings b ON s.idshow=b.idshow), films f
WHERE f.idfilm=s.idfilm AND s.day=:day AND s.hour=:hour AND f.idfilm=:idfilm
GROUP BY r.seats, s.idshow;




/**************************** INSERTS ****************************/
select new_room('Sala A', 50);
select new_room('Sala B', 70);
select new_room('Sala C', 90);
select new_room('Sala D', 10);

select new_film('Maleficent', 'Action, Adventure, Drama', 'Angelina Jolie, Elle Fanning, Sharlto Copley', 'Robert Stromberg', 97);
select new_film('X-Men: Days Of Future Past', 'Adventure', 'Jennifer Lawrence, Michael Fassbender, Hugh Jackman, James McAvoy, Ian McKellen, Patrick Stewart', 'Bryan Singer', 131);
select new_film('22 Jump Street', 'Action, Comedy', 'Channing Tatum, Jonah Hill, Ice Cube', 'Phil Lord, Chris Miller', 112);
select new_film('Dirty Dancing', 'Drama', 'Patrick Swayze, Jennifer Grey, Jerry Orbach', 'Emile Ardolino', 110);
select new_film('Edge of Tomorrow', 'Action', 'Tom Cruise, Emily Blunt, Bill Paxton', 'Doug Liman', 113);
select new_film('A Million Ways to Die in the West', 'Comedy', 'Charlize Theron, Amanda Seyfried, Liam Neeson', 'Seth MacFarlane', 116);

select new_show(current_date, 10, 8.50, 1, 1);
select new_show(current_date, 17, 8.50, 1, 1);
select new_show(current_date, 11, 8.50, 5, 2);
select new_show(current_date, 14, 8.50, 4, 3);

select new_show(current_date, 10, 8.50, 2, 3);
select new_show(current_date, 17, 8.50, 3, 2);
select new_show(current_date, 11, 8.50, 6, 4);
select new_show(current_date, 13, 8.50, 4, 1);