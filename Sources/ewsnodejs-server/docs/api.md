# Open Speech Platform Server API

All URIs are relative to *http://localhost:5000/api*

Documents the API implemented by the Embedded Web Server.

Currently this only applies to the nodejs-based server.

---

## Requests

### Param
Method | HTTP request | Description
--- | --- | ---
**GET** | [/param/amplification](param/GETamplification.md) | Gets all amplification profiles
**POST** | [/param/amplification](param/POSTamplification.md) | Saves amplification parameters to the database
**POST** | [/param/getparam](param/POSTgetparam.md) | Gets RT-MHA parameters
**POST** | [/param/setparam](param/POSTsetparam.md) | Sets RT-MHA parameters

### Researcher
Method | HTTP request | Description
--- | --- | ---
**GET** | [/researcher](researcher/GETresearcher.md) | Get list of researchers
**GET** | [/researcher/audiopath](researcher/GETaudiopath.md) | Returns the absolute path to the audio files
**POST** | [/researcher/delete](researcher/POSTdelete.md) | Delete a file
**GET** | [/researcher/filenames](researcher/GETfilenames.md) | Get list of files and transcripts
**POST** | [/researcher/jsonfile](researcher/POSTjsonfile.md) | Get the contents of a json file
**POST** | [/researcher/login](researcher/POSTlogin.md) | Researcher Login
**POST** | [/researcher/signup](researcher/POSTsignup.md) | Create new account
**POST** | [/researcher/upload](researcher/POSTupload.md) | Upload a file
**GET** | [/researcher/UserAssessmentConfig](researcher/GETUserAssessmentConfig.md) | Returns the contents of the UserAssessmentConfig file.
**GET** | [/researcher/4AFCConfig](researcher/GET4AFCConfig.md) | Returns the contents of the 4AFCConfig file.

### Listener
Method | HTTP request | Description
--- | --- | ---
**GET** | [/listener](listener/GETlistener.md) | Gets the list of listeners for a researcher
**POST** | [/listener/addlog](listener/POSTaddlog.md) | Add or modify the JSON userLog
**POST** | [/listener/delete](listener/POSTdelete.md) | Deletes a listener
**GET** | [/listener/getparameters](listener/GETgetparameters.md) | Gets the preferred parameters for a user
**POST** | [/listener/login](listener/POSTlogin.md) | Gets login information for a listener
**POST** | [/listener/password](listener/POSTpassword.md) | Changes the listener password
**POST** | [/listener/signup](listener/POSTsignup.md) | Create a new listener account
**POST** | [/listener/updateparameters](listener/POSTupdateparameters.md) | Update the preferred parameters for the user

### Key-Value Tables
Method | HTTP request | Description
--- | --- | ---
**GET** | [/db/:table](db/GETkeyval.md) | Get all keys, or the corresponding value for a key in the given table
**POST** | [/db/:table](db/POSTkeyval.md) | Create or update a key-value entry in the given table
**DELETE** | [/db/:table](db/DELETEkeyval.md) | Delete a key-value entry in the given table
**POST** | [/db/table/create/:table](db/POSTkeyvaltable.md) | Create a new key-value table
**DELETE** | [/db/table/delete/:table](db/DELETEkeyvaltable.md) | Delete a key-value table

---

## Issues

1. We need to clarify the server file scheme.  Filenames must be passed around relative to this scheme.  Absolute pathnames must be removed.
2. TESTING.  All APIs must be tested. We will implement the tests with pytest scripts.  Databases should be tested through this API.
3. Consistency. "researcher" vs "researcherID", "listener" vs "listenerID".  /researcher/delete delete a FILE; /listener/delete delete a listener.  How do researchers get deleted?

## Adding New API Calls

1. Ask before you do it.  Post on Slack.
2. APIs should do one simple thing and do it securely.  We don't implement APIs that delete random files or execute user-supplied commands.
3. APIs shoule NEVER crash.  Test them.  Pass in bad parameters and see what happens.

## Proposed File Scheme

For testing we may have multiple copies of the server installed and multiple copies of the media and config files.  


OSP_HOME - env variable. Base directory for OSP installations.  
* OSP_HOME/bin
* OSP_HOME/lib
* OSP_HOME/ews
* etc

OSP_MEDIA - env variable.  Defaults to OSP_HOME/media if OSP_HOME is defined. Otherwise we use the ews/media directory (which needs to be created).  This is where audio and video clips get stored.

* OSP_MEDIA/{appname} - where web apps and demos should put their files.  These files will be checked into the ews server repo under "media".

The RT-MHA (osp) process will also use these env variables to locate audio files for playback.

## To Do
