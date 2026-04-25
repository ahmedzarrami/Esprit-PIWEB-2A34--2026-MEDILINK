USE medilink;

ALTER TABLE medicaments DROP COLUMN IF EXISTS stock;
ALTER TABLE medicaments DROP COLUMN IF EXISTS date_expiration;
