-- ============================================================
-- Tabel: PENERBIT_STATUS
-- Riwayat perubahan status kepatuhan penerbit.
-- Oracle 11g tidak punya IDENTITY, jadi ID auto-increment
-- dibuat lewat SEQUENCE + TRIGGER (before insert).
-- ============================================================

CREATE TABLE PENERBIT_STATUS (
    ID              NUMBER(19)      NOT NULL,
    PENERBIT_ID     NUMBER(19)      NOT NULL,
    STATUSAWAL      VARCHAR2(50),
    STATUSAKHIR     VARCHAR2(50)    NOT NULL,
    NOTE            VARCHAR2(1000),
    CREATEDATE      DATE            DEFAULT SYSDATE NOT NULL,
    CREATEBY        VARCHAR2(100),
    CREATETERMINAL  VARCHAR2(100),
    CONSTRAINT PK_PENERBIT_STATUS PRIMARY KEY (ID),
    CONSTRAINT FK_PENERBIT_STATUS_PENERBIT
        FOREIGN KEY (PENERBIT_ID) REFERENCES PENERBIT (ID)
);

-- Index untuk lookup riwayat per penerbit (paling sering dipakai)
CREATE INDEX IX_PENERBIT_STATUS_PENERBIT_ID
    ON PENERBIT_STATUS (PENERBIT_ID);

-- Index untuk query/report berdasarkan tanggal perubahan
CREATE INDEX IX_PENERBIT_STATUS_CREATEDATE
    ON PENERBIT_STATUS (CREATEDATE);

-- Index gabungan: riwayat status penerbit tertentu urut waktu
CREATE INDEX IX_PENERBIT_STATUS_PEN_DATE
    ON PENERBIT_STATUS (PENERBIT_ID, CREATEDATE);

-- Sequence untuk generate ID
CREATE SEQUENCE SEQ_PENERBIT_STATUS
    START WITH 1
    INCREMENT BY 1
    NOCACHE
    NOCYCLE;

-- Trigger: isi ID otomatis dari sequence saat insert
CREATE OR REPLACE TRIGGER TRG_PENERBIT_STATUS_BI
BEFORE INSERT ON PENERBIT_STATUS
FOR EACH ROW
WHEN (NEW.ID IS NULL)
BEGIN
    SELECT SEQ_PENERBIT_STATUS.NEXTVAL
    INTO :NEW.ID
    FROM DUAL;
END;
/
