I used a 'test' database.
I have listed the commands here that I used to make the appropriate stuffs (more for convenience then anything)

test=# CREATE USER test WITH PASSWORD 'pass';
test=# CREATE TABLE calendar ("id" SERIAL PRIMARY KEY,"title" varchar(255),"start" timestamp,"end" timestamp,"allDay" smallint);
NOTICE:  CREATE TABLE will create implicit sequence "calendar_id_seq" for serial column "calendar.id"
NOTICE:  CREATE TABLE / PRIMARY KEY will create implicit index "calendar_pkey" for table "calendar"
test=# GRANT ALL PRIVILEGES ON calendar to test;
test=# GRANT ALL PRIVILEGES ON calendar_id_seq to test;
