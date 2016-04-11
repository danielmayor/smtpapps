-- Role: fluentd

-- DROP ROLE fluentd;

CREATE ROLE fluentd LOGIN
  ENCRYPTED PASSWORD 'md5d70f788e89987c6d0f71cf4300f19ff0'
  NOSUPERUSER INHERIT NOCREATEDB NOCREATEROLE;

-- Schema: fluentd

-- DROP SCHEMA fluentd;

CREATE SCHEMA fluentd
  AUTHORIZATION fluentd;

COMMENT ON SCHEMA fluentd
  IS 'Esquema para colector de logs fluentd';


  
-- Table: fluentd."from"

-- DROP TABLE fluentd."from";

CREATE TABLE fluentd."from"
(
  id_from bigint NOT NULL DEFAULT nextval('fluentd.from_id_from_seq'::regclass),
  hostname character(8),
  process character(30),
  queue_id character(30),
  desde character varying(254),
  size integer,
  nrcpt character varying(18),
  "time" timestamp without time zone,
  CONSTRAINT from_pkey PRIMARY KEY (id_from)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE fluentd."from"
  OWNER TO fluentd;


-- Table: fluentd.postfix

-- DROP TABLE fluentd.postfix;

CREATE TABLE fluentd.postfix
(
  id_log bigint NOT NULL DEFAULT nextval('fluentd.postfix_id_log_seq'::regclass),
  hostname character(8),
  queue_id character(30),
  para character varying(254),
  domain character varying(254),
  relay character varying(64),
  delay double precision,
  delays character varying(19),
  dsn character(5),
  status character varying(10),
  status_detail text,
  process character(20),
  "time" timestamp without time zone,
  CONSTRAINT postfix_pkey PRIMARY KEY (id_log)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE fluentd.postfix
  OWNER TO fluentd;


-- Sequence: fluentd.from_id_from_seq

-- DROP SEQUENCE fluentd.from_id_from_seq;

CREATE SEQUENCE fluentd.from_id_from_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;
ALTER TABLE fluentd.from_id_from_seq
  OWNER TO fluentd;


-- Sequence: fluentd.postfix_id_log_seq

-- DROP SEQUENCE fluentd.postfix_id_log_seq;

CREATE SEQUENCE fluentd.postfix_id_log_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 9192
  CACHE 1;
ALTER TABLE fluentd.postfix_id_log_seq
  OWNER TO fluentd;
