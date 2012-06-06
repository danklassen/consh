consh
=====

*I would strongly advise not using this yet as it's no where near production ready*

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

* ```clone``` this repo
* ```symlink``` (I know... there should be a better way) the consh script to your bin folder (ie ~/bin/ or /usr/local/bin)
* ```cd``` to your local c5 install public_html folder
* run ```consh config``` to create a config file

that's it! You should now be able to run consh commands




