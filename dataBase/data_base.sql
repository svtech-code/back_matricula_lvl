/**
* Tabla pensada en el manejo de periodo escolar
* RESPONSABILIDAD:
*   - Definir el inicio de un periodo lectivo
*   - Habilitar la prematricula
*   - Habilitar autocorrelativo de numeros de lista
*/
create table periodo_lectivo (
	cod_periodo_lectivo serial primary key,
	periodo_lectivo int not null unique,
	esta_activo boolean not null default false,
	fecha_inicio_clases date,
	fecha_termino_clases date,
	permite_modificar_fecha boolean default false,
	autocorrelativo_lista boolean default false,
	create_at timestamp with time zone default current_timestamp
)

/**
* Tabla para los antecedentes designados en el grupo junaeb
*/
create table antecedentes_junaeb (
	cod_antecedentes_junaeb serial primary key,
	beneficio_alimentacion boolean default false,
	etnia_perteneciente varchar(100),
	beca_indigena boolean default false,
	beca_presidente_republica boolean default false
)

/**
* Tabla para los antecedentes designados en el grupo salud
*/
create table antecedentes_salud (
	cod_antecedentes_salud serial primary key,
	enfermedad_diagnosticada varchar(80),
	documentacion_enfermedades boolean default false,
	medicamentos_indicados varchar(120),
	medicamentos_contraindicados varchar(120),
	grupo_sanguineo varchar(10),
	atendido_psicologo boolean default false,
	atendido_psiquiatra boolean default false,
	atendido_psicopedagogo boolean default false,
	atendido_fonoaudiologo boolean default false,
	atendido_otro boolean default false,
	tratamiento_con_especialista boolean default false,
	nombre_especialista varchar(120),
	especialidad varchar(80)
)

/**
* Tabla para los antecedentes designados en el grupo pie
*/
create table antecedentes_pie (
	cod_antecedentes_pie serial primary key,
	pertenecio_pie boolean default false,
	diagnostico_pie varchar(80),
	curso_estuvo_pie varchar(50),
	tiene_documentacion_pie boolean default false,
	colegio_estuvo_pie varchar(120)
)

/**
* Tabla para los antecedentes designados en el grupo localidad
*/
create table antecedentes_localidad (
	cod_antecedentes_localidad serial primary key,
	direccion varchar(120) not null,
	referencia_direccion varchar(120),
	comuna varchar(80) not null,
	vive_sector_rural boolean default false,
	tiene_acceso_internet boolean default false
)

/**
* Tabla para los antecedentes designados en el grupo social
*/
create table antecedentes_sociales (
	cod_antecedentes_sociales serial primary key,
	numero_personas_casa int not null,
	numero_dormitorios int not null,
	tiene_agua_potable boolean default false,
	tiene_luz_electrica boolean default false,
	porcentaje_social_hogares int not null,
	tiene_alcantarillado boolean default false,
	prevision_salud varchar(80) not null,
	subsidio_familiar boolean default false,
	seguro_complementario_salud boolean default false,
	institucion_atencion_seguro varchar(80),
	consultorio_atencion_primaria varchar(80) not null
)

/**
* Tabla para los antecedentes designados en el grupo soacademico
*/
create table antecedentes_academicos (
	cod_antecedentes_academicos serial primary key,
	colegio_procedencia varchar(80) not null,
	cursos_reprobados varchar(50) not null,
	curso_periodo_anterior varchar(10) not null
)

/**
* Tabla para los antecedentes designados en el grupo personal
*/
create table antecedentes_personales (
	cod_antecedentes_personales serial primary key,
	numero_telefonico varchar(20),
	numero_telefonico_emergencia varchar(20) not null,
	email varchar(120),
	persona_convive varchar(80),
	talentos_academicos varchar(120),
	diciplina_practicada varchar(120),
	pertenece_programa_sename boolean default false
)

/**
* Tabla de datos para opciones de genero disponible
*/
create table genero (
	cod_genero serial primary key,
	descripcion varchar(80) not null,
	create_at timestamp with time zone default current_timestamp
)

/**
* Tabla tabla de datos de estudiantes
* REPONSABILIDAD:
* - Almacenar datos de estudiantes permanentes durante la formacion educacional
*/
create table estudiante (
	cod_estudiante serial primary key,
	run_estudiante int not null,
	dv_rut_estudiante varchar(1) not null,
	nombres varchar(80) not null,
	nombre_social varchar(40),
	apellido_paterno varchar(80) not null,
	apellido_materno varchar(40),
	fecha_nacimiento date not null,
	nacionalidad varchar(40),
	cod_genero int not null,
	create_at timestamp with time zone default current_timestamp,
	update_at timestamp with time zone default current_timestamp,

	constraint fk_cod_genero
		foreign key (cod_genero)
		references genero(cod_genero)
)

/**
* Tabla tabla contenedora de los datos que conforman una ficha de matricula (cambia cada año)
* REPONSABILIDAD:
* - Agrupar datos para conformar la ficha de matricula de un estudiante
*/
create table ficha_matricula (
	cod_ficha_matricula serial primary key,
    cod_estudiante int not null,
	cod_antecedentes_personales int not null,
	cod_antecedentes_academicos int not null,
	cod_antecedentes_localidad int not null,
	cod_antecedentes_pie int not null,
	cod_antecedentes_salud int not null,
	cod_antecedentes_sociales int not null,
	cod_antecedentes_junaeb int not null,
	cod_periodo_lectivo int not null,
	create_at timestamp with time zone default current_timestamp,

    constraint fk_ficha_cod_estudiante
        foreign key (cod_estudiante)
        references estudiante(cod_estudiante)
		on delete cascade,
	constraint fk_cod_antecedentes_personales
		foreign key (cod_antecedentes_personales)
		references antecedentes_personales(cod_antecedentes_personales)
		on delete cascade,
	constraint fk_cod_antecedentes_academicos
		foreign key (cod_antecedentes_academicos)
		references antecedentes_academicos(cod_antecedentes_academicos)
		on delete cascade,
	constraint fk_cod_antecedentes_localidad
		foreign key (cod_antecedentes_localidad)
		references antecedentes_localidad(cod_antecedentes_localidad)
		on delete cascade,
	constraint fk_cod_antecedentes_pie
		foreign key (cod_antecedentes_pie)
		references antecedentes_pie(cod_antecedentes_pie)
		on delete cascade,
	constraint fk_cod_antecedentes_salud
		foreign key (cod_antecedentes_salud)
		references antecedentes_salud(cod_antecedentes_salud)
		on delete cascade,
	constraint fk_cod_antecedentes_sociales
		foreign key (cod_antecedentes_sociales)
		references antecedentes_sociales(cod_antecedentes_sociales)
		on delete cascade,
	constraint fk_cod_antecedentes_junaeb
		foreign key (cod_antecedentes_junaeb)
		references antecedentes_junaeb(cod_antecedentes_junaeb)
		on delete cascade
)

/**
* Tabla de asignaturas disposibles para electividades generales
*/
create table formacion_general_opciones (
	cod_fg_opciones serial primary key,
	nombre_asignatura varchar(50) not null unique,
	categoria varchar(50) not null
)

/**
* Tabla contenedora de las asignaturas seleccionadas
*/
create table ficha_fg_seleccion (
	cod_ficha_fg_seleccion serial primary key,
    cod_ficha_matricula int not null,
	cod_fg_opciones int not null,
	
    constraint fk_cod_ficha_matricula
        foreign key (cod_ficha_matricula)
        references ficha_matricula(cod_ficha_matricula),
	constraint fk_cod_fg_opciones
		foreign key (cod_fg_opciones) 
		references formacion_general_opciones(cod_fg_opciones),
	
	constraint uq_ficha_opciones
		unique (cod_ficha_matricula, cod_fg_opciones)
)


/**
* Tabla de datos para opciones de escolaridad
*/
create table escolaridad (
	cod_escolaridad serial primary key,
	descripcion varchar(80) not null,
	create_at timestamp with time zone default current_timestamp
)

/**
* Tabla de datos para registrar datos de familiares
*/
create table familiar (
	cod_familiar serial primary key,
	run_familiar int not null,
	dv_run_familiar varchar(1) not null,
	nombres varchar(80) not null,
	apellido_paterno varchar(80) not null,
	apellido_materno varchar(80),
	direccion varchar(120) not null,
	comuna varchar(80) not null,
	actividad_laboral varchar(120),
	cod_escolaridad int not null,
	lugar_trabajo varchar(80) not null,
	email varchar(120) not null,
	numero_telefonico varchar(20) not null,
	create_at timestamp with time zone default current_timestamp,
	update_at timestamp with time zone default current_timestamp,

	constraint fk_cod_escolaridad
		foreign key (cod_escolaridad)
		references escolaridad(cod_escolaridad)
)

/**
* Tabla de datos para registrar los tipos de familiares disponibles
*/
create table tipo_familiar (
	cod_tipo_familiar serial primary key,
	descripcion_familiar varchar(80) not null
)

/**
* Tabla de datos para registrar los familiares por estudiante
*/
create table familiar_por_ficha_estudiante (
	cod_familiar_por_ficha_estudiante serial primary key,
	cod_ficha_matricula int not null,
	cod_familiar int not null,
	cod_tipo_familiar int not null,
	es_titular boolean default false,
	es_suplente boolean default false,

	constraint fk_cod_ficha_matricula
		foreign key (cod_ficha_matricula)
		references ficha_matricula(cod_ficha_matricula),
	constraint fk_cod_familiar
		foreign key (cod_familiar)
		references familiar (cod_familiar),
	constraint fk_cod_tipo_familiar
		foreign key (cod_tipo_familiar)
		references tipo_familiar (cod_tipo_familiar),
	constraint uq_familiar_por_ficha_estudiante
		unique (cod_ficha_matricula, cod_familiar)
)