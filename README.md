# synoAuth
Synology Authentification PHP API

This project provides (for moment):
- A php class and API in order to get active login basic information from synotoken value
- A Javascript class in order to get synoToken value of the active session

## Dependency
- A Synology Disk Station
- The javascript uses JQuery but call the google hosted library (neither required localy nor included in package) 

## Setup
Go to [a Releases](https://github.com/kavod/synoAuth/releases) to get the lasted synoAuth.spk
Use package Center of DSM in order to install
You may have to adapt the "Trust Level" to "Any publisher" since this package is not yet "validated" by Synology Inc.
It's done

## Build from sources
Just execute ```make``` in the folder in order to build ```synoAuth.spk```

## PHP API class
By calling ```/webman/3rdparty/synoAuth/``` with synoToken querystring (with GET or POST) you get a JSON response with user data (for moment: username & usergroups) if, and only if:
- you browser has an logged in and active session in DSM
- the provided synoToken matches with IP Address and auth Cookie id
 
## Javascript class
Since PHP API needs synoToken, synoAuth provides a javascript class in order to get the active session synoToken.
This class also provide the following customEvents in order to notify $(document) about session verification process:
- ```synoToken``` event is raised when synoToken determination is done
- ```login``` event is raised when userdata and usergroups determination is done

## Usage example
A simple script is available in ```/webman/3rdparty/synoAuth/test.php```
