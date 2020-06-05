CREATE USER template
  WITH
    LOGIN
    CREATEDB
    NOSUPERUSER
    ENCRYPTED PASSWORD 'password';

CREATE DATABASE template 
  WITH
    OWNER = template
    TEMPLATE = template0
    CONNECTION LIMIT = -1;