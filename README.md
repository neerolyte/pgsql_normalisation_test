# Overview

This is still very much a work in progress. You're welcome to poke around at the contents, just don't expect it to be complete and fully formed.

I work with an ORM-like app that happens to have some pretty denormalised tables in its main database backend. Recently I've started to suspect this denormalisation is responsible for more of our slow down than we might otherwise expect.

These tests are an attempt to emulate just the minimum set of what goes in our backend for asset attribute storage with the current (denormalised) structure and what would go on with a normalised structure.

I think these tests might be generally useful to anyone thinking about DB design on top of PostgreSQL though.

I'm open to finding out what other variables people think need to be played with to isolate where this starts to break down and whether or not it is worth a large scale redesign to fix this. Assuming it is, as I suspect, a problem.

# Execution

"init.sql" creates the basic schema inside an existing PostgreSQL DB.

I've been testing with PostgreSQL 8.4.9 with a LC_CTYPE value of "C" as this is closest to what our production instances run.

"generate_raw_rows.php" outputs two .sql files.

"normal.sql" is used to perform inserts in to the normalised tables.

"denormal.sql" is used to perform inserts in to the denormalised tables.


# Findings

## Maintainability

The denormalised form essentially requires decoupling the name of an attribute from its value during insert, e.g.:

	INSERT INTO nt_attr_val (assetid, attrid, value)
	VALUES ('$assetid', 0, 'some value');
     
Without some external lookup it's not obvious what attrid "0" translates to.


The denormalised form requires twice as many indexes as the normalised form. This is likely to increase the space PostgreSQL needs to store the indexes both in memory and on disk (TODO: test this - requires oid2name lookups).
		
## Size

Dump files are bigger because there's more rows.

This makes backups larger and slower to produce.

## Usability

TODO: cover select queries once data is already in there

# Example runs

Here are some example runs:
     
	 [root@foo pgsql_normalisation_test]# psql -U postgres nt < init.sql > /dev/null; time psql -U postgres nt < denormal.sql  > /dev/null
     
     real	0m24.111s
     user	0m0.240s
     sys	0m0.100s
     [root@foo pgsql_normalisation_test]# psql -U postgres nt < init.sql > /dev/null; time psql -U postgres nt < denormal.sql  > /dev/null
     
     real	0m24.768s
     user	0m0.220s
     sys	0m0.100s
     [root@foo pgsql_normalisation_test]# psql -U postgres nt < init.sql > /dev/null; time psql -U postgres nt < denormal.sql  > /dev/null
     
     real	0m24.602s
     user	0m0.130s
     sys	0m0.050s
     [root@foo pgsql_normalisation_test]# psql -U postgres nt < init.sql > /dev/null; time psql -U postgres nt < normal.sql  > /dev/null
     
     real	0m9.983s
     user	0m0.110s
     sys	0m0.070s
     [root@foo pgsql_normalisation_test]# psql -U postgres nt < init.sql > /dev/null; time psql -U postgres nt < normal.sql  > /dev/null
     
     real	0m10.331s
     user	0m0.110s
     sys	0m0.090s
     [root@foo pgsql_normalisation_test]# psql -U postgres nt < init.sql > /dev/null; time psql -U postgres nt < normal.sql  > /dev/null
     
     real	0m10.012s
     user	0m0.130s
     sys	0m0.010s
