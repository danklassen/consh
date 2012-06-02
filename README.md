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




