CREATE USER web WITH ENCRYPTED PASSWORD 'password';

CREATE DATABASE tests OWNER web;
GRANT ALL PRIVILEGES ON DATABASE tests TO web;

ALTER ROLE web SUPERUSER;