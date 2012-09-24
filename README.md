consh
=====

At the moment this has only been tested on an ubuntu workstation connecting to an ubuntu server. As far as I know there isn't any reason it shouldn't work on any *nix setup though.

concrete5 shell scripts

This is the base of a set of concrete5 scripts to ease the management of dev and live environments. At the moment the only functionality is pulling a remote database, and rsyncing the files directory.

general syntax is similiar to rake tasks.

```consh db:pull```

```consh files:pull```

Commands can be added in to the commands folder and must extend the base command object. When a user runs ```consh fun:task```
the method ```FunTask->run()``` will be called. FunTask should be defined in ```commands/fun/task.php```

setup
=====

You will need php's ssh2 library installed. For ubuntu: ```sudo apt-get install libssh2-php``` Other distos may have something similiar or you can install it using PECL

* ```git clone``` this repo
* ```symlink``` (I know... there should be a better way) the consh script to your bin folder (ie ~/bin/ or /usr/local/bin/)
* ```cd``` to your local c5 install public_html folder
* run ```consh config``` to create a config file
* running ```consh``` will show a list of currently implemented commands

that's it! You should now be able to run consh commands

Tasks
=====

The current list of tasks avilable are:
```
Version             Displays the consh version
Files:Pull          Pull remote files locally
DB:Push             Push local database
DB:Pull             Pull remote database
Generate:Table      Generates a db.xml file for the passed in attributes
Generate:Model      Generates a skeleton model
Pull                Pull a remote database and files
Config              Create a configuration file
```






