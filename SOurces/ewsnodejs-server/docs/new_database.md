# New Database Proposal

1. We will implement a [key-value store](https://en.wikipedia.org/wiki/Key%E2%80%93value_database) on top of sqlite. 
2. We will update the current web apps, if necessary, to use it.
3. This document will be updated to provide a reference for web app developers to use the database.

## How this works

Lets say you have a database named "researchers".  You might have keys like "{researcherid}-organization" and "{researcherid}-password".   Then when researcher "Martin" logs in, the database is queried for key "Martin-password" and checks the value against the supplied password.

We don't have "joins" with a key-value database, so for an operation like finding all the users for a researcher, we have to either keep a json list under the researcher name, like "{researcherid}-users".  Or if the users table has a key "{user}-researcher" then we have a database query that finds "*-user" keys and builds a list of the results.

## Design

All web apps must access the database through a REST API.  The existing database calls are spread throughout the API, which is documented [here](api.md).  Web apps can use the database API directly or implement other APIs that use it.  For example,  /researcher/login does a database lookup.

New tables (stores) can be created through the REST API.  Web apps can create their own or they can share with other apps.  We won't sp

## API

We need to document the API to do the following operations
- create a new database
- set a key to a value
- get a value for a given key
- delete a key
- find matching keys.  There might be multiple functions here.  We could have findall(), findstart(), findend() and/or findregex().  At the very least we need findall(), which returns all the entries in a table.  findstart() would return keys that start with the given string, findend() would return keys that end with the given string, and findregex() would use a regular expression.  Or maybe we just have some simple wildcard syntax. 

Keep it simple.

I recommend we put this api under "/api/db"



