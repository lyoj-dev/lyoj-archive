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
    stream=popen((string(cmd)+" 2>&1").c_str(),"r");
    fread(buf,sizeof(char),sizeof(buf),stream);
    pclose(stream);return buf;
}
bool exec(string hint,string cmd) {
    cout<<" * "<<hint<<endl;
    int tmp=system(cmd.c_str());
    sleep(1);return tmp;
}
int main(int argc,char * argv[]) {
    if (argc<=1) {
        cout<<"judgemgr: missing operand"<<endl;
        cout<<"Try 'judgemgr help' for more information."<<endl;
        return 0;
    } string cmd=string(argv[1]);
    if (cmd=="build") {
        string mysqladdr,mysqlname,mysqlpw,mysqldb;int mysqlport;
        cout<<"Opening Service..."<<endl;cout<<flush;
        int tmp=system("service mysql start");
        tmp=system("service nginx start");
        tmp=system("service php$(php -r \"echo explode('.',phpversion())[0].'.'.explode('.',phpversion())[1];\")-fpm start");
        
        
        
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
        exec("Deleting Backup Main Directory... (/etc/judge.back/)","rm /etc/judge.back -r");
        exec("Deleting Backup Log Directory... (/var/log/judge.back/)","rm /var/log/judge.back -r");
        exec("Backup Exist Main Directory... (/etc/judge/ -> /etc/judge.back/)","mv /etc/judge /etc/judge.back");
        exec("Backup Log Directory... (/var/log/judge/ -> /var/log/judge.back)","mv /var/log/judge /var/log/judge.back");
        exec("Backup MySQL/MariaDB Data... ("+mysqldb+" > ./"+mysqldb+".sql)",
        "mysqldump -h"+mysqladdr+" --port="+to_string(mysqlport)+" -u"+mysqlname+" -p"+mysqlpw+" "+mysqldb+" > ./"+mysqldb+".sql");
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
        exec("Copy Website Files... (./web/* -> /etc/judge/web/)","cp ./web/* /etc/judge/web/ -r");
        exec("Copy config.json... (./config.json -> /etc/judge/config.json)","cp ./config.json /etc/judge/config.json");
        
        
        
        cout<<endl<<"Compiling..."<<endl;
        exec("Compiling Special Judge...","g++ ./spjtemp/1.cpp -o /etc/judge/spjtemp/1");
        exec("Compiling Main Judge Service...","g++ ./judge.cpp -O2 -o /usr/bin/judge -lmysqlclient -ljsoncpp");
        cout<<endl<<"Success!"<<endl;
        return 0;
    }
    if (cmd=="start") {
        cout<<" * Starting Judge Service... ";cout<<flush;
        int ret=system("nohup judge >/dev/null 2>&1 &");sleep(1);
        string tmp=system2("ps -ef|grep judge|grep -v grep|grep -v judgemgr");
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
        int ret=system("nohup judge >/dev/null 2>&1 &");sleep(1);
        tmp=system2("ps -ef|grep judge|grep -v grep|grep -v judgemgr");
        if (tmp=="") cout<<"Failed!"<<endl;
        else cout<<"Success!"<<endl;
        return 0;
    }
    if (cmd=="output") {
        int tmp=system("tail -f /var/log/judge/info.log");
        return 0;
    }
    if (cmd=="help") {
        cout<<"Usage: judgemgr <command>"<<endl;
        cout<<endl;
        cout<<"Commands:"<<endl;
        cout<<"  build: Build the judge service."<<endl;
        cout<<"  compile: Re-compile all the necessary executable programs."<<endl;
        cout<<"  start: Start judge service."<<endl;
        cout<<"  stop: Stop judge service."<<endl;
        cout<<"  restart: Restart judge service."<<endl;
        // cout<<"  check: Check necessary dependences."<<endl;
        cout<<"  status: View judge service status."<<endl;
        cout<<"  output: View output information of judge service."<<endl;
        cout<<endl;
        return 0;
    }
    cout<<"judgemgr: unknown command '"<<cmd<<"'"<<endl;
    cout<<"Try 'judgemgr help' for more information."<<endl;
    return 0;
}