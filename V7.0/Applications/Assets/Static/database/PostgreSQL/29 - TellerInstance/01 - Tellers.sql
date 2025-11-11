/* Teller app */

/* Tellers, TellerSessions, TellerTransactions, TellerPayments, TellerReceipts, TellerTransfers */

-- Table: public.cl_Tellers

DROP TABLE IF EXISTS public."cl_Tellers";
CREATE TABLE public."cl_Tellers"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"ProfileID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Profiles" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"StartDate" timestamp without time zone DEFAULT NOW(),
    "EndDate" timestamp without time zone,
	"SessionID" text COLLATE pg_catalog."default" UNIQUE,
	"SessionState" character varying(50) COLLATE pg_catalog."default" NOT NULL CHECK ('OPEN', 'CLOSED', 'SUSPENDED'),
	"IP" character varying(50) COLLATE pg_catalog."default",
	"Action" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" character varying(50) COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- FUNCTION: public."t_LogTeller"()

CREATE OR REPLACE FUNCTION public."t_LogTeller"()
	RETURNS trigger
	LANGUAGE 'plpgsql'
	COST 100
	VOLATILE NOT LEAKPROOF
AS $BODY$
BEGIN
	IF TG_OP = 'INSERT' THEN
		CALL public."p_InsertAudit"(TG_OP::character varying(50), TG_TABLE_NAME::character varying(50), NEW."ID"::character varying(50), row_to_json(NEW)::jsonb);		
	ELSIF TG_OP = 'UPDATE' THEN
		CALL public."p_InsertAudit"(
			CASE
				WHEN NEW."EndDate" IS NOT NULL THEN 'DISABLED'
				WHEN NEW."IsActive" IS NOT NULL THEN 'DEACTIVATE'
				ELSE TG_OP::character varying(50)
			END,
			TG_TABLE_NAME::character varying(50),
			OLD."ID"::character varying(50),
			json_build_object('before: ', row_to_json(OLD), ' after: ', row_to_json(NEW))::jsonb
		);
	ELSIF TG_OP = 'DELETE' THEN
		CALL public."p_InsertAudit"(TG_OP::character varying(50), TG_TABLE_NAME::character varying(50), OLD."ID"::character varying(50), row_to_json(OLD)::jsonb);
	END IF;
	RETURN CASE WHEN TG_OP = 'INSERT' THEN NEW ELSE OLD END;
END;
$BODY$;

-- FUNCTION: public.f_CheckSessionState(character varying)

CREATE OR REPLACE FUNCTION public."f_CheckSessionState"(_id character varying(50))
	RETURNS character varying
	LANGUAGE 'plpgsql'
	COST 100
	VOLATILE PARALLEL UNSAFE
AS $BODY$
DECLARE _sessionstate character varying(50);
BEGIN
	SELECT "SessionState" INTO _sessionstate FROM public."cl_Tellers" WHERE "ID" = _id;
	RETURN _sessionstate;
END;
$BODY$;

-- PROCEDURE: public.p_SessionState(character varying, character varying, character varying)

CREATE OR REPLACE PROCEDURE public."p_SessionState"(
	IN _id character varying(50),
	IN _sessionstate character varying(50),
	IN _ip character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Tellers';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "SessionState" = %L, "IP" = %L, "Action" = ''SESSION_ATTEMPT'' WHERE "ID" = %L;', _tablename, _sessionstate, _ip, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- FUNCTION: public.f_CheckTeller(character varying, character varying)

CREATE OR REPLACE FUNCTION public."f_CheckTeller"(
	_profileid character varying(50),
	_ip character varying(50))
    RETURNS SETOF "cl_Tellers" 
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
    ROWS 1000
AS $BODY$
DECLARE _id character varying(50);
BEGIN
	-- Check successful
	RETURN QUERY SELECT * FROM public."cl_Tellers" WHERE "ProfileID" = _profileid AND "EndDate" IS NULL AND "IsActive" IS NULL;
	
	-- Check failed
	IF NOT FOUND THEN
		IF EXISTS(SELECT 1 FROM public."cl_Tellers" WHERE "ProfileID" = _profileid) THEN
			SELECT "ID" INTO _id FROM public."cl_Tellers" WHERE "ProfileID" = _profileid;
		ELSE
			SELECT "ID" INTO _id FROM public."cl_Profiles" WHERE "LastName" = 'unkown';
		END IF;
		CALL public."p_SessionState"(_id, 'SUSPENDED', _ip);
	END IF;
END;
$BODY$;

-- PROCEDURE: public.p_InsertTeller(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertTeller"(
	IN _profileid character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Tellers'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, NOW(), NULL, NULL, %L);', _tablename, _id, _profileid, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'TEL');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateTeller(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateTeller"(
	IN _id character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Tellers';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Description" = %L WHERE "ID" = %L AND "EndDate" IS NULL;', _tablename, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteTeller(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteTeller"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Tellers';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L AND "EndDate" IS NULL;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DisableTeller(character varying)

CREATE OR REPLACE PROCEDURE public."p_DisableTeller"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Tellers';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "EndDate" = NOW() WHERE "ID" = %L AND "EndDate" IS NULL;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Teller

CREATE OR REPLACE TRIGGER "Delete_Teller"
    BEFORE DELETE
    ON public."cl_Tellers"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Teller

CREATE OR REPLACE TRIGGER "Insert_Teller"
    BEFORE INSERT 
    ON public."cl_Tellers"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Teller

CREATE OR REPLACE TRIGGER "Update_Teller"
    BEFORE UPDATE 
    ON public."cl_Tellers"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_TellerSessions

DROP TABLE IF EXISTS public."cl_TellerSessions";
CREATE TABLE public."cl_TellerSessions"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "TellerID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Tellers" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"SessionID" text COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Tellers" ("SessionID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
    "Action" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"Balance" numeric(8,2) NOT NULL CHECK ("Balance" >= 0),
	"CashIn" numeric(8,2) NOT NULL CHECK ("CashIn" >= 0),
	"CashOut" numeric(8,2) NOT NULL CHECK ("CashOut" >= 0),
	"IP" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "ActionDate" timestamp without time zone NOT NULL
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertTellerSession(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertTellerSession"(
	IN _tellerid character varying(50),
	IN _sessionid character varying(50),
	IN _action character varying(50),
	IN _balance numeric(8,2),
	IN _cashin numeric(8,2),
	IN _cashout numeric(8,2),
	IN _IP character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_TellerSessions'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, %L, %L, %L, %L, NOW());', _tablename, _id, _tellerid, _sessionid, _action, _balance, _cashin, _cashout, _IP);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'TSS');
END;
$BODY$;

-- Trigger: Delete_TellerSession

CREATE OR REPLACE TRIGGER "Delete_TellerSession"
    BEFORE DELETE OR UPDATE
    ON public."cl_TellerSessions"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_TellerSession

CREATE OR REPLACE TRIGGER "Insert_TellerSession"
    BEFORE INSERT 
    ON public."cl_TellerSessions"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Table: public.cl_TellerTransactions

DROP TABLE IF EXISTS public."cl_TellerTransactions";
CREATE TABLE public."cl_TellerTransactions"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"SessionID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_TellerSessions" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"TransactionDate" timestamp without time zone NOT NULL,
	"EditDate" timestamp without time zone NOT NULL,
	"ReferenceID" character varying(50) COLLATE pg_catalog."default" NOT NULL CHECK (public."f_CheckReference"("ReferenceID", "AppID")), -- Customers, Suppliers, Employees
	"AppID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Apps" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"TransactionType" character varying(50) COLLATE pg_catalog."default" NOT NULL CHECK ("TransactionType" IN ('CASHIN', 'CASHOUT', 'REFUND', 'TRANSFER')),
	"StockID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Stocks" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"UnitID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Units" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"TransactionStatus" character varying(50) COLLATE pg_catalog."default" NOT NULL CHECK ("TransactionStatus" IN ('PENDING', 'POSTED', 'REVERSED', 'CANCELLED')),
	"UnitPrice" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_PriceRelations" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"Quantity" numeric(8,2) NOT NULL CHECK ("Quantity" >= 0),
	"DiscountID" character varying(50) COLLATE pg_catalog."default" REFERENCES public."cl_discounts" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"TaxID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Taxes" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"Amount" numeric(8,2) NOT NULL CHECK ("Amount" >= 0),
	"CreatedBy" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Tellers" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"ApprovedBy" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Tellers" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
    "IsActive" timestamp without time zone,
    "Description" character varying(50) COLLATE pg_catalog."default",
	CONSTRAINT "CK_TellerTransaction" CHECK (public."f_CheckReference"("ReferenceID", "AppID"))
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertTellerTransaction(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertTellerTransaction"(
	IN _tellerid character varying(50),
	IN _sessionid character varying(50),
	IN _transactiondate timestamp without time zone DEFAULT NOW(),
	IN _referenceid character varying(50),
	IN _appid character varying(50),
	IN _transactiontype character varying(50),
	IN _stockid character varying(50),
	IN _unitid character varying(50),
	IN _transactionstatus character varying(50),
	IN _unitprice character varying(50),
	IN _quantity numeric(8,2),
	IN _taxid character varying(50),
	IN _approbator character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_TellerTransactions'; _id character varying(50) := '%s'; _discount numeric(8,2); _taxvalue numeric(8,2); _price numeric(8,2);
BEGIN
	--
	SELECT "Value" INTO _discount FROM public."cl_Discounts" WHERE "ID" = _discountid;
	SELECT "Value" INTO _taxvalue FROM public."cl_Taxes" WHERE "ID" = _taxid;
	SELECT "UnitPrice" INTO _price FROM public."cl_PriceRelations" WHERE "ID" = _unitprice;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NOW(), %L, %L, %L, %L, %L, %L, %L, %L, %L, %L, %L, %L, %L, %L) ON CONFLICT ("ReferenceID", "AppID") DO NOTHING;', _tablename, _id, _sessionid,
	_transactiondate, _referenceid, _appid, _transactiontype, _stockid, _unitid, _transactionstatus, _price, _quantity, _discount, _taxvalue, ((_price * _quantity) - discount + _taxvalue), _tellerid, _approbator,
	_description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'TTN');
END;
$BODY$;

-- Trigger: Delete_TellerTransaction

CREATE OR REPLACE TRIGGER "Delete_TellerTransaction"
    BEFORE DELETE OR UPDATE
    ON public."cl_TellerTransactions"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_TellerTransaction

CREATE OR REPLACE TRIGGER "Insert_TellerTransaction"
    BEFORE INSERT 
    ON public."cl_TellerTransactions"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Table: public.cl_TellerPayments

DROP TABLE IF EXISTS public."cl_TellerPayments";
CREATE TABLE public."cl_TellerPayments"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"PaymentDate" timestamp without time zone NOT NULL,
	"EditDate" timestamp without time zone NOT NULL,
	"TransactionID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_TellerTransactions" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"PaymentID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_PaymentMethods" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"ReferenceNumber" text COLLATE pg_catalog."default" UNIQUE NOT NULL,
	"Amount" numeric(8,2) NOT NULL CHECK ("Amount" >= 0),
	"CreatedBy" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Tellers" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"ApprovedBy" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Tellers" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertTellerPayment(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertTellerPayment"(
	IN _tellerid character varying(50),
	IN _paymentdate timestamp without time zone DEFAULT NOW(),
	IN _transactionid character varying(50),
	IN _paymentid character varying(50),
	IN _referencenumber text,
	IN _amount numeric(8,2),
	IN _approbator character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_TellerPayments'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, NOW(), %L, %L, %L, %L, %L, %L, NULL, %L);', _tablename, _id, _paymentdate, _transactionid, _paymentid, _referencenumber, _amount, _tellerid, 
	_approbator, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'TPM');
END;
$BODY$;

-- FUNCTION: public.t_CheckTellerPayment()

CREATE OR REPLACE FUNCTION public."t_CheckTellerPayment"()
    RETURNS trigger
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE NOT LEAKPROOF
AS $BODY$
DECLARE
    _amount NUMERIC(8,2);
    _total NUMERIC(8,2);
BEGIN
    SELECT "Amount" INTO _amount FROM public."cl_TellerTransactions" WHERE "ID" = NEW."TransactionID";

	SELECT COALESCE(SUM(Amount), 0) INTO _total FROM "cl_TellerPayments" WHERE "TransactionID" = NEW."TransactionID";

	IF _total > _amount THEN
		RAISE EXCEPTION 'Payment amount exceeds transaction limit. Transaction: %, Paid: %, New Payment: %',_amount, _total - NEW.Amount, NEW.Amount;
	END IF;

	RETURN NEW;
END;
$BODY$;

-- Trigger: Check_TellerPayment

CREATE OR REPLACE TRIGGER "Check_TellerPayment"
	BEFORE INSERT OR UPDATE
	ON public."cl_TellerPayments"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_CheckTellerPayment"();

-- Trigger: Delete_TellerPayment

CREATE OR REPLACE TRIGGER "Delete_TellerPayment"
    BEFORE DELETE OR UPDATE
    ON public."cl_TellerPayments"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_TellerPayment

CREATE OR REPLACE TRIGGER "Insert_TellerPayment"
    BEFORE INSERT 
    ON public."cl_TellerPayments"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Table: public.cl_TellerReceipts

DROP TABLE IF EXISTS public."cl_TellerReceipts";
CREATE TABLE public."cl_TellerReceipts"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"ReceiptDate" timestamp without time zone NOT NULL,
	"EditDate" timestamp without time zone NOT NULL,
	"TransactionID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_TellerTransactions" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"ReceiptNumber" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL,
	"ReferenceID" character varying(50) COLLATE pg_catalog."default" NOT NULL CHECK (public."f_CheckReference"("ReferenceID", "AppID")), -- Customers, Suppliers, Employees
	"AppID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Apps" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"PrintedBy" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Tellers" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertTellerReceipt(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertTellerReceipt"(
	IN _tellerid character varying(50),
	IN _receiptdate timestamp without time zone DEFAULT NOW(),
	IN _transactionid character varying(50),
	IN _receiptnumber character varying(50),
	IN _referenceid character varying(50),
	IN _appid character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_TellerReceipts'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NOW(), %L, %L, %L, %L, %L, %L, %L, %L, %L);', _tablename, _id, _receiptdate, _transactionid, _receiptnumber, _referenceid, _appid, _tellerid,
	_description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'TRT');
END;
$BODY$;

-- Trigger: Delete_TellerReceipt

CREATE OR REPLACE TRIGGER "Delete_TellerReceipt"
    BEFORE DELETE OR UPDATE
    ON public."cl_TellerReceipts"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_TellerReceipt

CREATE OR REPLACE TRIGGER "Insert_TellerReceipt"
    BEFORE INSERT 
    ON public."cl_TellerReceipts"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Table: public.cl_TellerTransfers

DROP TABLE IF EXISTS public."cl_TellerTransfers";
CREATE TABLE public."cl_TellerTransfers"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"TransferDate" timestamp without time zone NOT NULL,
	"EditDate" timestamp without time zone NOT NULL,
	"ReferenceNumber" text COLLATE pg_catalog."default" UNIQUE NOT NULL,
	"TransactionID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_TellerTransactions" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"TellerFrom" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Tellers" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"TellerTo" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Tellers" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"Amount" numeric(8,2) NOT NULL CHECK ("Amount" >= 0),
	"TransferStatus" character varying(50) COLLATE pg_catalog."default" NOT NULL CHECK ("TrasnferStatus" IN ('PENDING', 'APPROVED', 'REJECTED')),
	"ApprovedBy" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Tellers" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertTellerTransfer(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertTellerTransfer"(
	IN _tellerid character varying(50),
	IN _receiverid character varying(50),
	IN _Transferdate timestamp without time zone DEFAULT NOW(),
	IN _referencenumber character varying(50),
	IN _transactionid character varying(50),
	IN _amount numeric(8,2),
	IN _status character varying(50),
	IN _approbator character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_TellerTransfers'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, NOW(), %L, %L, %L, %L, %L, %L, %L, NULL, %L);', _tablename, _id, _transferdate, _referencenumber, _transactionid, _tellerid, _receiverid, _amount,
	_status, _approbator, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'TRT');
END;
$BODY$;

-- Trigger: Delete_TellerTransfer

CREATE OR REPLACE TRIGGER "Delete_TellerTransfer"
    BEFORE DELETE OR UPDATE
    ON public."cl_TellerTransfers"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_TellerTransfer

CREATE OR REPLACE TRIGGER "Insert_TellerTransfer"
    BEFORE INSERT 
    ON public."cl_TellerTransfers"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();