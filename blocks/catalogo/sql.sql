--crea usuario dba

CREATE ROLE catalogodba LOGIN ENCRYPTED PASSWORD 'md5f6ac4582dc60373a41d9b16115c73b6d'
   VALID UNTIL 'infinity';


--crea usuario de aplicacion

-----NOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO
CREATE ROLE catalogousr LOGIN ENCRYPTED PASSWORD 'md54dfb31791290aa7af4b82f1bab97e21f'
   VALID UNTIL 'infinity';


--crea esquema

CREATE SCHEMA catalogo
       AUTHORIZATION catalogodba;

--crea tablas

  
  -- Table: catalogo.catalogo_lista

-- DROP TABLE catalogo.catalogo_lista;

CREATE TABLE catalogo.catalogo_lista
(
  lista_id serial NOT NULL,
  lista_nombre text NOT NULL,
  lista_fecha_creacion date NOT NULL DEFAULT ('now'::text)::date,
  CONSTRAINT lista_pk PRIMARY KEY (lista_id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE catalogo.catalogo_lista
  OWNER TO catalogodba;

CREATE TABLE catalogo.catalogo_lista_h
(
  lista_h_id serial NOT NULL,
  lista_id_h integer NOT NULL,
  lista_nombre_h text NOT NULL,
  lista_fecha_creacion_h date NOT NULL DEFAULT ('now'::text)::date,
  CONSTRAINT lista_h_pk PRIMARY KEY (lista_h_id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE catalogo.catalogo_lista
  OWNER TO catalogodba;
  
  -- Table: catalogo.catalogo_elemento

-- DROP TABLE catalogo.catalogo_elemento;

 
 
CREATE TABLE catalogo.catalogo_elemento
(
  elemento_id serial NOT NULL,
  elemento_padre integer NOT NULL DEFAULT 0,
  elemento_codigo integer NOT NULL,
  elemento_catalogo integer,
  elemento_nombre text NOT NULL,
  elemento_fecha_creacion date NOT NULL DEFAULT ('now'::text)::date,
  CONSTRAINT elemento_pk PRIMARY KEY (elemento_id),
  CONSTRAINT elemento_fk FOREIGN KEY (elemento_catalogo)
      REFERENCES catalogo.catalogo_lista (lista_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE catalogo.catalogo_elemento
  OWNER TO catalogodba;

  
  
CREATE TABLE catalogo.catalogo_elemento_h
(
  elemento_h_id serial NOT NULL,
  elemento_id_h integer NOT NULL,
  elemento_padre_h integer NOT NULL DEFAULT 0,
  elemento_codigo_h integer NOT NULL,
  elemento_catalogo_h integer,
  elemento_nombre_h text NOT NULL,
  elemento_fecha_creacion_h date NOT NULL ,
  elemento_fecha_h date NOT NULL DEFAULT ('now'::text)::date,
  CONSTRAINT elemento_h_pk PRIMARY KEY (elemento_h_id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE catalogo.catalogo_elemento_h
  OWNER TO catalogodba;
  
  