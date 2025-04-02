-- Check IsProc status
select public."f_IsProc"();

-- Check CurrentThread status
select public."f_CurrentThread"();

-- Get a random value
select * from public."v_Rand"

-- Get random generated password
select public."f_PwdGenerator"();

-- Get activation date
select public."f_Activation"('2024-01-01'); -- EAAJFGEAAIB
select public."f_Activation"('2025-10-01'); -- EABBHCDCJIB
select public."f_Activation"('2025-12-31'); -- EABBHGIIGBB
select public."f_ActiveUser"(4, 2024); -- AAJFEE4HJCE

-- Check if parameter is locked
select public."f_Locked"(1); -- FALSE (IsProc)
select public."f_Locked"(2); -- TRUE (Serial)

-- Check if parameter is auditable
select public."f_Auditable"(1); -- FALSE (IsProc)
select public."f_Auditable"(3); -- TRUE (Activation)

-- Set IsProc to true
call public."p_IsProc"(true);
select public."f_CurrentThread"();
select public."f_IsProc"();

-- Set IsProc to false
call public."p_IsProc"(false);
select public."f_CurrentThread"();
select public."f_IsProc"();

-- Set CurrentThread to opposite status
call public."p_CurrentThread"(not public."f_CurrentThread"());
select public."f_CurrentThread"();

-- Check CodeLength
select public."f_CheckCodeLength"(4);
select public."f_CheckCodeLength"(5);
select public."f_CheckCodeLength"(2);

-- Check Parameter Relations
select public."f_CheckParameterRelation"('TestName1');

-- Insert new parameters
insert into public."cl_Parameters" ("ParamName", "ParamUValue", "ParamValue", "OwnerApp", "ParamLock", "Auditable") values
(digest('TestName1', 'sha256'), null, digest('TestValue1', 'sha256'), 'TestApp', false, false),
(digest('TestName2', 'sha256'), 'TestValue2', null, 'TestApp', true, false),
(digest('TestName3', 'sha256'), 'TestValue3', null, 'TestApp', true, true),
(digest('TestName4', 'sha256'), null, digest('TestValue4', 'sha256'), 'TestApp', false, true);
--
call public."p_InsertParameter"('TestName1', 'TestValue1', true, 'TestApp', false, false);
call public."p_InsertParameter"('TestName2', 'TestValue2', false, 'TestApp', true, false);
call public."p_InsertParameter"('TestName3', 'TestValue3', false, 'TestApp', true, true);
call public."p_InsertParameter"('TestName4', 'TestValue4', true, 'TestApp', false, true);
--
call public."p_InsertParameterRelation"('TestName1', 'Administration');
call public."p_InsertParameterRelation"('TestName1', 'TestApp');
call public."p_InsertParameterRelation"('TestName2', 'Administration');
call public."p_InsertParameterRelation"('TestName2', 'TestApp');
call public."p_InsertParameterRelation"('TestName3', 'Administration');
call public."p_InsertParameterRelation"('TestName3', 'TestApp');
call public."p_InsertParameterRelation"('TestName4', 'Administration');
call public."p_InsertParameterRelation"('TestName4', 'TestApp');

-- Update parameters
call public."p_UpdateParameter"('TestName1', 'UpdatedTestValue1', false);
call public."p_UpdateParameter"('TestName2', 'UpdatedTestValue2', true);
call public."p_UpdateParameter"('TestName3', 'UpdatedTestValue3', false);
call public."p_UpdateParameter"('TestName4', 'UpdatedTestValue4', true);
select * from public."cl_Parameters" where "ParamName" = digest('TestName4', 'sha256') and "ParamValue" = digest('UpdatedTestValue4', 'sha256');

-- Check Activation
select public."f_CheckActivation"('{60E9AA19-8DDD-41B6-86DB-2D4CA1E2CB32}', '2025-03-04');
select public."f_CheckActivation"('{60E9AA19-8DDD-41B6-86DB-2D4CA1E2CB32}', '2025-11-04');
select public."f_CheckActivation"('{60E9AA19-8DDD-41B6-86DB-2D4CA1E2CB32}', '2026-03-04');
--
call public."p_Activation"(false);
call public."p_Activation"(true);

-- Check Period
select public."f_CheckPeriod"('2025-03-04');
select public."f_CheckPeriod"('2025-11-04');
select public."f_CheckPeriod"('2026-03-04');

-- Read app version
select public."f_Readme"();

-- Check Parameters table index
select pg_get_serial_sequence('"cl_Parameters"', 'ID');
select last_value from public."cl_Parameters_ID_seq";
select setval('"cl_Parameters_ID_seq"', 11);
select currval('"cl_Parameters_ID_seq"');
--
select pg_get_serial_sequence('"cl_ParameterRelations"', 'ID');
select last_value from public."cl_ParameterRelations_ID_seq";
select setval('"cl_ParameterRelations_ID_seq"', 11);
select currval('"cl_ParameterRelations_ID_seq"');

-- Insert with audit log
call public."p_InsertParameter"('TestName1', 'TestValue1', true, 'TestApp', false, false);
call public."p_InsertParameter"('TestName2', 'TestValue2', false, 'TestApp', true, false);
call public."p_InsertParameter"('TestName3', 'TestValue3', false, 'TestApp', true, true);
call public."p_InsertParameter"('TestName4', 'TestValue4', true, 'TestApp', false, true);
--
call public."p_InsertParameterRelation"('TestName1', 'Administration');
call public."p_InsertParameterRelation"('TestName1', 'TestApp');
call public."p_InsertParameterRelation"('TestName2', 'Administration');
call public."p_InsertParameterRelation"('TestName2', 'TestApp');
call public."p_InsertParameterRelation"('TestName3', 'Administration');
call public."p_InsertParameterRelation"('TestName3', 'TestApp');
call public."p_InsertParameterRelation"('TestName4', 'Administration');
call public."p_InsertParameterRelation"('TestName4', 'TestApp');

-- Insert languages
CALL public."p_InsertLanguage"('US', 'English (US)');
CALL public."p_InsertLanguage"('GB', 'English (GB)');
CALL public."p_InsertLanguage"('FR', 'Français');
CALL public."p_InsertLanguage"('ES', 'Español');
CALL public."p_InsertLanguage"('AR', 'عربي');

-- Update languages
CALL public."p_UpdateLanguage"('4684220005', 'IT', 'Italian');

-- Delete languages
CALL public."p_DeleteLanguage"('4684220005');
CALL public."p_UpdateLanguage"('4684220005', 'AR', 'عربي');

-- Insert language relations
DO
$BODY$
DECLARE _id character varying(50); _us character varying(50); _gb character varying(50); _fr character varying(50); _es character varying(50); _ar character varying(50);

BEGIN
	SELECT "ID" INTO _us FROM public."cl_Languages" WHERE "Label" = 'US';
	SELECT "ID" INTO _gb FROM public."cl_Languages" WHERE "Label" = 'GB';
	SELECT "ID" INTO _fr FROM public."cl_Languages" WHERE "Label" = 'FR';
	SELECT "ID" INTO _es FROM public."cl_Languages" WHERE "Label" = 'ES';
	SELECT "ID" INTO _ar FROM public."cl_Languages" WHERE "Label" = 'AR';
	-- Languages
	SELECT "ID" INTO _id FROM public."cl_Languages" WHERE "Label" = 'US';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'en-US');
	/*SELECT "ID" INTO _id FROM public."cl_Languages" WHERE "Label" = 'GB';
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'en-GB');
	SELECT "ID" INTO _id FROM public."cl_Languages" WHERE "Label" = 'FR';
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'fr-FR');
	SELECT "ID" INTO _id FROM public."cl_Languages" WHERE "Label" = 'ES';
	CALL public."p_InsertLanguageRelation"(_es, _id, 'es-ES');
	SELECT "ID" INTO _id FROM public."cl_Languages" WHERE "Label" = 'AR';
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'ar-SA');
	-- Continents
	SELECT "ID" INTO _id FROM public."cl_Continents" WHERE "Name" = 'Africa';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Africa');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Africa');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Afrique');
	SELECT "ID" INTO _id FROM public."cl_Continents" WHERE "Name" = 'America';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'America');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'America');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Amérique');
	SELECT "ID" INTO _id FROM public."cl_Continents" WHERE "Name" = 'Asia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Asia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Asia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Asie');
	SELECT "ID" INTO _id FROM public."cl_Continents" WHERE "Name" = 'Europe';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Europe');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Europe');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Europe');
	SELECT "ID" INTO _id FROM public."cl_Continents" WHERE "Name" = 'Oceania';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Oceania');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Oceania');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Oceanie');*/
END $BODY$;