# lyoj

lyoj is the an online judge system that can be built in linux with any architectures.

------

## Features

1. Multi Thread is not supported.
2. Use JSON to transfor data.
3. MySQL/MariaDB is supported.
4. Use C++ to develop. Loop listening to the database.
5. Can not be run behind the Bash.
6. Special Judge in LimonLime is supported, "testlib.h" is not supported.
7. Costomize Special Judge Template is also supported.
8. Wait to update......

## Branch

Default branch: The newest stable version of the lyoj.

"vxxx-stable" branch: All of the stable version.

"vxxx-beta" branch: All of the beta version that released.

"dev" branch: Developing version, may cause some problems.

We recommend you to choose the default branch.

If you want to new features earlier than stable version , you can choose beta version.

Do not use dev branch!

## Build

The judge.cpp is written for Linux. Windows & MacOS are not supported.

### Dependences

1. Libraries: libmysqlclient-dev libjsoncpp-dev
2. Database: mysql-server mysql-client/mariadb-server mariadb-client
3. Necessities: php g++ nginx/apache
4. Selective Packages: gcc default-jre python2 python3

### Build Automatically

1. Install all the dependences.
2. Get source file from the Github.
3. Enter the main source directory.
4. Compile judgemgr.cpp with command below:
```bash
$ g++ judgemgr.cpp -o /usr/bin/judgemgr -lmysqlclient -O2 -std=c++14
```
5. Build the main Judge Service with command below:
```bash
$ judgemgr build
```

### Build Manually

1. Install all the dependences.
2. Get source file from Github.
3. Initialize MySQL/MariaDB database.
4. Compile the main judge program
5. Compile the Special Judge Template in the folder "./spjtemp"
6. Configure in file "./config.json" & "./web/config.php"
7. Configure your website, the root folder is "./web/"
8. Open your website, register an admin account.
9. GIve your admin account the highest permission in the database.
10. Open the main judge service.
11. Add some problem in the system, and enjoy the online judge.

## Upgrade

If you build your application manually, please backup your data(include database and main directory data), and build it again.

If you build your application with judge manager, you can upgrade it with the command below:
```bash
$ judgemgr upgrade 
```

## "judgemgr"

judgemgr(Judge Manager), a application help user to control their judge service and their web application. Here are its usage(You can view it by using "judgemgr help" command):

```bash
Usage: judgemgr <command>

Commands:
  build: Build the judge service.
  compile: Re-compile all the necessary executable programs.
  start: Start judge service.
  stop: Stop judge service.
  restart: Restart judge service.
  status: View judge service status.
  output: View output information of judge service.
Here are the command waited to develop:
  upgrade: Upgrade your judge service.
  backup: Backup your all data to a zip file(need library zip)
  restore: Restore your all data from a zip file(need library zip)
```

## Feedback

If you find some problems when use it, you can report it to the [issues]().

If you want to report some problems, you must make sure you have follow the rules below:

1. You must make sure you are not using dev branch. If you report the problem but you are using dev branch, We will close the problem without any reasons.
2. You must follow this format:

````markdown
### What version are you using?

### What is your machine configure?

### What problems do you find?

### How do you find it?

### Paste your log file here: 
/var/log/judge/error.log: 
```

```
/var/log/judge/info.log:
```

```
````

If you want to make the contribution to this project. You can clone this repository and make some changes, and then pull request it to the github.