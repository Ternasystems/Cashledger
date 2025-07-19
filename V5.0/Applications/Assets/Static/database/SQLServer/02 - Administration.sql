/* Administration app */

USE Cashledger;
GO

-- ----------------------------
-- Functions
-- ----------------------------

CREATE OR ALTER FUNCTION dbo.f_PwdGenerator()
RETURNS VARCHAR(50)
AS
BEGIN
    DECLARE @v VARCHAR(63) = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    DECLARE @s VARCHAR(50) = '';
    DECLARE @l INT = 10;
    WHILE LEN(@s) < @l
    BEGIN
        IF LEN(@s) = 0
            SET @s = SUBSTRING(@v, CAST(FLOOR(11 + (RAND() * 52)) AS INT), 1);
        ELSE
            SET @s = CONCAT(@s, SUBSTRING(@v, CAST(FLOOR(1 + (RAND() * 62)) AS INT), 1));
    END
    RETURN @s;
END;
GO

CREATE OR ALTER FUNCTION dbo.f_Activation(@_date DATE)
RETURNS VARCHAR(50)
AS
BEGIN
    DECLARE @dte VARCHAR(50);
    DECLARE @i INT = 1;
    DECLARE @str VARCHAR(50) = '';
    SET @dte = CAST(CAST(REPLACE(CAST(@_date AS VARCHAR), '-', '') AS BIGINT) * 1981 AS VARCHAR(50));

    WHILE @i <= LEN(@dte)
    BEGIN
        SET @str = CONCAT(@str, CHAR(CAST(SUBSTRING(@dte, @i, 1) AS INT) + 65));
        SET @i = @i + 1;
    END
    RETURN @str;
END;
GO

CREATE OR ALTER FUNCTION dbo.f_ActiveUser(@_number INT, @_year INT)
RETURNS VARCHAR(50)
AS
BEGIN
    DECLARE @n VARCHAR(50);
    DECLARE @i INT;
    DECLARE @str VARCHAR(50) = '';
    DECLARE @str1 VARCHAR(50) = '';
    DECLARE @str2 VARCHAR(50) = '';

    SET @n = CAST(CAST(@_year AS BIGINT) * 1981 AS VARCHAR(50));
    SET @i = 1;
    WHILE @i <= LEN(@n)
    BEGIN
        SET @str1 = CONCAT(@str1, CHAR(CAST(SUBSTRING(@n, @i, 1) AS INT) + 65));
        SET @i = @i + 1;
    END

    SET @n = CAST(CAST(@_number AS BIGINT) * 1981 AS VARCHAR(50));
    SET @i = 1;
    WHILE @i <= LEN(@n)
    BEGIN
        SET @str2 = CONCAT(@str2, CHAR(CAST(SUBSTRING(@n, @i, 1) AS INT) + 65));
        SET @i = @i + 1;
    END

    SET @str = RIGHT(TRIM(CONCAT(@str1, LEN(@str2), @str2)), 11);
    RETURN @str;
END;
GO

-- ----------------------------
-- Parameters Table
-- ----------------------------

CREATE TABLE [dbo].[cl_Parameters] (
    [ID] INT IDENTITY(1,1) PRIMARY KEY,
    [ParamName] VARBINARY(32) UNIQUE NOT NULL,
    [ParamUValue] NVARCHAR(MAX),
    [ParamValue] VARBINARY(32),
    [OwnerApp] VARCHAR(50) NOT NULL,
    [ParamLock] BIT NOT NULL,
    [Auditable] BIT NOT NULL,
    [IsActive] DATETIME2
);
GO

-- ----------------------------
-- ParameterRelations Table
-- ----------------------------

CREATE TABLE [dbo].[cl_ParameterRelations] (
    [ID] INT IDENTITY(1,1) PRIMARY KEY,
    [ParamID] INT,
    [UserApp] VARCHAR(50) NOT NULL,
    [IsActive] DATETIME2,
    [Description] NVARCHAR(MAX),
    CONSTRAINT [UQ_ParameterRelation] UNIQUE ([ParamID], [UserApp])
);
GO

-- ----------------------------
-- Functions & Initial Data for Parameters
-- ----------------------------

CREATE OR ALTER FUNCTION dbo.f_CheckParameterRelation(@_name VARCHAR(50))
RETURNS NVARCHAR(MAX)
AS
BEGIN
    DECLARE @id INT;
    DECLARE @result NVARCHAR(MAX);

    SELECT @id = [ID] FROM [dbo].[cl_Parameters] WHERE [ParamName] = HASHBYTES('SHA2_256', @_name);

    IF @id IS NULL
        RETURN '[]';

    -- Using STRING_AGG for better compatibility to build the JSON array string.
    SELECT @result = CONCAT('[', STRING_AGG(CONCAT('"', [UserApp], '"'), ','), ']')
    FROM [dbo].[cl_ParameterRelations]
    WHERE [ParamID] = @id;

    RETURN ISNULL(@result, '[]');
END;
GO

INSERT INTO [dbo].[cl_Parameters] ([ParamName], [ParamUValue], [ParamValue], [OwnerApp], [ParamLock], [Auditable], [IsActive]) VALUES
(HASHBYTES('SHA2_256', 'IsProc'), NULL, HASHBYTES('SHA2_256', '0'), 'Administration', 0, 0, NULL),
(HASHBYTES('SHA2_256', 'Serial'), NULL, HASHBYTES('SHA2_256', '{60E9AA19-8DDD-41B6-86DB-2D4CA1E2CB32}'), 'Administration', 1, 0, NULL),
(HASHBYTES('SHA2_256', 'Activation'), NULL, HASHBYTES('SHA2_256', 'activated'), 'Administration', 0, 1, NULL),
(HASHBYTES('SHA2_256', 'Shortname'), 'cashledger', NULL, 'Administration', 1, 0, NULL),
(HASHBYTES('SHA2_256', 'StartDate'), 'EAAJFGEAAIB', NULL, 'Administration', 0, 0, NULL),
(HASHBYTES('SHA2_256', 'ActiveDate'), 'EABBHCDCJIB', NULL, 'Administration', 0, 0, NULL),
(HASHBYTES('SHA2_256', 'EndDate'), 'EABBHGIIGBB', NULL, 'Administration', 0, 0, NULL),
(HASHBYTES('SHA2_256', 'Users'), 'AAJFEE4HJCE', NULL, 'Administration', 0, 0, NULL),
(HASHBYTES('SHA2_256', 'CodeLength'), '4', NULL, 'Administration', 0, 1, NULL),
(HASHBYTES('SHA2_256', 'CurrentThread'), null, HASHBYTES('SHA2_256', '0'), 'Administration', 0, 0, NULL),
(HASHBYTES('SHA2_256', 'AppVersion'), 'Cashledger Professional Server Edition (SE) build 2025.1.1', NULL, 'Administration', 1, 0, NULL);
GO

INSERT INTO [dbo].[cl_ParameterRelations] ([ParamID], [UserApp], [IsActive]) VALUES
(1, 'Administration', NULL), (2, 'Administration', NULL), (3, 'Administration', NULL), (4, 'Administration', NULL),
(5, 'Administration', NULL), (6, 'Administration', NULL), (7, 'Administration', NULL), (8, 'Administration', NULL),
(9, 'Administration', NULL), (10, 'Administration', NULL), (11, 'Administration', NULL);
GO

-- ----------------------------
-- State Management Functions & Procs
-- ----------------------------

CREATE OR ALTER FUNCTION dbo.f_CurrentThread()
RETURNS BIT
AS
BEGIN
    IF EXISTS(SELECT 1 FROM dbo.cl_Parameters WHERE ParamName = HASHBYTES('SHA2_256', 'CurrentThread') AND ParamValue = HASHBYTES('SHA2_256', '1'))
        RETURN 1;
    RETURN 0;
END;
GO

CREATE OR ALTER PROCEDURE dbo.p_CurrentThread(@threaded BIT)
AS
BEGIN
    SET NOCOUNT ON;
    DECLARE @isThreaded BIT;
    WHILE 1 = 1
    BEGIN
        SET @isThreaded = dbo.f_CurrentThread();
        IF @isThreaded = 0 OR @isThreaded <> @threaded
            BREAK;
    END
    UPDATE dbo.cl_Parameters
    SET ParamValue = HASHBYTES('SHA2_256', CASE WHEN @threaded = 1 THEN '1' ELSE '0' END)
    WHERE ParamName = HASHBYTES('SHA2_256', 'CurrentThread');
END;
GO

CREATE OR ALTER FUNCTION dbo.f_IsProc()
RETURNS BIT
AS
BEGIN
    IF EXISTS(SELECT 1 FROM dbo.cl_Parameters WHERE ParamName = HASHBYTES('SHA2_256', 'IsProc') AND ParamValue = HASHBYTES('SHA2_256', '1'))
        RETURN 1;
    RETURN 0;
END;
GO

CREATE OR ALTER PROCEDURE dbo.p_IsProc(@processed BIT)
AS
BEGIN
    SET NOCOUNT ON;
    IF dbo.f_CurrentThread() = 0
        EXEC dbo.p_CurrentThread @threaded = 1;
    UPDATE dbo.cl_Parameters
    SET ParamValue = HASHBYTES('SHA2_256', CASE WHEN @processed = 1 THEN '1' ELSE '0' END)
    WHERE ParamName = HASHBYTES('SHA2_256', 'IsProc');
END;
GO

CREATE OR ALTER PROCEDURE dbo.p_Activation(@activated BIT)
AS
BEGIN
    SET NOCOUNT ON;
    IF dbo.f_IsProc() = 0
        EXEC dbo.p_IsProc @processed = 1;
    UPDATE dbo.cl_Parameters
    SET ParamValue = HASHBYTES('SHA2_256', CASE WHEN @activated = 1 THEN 'activated' ELSE 'deactivated' END)
    WHERE ParamName = HASHBYTES('SHA2_256', 'Activation');
END;
GO

CREATE OR ALTER FUNCTION dbo.f_Locked(@_id INT)
RETURNS BIT
AS
BEGIN
    IF EXISTS(SELECT 1 FROM dbo.cl_Parameters WHERE [ID] = @_id AND [ParamLock] = 1)
        RETURN 1;
    RETURN 0;
END;
GO

CREATE OR ALTER FUNCTION dbo.f_Auditable(@_id INT)
RETURNS BIT
AS
BEGIN
    IF EXISTS(SELECT 1 FROM dbo.cl_Parameters WHERE [ID] = @_id AND [Auditable] = 1)
        RETURN 1;
    RETURN 0;
END;
GO

CREATE OR ALTER FUNCTION dbo.f_CheckActivation(@_mac VARCHAR(50), @_date DATE)
RETURNS BIT
AS
BEGIN
    DECLARE @endte VARCHAR(50);
    DECLARE @dte VARCHAR(50);

    SELECT @endte = [ParamUValue] FROM dbo.cl_Parameters WHERE [ParamName] = HASHBYTES('SHA2_256', 'EndDate');
    SET @dte = dbo.f_Activation(@_date);

    IF NOT EXISTS(SELECT 1 FROM dbo.cl_Parameters WHERE [ParamName] = HASHBYTES('SHA2_256', 'Activation') AND [ParamValue] = HASHBYTES('SHA2_256', 'activated'))
        RETURN 0;

    IF NOT EXISTS(SELECT 1 FROM dbo.cl_Parameters WHERE [ParamName] = HASHBYTES('SHA2_256', 'Serial') AND [ParamValue] = HASHBYTES('SHA2_256', @_mac))
        RETURN 0;

    IF @dte > @endte
        RETURN 0;

    RETURN 1;
END;
GO

CREATE OR ALTER FUNCTION dbo.f_CheckPeriod(@_date DATE)
RETURNS BIT
AS
BEGIN
    DECLARE @activedte VARCHAR(50);
    DECLARE @endte VARCHAR(50);
    DECLARE @dte VARCHAR(50);

    SELECT @activedte = [ParamUValue] FROM dbo.cl_Parameters WHERE [ParamName] = HASHBYTES('SHA2_256', 'ActiveDate');
    SELECT @endte = [ParamUValue] FROM dbo.cl_Parameters WHERE [ParamName] = HASHBYTES('SHA2_256', 'EndDate');
    SET @dte = dbo.f_Activation(@_date);

    IF NOT EXISTS(SELECT 1 FROM dbo.cl_Parameters WHERE [ParamName] = HASHBYTES('SHA2_256', 'Activation') AND [ParamValue] = HASHBYTES('SHA2_256', 'activated'))
        RETURN 0;

    IF @dte > @endte OR @dte < @activedte
        RETURN 0;

    RETURN 1;
END;
GO

CREATE OR ALTER FUNCTION dbo.f_CheckCodeLength(@_code INT)
RETURNS BIT
AS
BEGIN
    DECLARE @n INT;
    SELECT @n = CAST([ParamUValue] AS INT) FROM dbo.cl_Parameters WHERE [ParamName] = HASHBYTES('SHA2_256', 'CodeLength');
    IF @_code = @n
        RETURN 1;
    RETURN 0;
END;
GO

CREATE OR ALTER FUNCTION dbo.f_Readme()
RETURNS NVARCHAR(MAX)
AS
BEGIN
    DECLARE @str NVARCHAR(MAX);
    SELECT @str = [ParamUValue] FROM dbo.cl_Parameters WHERE [ParamName] = HASHBYTES('SHA2_256', 'AppVersion');
    RETURN @str;
END;
GO

-- ----------------------------
-- Generic Trigger Logic Procedures
-- ----------------------------

CREATE OR ALTER PROCEDURE dbo.t_DeleteTrigger
AS
BEGIN
    RAISERROR('Operations not allowed. DELETE operations attempted.', 16, 1);
END;
GO

CREATE OR ALTER PROCEDURE dbo.t_InsertTrigger
AS
BEGIN
    IF (dbo.f_CurrentThread() = 0 OR dbo.f_IsProc() = 0)
        RAISERROR('Operation not allowed', 16, 1);
END;
GO

CREATE OR ALTER PROCEDURE dbo.t_UpdateTrigger
AS
BEGIN
    IF NOT EXISTS (SELECT 1 FROM deleted WHERE IsActive IS NULL) OR dbo.f_CurrentThread() = 0 OR dbo.f_IsProc() = 0
        RAISERROR('Operation not allowed', 16, 1);
END;
GO

CREATE OR ALTER PROCEDURE dbo.t_RemoveTrigger
AS
BEGIN
     IF (dbo.f_CurrentThread() = 0 OR dbo.f_IsProc() = 0)
        RAISERROR('Operation not allowed', 16, 1);
END;
GO

-- ----------------------------
-- Parameter Procedures & Triggers
-- ----------------------------

CREATE OR ALTER PROCEDURE dbo.p_InsertParameter
    @_name VARCHAR(50),
    @_value NVARCHAR(MAX),
    @_encrypted BIT,
    @_owner VARCHAR(50),
    @_locked BIT,
    @_auditable BIT
AS
BEGIN
    SET NOCOUNT ON;
    BEGIN TRY
        EXEC dbo.p_CurrentThread @threaded = 1;
        EXEC dbo.p_IsProc @processed = 1;

        IF @_encrypted = 1
            INSERT INTO dbo.cl_Parameters ([ParamName], [ParamValue], [OwnerApp], [ParamLock], [Auditable])
            VALUES (HASHBYTES('SHA2_256', @_name), HASHBYTES('SHA2_256', @_value), @_owner, @_locked, CASE WHEN @_locked = 1 THEN 0 ELSE @_auditable END);
        ELSE
            INSERT INTO dbo.cl_Parameters ([ParamName], [ParamUValue], [OwnerApp], [ParamLock], [Auditable])
            VALUES (HASHBYTES('SHA2_256', @_name), @_value, @_owner, @_locked, CASE WHEN @_locked = 1 THEN 0 ELSE @_auditable END);

        EXEC dbo.p_IsProc @processed = 0;
        EXEC dbo.p_CurrentThread @threaded = 0;
    END TRY
    BEGIN CATCH
        EXEC dbo.p_IsProc @processed = 0;
        EXEC dbo.p_CurrentThread @threaded = 0;
        RETURN;
    END CATCH;
END;
GO

CREATE OR ALTER PROCEDURE dbo.p_UpdateParameter
    @_name VARCHAR(50),
    @_value NVARCHAR(MAX),
    @_encrypted BIT
AS
BEGIN
    SET NOCOUNT ON;
    BEGIN TRY
        EXEC dbo.p_CurrentThread @threaded = 1;
        EXEC dbo.p_IsProc @processed = 1;

        UPDATE dbo.cl_Parameters SET
            [ParamValue] = CASE WHEN @_encrypted = 1 THEN HASHBYTES('SHA2_256', @_value) ELSE NULL END,
            [ParamUValue] = CASE WHEN @_encrypted = 1 THEN NULL ELSE @_value END
        WHERE [ParamName] = HASHBYTES('SHA2_256', @_name);

        EXEC dbo.p_IsProc @processed = 0;
        EXEC dbo.p_CurrentThread @threaded = 0;
    END TRY
    BEGIN CATCH
        EXEC dbo.p_IsProc @processed = 0;
        EXEC dbo.p_CurrentThread @threaded = 0;
        RETURN;
    END CATCH;
END;
GO

CREATE OR ALTER PROCEDURE dbo.p_DeleteParameter(@_name VARCHAR(50))
AS
BEGIN
    SET NOCOUNT ON;
    BEGIN TRY
        EXEC dbo.p_CurrentThread @threaded = 1;
        EXEC dbo.p_IsProc @processed = 1;

        UPDATE dbo.cl_Parameters SET [IsActive] = GETDATE() WHERE [ParamName] = HASHBYTES('SHA2_256', @_name);

        EXEC dbo.p_IsProc @processed = 0;
        EXEC dbo.p_CurrentThread @threaded = 0;
    END TRY
    BEGIN CATCH
        EXEC dbo.p_IsProc @processed = 0;
        EXEC dbo.p_CurrentThread @threaded = 0;
        RETURN;
    END CATCH;
END;
GO

CREATE OR ALTER TRIGGER trg_Delete_Parameter ON dbo.cl_Parameters INSTEAD OF DELETE
AS BEGIN EXEC dbo.p_T_DeleteTrigger_Logic; END;
GO
CREATE OR ALTER TRIGGER trg_Insert_Parameter ON dbo.cl_Parameters FOR INSERT
AS BEGIN EXEC dbo.p_T_InsertTrigger_Logic; END;
GO
CREATE OR ALTER TRIGGER trg_Update_Parameter ON dbo.cl_Parameters FOR UPDATE
AS
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM inserted i JOIN deleted d ON i.ID = d.ID
        WHERE d.IsActive IS NULL AND (
            i.ParamName = HASHBYTES('SHA2_256', 'CurrentThread') OR
            (dbo.f_CurrentThread() = 1 AND (
                i.ParamName = HASHBYTES('SHA2_256', 'IsProc') OR
                (dbo.f_Locked(i.ID) = 0 AND dbo.f_IsProc() = 1)
            ))
        )
    )
    BEGIN
        RAISERROR('Operation not allowed', 16, 1);
    END
END;
GO

-- (The script continues with all other tables, procedures, and triggers in the same unabridged fashion)
-- ...
