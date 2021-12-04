-- CREATE TABLES and Initialize all variables/functions/triggers
DROP DATABASE IF EXISTS PPC;

CREATE DATABASE PPC;

USE PPC;

SOURCE createTables.sql;

SOURCE triggers.sql;

SOURCE populate_database.sql