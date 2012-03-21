/* remove old ones */
DROP TABLE nt_attr_norm;
DROP TABLE nt_attr;
DROP TABLE nt_attr_val;

/* create denormalised tables */
CREATE TABLE nt_attr (
    attrid integer NOT NULL,
    name character varying(100) NOT NULL
);
CREATE INDEX nt_attr_name ON nt_attr USING btree (name);
CREATE INDEX nt_attr_attrid ON nt_attr USING btree (attrid);
INSERT INTO nt_attr (attrid, name) VALUES (0, 'name');
INSERT INTO nt_attr (attrid, name) VALUES (1, 'short_name');
INSERT INTO nt_attr (attrid, name) VALUES (2, 'description');
INSERT INTO nt_attr (attrid, name) VALUES (3, 'html');

CREATE TABLE nt_attr_val (
    assetid character varying(15) NOT NULL,
    attrid integer NOT NULL,
    value text
);
CREATE INDEX nt_attr_val_assetid ON nt_attr_val USING btree (assetid);
CREATE INDEX nt_attr_val_attrid ON nt_attr_val USING btree (attrid);

/* create normalised tables */
CREATE TABLE nt_attr_norm (
    assetid character varying(15) NOT NULL,
    name character varying(255) NOT NULL,
    short_name character varying(255) NOT NULL,
	description text,
    html text
);
CREATE INDEX nt_attr_norm_assetid ON nt_attr_norm USING btree (assetid);
CREATE INDEX nt_attr_norm_name ON nt_attr_norm USING btree (name);