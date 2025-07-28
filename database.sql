create database postgres;
create table if not exists roles
(
    id         serial
    primary key,
    nombre_rol varchar(50) not null
    unique
    );

alter table roles
    owner to postgres;

create table if not exists colaboradores
(
    id                      serial
    primary key,
    primer_nombre           varchar(50)                                               not null,
    segundo_nombre          varchar(50),
    primer_apellido         varchar(50)                                               not null,
    segundo_apellido        varchar(50),
    sexo                    char                                                      not null,
    identificacion          varchar(25)                                               not null
    unique,
    fecha_nacimiento        date                                                      not null,
    foto_perfil             varchar(255),
    correo_personal         varchar(100)                                              not null
    unique,
    telefono                varchar(20),
    celular                 varchar(20),
    direccion               text,
    activo                  boolean     default true                                  not null,
    estatus                 varchar(50) default 'Activo Laborando'::character varying not null,
    historial_academico_pdf varchar(255)
    );

alter table colaboradores
    owner to postgres;

create table if not exists vacaciones
(
    id                serial
    primary key,
    colaborador_id    integer not null
    references colaboradores,
    fecha_resuelto    date    not null,
    dias_tomados      integer not null,
    documento_pdf_url varchar(255)
    );

alter table vacaciones
    owner to postgres;

create table if not exists departamentos
(
    id_departamento     serial
    constraint departamentos_pk
    primary key,
    nombre_departamento varchar(30) not null
    );

alter table departamentos
    owner to postgres;

create table if not exists cargos
(
    id                 serial
    primary key,
    colaborador_id     integer        not null
    references colaboradores,
    sueldo             numeric(10, 2) not null,
    departamento_id    integer        not null
    constraint cargos_departamentos_id_departamento_fk
    references departamentos,
    ocupacion          varchar(100)   not null,
    tipo_contrato      varchar(50)    not null,
    fecha_contratacion date           not null,
    activo             boolean                  default true,
    firma_digital      text,
    fecha_creacion     timestamp with time zone default now()
    );

alter table cargos
    owner to postgres;

create table if not exists usuarios
(
    id            serial
    primary key,
    nombre        varchar(100) not null,
    email         varchar(100) not null
    unique,
    password_hash varchar(255) not null,
    activo        boolean default true,
    rol_id        integer
    references roles
    );

alter table usuarios
    owner to postgres;

