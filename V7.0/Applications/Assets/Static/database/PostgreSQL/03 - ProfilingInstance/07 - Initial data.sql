/* Profiling app */

/* Initial data */

DO
$BODY$
DECLARE _profileid character varying(50); _langid character varying(50); _id character varying(50); _pwd character varying(50);
BEGIN

	-- App registry
	
	CALL public."p_InsertApp"('Profiling');
	
	-- Insert References
	
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Profiling';
	--
	CALL public."p_InsertReferenceTable"('cl_Profiles');
	SELECT "ID" INTO _profileid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Profiles';
	CALL public."p_InsertReferenceRelation"(_profileid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_Customers');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Customers';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_Suppliers');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Suppliers';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_Employees');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Employees';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_Civilities');
	SELECT "ID" INTO _profileid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Civilities';
	CALL public."p_InsertReferenceRelation"(_profileid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_Genders');
	SELECT "ID" INTO _profileid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Genders';
	CALL public."p_InsertReferenceRelation"(_profileid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_Statuses');
	SELECT "ID" INTO _profileid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Statuses';
	CALL public."p_InsertReferenceRelation"(_profileid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_Occupations');
	SELECT "ID" INTO _profileid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Occupations';
	CALL public."p_InsertReferenceRelation"(_profileid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_Titles');
	SELECT "ID" INTO _profileid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Titles';
	CALL public."p_InsertReferenceRelation"(_profileid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_ContactTypes');
	SELECT "ID" INTO _profileid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_ContactTypes';
	CALL public."p_InsertReferenceRelation"(_profileid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_Contacts');
	SELECT "ID" INTO _profileid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Contacts';
	CALL public."p_InsertReferenceRelation"(_profileid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_Credentials');
	SELECT "ID" INTO _profileid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Credentials';
	CALL public."p_InsertReferenceRelation"(_profileid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_Permissions');
	SELECT "ID" INTO _profileid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Permissions';
	CALL public."p_InsertReferenceRelation"(_profileid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_Roles');
	SELECT "ID" INTO _profileid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Roles';
	CALL public."p_InsertReferenceRelation"(_profileid, _id);
	
	-- Insert Profiles
	
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "ISO3" = 'CMR';
	SELECT "ID" INTO _profileid FROM public."cl_Cities" WHERE "Name" = 'DLA';
	CALL public."p_InsertProfile"('Jéoline', LOCALTIMESTAMP, _id, _profileid);
	CALL public."p_InsertProfile"('Unknown', LOCALTIMESTAMP, _id, _profileid);
	CALL public."p_InsertProfile"('Administrator', LOCALTIMESTAMP, _id, _profileid);
	CALL public."p_InsertProfile"('Anonymous F', LOCALTIMESTAMP, _id, _profileid);
	CALL public."p_InsertProfile"('Anonymous M', LOCALTIMESTAMP, _id, _profileid);
	CALL public."p_InsertProfile"('Anonymous', LOCALTIMESTAMP, _id, _profileid);
	--
	SELECT "ID" INTO _id FROM public."cl_Profiles" WHERE "LastName" = 'Unknown';
	CALL public."p_DeleteProfile"(_id);
	
	-- Insert titles
	
	CALL public."p_InsertTitle"('Non applicable');
	
	-- Insert TitleRelations
	
	SELECT "ID" INTO _id FROM public."cl_Titles" WHERE "Code" = 1;
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Jéoline';
	CALL public."p_InsertTitleRelation"(_id, _profileid);
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Administrator';
	CALL public."p_InsertTitleRelation"(_id, _profileid);
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous F';
	CALL public."p_InsertTitleRelation"(_id, _profileid);
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous M';
	CALL public."p_InsertTitleRelation"(_id, _profileid);
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous';
	CALL public."p_InsertTitleRelation"(_id, _profileid);
	
	-- Insert Statuses
	
	CALL public."p_InsertStatus"('Non applicable');
	CALL public."p_InsertStatus"('Single');
	CALL public."p_InsertStatus"('Married');
	CALL public."p_InsertStatus"('Divorced');
	CALL public."p_InsertStatus"('Widow');
	
	-- Insert StatusRelations
	
	SELECT "ID" INTO _id FROM public."cl_Statuses" WHERE "Code" = 1;
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Jéoline';
	CALL public."p_InsertStatusRelation"(_id, _profileid);
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Administrator';
	CALL public."p_InsertStatusRelation"(_id, _profileid);
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous F';
	CALL public."p_InsertStatusRelation"(_id, _profileid);
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous M';
	CALL public."p_InsertStatusRelation"(_id, _profileid);
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous';
	CALL public."p_InsertStatusRelation"(_id, _profileid);
	
	-- Insert Genders
	
	CALL public."p_InsertGender"('Non applicable');
	CALL public."p_InsertGender"('Male');
	CALL public."p_InsertGender"('Female');
	
	-- Insert GenderRelations
	
	SELECT "ID" INTO _id FROM public."cl_Genders" WHERE "Code" = 1;
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Jéoline';
	CALL public."p_InsertGenderRelation"(_id, _profileid);
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Administrator';
	CALL public."p_InsertGenderRelation"(_id, _profileid);
	SELECT "ID" INTO _id FROM public."cl_Genders" WHERE "Code" = 3;
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous F';
	CALL public."p_InsertGenderRelation"(_id, _profileid);
	SELECT "ID" INTO _id FROM public."cl_Genders" WHERE "Code" = 2;
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous M';
	CALL public."p_InsertGenderRelation"(_id, _profileid);
	SELECT "ID" INTO _id FROM public."cl_Genders" WHERE "Code" = 1;
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous';
	CALL public."p_InsertGenderRelation"(_id, _profileid);
	
	-- Insert Civilities
	
	CALL public."p_InsertCivility"('Non applicable');
	CALL public."p_InsertCivility"('Mister');
	CALL public."p_InsertCivility"('Madam');
	CALL public."p_InsertCivility"('Miss');
	
	-- Insert CivilityRelations
	
	SELECT "ID" INTO _id FROM public."cl_Civilities" WHERE "Code" = 1;
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Jéoline';
	CALL public."p_InsertCivilityRelation"(_id, _profileid);
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Administrator';
	CALL public."p_InsertCivilityRelation"(_id, _profileid);
	SELECT "ID" INTO _id FROM public."cl_Civilities" WHERE "Code" = 3;
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous F';
	CALL public."p_InsertCivilityRelation"(_id, _profileid);
	SELECT "ID" INTO _id FROM public."cl_Civilities" WHERE "Code" = 2;
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous M';
	CALL public."p_InsertCivilityRelation"(_id, _profileid);
	SELECT "ID" INTO _id FROM public."cl_Civilities" WHERE "Code" = 1;
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous';
	CALL public."p_InsertCivilityRelation"(_id, _profileid);
	
	-- Insert Occupations
	
	CALL public."p_InsertOccupation"('Non applicable');
	
	-- Insert OccupationRelations
	
	SELECT "ID" INTO _id FROM public."cl_Occupations" WHERE "Code" = 1;
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Jéoline';
	CALL public."p_InsertOccupationRelation"(_id, _profileid);
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Administrator';
	CALL public."p_InsertOccupationRelation"(_id, _profileid);
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous F';
	CALL public."p_InsertOccupationRelation"(_id, _profileid);
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous M';
	CALL public."p_InsertOccupationRelation"(_id, _profileid);
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous';
	CALL public."p_InsertOccupationRelation"(_id, _profileid);

	-- Insert Customers

	SELECT "ID" INTO _id FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous';
	CALL public."p_InsertCustomer"(_id);
	SELECT "ID" INTO _id FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous M';
	CALL public."p_InsertCustomer"(_id);
	SELECT "ID" INTO _id FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous F';
	CALL public."p_InsertCustomer"(_id);

	-- Insert Suppliers

	SELECT "ID" INTO _id FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous';
	CALL public."p_InsertSupplier"(_id);
	SELECT "ID" INTO _id FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous M';
	CALL public."p_InsertSupplier"(_id);
	SELECT "ID" INTO _id FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous F';
	CALL public."p_InsertSupplier"(_id);
	
	-- Insert ContactTypes
	
	CALL public."p_InsertContactType"('Phone');
	CALL public."p_InsertContactType"('Email');
	CALL public."p_InsertContactType"('Address');
	CALL public."p_InsertContactType"('WhatsApp');
	CALL public."p_InsertContactType"('Telegram');
	CALL public."p_InsertContactType"('YouTube');
	CALL public."p_InsertContactType"('Instagram');
	CALL public."p_InsertContactType"('Twitter');
	CALL public."p_InsertContactType"('LinkedIn');
	
	-- Insert Contacts
	
	SELECT "ID" INTO _id FROM public."cl_ContactTypes" WHERE "Name" = 'Email';
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Jéoline';
	CALL public."p_InsertContact"(_id, _profileid, 'Jéoline email');
	SELECT "ID" INTO _id FROM public."cl_ContactTypes" WHERE "Name" = 'Phone';
	CALL public."p_InsertContact"(_id, _profileid, 'Jéoline phone');
	SELECT "ID" INTO _id FROM public."cl_ContactTypes" WHERE "Name" = 'Address';
	CALL public."p_InsertContact"(_id, _profileid, 'Jéoline location');
	
	-- Insert ContactRelations
	
	SELECT "ID" INTO _langid FROM public."cl_Languages" WHERE "Label" = 'US';
	SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Jéoline email';
	CALL public."p_InsertContactRelation"(_langid, _id, 'infos@jeolinecorporates.com', 'Email_round_black');
	SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Jéoline phone';
	CALL public."p_InsertContactRelation"(_langid, _id, '+237675507158', 'Phone_round_black');
	SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Jéoline location';
	CALL public."p_InsertContactRelation"(_langid, _id, '264 de la Motte-Picquet Street, Bonanjo', 'Location_round_black');
	
	--
	SELECT "ID" INTO _langid FROM public."cl_Languages" WHERE "Label" = 'GB';
	SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Jéoline email';
	CALL public."p_InsertContactRelation"(_langid, _id, 'infos@jeolinecorporates.com', 'Email_round_black');
	SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Jéoline phone';
	CALL public."p_InsertContactRelation"(_langid, _id, '+237675507158', 'Phone_round_black');
	SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Jéoline location';
	CALL public."p_InsertContactRelation"(_langid, _id, '264 de la Motte-Picquet Street, Bonanjo', 'Location_round_black');
	
	--
	SELECT "ID" INTO _langid FROM public."cl_Languages" WHERE "Label" = 'FR';
	SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Jéoline email';
	CALL public."p_InsertContactRelation"(_langid, _id, 'infos@jeolinecorporates.com', 'Email_round_black');
	SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Jéoline phone';
	CALL public."p_InsertContactRelation"(_langid, _id, '+237675507158', 'Phone_round_black');
	SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Jéoline location';
	CALL public."p_InsertContactRelation"(_langid, _id, '264 rue de la Motte-Picquet, Bonanjo', 'Location_round_black');
	
	-- Insert Credentials
	
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Jéoline';
	CALL public."p_InsertCredential"(_pwd, _profileid, 'infos@jeolinecorporates.com');
	SELECT "ID" INTO _id FROM public."cl_Credentials" WHERE "ProfileID" = _profileid;
	CALL public."p_UpdatePassword"(_id, _pwd, 'pat1380/*56');
	--
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Unknown';
	CALL public."p_InsertCredential"(_pwd, _profileid, 'unkown@unkown.com');
	SELECT "ID" INTO _id FROM public."cl_Credentials" WHERE "ProfileID" = _profileid;
	CALL public."p_DeleteCredential"(_id);
	--
	SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Administrator';
	CALL public."p_InsertCredential"(_pwd, _profileid, 'admin@cashledger.com');
	SELECT "ID" INTO _id FROM public."cl_Credentials" WHERE "ProfileID" = _profileid;
	CALL public."p_UpdatePassword"(_id, _pwd, 'admin1234');
	
	-- Insert Permissions
	
	CALL public."p_InsertPermission"('All', 'A', 'Full resource permission');
	CALL public."p_InsertPermission"('None', 'N', 'No resource permission');
	CALL public."p_InsertPermission"('Create', 'C', 'Create data permission');
	CALL public."p_InsertPermission"('Read', 'R', 'Read data permission');
	CALL public."p_InsertPermission"('Update', 'U', 'Update data permission');
	CALL public."p_InsertPermission"('Delete', 'D', 'Delete data permission');
	CALL public."p_InsertPermission"('Execute', 'X', 'Execute app permission');
	
	-- Insert Roles
	
	CALL public."p_InsertRole"('Administrator');
	
	-- Insert RoleRelations
	
	SELECT "ID" INTO _profileid FROM public."cl_Credentials" WHERE "UserName" = 'infos@jeolinecorporates.com';
	SELECT "ID" INTO _id FROM public."cl_Roles" WHERE "Name" = 'Administrator';
	CALL public."p_InsertRoleRelation"(_profileid, _id);
	SELECT "ID" INTO _profileid FROM public."cl_Credentials" WHERE "UserName" = 'admin@cashledger.com';
	CALL public."p_InsertRoleRelation"(_profileid, _id);
	
	-- Insert Permissions tokens
	
	SELECT "ID" INTO _id FROM public."cl_Roles" WHERE "Name" = 'Administrator';
	CALL public."p_InsertRole_Administrator"(_id, 'All', 'All', 'A', 'All');

END $BODY$;

-- Insert LanguageRelations

DO
$BODY$
DECLARE _id character varying(50); _appid character varying(50); _us character varying(50); _gb character varying(50); _fr character varying(50); _es character varying(50); _ar character varying(50);

BEGIN
	SELECT "ID" INTO _us FROM public."cl_Languages" WHERE "Label" = 'US';
	SELECT "ID" INTO _gb FROM public."cl_Languages" WHERE "Label" = 'GB';
	SELECT "ID" INTO _fr FROM public."cl_Languages" WHERE "Label" = 'FR';
	SELECT "ID" INTO _es FROM public."cl_Languages" WHERE "Label" = 'ES';
	SELECT "ID" INTO _ar FROM public."cl_Languages" WHERE "Label" = 'AR';

	-- App

	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Profiling';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Profiling');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Profiling');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Gestion des utilisateurs');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Perfilado');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'التحليل الشخصي');
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'User management') THEN
		CALL public."p_InsertAppCategory"('User management');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'User management';
		CALL public."p_InsertLanguageRelation"(_us, _appid, 'User management');
		CALL public."p_InsertLanguageRelation"(_gb, _appid, 'User management');
		CALL public."p_InsertLanguageRelation"(_fr, _appid, 'Gestion des utilisateurs');
		CALL public."p_InsertLanguageRelation"(_es, _appid, 'Gestión de usuarios');
		CALL public."p_InsertLanguageRelation"(_ar, _appid, 'إدارة المستخدمين');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Profiling';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'User management';
	CALL public."p_InsertAppRelation"(_id, _appid);
	
	-- Titles
	
	SELECT "ID" INTO _id FROM public."cl_Titles" WHERE "Name" = 'Non applicable';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'No aplica');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'غير مطبق');
	--
	SELECT "ID" INTO _id FROM public."cl_Titles" WHERE "Name" = 'Doctor';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Docteur');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Doctor');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Doctor');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Doctor');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'دكتور');
	--
	SELECT "ID" INTO _id FROM public."cl_Titles" WHERE "Name" = 'Professor';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Professeur');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Professor');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Professor');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Profesor');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'أستاذ');
	
	-- Statuses
	
	SELECT "ID" INTO _id FROM public."cl_Statuses" WHERE "Name" = 'Non applicable';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'No aplica');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'غير مطبق');
	SELECT "ID" INTO _id FROM public."cl_Statuses" WHERE "Name" = 'Single';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Single');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Single');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Célibataire');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Soltero/a');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'أعزب/عزباء');
	SELECT "ID" INTO _id FROM public."cl_Statuses" WHERE "Name" = 'Married';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Married');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Married');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Marié(e)');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Casado/a');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'متزوج/متزوجة');
	SELECT "ID" INTO _id FROM public."cl_Statuses" WHERE "Name" = 'Divorced';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Divorced');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Divorced');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Divorcé(e)');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Divorciado/a');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'مطلق/مطلقة');
	SELECT "ID" INTO _id FROM public."cl_Statuses" WHERE "Name" = 'Widow';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Widow');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Widow');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Veuf(ve)');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Viudo/a');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'أرمل/أرملة');
	
	-- Genders
	
	SELECT "ID" INTO _id FROM public."cl_Genders" WHERE "Name" = 'Non applicable';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'No aplica');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'غير مطبق');
	SELECT "ID" INTO _id FROM public."cl_Genders" WHERE "Name" = 'Male';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Male');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Male');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Homme');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Masculino');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'ذكر');
	SELECT "ID" INTO _id FROM public."cl_Genders" WHERE "Name" = 'Female';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Female');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Female');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Femme');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Femenino');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'أنثى');
	
	-- Civilities
	
	SELECT "ID" INTO _id FROM public."cl_Civilities" WHERE "Name" = 'Non applicable';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'No aplica');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'غير مطبق');
	SELECT "ID" INTO _id FROM public."cl_Civilities" WHERE "Name" = 'Mister';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Mister');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Mister');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Monsieur');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Señor');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'السيد');
	SELECT "ID" INTO _id FROM public."cl_Civilities" WHERE "Name" = 'Madam';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Madam');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Madam');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Madame');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Señora');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'السيدة');
	SELECT "ID" INTO _id FROM public."cl_Civilities" WHERE "Name" = 'Miss';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Miss');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Miss');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Mademoiselle');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Señorita');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'الآنسة');
	
	-- Occupations
	
	SELECT "ID" INTO _id FROM public."cl_Occupations" WHERE "Name" = 'Non applicable';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'No aplica');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'غير مطبق');
	--
	SELECT "ID" INTO _id FROM public."cl_Occupations" WHERE "Name" = 'Gynecologist';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'No aplica');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'غير مطبق');
	--
	SELECT "ID" INTO _id FROM public."cl_Occupations" WHERE "Name" = 'Obstetrician';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Obstetrician');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Obstetrician');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Obstétricien');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Obstetra');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'طبيب توليد');
	--
	SELECT "ID" INTO _id FROM public."cl_Occupations" WHERE "Name" = 'Nurse';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Nurse');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Nurse');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Infirmier');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Enfermero/a');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'ممرض/ممرضة');
	--
	SELECT "ID" INTO _id FROM public."cl_Occupations" WHERE "Name" = 'Lab technician';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Lab technician');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Lab technician');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Laborantin');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Técnico de laboratorio');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'فني مختبر');
	--
	SELECT "ID" INTO _id FROM public."cl_Occupations" WHERE "Name" = 'Anesthetist';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Anesthetist');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Anesthetist');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'anésthésiste');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Anestesista');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'طبيب تخدير');
	
	-- ContactTypes
	
	SELECT "ID" INTO _id FROM public."cl_ContactTypes" WHERE "Name" = 'Phone';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Phone');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Phone');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Téléphone');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Teléfono');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'هاتف');
	SELECT "ID" INTO _id FROM public."cl_ContactTypes" WHERE "Name" = 'Email';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Email');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Email');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Email');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Correo electrónico');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'البريد الإلكتروني');
	SELECT "ID" INTO _id FROM public."cl_ContactTypes" WHERE "Name" = 'Address';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Location');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Location');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Localisation');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Ubicación');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'الموقع');
	SELECT "ID" INTO _id FROM public."cl_ContactTypes" WHERE "Name" = 'WhatsApp';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'WhatsApp');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'WhatsApp');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'WhatsApp');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'WhatsApp');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'واتساب');
	SELECT "ID" INTO _id FROM public."cl_ContactTypes" WHERE "Name" = 'Telegram';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Telegram');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Telegram');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Telegram');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Telegram');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'تيليجرام');
	SELECT "ID" INTO _id FROM public."cl_ContactTypes" WHERE "Name" = 'YouTube';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'YouTube');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'YouTube');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'YouTube');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'YouTube');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'يوتيوب');
	SELECT "ID" INTO _id FROM public."cl_ContactTypes" WHERE "Name" = 'Instagram';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Instagram');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Instagram');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Instagram');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Instagram');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'إنستغرام');
	SELECT "ID" INTO _id FROM public."cl_ContactTypes" WHERE "Name" = 'Twitter';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Twitter');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Twitter');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Twitter');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Twitter');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'تويتر');
	SELECT "ID" INTO _id FROM public."cl_ContactTypes" WHERE "Name" = 'LinkedIn';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'LinkedIn');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'LinkedIn');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'LinkedIn');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'LinkedIn');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'لينكدإن');
	
	-- Roles
	
	SELECT "ID" INTO _id FROM public."cl_Roles" WHERE "Name" = 'Administrator';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Administrator');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Administrator');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Administrateur');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Administrador');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'مدير النظام');
	
END $BODY$;

-- Trigger: Credential_Log

CREATE OR REPLACE TRIGGER "Credential_Log"
	AFTER UPDATE
	ON public."cl_Credentials"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_CreditLog"();

-- Trigger: Log_Credential

CREATE OR REPLACE TRIGGER "Log_Credential"
	AFTER INSERT OR UPDATE OR DELETE
	ON public."cl_Credentials"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_LogCredential"();

-- Trigger: Log_Audit
DO
$BODY$
DECLARE _tablename text; _triggername text;
	_tablenames text[] := ARRAY['cl_AppCategories', 'cl_AppRelations', 'cl_Apps', 'cl_Cities', 'cl_Civilities', 'cl_CivilityRelations', 'cl_ContactRelations', 'cl_Contacts', 'cl_ContactTypes', 'cl_Continents',
	'cl_Countries', 'cl_Customers', 'cl_Employees', 'cl_GenderRelations', 'cl_Genders', 'cl_LanguageRelations', 'cl_Languages', 'cl_OccupationRelations', 'cl_Occupations', 'cl_ParameterRelations', 'cl_Parameters',
	'cl_Permissions', 'cl_Profiles', 'cl_ReferenceRelations', 'cl_ReferenceTables', 'cl_RoleRelations', 'cl_Roles', 'cl_Statuses', 'cl_StatusRelations', 'cl_Suppliers', 'cl_TitleRelations', 'cl_Titles'];
BEGIN
	FOR _tablename IN SELECT tablename FROM pg_tables WHERE schemaname = 'public'
	LOOP
		IF _tablename != ALL(_tablenames) THEN CONTINUE; END IF;
		_triggername :=
			CASE
				WHEN _tablename ~ 'ies$' THEN REGEXP_REPLACE(SUBSTRING(_tablename FROM 4 FOR LENGTH(_tablename) - 3), 'ies$', 'y')
				WHEN _tablename = 'cl_Statuses' THEN 'Status'
				ELSE REGEXP_REPLACE(SUBSTRING(_tablename FROM 4 FOR LENGTH(_tablename) - 1), 's$', '')
			END;
		_triggername := 'Log_' || _triggername;
		IF _triggername NOT IN (SELECT tgname FROM pg_trigger WHERE tgname ~ 'Log_') THEN
			EXECUTE FORMAT('
				CREATE OR REPLACE TRIGGER %I
					AFTER INSERT OR UPDATE OR DELETE
					ON public.%I
					FOR EACH ROW
					EXECUTE FUNCTION public."t_LogAudit"();
			', _triggername, _tablename);
		END IF;
		--
		_triggername :=
			CASE
				WHEN _tablename ~ 'ies$' THEN REGEXP_REPLACE(SUBSTRING(_tablename FROM 4 FOR LENGTH(_tablename) - 3), 'ies$', 'y')
				WHEN _tablename = 'cl_Statuses' THEN 'Status'
				ELSE REGEXP_REPLACE(SUBSTRING(_tablename FROM 4 FOR LENGTH(_tablename) - 1), 's$', '')
			END;
			_triggername := 'Release_' || _triggername;
		EXECUTE FORMAT('
			CREATE OR REPLACE TRIGGER %I
				AFTER INSERT OR UPDATE OR DELETE
				ON public.%I
				FOR EACH ROW
				EXECUTE FUNCTION public."t_ReleaseThread"();
		', _triggername, _tablename);
	END LOOP;
END $BODY$;