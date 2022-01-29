#include<bits/stdc++.h>
#include<unistd.h>
#include<stdlib.h>
#include<sys/resource.h>
#include<sys/wait.h>
#include<mysql/mysql.h>
#include"mysqld.h"
using namespace std;
string system2(const char* cmd) {
    FILE *stream,*wstream;
    char buf[1024*1024]; 
    memset(buf,'\0',sizeof(buf));
    stream=popen(("sudo "+string(cmd)+" 2>&1").c_str(),"r");
    int k=fread(buf,sizeof(char),sizeof(buf),stream);
    pclose(stream);return buf;
}
bool checkCommandExist(const char* cmd) {
    string ret=system2(("sudo command -v "+string(cmd)).c_str());
    return ret!="";
}
bool checkHeaderExist(const char* header,const char* cmd) {
    string output_code="#include<"+string(header)+">\nint main(){}";
    ofstream fout("/tmp/main.cpp");
    fout<<output_code;fout.close();
    string ret=system2(("g++ /tmp/main.cpp -o /tmp/main "+string(cmd)).c_str());
    unlink("/tmp/main.cpp");if (ret=="") unlink("/tmp/main");
    return ret=="";
}
bool exec(string hint,string cmd) {
    cout<<" * "<<hint<<endl;
    int tmp=system(("sudo "+cmd).c_str());
    sleep(1);return tmp;
}
int main(int argc,char * argv[]) {
    if (argc<=1) {
        cout<<"judgemgr: missing operand"<<endl;
        cout<<"Try 'judgemgr help' for more information."<<endl;
        return 0;
    } string cmd=string(argv[1]);
    char tmp[1024]="";char* kkkk=getcwd(tmp,1024);string path=tmp;
    if (cmd=="build") {
        string mysqladdr,mysqlname,mysqlpw,mysqldb;int mysqlport;
        cout<<"Opening Service..."<<endl;cout<<flush;
        
        
        cout<<endl<<"Collecting Datas..."<<endl;
        cout<<" * Login to the MySQL/MariaDB Server: "<<endl;
        cout<<"address: ";cin>>mysqladdr;
        cout<<"port: ";cin>>mysqlport;
        cout<<"username: ";cin>>mysqlname;
        cout<<"password: ";cin>>mysqlpw;
        cout<<"database: ";cin>>mysqldb;
        cout<<" * Connecting...";
        mysqld conn=mysqli_connect(mysqladdr.c_str(),mysqlname.c_str(),
            mysqlpw.c_str(),mysqldb.c_str(),mysqlport);
        if (!conn) {
            cout<<endl<<"Failed to Connect MySQL/MariaDB Server!"<<endl;
            cout<<mysqli_error(conn)<<endl;
            return 0;
        } cout<<" Success!"<<endl;



        cout<<endl<<"Clearing..."<<endl;
        // exec("Deleting Backup Main Directory... (/etc/judge.back/)","rm /etc/judge.back -r");
        // exec("Deleting Backup Log Directory... (/var/log/judge.back/)","rm /var/log/judge.back -r");
        // exec("Backup Exist Main Directory... (/etc/judge/ -> /etc/judge.back/)","mv /etc/judge /etc/judge.back");
        // exec("Backup Log Directory... (/var/log/judge/ -> /var/log/judge.back)","mv /var/log/judge /var/log/judge.back");
        // exec("Backup MySQL/MariaDB Data... ("+mysqldb+" > ./"+mysqldb+".sql)",
        // "mysqldump -h"+mysqladdr+" --port="+to_string(mysqlport)+" -u"+mysqlname+" -p"+mysqlpw+" "+mysqldb+" > ./"+mysqldb+".sql");
        exec("Deleting Exist Main Directory... (/etc/judge/)","rm /etc/judge -r");
        exec("Deleting Log Directory... (/var/log/judge/)","rm /var/log/judge -r");
        exec("Deleting MySQL/MariaDB Data...","");
        mysqld row,result=mysqli_query(conn,("select concat('drop table ',table_name,';')"+
        (string)"from information_schema.TABLES where table_schema='"+mysqldb+"'").c_str());
        while (row=mysqli_fetch_assoc(result)) {
            string exe=row["concat('drop table ',table_name,';')"];
            mysqli_query(conn,exe.c_str());
        }



        cout<<endl<<"Solving..."<<endl;
        exec("Initialize MySQL/MariaDB Database...",
        "mysql -h"+mysqladdr+" --port="+to_string(mysqlport)+" -u"+mysqlname+" -p"+mysqlpw+" "+mysqldb+" -e'source ./init.sql'");
        exec("Creating Log Directory... (/var/log/judge/)","mkdir /var/log/judge/");
        exec("Creating Configure Directory... (/etc/judge/)","mkdir /etc/judge/");
        exec("Creating Temporary Directory... (/etc/judge/tmp/)","mkdir /etc/judge/tmp");
        exec("Creating Special Judge Template Directory... (/etc/judge/spjtemp/)","mkdir /etc/judge/spjtemp");
        exec("Creating Problem Directory... (/etc/judge/problem/)","mkdir /etc/judge/problem");
        exec("Creating Website Root Directory... (/etc/judge/web/)","mkdir /etc/judge/web");
        exec("Copy config.json... (./config.json -> /etc/judge/config.json)","cp ./config.json /etc/judge/config.json");
        exec("Copy Special Judge Template... (./spjtemp/* -> /etc/judge/spjtemp/)","cp ./spjtemp/* /etc/judge/spjtemp -r");
        exec("Copy Website Files... (./web/* -> /etc/judge/web/)","cp ./web/* /etc/judge/web/ -r");
        exec("Copy judge.cpp... (./judge.cpp -> /etc/judge/judge.cpp)","cp ./judge.cpp /etc/judge/judge.cpp");
        exec("Copy mysqld.h... (./mysqld.h -> /etc/judge/mysqld.h)","cp ./mysqld.h /etc/judge/mysqld.h");
        
        
        cout<<endl<<"Compiling..."<<endl;
        exec("Compiling Special Judge...","g++ /etc/judge/spjtemp/1.cpp -O2 -std=c++14 -o /etc/judge/spjtemp/1");
        exec("Compiling Main Judge Service...","g++ /etc/judge/judge.cpp -O2 -std=c++14 -o /usr/bin/judge -lmysqlclient -ljsoncpp");
        cout<<endl<<"Success!"<<endl;
        return 0;
    }
    if (cmd=="start") {
	cout<<" * Starting MySQL/MariaDB Database... ";cout<<flush;
	string kkk=system2("service mysql start");sleep(1);
	string tmp=system2("ps -ef|grep mysqld|grep -v grep");
	if (tmp=="") {cout<<"Failed!"<<endl;return 0;}
	else cout<<"Success!"<<endl;
        cout<<" * Starting Judge Service... ";cout<<flush;
        int ret=system("ulimit -s unlimited");
        ret=system("sudo nohup judge >/dev/null 2>&1 &");sleep(1);
        tmp=system2("ps -ef|grep judge|grep -v grep|grep -v judgemgr");
        if (tmp=="") cout<<"Failed!"<<endl;
        else cout<<"Success!"<<endl;
        return 0;
    }
    if (cmd=="compile") {
        exec("Compiling Special Judge...","g++ ./spjtemp/1.cpp -o /etc/judge/spjtemp/1");
        exec("Compiling Main Judge Service...","g++ ./judge.cpp -O2 -o /usr/bin/judge -lmysqlclient -ljsoncpp");
        cout<<endl<<"Success!"<<endl;
        return 0;
    }
    if (cmd=="stop") {
        cout<<" * Stoping Judge Service... ";cout<<flush;
        string tmp=system2("killall judge");sleep(1);
        tmp=system2("ps -ef|grep judge|grep -v grep|grep -v judgemgr");
        if (tmp=="") cout<<"Success!"<<endl;
        else cout<<"Failed!"<<endl;cout<<flush;
        return 0;
    }
    if (cmd=="status") {
        string tmp=system2("ps -ef|grep judge|grep -v grep|grep -v judgemgr");
        if (tmp=="") cout<<" * Judge Service is not running!"<<endl;
        else cout<<" * Judge Service is running!"<<endl;cout<<flush;
        return 0;
    }
    if (cmd=="restart") {
        cout<<" * Stoping Judge Service... ";cout<<flush;
        string tmp=system2("killall judge");sleep(1);
        tmp=system2("ps -ef|grep judge|grep -v grep|grep -v judgemgr");
        if (tmp=="") cout<<"Success!"<<endl;
        else {cout<<"Failed!"<<endl;return 0;}cout<<flush;
        cout<<" * Starting Judge Service... ";cout<<flush;
        int ret=system("sudo nohup judge >/dev/null 2>&1 &");sleep(1);
        tmp=system2("ps -ef|grep judge|grep -v grep|grep -v judgemgr");
        if (tmp=="") cout<<"Failed!"<<endl;
        else cout<<"Success!"<<endl;
        return 0;
    }
    if (cmd=="output") {
        int tmp=system("sudo tail -f /var/log/judge/info.log");
        return 0;
    }
    if (cmd=="check") {
        cout<<"Applications: "<<endl;
        cout<<" * Checking MySQL/MariaDB Database... ";
        if (!checkCommandExist("mysql")) {
            cout<<endl<<"We can not find your MySQL/MariaDB Database."<<endl;
            cout<<"Please check whether you have installed it!"<<endl;
            return 0;
        } else cout<<"Found!"<<endl;
        cout<<" * Checking Nginx Web Server... ";
        if (!checkCommandExist("nginx")) {
            cout<<endl<<"We can not find your Nginx Web Server."<<endl;
            cout<<"Please check whether you have installed it!"<<endl;
            return 0;
        } else cout<<"Found!"<<endl;
        cout<<" * Checking php-cli Interpreter... ";
        if (!checkCommandExist("php")) {
            cout<<endl<<"We can not find your php-cli Interpreter."<<endl;
            cout<<"Please check whether you have installed it!"<<endl;
            return 0;
        } else cout<<"Found!"<<endl;
        cout<<" * Checking Zip Compressor... ";
        if (!checkCommandExist("zip")) {
            cout<<endl<<"We can not find your Zip Compressor."<<endl;
            cout<<"You may cannot use backup function!"<<endl;
        } else cout<<"Found!"<<endl;



        cout<<endl<<"Dependence:"<<endl;
        cout<<" * Checking libmysqlclient-dev... ";
        if (!checkHeaderExist("mysql/mysql.h","-lmysqlclient")) {
            cout<<endl<<"We can not find libmysqlclient-dev."<<endl;
            cout<<"Please check whether you have installed it!"<<endl;
            return 0;
        } else cout<<"Found!"<<endl;
        cout<<" * Checking libjsoncpp-dev... ";
        if (!checkHeaderExist("jsoncpp/json/json.h","-ljsoncpp")) {
            cout<<endl<<"We can not find libjsoncpp-dev."<<endl;
            cout<<"Please check whether you have installed it!"<<endl;
            return 0;
        } else cout<<"Found!"<<endl;



        cout<<endl<<"Success!"<<endl;
        return 0;
    }
    if (cmd=="backup") {
        string mysqladdr,mysqlname,mysqlpw,mysqldb;int mysqlport;
        cout<<"Collecting Datas..."<<endl;
        cout<<" * Login to the MySQL/MariaDB Server: "<<endl;
        cout<<"address: ";cin>>mysqladdr;
        cout<<"port: ";cin>>mysqlport;
        cout<<"username: ";cin>>mysqlname;
        cout<<"password: ";cin>>mysqlpw;
        cout<<"database: ";cin>>mysqldb;
        cout<<" * Connecting...";
        mysqld conn=mysqli_connect(mysqladdr.c_str(),mysqlname.c_str(),
            mysqlpw.c_str(),mysqldb.c_str(),mysqlport);
        if (!conn) {
            cout<<endl<<"Failed to Connect MySQL/MariaDB Server!"<<endl;
            cout<<mysqli_error(conn)<<endl;
            return 0;
        } cout<<" Success!"<<endl;



        cout<<endl<<"Backuping..."<<endl;
        exec("Creating Temporary Directory... (/tmp/judge/)","mkdir /tmp/judge");
        exec("Copying Main Directory... (/etc/judge/* -> /tmp/judge/)","cp /etc/judge/* /tmp/judge -r");
        exec("Removing tmp Diretory... (/tmp/judge/tmp/)","rm /tmp/judge/tmp -r");
        exec("Creating Log Directory... (/tmp/judge/log/)","mkdir /tmp/judge/log");
        exec("Copying Log Directory... (/var/log/judge/* -> /tmp/judge/log/)","cp /var/log/judge/* /tmp/judge/log -r");
        exec(("Backup MySQL/MariaDB Database... ("+mysqldb+" -> /tmp/judge/data.sql)").c_str(),
        ("mysqldump -h"+mysqladdr+" --port="+to_string(mysqlport)+" -u"+mysqlname+" -p"+mysqlpw+" "+mysqldb+" > /tmp/judge/data.sql").c_str());
        int ret=chdir("/tmp/judge");
        exec("Compressing... (/tmp/judge/* -> ./judge.zip)",("zip -q "+path+"/judge.zip ./* -r").c_str());
        ret=chdir(path.c_str());
        exec("Removing Temporary Files... (/tmp/judge/)","rm /tmp/judge -r");
        cout<<"Success!"<<endl;
        return 0;
    }
    if (cmd=="restore") {
        string mysqladdr,mysqlname,mysqlpw,mysqldb;int mysqlport;
        cout<<"Collecting Datas..."<<endl;
        cout<<" * Login to the MySQL/MariaDB Server: "<<endl;
        cout<<"address: ";cin>>mysqladdr;
        cout<<"port: ";cin>>mysqlport;
        cout<<"username: ";cin>>mysqlname;
        cout<<"password: ";cin>>mysqlpw;
        cout<<"database: ";cin>>mysqldb;
        cout<<" * Connecting...";
        mysqld conn=mysqli_connect(mysqladdr.c_str(),mysqlname.c_str(),
            mysqlpw.c_str(),mysqldb.c_str(),mysqlport);
        if (!conn) {
            cout<<endl<<"Failed to Connect MySQL/MariaDB Server!"<<endl;
            cout<<mysqli_error(conn)<<endl;
            return 0;
        } cout<<" Success!"<<endl;



        cout<<endl<<"Restoring..."<<endl;
        exec("Extracting Compressed Files... (./judge.zip -> /etc/judge/)","unzip -q -o judge.zip -d /etc/judge/");
        exec("Copying Log Directory... (/etc/judge/log/* -> /var/log/judge/)","cp /etc/judge/log/* /var/log/judge -r");
        exec("Removing Log Directory... (/etc/judge/log/)","rm /etc/judge/log -r");
        exec("Deleting MySQL/MariaDB Data...","");
        mysqld row,result=mysqli_query(conn,("select concat('drop table ',table_name,';')"+
        (string)"from information_schema.TABLES where table_schema='"+mysqldb+"'").c_str());
        while (row=mysqli_fetch_assoc(result)) {
            string exe=row["concat('drop table ',table_name,';')"];
            mysqli_query(conn,exe.c_str());
        }
        exec(("Restoring MySQL/MariaDB Database... (/etc/judge/data.sql -> "+mysqldb+")").c_str(),
        ("mysql -h"+mysqladdr+" --port="+to_string(mysqlport)+" -u"+mysqlname+" -p"+mysqlpw+" "+mysqldb+" -e'source /etc/judge/data.sql'").c_str());
        cout<<"Success!"<<endl;
        return 0;
    }
    if (cmd=="help") {
        cout<<"Usage: judgemgr <command>"<<endl;
        cout<<endl;
        cout<<"Online judger manager can help to manage background judge service."<<endl;
        cout<<"It will be more convenience to build it by judgemgr."<<endl;
        cout<<endl;
        cout<<"Commands:"<<endl;
        cout<<"  build: Build the judge service."<<endl;
        cout<<"  compile: Re-compile all the necessary executable programs."<<endl;
        cout<<"  start: Start judge service."<<endl;
        cout<<"  stop: Stop judge service."<<endl;
        cout<<"  restart: Restart judge service."<<endl;
        cout<<"  check: Check necessary dependences."<<endl;
        cout<<"  status: View judge service status."<<endl;
        cout<<"  output: View output information of judge service."<<endl;
        cout<<"  backup: Backup your all data to a zip file(need library zip)"<<endl;
        cout<<"  restore: Restore your all data from a zip file(need library zip)"<<endl;
        cout<<endl;
        cout<<"                           This manager tool has Super Cow Powers."<<endl;
        return 0;
    }
    cout<<"judgemgr: unknown command '"<<cmd<<"'"<<endl;
    cout<<"Try 'judgemgr help' for more information."<<endl;
    return 0;
}
