consh
=====

At the moment this has only been tested on an ubuntu workstation connecting to an ubuntu server. As far as I know there isn't any reason it shouldn't work on any *nix setup though.

concrete5 shell scripts

This is the base of a set of concrete5 scripts to ease the management of dev and live environments. At the moment the only functionality is pulling a remote database, and rsyncing the files directory.

general syntax is similiar to rake tasks.

```consh db:pull```

```consh files:pull```

```consh backup:db MyBackup```

```consh restore:remote:db 1362710294-MyBackup.sql```

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
Current Commands:
Backup:DB                   Backup the remote database locally
Config                      Create a configuration file
DB:Connect                  Connect to the local db
DB:Pull                     Pull remote database
DB:Push                     Push local database
Deploy                      Deploy the code from origin master to the remote server
Files:Pull                  Pull remote files locally
Files:Push                  Push local files to the remote server
Generate:Block              Generates a skeleton block
Generate:Block:Override     Copies the controller for a core block
Generate:Block:Template     Generates a template for an existing block
Generate:Model              Generates a skeleton model
Generate:Pagetype           Generates a pagetype controller
Generate:Table              Generates a db.xml file for the passed in attributes
Generate:Theme              Generates a skeleton theme
Git:Pull                    Pull remote repo locally
Git:Push                    Push local git repo to remote
Help                        Display help about a command
Pull                        Pull a remote database and files
Push                        Push local git repo to remote and then deploy
Restore:DB                  Restore a db backup to the local server
Restore:Remote:DB           Restore a db backup to the remote server
Site:Push                   Push local site to the remote server (files and database)
Version                     Displays the consh version
```

Hooks
=====

Hooks can be fired after key points of the execution. I was needing a way to pull files from a separate file location on the server into the main files folder and update the file storage location in the database for one project.

Hooks should either be a Closure or in a file which resides in <project_folder>/consh/hook/hook_class_name.php

A simple hook may look something like the following

```
<?php
//filename: /home/user/workspace/project_name/consh/hook/pull.php
class HookPull
{
    // sync files & then update the database to use a file location which works locally
    public static function post_pull()
    {
        $to_dir = C5_DIR . "/" . "files/";
        $output = shell_exec('rsync -az '.REMOTE_USER.'@'.REMOTE_HOST.':~/secure_files/ '. $to_dir);
        shell_exec('chmod 777 files/');
        $db = new LocalDB();
        $res = $db->execute('Update FileStorageLocations set fslDirectory="/home/user/workspace/project_name/files"');
        output("Sync of secure files and update of storage location complete", 'success');
    }
}
```

In the consh settings file add something like


```
Hook::register('after_pull', null, 'HookPull', 'post_pull');
```

Generating a Block
==================

run something like:

```
consh Generate:Block my_block_name bID:id title:string body:text page_id:page image_id:image file_id:file
```

to create a block in the folder /blocks/my_block_name. The block will be named 'My Block Name' and have an add / edit interface for the passed in fields


Configuration Options
=====================

There are a number of required and optional configuration values which can be used to customize how consh interacts with the remote site.

## SSH / Host connection Information

### REMOTE_HOST

The remote host that the site runs on

### REMOTE_USER

The username on the remote system to ssh in as

### REMOTE_HOME_PATH

The full path to the home folder of the website

### REMOTE_DOC_ROOT

Where the document root is on the remote server

### REMOTE_PASS

Currently this is not used as only ssh-key based authentication is used

### REMOTE_USE_KEY

Currently not used and is always true

### REMOTE_PUB_KEY_PATH

This should point to your ~/.ssh/id_rsa.pub file path

### REMOTE_PRIV_KEY_PATH

This should point to your ~/.ssh/id_rsa file

### REMOTE_PORT

If you are running ssh on a port besides 22 you will want to update this

## Remote Database Connection Details

### REMOTE_DB_HOST

The host the remote website uses to connect to the database (usually localhost)

### REMOTE_DB_USER

The username the remote website uses to connect to the database

### REMOTE_DB_PASS

The password the remote website uses to connect to the database

### REMOTE_DB_NAME

The database name the remote website uses

## Git / Deployment information

### DEPLOY_BRANCH

Which branch to use when deploying (use master if you're not sure)

### DEPLOY_REMOTE

Which remote to use when deploying (use origin if you're not sure)

### DEPLOY_STRATEGY

Currently only ```git_pull``` is supported. Future plans for a strategy more similar to capistrano's multiple versions, etc are in place.

## Others

### LOCAL_BACKUP_DIR

Where to store the contents of the ```consh backup:db``` command
