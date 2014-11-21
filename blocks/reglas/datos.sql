

--Crea Usuario
CREATE USER reglas WITH PASSWORD '123456';

--Crea esquema y asigna permisos
CREATE SCHEMA reglas
  AUTHORIZATION reglas;

--Tabla tipo_datos creacion
CREATE TABLE reglas.tipo_datos
(
  tipo_id serial NOT NULL,
  tipo_nombre text NOT NULL,
  tipo_alias text NOT NULL,
  CONSTRAINT tipo_pk PRIMARY KEY (tipo_id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE reglas.tipo_datos
  OWNER TO reglas;

 --Llenar tabla
  INSERT INTO reglas.tipo_datos(
            tipo_nombre, tipo_alias)
    VALUES 
    ('boolean','Boleano'),
    ('integer','Entero'),
    ('double','Doble'),
    ('percent','Porcentaje'),
    ('date','Fecha'),
    ('string','Texto'),
    ('array','Lista'),
    ('NULL','Nulo');
  
  -- Crea tablas de estados
  CREATE TABLE reglas.estados
(
  estados_id serial NOT NULL,
  estados_nombre text NOT NULL,
  estados_alias text NOT NULL,
  CONSTRAINT estados_pk PRIMARY KEY (estados_id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE reglas.estados
  OWNER TO reglas;
  
  --llena Tabla de estados
  
  
  INSERT INTO reglas.estados(
            estados_nombre,estados_alias)
    VALUES ( 'activo','Activo'),
	   ('inactivo','Inactivo'),
	   ('creado','Creado');
  
  
--Crea tabla de Objetos  
  CREATE TABLE reglas.objetos
(
  objetos_id serial NOT NULL ,
  objetos_nombre text NOT NULL,
  objetos_alias text NOT NULL,
  CONSTRAINT objetos_pk PRIMARY KEY (objetos_id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE reglas.objetos
  OWNER TO reglas;
  
  
  ---Lnea Tabla de Objetos
  INSERT INTO reglas.objetos(
            objetos_nombre,objetos_alias)
    VALUES ( 'reglas.parametros','Parametros'),
    		( 'reglas.variables','Variables'),
    		( 'reglas.funciones','Funciones'),
    		( 'reglas.reglas','Reglas'),
    		( 'reglas.usuarios','Usuarios'),
    		( 'reglas.relaciones','Permisos'),
    		( 'reglas.acceso','Acceso');
    		



--Crea tabla de Permisos  
  CREATE TABLE reglas.permisos
(
  permisos_id serial NOT NULL,
  permisos_nombre text NOT NULL,
  permisos_alias text NOT NULL,
  CONSTRAINT permisos_pk PRIMARY KEY (permisos_id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE reglas.permisos
  OWNER TO reglas;
  
--set a la secuencia en 0
ALTER SEQUENCE "permisos_permisos_id_seq" MINVALUE 0 START 0 RESTART 0;

---Llena Tabla de Permisos
  INSERT INTO reglas.permisos(
            permisos_nombre,permisos_alias)
    VALUES ( 'propietario','Propietario'),
    		( 'crear','crear'),
    		( 'consultar','Consultar'),
    		( 'actualizar','Actualizar'),
    		( 'eliminar','Eliminar'),
    		( 'administrador','Administrador');




--Tabla de parametros	   
CREATE TABLE reglas.parametros
(
  par_id serial NOT NULL,
  par_nombre text UNIQUE NOT NULL,
  par_descripcion text,
  par_proceso integer NOT NULL,
  par_tipo integer NOT NULL,
  par_valor text NOT NULL,
  par_estado integer NOT NULL,
  par_fecha_registro date NOT NULL DEFAULT ('now'::text)::date,
  CONSTRAINT parametros_pk PRIMARY KEY (par_id),
  CONSTRAINT parametros_estados_fk FOREIGN KEY (par_estado)
      REFERENCES reglas.estados (estados_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE SET NULL,
  CONSTRAINT parametros_tipo_fk FOREIGN KEY (par_tipo)
      REFERENCES reglas.tipo_datos (tipo_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
ALTER TABLE reglas.parametros
  OWNER TO reglas;
     
--tabla h parametros
 CREATE TABLE reglas.parametros_h
(
  par_hid serial NOT NULL,
  par_id_h integer NOT NULL,
  par_nombre_h text NOT NULL,
  par_descripcion_h text,
  par_proceso_h integer NOT NULL,
  par_tipo_h integer NOT NULL,
  par_valor_h text NOT NULL,
  par_estado_h integer NOT NULL,
  par_fecha_registro_h date NOT NULL ,
  par_fecha_h date NOT NULL DEFAULT ('now'::text)::date,
  par_usuario text NOT NULL,
  CONSTRAINT parametros_h_pk PRIMARY KEY (par_hid)
  
)
WITH (
  OIDS=FALSE
);
ALTER TABLE reglas.parametros_h
  OWNER TO reglas;


--Variables
--Tabla de Variables	   
CREATE TABLE reglas.variables
(
  var_id serial NOT NULL,
  var_nombre text UNIQUE NOT NULL,
  var_descripcion text,
  var_proceso integer NOT NULL,
  var_tipo integer NOT NULL,
  var_valor text NOT NULL,
  var_estado integer NOT NULL,
  var_fecha_registro date NOT NULL DEFAULT ('now'::text)::date,
  CONSTRAINT variables_pk PRIMARY KEY (var_id),
  CONSTRAINT variables_estados_fk FOREIGN KEY (var_estado)
      REFERENCES reglas.estados (estados_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE SET NULL,
  CONSTRAINT variables_tipo_fk FOREIGN KEY (var_tipo)
      REFERENCES reglas.tipo_datos (tipo_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
ALTER TABLE reglas.variables
  OWNER TO reglas;
     
--tabla h variables
 CREATE TABLE reglas.variables_h
(
  var_hid serial NOT NULL,
  var_id_h integer NOT NULL,
  var_nombre_h text NOT NULL,
  var_descripcion_h text,
  var_proceso_h integer NOT NULL,
  var_tipo_h integer NOT NULL,
  var_valor_h text NOT NULL,
  var_estado_h integer NOT NULL,
  var_fecha_registro_h date NOT NULL ,
  var_fecha_h date NOT NULL DEFAULT ('now'::text)::date,
  var_usuario text NOT NULL,
  CONSTRAINT variables_h_pk PRIMARY KEY (var_hid)
  
)
WITH (
  OIDS=FALSE
);
ALTER TABLE reglas.variables_h
  OWNER TO reglas;
  
  
 --Funciones
--Tabla de funciones	   
CREATE TABLE reglas.funciones
(
  fun_id serial NOT NULL,
  fun_nombre text UNIQUE NOT NULL,
  fun_descripcion text,
  fun_proceso integer NOT NULL,
  fun_tipo integer NOT NULL,
  fun_valor text NOT NULL,
  fun_estado integer NOT NULL,
  fun_fecha_registro date NOT NULL DEFAULT ('now'::text)::date,
  CONSTRAINT funciones_pk PRIMARY KEY (fun_id),
  CONSTRAINT funciones_estados_fk FOREIGN KEY (fun_estado)
      REFERENCES reglas.estados (estados_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE SET NULL,
  CONSTRAINT funciones_tipo_fk FOREIGN KEY (fun_tipo)
      REFERENCES reglas.tipo_datos (tipo_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
ALTER TABLE reglas.funciones
  OWNER TO reglas;
     
--tabla h variables
 CREATE TABLE reglas.funciones_h
(
  fun_hid serial NOT NULL,
  fun_id_h integer NOT NULL,
  fun_nombre_h text NOT NULL,
  fun_descripcion_h text,
  fun_proceso_h integer NOT NULL,
  fun_tipo_h integer NOT NULL,
  fun_valor_h text NOT NULL,
  fun_estado_h integer NOT NULL,
  fun_fecha_registro_h date NOT NULL ,
  fun_fecha_h date NOT NULL DEFAULT ('now'::text)::date,
  fun_usuario text NOT NULL,
  CONSTRAINT funciones_h_pk PRIMARY KEY (fun_hid)
  
)
WITH (
  OIDS=FALSE
);
ALTER TABLE reglas.funciones_h
  OWNER TO reglas;
  
  

 --Reglas
--Tabla de Reglas	   
CREATE TABLE reglas.reglas
(
  reg_id serial NOT NULL,
  reg_nombre text UNIQUE NOT NULL,
  reg_descripcion text,
  reg_proceso integer NOT NULL,
  reg_tipo integer NOT NULL,
  reg_valor text NOT NULL,
  reg_estado integer NOT NULL,
  reg_fecha_registro date NOT NULL DEFAULT ('now'::text)::date,
  CONSTRAINT reglas_pk PRIMARY KEY (reg_id),
  CONSTRAINT reglas_estados_fk FOREIGN KEY (reg_estado)
      REFERENCES reglas.estados (estados_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE SET NULL,
  CONSTRAINT funciones_tipo_fk FOREIGN KEY (reg_tipo)
      REFERENCES reglas.tipo_datos (tipo_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
ALTER TABLE reglas.reglas
  OWNER TO reglas;
     
--tabla h variables
 CREATE TABLE reglas.reglas_h
(
  reg_hid serial NOT NULL,
  reg_id_h integer NOT NULL,
  reg_nombre_h text NOT NULL,
  reg_descripcion_h text,
  reg_proceso_h integer NOT NULL,
  reg_tipo_h integer NOT NULL,
  reg_valor_h text NOT NULL,
  reg_estado_h integer NOT NULL,
  reg_fecha_registro_h date NOT NULL ,
  reg_fecha_h date NOT NULL DEFAULT ('now'::text)::date,
  reg_usuario text NOT NULL,
  CONSTRAINT reglas_h_pk PRIMARY KEY (reg_hid)
  
)
WITH (
  OIDS=FALSE
);
ALTER TABLE reglas.reglas_h
  OWNER TO reglas;



--Tabla tipo_usuarios creacion
CREATE TABLE reglas.tipo_usuarios
(
  tipo_usuarios_id serial NOT NULL,
  tipo_usuarios_nombre text NOT NULL,
  tipo_usuarios_alias text NOT NULL,
  CONSTRAINT tipo_usuarios_pk PRIMARY KEY (tipo_usuarios_id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE reglas.tipo_usuarios
  OWNER TO reglas;

 --Llenar tabla
  INSERT INTO reglas.tipo_usuarios(
            tipo_usuarios_nombre, tipo_usuarios_alias)
    VALUES 
    ('usuarios','Usuarios'),
    ('grupo','Grupos');


--usuarios
--Tabla de usuarios	   
CREATE TABLE reglas.usuarios
(
  usu_id integer NOT NULL,
  usu_tipo integer UNIQUE NOT NULL,
  usu_estado integer NOT NULL,
  usu_fecha_registro date NOT NULL DEFAULT ('now'::text)::date,
  CONSTRAINT usuarios_pk PRIMARY KEY (usu_id),
  CONSTRAINT reglas_estados_fk FOREIGN KEY (usu_estado)
      REFERENCES reglas.estados (estados_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE SET NULL,
  CONSTRAINT funciones_tipo_usuarios_fk FOREIGN KEY (usu_tipo)
      REFERENCES reglas.tipo_usuarios (tipo_usuarios_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
ALTER TABLE reglas.usuarios
  OWNER TO reglas;
  
 --Acceso
--Tabla de Acceso	   
CREATE TABLE reglas.acceso
(
  acc_id serial NOT NULL,
  acc_codigo text UNIQUE NOT NULL,
  acc_usuario text NOT NULL,
  acc_detalle text NOT NULL,
  acc_fecha_registro date NOT NULL DEFAULT ('now'::text)::date,
  CONSTRAINT acceso_pk PRIMARY KEY (acc_id)
  )
WITH (
  OIDS=FALSE
);
ALTER TABLE reglas.acceso
  OWNER TO reglas;



--Relaciones
 --Relaciones
--Tabla de Relaciones	   
CREATE TABLE reglas.relaciones
(
  rel_id serial NOT NULL,
  rel_usuario integer NOT NULL,
  rel_objeto integer NOT NULL,
  rel_registro integer NOT NULL,
  rel_permiso integer NOT NULL,
  rel_estado	 integer NOT NULL ,
  rel_fecha_registro date NOT NULL DEFAULT ('now'::text)::date,
  
  CONSTRAINT relaciones_pk PRIMARY KEY (rel_id),
  CONSTRAINT relaciones_estados_fk FOREIGN KEY (rel_estado)
      REFERENCES reglas.estados (estados_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE SET NULL,
  CONSTRAINT relaciones_usuarios_fk FOREIGN KEY (rel_usuario)
      REFERENCES reglas.usuarios (usu_id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE SET NULL
  
  
)
WITH (
  OIDS=FALSE
);
ALTER TABLE reglas.relaciones
  OWNER TO reglas;
 
CREATE TABLE reglas.relaciones_h
(
  
  rel_hid serial NOT NULL,
  rel_id_h integer NOT NULL,
  rel_usuario_h integer NOT NULL,
  rel_objeto_h integer NOT NULL,
  rel_registro_h integer NOT NULL,
  rel_permiso_h integer NOT NULL,
  rel_estado_h	 integer NOT NULL ,
  rel_fecha_registro_h date NOT NULL DEFAULT ('now'::text)::date,
  rel_fecha_h date NOT NULL DEFAULT ('now'::text)::date,
  rel_usuario text NOT NULL,
  CONSTRAINT relaciones_h_pk PRIMARY KEY (rel_hid)
  
  
)
WITH (
  OIDS=FALSE
);
ALTER TABLE reglas.relaciones_h
  OWNER TO reglas;
   
  
 