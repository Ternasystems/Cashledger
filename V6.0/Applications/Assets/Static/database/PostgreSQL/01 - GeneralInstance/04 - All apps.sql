-- Initial data

DO
$BODY$
DECLARE _id character varying(50); _appid character varying(50); _us character varying(50); _gb character varying(50); _fr character varying(50);
BEGIN

	SELECT "ID" INTO _us FROM public."cl_Languages" WHERE "Label" = 'US';
	SELECT "ID" INTO _gb FROM public."cl_Languages" WHERE "Label" = 'GB';
	SELECT "ID" INTO _fr FROM public."cl_Languages" WHERE "Label" = 'FR';
	
	-- App registry
	
	CALL public."p_InsertApp"('Accounting');
	
	-- App
	
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Accounting';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Accounting');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Accounting');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Comptabilité');
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Accounting') THEN
		CALL public."p_InsertAppCategory"('Accounting');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Accounting';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Accounting');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Accounting');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Comptabilité');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Accounting';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Accounting';
	CALL public."p_InsertAppRelation"(_id, _appid);

	-- App registry
	
	CALL public."p_InsertApp"('Billing');

	-- App
	
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Billing';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Billing');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Billing');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Tarification');
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Sales') THEN
		CALL public."p_InsertAppCategory"('Sales');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Sales';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Sales');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Sales');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Ventes');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Billing';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Sales';
	CALL public."p_InsertAppRelation"(_id, _appid);
	
	-- App registry
	
	CALL public."p_InsertApp"('Booking');

	-- App
	
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Booking';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Booking');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Booking');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Réservations');
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Booking') THEN
		CALL public."p_InsertAppCategory"('Booking');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Booking';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Booking');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Booking');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Réservations');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Booking';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Booking';
	CALL public."p_InsertAppRelation"(_id, _appid);
	
	-- App registry
	
	CALL public."p_InsertApp"('Control');

	-- App
	
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Control';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Control');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Control');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Contrôle');
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Audits & Controls') THEN
		CALL public."p_InsertAppCategory"('Audits & Controls');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Audits & Controls';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Audits & Controls');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Audits & Controls');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Audits et Contrôles');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Control';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Audits & Controls';
	CALL public."p_InsertAppRelation"(_id, _appid);
	
	-- App registry
	
	CALL public."p_InsertApp"('Dashboarding');

	-- App
	
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Dashboarding';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Dashboarding');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Dashboarding');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Tableaux de bord');
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Decision making') THEN
		CALL public."p_InsertAppCategory"('Decision making');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Decision making';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Decision making');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Decision making');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Prise de décisions');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Reporting') THEN
		CALL public."p_InsertAppCategory"('Reporting');
		SELECT "ID" INTO _id from public."cl_AppCategories" WHERE "Name" = 'Reporting';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Reporting');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Reporting');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Reporting');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Dashboarding';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Decision making';
	CALL public."p_InsertAppRelation"(_id, _appid);
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Reporting';
	CALL public."p_InsertAppRelation"(_id, _appid);
	
	-- App registry
	
	CALL public."p_InsertApp"('Emailing');

	-- App
	
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Emailing';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Emailing');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Emailing');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Courriel');
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Communication') THEN
		CALL public."p_InsertAppCategory"('Communication');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Communication';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Communication');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Communication');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Communication');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Emailing';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Communication';
	CALL public."p_InsertAppRelation"(_id, _appid);
	
	-- App registry
	
	CALL public."p_InsertApp"('Forecasting');

	-- App
	
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Forecasting';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Forecasting');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Forecasting');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Prévisions');
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Decision making') THEN
		CALL public."p_InsertAppCategory"('Decision making');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Decision making';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Decision making');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Decision making');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Prise de décisions');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Analysis') THEN
		CALL public."p_InsertAppCategory"('Analysis');
		SELECT "ID" INTO _id from public."cl_AppCategories" WHERE "Name" = 'Analysis';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Analysis');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Analysis');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Analyses');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Forecasting';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Decision making';
	CALL public."p_InsertAppRelation"(_id, _appid);
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Analysis';
	CALL public."p_InsertAppRelation"(_id, _appid);
	
	-- App registry
	
	CALL public."p_InsertApp"('Hrm');

	-- App
	
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Hrm';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Human resources management');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Human resources management');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Gestion des ressources humaines');
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'HR') THEN
		CALL public."p_InsertAppCategory"('HR');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'HR';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'HR');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'HR');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'RH');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Hrm';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'HR';
	CALL public."p_InsertAppRelation"(_id, _appid);
	
	-- App registry
	
	CALL public."p_InsertApp"('Hudel');

	-- App
	
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Hudel';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Hudel');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Hudel');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Hudel');
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Decision making') THEN
		CALL public."p_InsertAppCategory"('Decision making');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Decision making';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Decision making');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Decision making');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Prise de décisions');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Analysis') THEN
		CALL public."p_InsertAppCategory"('Analysis');
		SELECT "ID" INTO _id from public."cl_AppCategories" WHERE "Name" = 'Analysis';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Analysis');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Analysis');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Analyses');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'AI') THEN
		CALL public."p_InsertAppCategory"('AI');
		SELECT "ID" INTO _id from public."cl_AppCategories" WHERE "Name" = 'AI';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'AI');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'AI');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'IA');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Hudel';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Decision making';
	CALL public."p_InsertAppRelation"(_id, _appid);
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Analysis';
	CALL public."p_InsertAppRelation"(_id, _appid);
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'AI';
	CALL public."p_InsertAppRelation"(_id, _appid);
	
	-- App registry
	
	CALL public."p_InsertApp"('Ids');

	-- App
	
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Ids';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Integrated Data System');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Integrated Data System');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Système d''Intégration des Données');
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Storage') THEN
		CALL public."p_InsertAppCategory"('Storage');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Storage';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Storage');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Storage');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Stockage');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Analysis') THEN
		CALL public."p_InsertAppCategory"('Analysis');
		SELECT "ID" INTO _id from public."cl_AppCategories" WHERE "Name" = 'Analysis';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Analysis');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Analysis');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Analyses');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Ids';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Storage';
	CALL public."p_InsertAppRelation"(_id, _appid);
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Analysis';
	CALL public."p_InsertAppRelation"(_id, _appid);
	
	-- App registry
	
	CALL public."p_InsertApp"('Invoicing');

	-- App
	
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Invoicing';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Invoicing');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Invoicing');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Facturation');
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Sales') THEN
		CALL public."p_InsertAppCategory"('Sales');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Sales';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Sales');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Sales');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Ventes');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Invoicing';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Sales';
	CALL public."p_InsertAppRelation"(_id, _appid);
	
	-- App registry
	
	CALL public."p_InsertApp"('Meeting');

	-- App
	
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Meeting';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Meeting');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Meeting');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Conférences');
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Communication') THEN
		CALL public."p_InsertAppCategory"('Communication');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Communication';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Communication');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Communication');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Communication');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Meeting';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Communication';
	CALL public."p_InsertAppRelation"(_id, _appid);
	
	-- App registry
	
	CALL public."p_InsertApp"('Messaging');

	-- App
	
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Messaging';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Messaging');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Messaging');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Messagerie');
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Communication') THEN
		CALL public."p_InsertAppCategory"('Communication');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Communication';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Communication');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Communication');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Communication');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Messaging';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Communication';
	CALL public."p_InsertAppRelation"(_id, _appid);
	
	-- App registry
	
	CALL public."p_InsertApp"('Partnership');

	-- App
	
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Partnership';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Partnership');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Partnership');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Partenariat');
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'HR') THEN
		CALL public."p_InsertAppCategory"('HR');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'HR';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'HR');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'HR');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'RH');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Strategy') THEN
		CALL public."p_InsertAppCategory"('Strategy');
		SELECT "ID" INTO _id from public."cl_AppCategories" WHERE "Name" = 'Strategy';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Strategy');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Strategy');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Stratégie');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Partnership';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'HR';
	CALL public."p_InsertAppRelation"(_id, _appid);
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Strategy';
	CALL public."p_InsertAppRelation"(_id, _appid);
	
	-- App registry
	
	CALL public."p_InsertApp"('Payments');

	-- App
	
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Payments';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Payments');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Payments');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Moyens de paiements');
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Finance') THEN
		CALL public."p_InsertAppCategory"('Finance');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Finance';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Finance');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Finance');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Finance');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Payments';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Finance';
	CALL public."p_InsertAppRelation"(_id, _appid);
	
	-- App registry
	
	CALL public."p_InsertApp"('Payroll');

	-- App
	
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Payroll';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Payroll');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Payroll');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Paie');
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'HR') THEN
		CALL public."p_InsertAppCategory"('HR');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'HR';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'HR');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'HR');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'RH');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Payroll';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'HR';
	CALL public."p_InsertAppRelation"(_id, _appid);
	
	-- App registry
	
	CALL public."p_InsertApp"('Publishing');

	-- App
	
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Publishing';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Publishing');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Publishing');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Publication');
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Communication') THEN
		CALL public."p_InsertAppCategory"('Communication');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Communication';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Communication');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Communication');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Communication');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Reporting') THEN
		CALL public."p_InsertAppCategory"('Reporting');
		SELECT "ID" INTO _id from public."cl_AppCategories" WHERE "Name" = 'Reporting';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Reporting');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Reporting');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Reporting');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Publishing';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Communication';
	CALL public."p_InsertAppRelation"(_id, _appid);
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Reporting';
	CALL public."p_InsertAppRelation"(_id, _appid);
	
	-- App registry
	
	CALL public."p_InsertApp"('Purchase');

	-- App
	
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Purchase';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Purchase');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Purchase');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Achats');
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Purchases') THEN
		CALL public."p_InsertAppCategory"('Purchases');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Purchases';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Purchases');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Purchases');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Achats');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Purchase';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Purchases';
	CALL public."p_InsertAppRelation"(_id, _appid);
	
	-- App registry
	
	CALL public."p_InsertApp"('Reporting');

	-- App
	
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Reporting';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Reporting');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Reporting');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Tableaux de bord');
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Decision making') THEN
		CALL public."p_InsertAppCategory"('Decision making');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Decision making';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Decision making');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Decision making');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Prise de décisions');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Reporting') THEN
		CALL public."p_InsertAppCategory"('Reporting');
		SELECT "ID" INTO _id from public."cl_AppCategories" WHERE "Name" = 'Reporting';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Reporting');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Reporting');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Reporting');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Reporting';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Decision making';
	CALL public."p_InsertAppRelation"(_id, _appid);
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Reporting';
	CALL public."p_InsertAppRelation"(_id, _appid);
	
	-- App registry
	
	CALL public."p_InsertApp"('Tasks');

	-- App
	
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Tasks';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Tasks');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Tasks');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'A-Faire');
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Planning') THEN
		CALL public."p_InsertAppCategory"('Planning');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Planning';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Planning');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Planning');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Planification');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Tasks';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Planning';
	CALL public."p_InsertAppRelation"(_id, _appid);
	
	-- App registry
	
	CALL public."p_InsertApp"('Teller');

	-- App
	
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Teller';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Teller');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Teller');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Caisses');
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Finance') THEN
		CALL public."p_InsertAppCategory"('Finance');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Finance';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Finance');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Finance');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Finance');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Teller';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Finance';
	CALL public."p_InsertAppRelation"(_id, _appid);
	
	-- App registry
	
	CALL public."p_InsertApp"('Wholesale');

	-- App
	
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Wholesale';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Wholesale');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Wholesale');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Boutiques en ligne');
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Sales') THEN
		CALL public."p_InsertAppCategory"('Sales');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Sales';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Sales');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Sales');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Ventes');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Purchases') THEN
		CALL public."p_InsertAppCategory"('Purchases');
		SELECT "ID" INTO _id from public."cl_AppCategories" WHERE "Name" = 'Purchases';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Purchases');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Purchases');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Achats');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Wholesale';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Sales';
	CALL public."p_InsertAppRelation"(_id, _appid);
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Purchases';
	CALL public."p_InsertAppRelation"(_id, _appid);

END $BODY$;