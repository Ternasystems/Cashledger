/* Profiling app */

/* Permissions, Roles, RoleRelations */

-- Table: public.cl_Permissions

DROP TABLE IF EXISTS public."cl_Permissions";
CREATE TABLE public."cl_Permissions"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" character(1) COLLATE pg_catalog."default" UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertPermission(character varying, character, text)

CREATE OR REPLACE PROCEDURE public."p_InsertPermission"(
	IN _name character varying(50),
	IN _code character(1),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Permissions'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'PRM');
END;
$BODY$;

-- PROCEDURE: public.p_UpdatePermission(character varying, character varying, character, text)

CREATE OR REPLACE PROCEDURE public."p_UpdatePermission"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _code character(1),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Permissions';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "Code" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _code, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeletePermission(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeletePermission"(IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Permissions';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Permission

CREATE OR REPLACE TRIGGER "Delete_Permission"
    BEFORE DELETE
    ON public."cl_Permissions"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Permission

CREATE OR REPLACE TRIGGER "Insert_Permission"
    BEFORE INSERT
    ON public."cl_Permissions"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Permission

CREATE OR REPLACE TRIGGER "Update_Permission"
    BEFORE UPDATE 
    ON public."cl_Permissions"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_Roles

DROP TABLE IF EXISTS public."cl_Roles";
CREATE TABLE public."cl_Roles"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" integer UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertRole(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertRole"(
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _id character varying(50); _code integer; _sql text; _tablename text; _procname text;
BEGIN
	-- Set CurrentThread parameter
	CALL public."p_CurrentThread"(TRUE);
	
	-- Set IsProc parameter
    CALL public."p_IsProc"(TRUE);
	--
	BEGIN
		-- Create ID
		_id := public."f_CreateID"('ROL', 'cl_Roles');
		-- Set the Code
		SELECT COALESCE(MAX("Code"), 0) + 1 INTO _code FROM public."cl_Roles";
		-- Insert data into cl_Roles
		INSERT INTO public."cl_Roles" VALUES (_id, _code, _name, NULL, _description);
		-- Create role table
		_tablename := 'cl_Role_' || _name;
		EXECUTE FORMAT('
			CREATE TABLE IF NOT EXISTS public.%I
			(
				"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
				"RoleID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Roles" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
				"Controller" character varying(50) COLLATE pg_catalog."default" NOT NULL,
				"Action" character varying(50) COLLATE pg_catalog."default" NOT NULL,
				"Permissions" character varying(50) COLLATE pg_catalog."default" NOT NULL,
				"IsActive" timestamp without time zone,
   				"Description" text COLLATE pg_catalog."default",
				CONSTRAINT %I UNIQUE ("RoleID", "Controller", "Action") 
			)
			TABLESPACE pg_default;
		', _tablename, 'PK_' || _name, 'UQ_' || _name, 'FK_' || _name);
		--
		_procname := 'p_InsertRole_' || _name;
		EXECUTE FORMAT('
			CREATE OR REPLACE PROCEDURE public.%I(
				IN _roleid character varying(50),
				IN _controller character varying(50),
				IN _action character varying(50),
				IN _permissions character varying(50),
				IN _description text DEFAULT NULL::text)
			LANGUAGE ''plpgsql''
			AS $$
			DECLARE _id character varying(50);
			BEGIN
				-- Set CurrentThread parameter
				CALL public."p_CurrentThread"(TRUE);
				
				-- Set IsProc parameter
			    CALL public."p_IsProc"(TRUE);
				--
				BEGIN
				-- Create ID
				_id := public."f_CreateID"(''PRR'', %L);
				-- Insert data into cl_Roles
				INSERT INTO public.%I VALUES (_id, _roleid, _controller, _action, _permissions, NULL, _description);
				--
				EXCEPTION WHEN OTHERS THEN
					-- Reset IsProc parameter
				    CALL public."p_IsProc"(FALSE);
					
					-- Reset CurrentThread parameter
					CALL public."p_CurrentThread"(FALSE);
					RETURN;
				END;
				-- Reset IsProc parameter
			    CALL public."p_IsProc"(FALSE);
				
				-- Reset CurrentThread parameter
				CALL public."p_CurrentThread"(FALSE);
			END;
			$$;
		', _procname, _tablename, _tablename);
		_procname := 'p_UpdateRole_' || _name;
		EXECUTE FORMAT('
			CREATE OR REPLACE PROCEDURE public.%I(
				IN _id character varying(50),
				IN _permissions character varying(50),
				IN _description text DEFAULT NULL::text)
			LANGUAGE ''plpgsql''
			AS $$
			BEGIN
				-- Set CurrentThread parameter
				CALL public."p_CurrentThread"(TRUE);
				
				-- Set IsProc parameter
			    CALL public."p_IsProc"(TRUE);
				--
				BEGIN
				-- Update data from cl_Roles table
				UPDATE public.%I SET "Permissions" = _permissions, "Description" = _description WHERE "ID" = _id;
				--
				EXCEPTION WHEN OTHERS THEN
					-- Reset IsProc parameter
				    CALL public."p_IsProc"(FALSE);
					
					-- Reset CurrentThread parameter
					CALL public."p_CurrentThread"(FALSE);
					RETURN;
				END;
				-- Reset IsProc parameter
			    CALL public."p_IsProc"(FALSE);
				
				-- Reset CurrentThread parameter
				CALL public."p_CurrentThread"(FALSE);
			END;
			$$;
		', _procname, _tablename);
		_procname := 'p_DeleteRole_' || _name;
		EXECUTE FORMAT('
			CREATE OR REPLACE PROCEDURE public.%I(IN _id character varying(50))
			LANGUAGE ''plpgsql''
			AS $$
			BEGIN
				-- Set CurrentThread parameter
				CALL public."p_CurrentThread"(TRUE);
				
				-- Set IsProc parameter
			    CALL public."p_IsProc"(TRUE);
				--
				BEGIN
				-- Update data from cl_Roles table
				UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = _id;
				--
				EXCEPTION WHEN OTHERS THEN
					-- Reset IsProc parameter
				    CALL public."p_IsProc"(FALSE);
					
					-- Reset CurrentThread parameter
					CALL public."p_CurrentThread"(FALSE);
					RETURN;
				END;
				-- Reset IsProc parameter
			    CALL public."p_IsProc"(FALSE);
				
				-- Reset CurrentThread parameter
				CALL public."p_CurrentThread"(FALSE);
			END;
			$$;
		', _procname, _tablename);
		_procname := 'Delete_Role_' || _name;
		EXECUTE FORMAT('
			CREATE OR REPLACE TRIGGER %I
			    BEFORE DELETE
			    ON public.%I
			    FOR EACH ROW
			    EXECUTE FUNCTION public."t_DeleteTrigger"();
		', _procname, _tablename);
		_procname := 'Insert_Role_' || _name;
		EXECUTE FORMAT('
			CREATE OR REPLACE TRIGGER %I
			    BEFORE INSERT 
			    ON public.%I
			    FOR EACH ROW
			    EXECUTE FUNCTION public."t_InsertTrigger"();
		', _procname, _tablename);
		_procname := 'Update_Role_' || _name;
		EXECUTE FORMAT('
			CREATE OR REPLACE TRIGGER %I
			    BEFORE UPDATE 
			    ON public.%I
			    FOR EACH ROW
			    EXECUTE FUNCTION public."t_UpdateTrigger"();
		', _procname, _tablename);
		_procname := 'Log_Role_' || _name;
		EXECUTE FORMAT('
			CREATE OR REPLACE TRIGGER %I
			    AFTER INSERT OR UPDATE OR DELETE 
			    ON public.%I
			    FOR EACH ROW
			    EXECUTE FUNCTION public."t_LogAudit"();
		', _procname, _tablename);
		_procname := 'Release_Role_' || _name;
		EXECUTE FORMAT('
			CREATE OR REPLACE TRIGGER %I
				AFTER INSERT OR UPDATE OR DELETE
				ON public.%I
				FOR EACH ROW
				EXECUTE FUNCTION public."t_ReleaseThread"();
		', _procname, _tablename);
	--
	EXCEPTION WHEN OTHERS THEN
		-- Reset IsProc parameter
	    CALL public."p_IsProc"(FALSE);
		
		-- Reset CurrentThread parameter
		CALL public."p_CurrentThread"(FALSE);
		RETURN;
	END;
	-- Reset IsProc parameter
    CALL public."p_IsProc"(FALSE);
	
	-- Reset CurrentThread parameter
	CALL public."p_CurrentThread"(FALSE);
END;
$BODY$;

-- Trigger: Delete_Role

CREATE OR REPLACE TRIGGER "Delete_Role"
    BEFORE DELETE
    ON public."cl_Roles"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Role

CREATE OR REPLACE TRIGGER "Insert_Role"
    BEFORE INSERT
    ON public."cl_Roles"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Role

CREATE OR REPLACE TRIGGER "Update_Role"
    BEFORE UPDATE 
    ON public."cl_Roles"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_RoleRelations

DROP TABLE IF EXISTS public."cl_RoleRelations";
CREATE TABLE public."cl_RoleRelations"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "CredentialID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Credentials" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
    "RoleID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Roles" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default",
    CONSTRAINT "UQ_RoleRelation" UNIQUE ("CredentialID", "RoleID")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertRoleRelation(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertRoleRelation"(
	IN _credentialid character varying(50),
	IN _roleid character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_RoleRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _credentialid, _roleid, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'ROR');
END;
$BODY$;

-- PROCEDURE: public.p_DeleteRoleRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteRoleRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_RoleRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L', _id);
END;
$BODY$;

-- Trigger: Update_RoleRelation

CREATE OR REPLACE TRIGGER "Update_RoleRelation"
	BEFORE UPDATE
	ON public."cl_RoleRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_RoleRelation

CREATE OR REPLACE TRIGGER "Insert_RoleRelation"
    BEFORE INSERT
    ON public."cl_RoleRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Remove_RoleRelation

CREATE OR REPLACE TRIGGER "Remove_RoleRelation"
    BEFORE DELETE
    ON public."cl_RoleRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_RemoveTrigger"();

-- Insert References

CALL public."p_InsertReferenceTable"('cl_Permissions');
CALL public."p_InsertReferenceTable"('cl_Roles');
CALL public."p_InsertReferenceTable"('cl_RoleRelations');