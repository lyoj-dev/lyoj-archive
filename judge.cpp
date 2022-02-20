#include<bits/stdc++.h>
#include<unistd.h>
#include<stdlib.h>
#include<sys/resource.h>
#include<sys/wait.h>
#include<mysql/mysql.h>
#include<jsoncpp/json/json.h>
#include"mysqld.h"
using namespace std;

// ****************************************************
// Class Name: None
// Class Module: None
// Class Features: Single Functions
// ****************************************************

// Convert String to Integer
int StringToInt(string x) {
    int res=0;
    for (int i=0;i<x.size();i++) 
	res*=10,res+=x[i]-'0';
    return res;
}

// Convert Integer to String (Like Function to_string())
string IntToString(int x) {
	if (x==0) return "0";
    char res[101]="";int k=-1;
    while (x) {
        res[++k]=x%10+'0';
		x/=10;
    }
    reverse(res,res+k+1);
    return res;
}

// Replace A to B in String
string str_replace(const char* from,const char* to,const char* source) {
	string result=source;
	int st_place=0,where=result.find(from,st_place);
	while (where!=string::npos) {
		result.replace(where,((string)from).size(),to);
		st_place=where+((string)to).size();
		where=result.find(from,st_place);
	} return result;
}

// Get The Absolute Path
string getpath(const char* path) {
	char res[100010]="";
	if(realpath(path,res)) return res;
	else return "/";
}

// System Function Advanced Mode
int system2(const char* cmd,string& res) {
    FILE *stream,*wstream;
    char buf[1024*1024]; 
    memset(buf,'\0',sizeof(buf));
    stream=popen(("sudo "+string(cmd)+" 2>&1").c_str(),"r");
    int k=fread(buf,sizeof(char),sizeof(buf),stream);
	res=string(buf);
    return pclose(stream);
}




// ****************************************************
// Class Name: System Function
// Class Module: Main
// Class Features: System Features
// ****************************************************

// Basic Variables
ofstream infoout,errorout;

// Format System Time
string getTime() {
	time_t timep;time(&timep);
    char tmp[1024]="";
    strftime(tmp, sizeof(tmp), "%Y-%m-%d %H:%M:%S",localtime(&timep));
    return tmp;
}

// Output Program Info
void return_info(const char* info,bool no_header=false) {
	infoout.open("/var/log/judge/info.log",ios::app);
	infoout<<(!no_header?getTime()+" [Info] "+info:info)<<endl;
	cout<<(!no_header?getTime()+" [Info] "+info:info)<<endl;
	infoout.close();
	return;
}

// Output Program Error
void return_error(const char* error,bool killed=true) {
	errorout.open("/var/log/judge/error.log",ios::app);
	errorout<<getTime()<<" [Error] "<<error<<endl<<endl;
	cout<<getTime()<<" [Error] "<<error<<endl<<endl;
	errorout.close();
	if (killed) exit(0);
}




// ****************************************************
// Class Name: Program Runner
// Class Module: Main
// Class Features: Running Program Limited some Resource
// ****************************************************

// Basic Variables
int runtime_error_state=0;
int runtime_error_reason=0;

// Signal Processor
void handler(int sig) {
    if (sig==SIGCHLD) {
        int status;
        pid_t pid=waitpid(-1, &status, WNOHANG);
		if (pid>0) {
			if (!WIFEXITED(status)) {
				runtime_error_state=1;
				runtime_error_reason=WTERMSIG(status);
			}
			else runtime_error_state=0;
		}
    }
}

// Resource Monitor
unsigned int get_proc_mem(unsigned int pid){
	char file_name[64]={0};
	FILE *fd;
	char line_buff[512]={0};
	sprintf(file_name,"/proc/%d/status",pid);
	fd=fopen(file_name,"r");
	if(nullptr==fd){
		return 0;
	}
	char name[64];
	int vmrss;
	for (int i=0; ;i++){
		if (kill(pid,0)!=0) return 0;
		char* res=fgets(line_buff,sizeof(line_buff),fd);
		sscanf(line_buff,"%s",name);
		if ((string)name=="VmRSS:") {
			sscanf(line_buff,"%s %d",name,&vmrss);
			fclose(fd);
			return vmrss;
		}
	}
}

// The Main Judger
int run_code(const char* cmd,int& times,int& memory,int time_limit,
int memory_limit,bool special_judge=false) {
	char* argv[1010]={NULL};char* command=const_cast<char*>(cmd);
	int idnow=-1;argv[++idnow]=strtok(command," ");
	while (argv[idnow]!=NULL) argv[++idnow]=strtok(NULL," ");
	cout<<string(argv[0])<<" "<<string(argv[1])<<endl;
	times=memory=0;signal(SIGCHLD,handler);
	pid_t executive=fork();
    if(executive<0) {
	    return_error("Failed to execute program!");
        return 1;
    }
    else if (executive==0) {
        execv(string(argv[0]).c_str(),argv);
		exit(0);
	}
    else{ 
		time_t st=clock();pid_t ret2=-1;
		int status=0;
		while (1) { 
			if (kill(executive,0)!=0) {
				if (runtime_error_state) {
					if (!special_judge) return_info("Time usage: 0ms, memory usage: 0kb");
					else return_info("SPJ Time usage: 0ms, memory usage: 0kb");
					times=0;memory=0;
					return 4;
				}
				if (!special_judge) return_info(("Time usage: "+IntToString(times)+"ms, memory usage: "+IntToString(memory)+"kb").c_str());
				else return_info(("SPJ Time usage: "+IntToString(times)+"ms, memory usage: "+IntToString(memory)+"kb").c_str());
				return 0;
			}
			int mem=get_proc_mem(executive);
			if (mem!=0) times=(clock()-st)*1000.0/CLOCKS_PER_SEC,memory=mem;
			if (mem>memory_limit) {
				if (!special_judge) return_info(("Time usage: "+IntToString(times)+"ms, memory usage: "+IntToString(memory)+"kb").c_str());
				else return_info(("SPJ Time usage: "+IntToString(times)+"ms, memory usage: "+IntToString(memory)+"kb").c_str());
				int res=system(("kill "+IntToString(executive)).c_str());
				return 3;
			}
			if (times>time_limit) {
				if (!special_judge) return_info(("Time usage: "+IntToString(times)+"ms, memory usage: "+IntToString(memory)+"kb").c_str());
				else return_info(("SPJ Time usage: "+IntToString(times)+"ms, memory usage: "+IntToString(memory)+"kb").c_str());
				int res=system(("kill "+IntToString(executive)).c_str());
				return 2;
			}
		}
    } 
	return 0;
}

// Signal Analyst
string analysis_reason(int reason) {
	switch (reason) {
		case 1:return "SIGHUP";break;
		case 2:return "SIGINT";break;
		case 3:return "SIGQUIT";break;
		case 4:return "SIGILL";break;
		case 5:return "SIGTRAP";break;
		case 6:return "SIGABRT";break;
		case 7:return "SIGBUS";break;
		case 8:return "SIGFPE";break;
		case 9:return "SIGKILL";break;
		case 10:return "SIGUSR1";break;
		case 11:return "SIGSEGV";break;
		case 12:return "SIGUSR2";break;
		case 13:return "SIGPIPE";break;
		case 14:return "SIGALRM";break;
		case 15:return "SIGTERM";break;
		case 16:return "SIGSTKFLT";break;
		case 17:return "SIGCHLD";break;
		case 18:return "SIGCONT";break;
		case 19:return "SIGSTOP";break;
		case 20:return "SIGTSTP";break;
		case 21:return "SIGTTIN";break;
		case 22:return "SIGTTOU";break;
		case 23:return "SIGURG";break;
		case 24:return "SIGXCPU";break;
		case 25:return "SIGXFSZ";break;
		case 26:return "SIGVTALRM";break;
		case 27:return "SIGPROF";break;
		case 28:return "SIGWINCH";break;
		case 29:return "SIGIO";break;
		case 30:return "SIGPWR";break;
		case 31:return "SIGSYS";break;
		case 34:return "SIGRTMIN";break;
		case 35:return "SIGRTMIN+1";break;
		case 36:return "SIGRTMIN+2";break;
		case 37:return "SIGRTMIN+3";break;
		case 38:return "SIGRTMIN+4";break;
		case 39:return "SIGRTMIN+5";break;
		case 40:return "SIGRTMIN+6";break;
		case 41:return "SIGRTMIN+7";break;
		case 42:return "SIGRTMIN+8";break;
		case 43:return "SIGRTMIN+9";break;
		case 44:return "SIGRTMIN+10";break;
		case 45:return "SIGRTMIN+11";break;
		case 46:return "SIGRTMIN+12";break;
		case 47:return "SIGRTMIN+13";break;
		case 48:return "SIGRTMIN+14";break;
		case 49:return "SIGRTMIN+15";break;
		case 50:return "SIGRTMAX-14";break;
		case 51:return "SIGRTMAX-13";break;
		case 52:return "SIGRTMAX-12";break;
		case 53:return "SIGRTMAX-11";break;
		case 54:return "SIGRTMAX-10";break;
		case 55:return "SIGRTMAX-9";break;
		case 56:return "SIGRTMAX-8";break;
		case 57:return "SIGRTMAX-7";break;
		case 58:return "SIGRTMAX-6";break;
		case 59:return "SIGRTMAX-5";break;
		case 60:return "SIGRTMAX-4";break;
		case 61:return "SIGRTMAX-3";break;
		case 62:return "SIGRTMAX-2";break;
		case 63:return "SIGRTMAX-1";break;
		case 64:return "SIGRTMAX";break;
		default: return "Unknown Error";break;
	}
	return "Unknown Error";
}




// ****************************************************
// Class Name: Main Features
// Class Module: Main
// Class Features: The Main Function
// ****************************************************

// Basic Variables
Json::FastWriter writer;
		
// Main Function
int main() {
	// Creating Daemon Processor
	// if(daemon(1,0)<0) return_error("Failed to create daemon process.");
	// system("ls");

	// Updating Working Directory
	int res=chdir("/etc/judge");
	
	// Reading Judger Configure
	ifstream fin("./config.json");
	if (!fin) return_error("Failed to open config file.");
	Json::Value config;Json::Reader reader;
	if (!reader.parse(fin,config,false)) return_error("Failed to parse json object in config file");
	
	// Connecting to the Database 
    mysqld conn,result,row;
    conn=mysqli_connect(config["mysql"]["server"].asString().c_str(),config["mysql"]["user"].asString().c_str(),
						config["mysql"]["passwd"].asString().c_str(),config["mysql"]["database"].asString().c_str(),
						config["mysql"]["port"].asInt());
    if (!conn) return_error(("Failed to connect database: "+mysqli_error(conn)).c_str());
	return_info("Listening to the database...");return_info("",true);
	
	// The Main Processor to Monitor the Database
    while (1) {
    	
    	// Querying Waited Judge Program
        result=mysqli_query(conn,"SELECT * FROM waited_judge");
		if (mysqli_num_rows(result)==0) {
			// If didn't find any program, continue monitor.
    		mysql_free_result(result.res);
			continue;
		}
		
		// re-Reading Judger Configure
		ifstream fin("./config.json");
		if (!fin) return_error("Failed to open config file.");
		Json::Value judge;Json::Reader reader;
		if (!reader.parse(fin,judge,false)) return_error("Failed to parse json object in config file");	
		
		// Judging Submitted Program
		while (row=mysqli_fetch_assoc(result)) {
			
			// ****************************************************
			// Class Name: Data Gainer
			// Class Module: Main
			// Class Features: Gain Data from Database
			// ****************************************************
			
			// Gain Data from Queried Result
			int pid=StringToInt(row["pid"]),uid=StringToInt(row["uid"]),id=StringToInt(row["id"]),lang=StringToInt(row["lang"]);
			string code=row["code"],ideinfo=row["ideinfo"];
			int retc=system("rm ./tmp/* -r");
			retc=chdir("./tmp/");			
			
			
			// ****************************************************
			// Class Name: Info Outputer
			// Class Module: Main
			// Class Features: Output Submitted Information
			// ****************************************************
			
			// Output Submitted Information
			return_info(("Read status id #"+IntToString(id)).c_str());
			return_info(("Problem id: #"+IntToString(pid)).c_str());
			return_info(("Submitted user id: #"+IntToString(uid)).c_str());
			return_info(("Language: "+judge["lang"][lang]["name"].asString()).c_str());
			
			// Output Source to the File
			ofstream fout(judge["lang"][lang]["source_path"].asString().c_str());
			fout<<code<<endl;
			fout.close();
			code=str_replace("'","\\'",str_replace("\\","\\\\",code.c_str()).c_str());




			// ****************************************************
			// Class Name: Source Compiler
			// Class Module: Main
			// Class Features: Compile Source File
			// ****************************************************
			
			// Update Judging State
			mysqli_query(conn,("UPDATE waited_judge SET status='Compiling...' WHERE id="+to_string(id)).c_str());
			
			// Compiling Code
			time_t st=clock();int retcode; string info_string="";
			if (judge["lang"][lang]["type"].asInt()!=1) {
				return_info(("Compiling code from status id #"+IntToString(id)).c_str());
				retcode=system2(judge["lang"][lang]["command"].asString().c_str(),info_string);
				
				// The Situation of Compile Error
				if (retcode) {
					Json::Value res;
					return_info("Error compile code!");
					return_info(("Compiler return error code "+to_string(retcode)).c_str());
					res["result"]="Compile Error";
					res["output"]="Compile Error";
					res["compile_info"]=res["info"]=info_string;return_info("",true);
					
					// Insert Data to the Database
					mysqli_query(conn,("INSERT INTO status (id,pid,uid,code,lang,result,time) VALUES \
					("+IntToString(id)+","+IntToString(pid)+","+IntToString(uid)+",'"+code+"',"+
					IntToString(lang)+",'"+str_replace("'","\\'",str_replace("\\","\\\\",writer.write(res).c_str()).c_str())+
					"',"+to_string(clock())+")").c_str());
					mysqli_query(conn,("DELETE FROM waited_judge WHERE id="+IntToString(id)).c_str());
					retc=chdir("../");
					continue;
				}
				return_info(("Compile finished, use "+to_string((clock()-st))+"ms").c_str());
			}
			retc=chdir("../");
					
					
					
					
			// ****************************************************
			// Class Name: Program Configure Gainer
			// Class Module: Main
			// Class Features: Gain Program Configure
			// ****************************************************
			
			// Reading Problem Configure.
			Json::Reader reader;
			Json::Value val;
			ifstream fin(("./problem/"+IntToString(pid)+"/config.json").c_str());
			
			// Failed to Open the Problem Configure
			if (pid&&!fin) {
				return_error(("Failed to open problem config file id #"+IntToString(pid)).c_str(),false);
				Json::Value res;
				res["result"]="No Test Data";
				
				// Insert Data to the Database
				mysqli_query(conn,("INSERT INTO status (id,pid,uid,code,lang,result,time) VALUES \
				("+IntToString(id)+","+IntToString(pid)+","+IntToString(uid)+",'"+code+"',"+
				IntToString(lang)+",'"+str_replace("'","\\'",str_replace("\\","\\\\",writer.write(res).c_str()).c_str())+
				"',"+to_string(clock())+")").c_str());
				mysqli_query(conn,("DELETE FROM waited_judge WHERE id="+IntToString(id)).c_str());
				continue;
			}
			
			// Failed to Parse JSON Object
			if (pid&&!reader.parse(fin,val,false)) {
				return_error(("Failed to parse json object in problem config file #"+IntToString(pid)).c_str(),false);
				Json::Value res;
				res["result"]="No Test Data";
				cout<<endl;
				
				// Insert Data to the Database
				mysqli_query(conn,("INSERT INTO status (id,pid,uid,code,lang,result,time) VALUES \
				("+IntToString(id)+","+IntToString(pid)+","+IntToString(uid)+",'"+code+"',"+
				IntToString(lang)+",'"+str_replace("'","\\'",str_replace("\\","\\\\",writer.write(res).c_str()).c_str())+
				"',"+to_string(clock())+")").c_str());
				mysqli_query(conn,("DELETE FROM waited_judge WHERE id="+IntToString(id)).c_str());
				continue;
			}




			// ****************************************************
			// Class Name: Special Judger Compiler
			// Class Module: Main
			// Class Features: Compile Special Judger
			// ****************************************************
			
			// Compiling Special Judger
			return_info(("Compiling special judge from status id #"+IntToString(id)).c_str());
			int state=0;Json::Value info,res;st=clock();string spj_path;
			
			if (!pid) ;
			// New Special Judger
			else if (val["spj"]["type"].asInt()==0) {
				int res=chdir(("./problem/"+to_string(pid)).c_str()); string info_string2="";
				retcode=system2((val["spj"]["compile_cmd"].asString()).c_str(),info_string2);
				if (retcode) {
					Json::Value res;
					return_info("Error compile special judge!");
					res["result"]="Compile Error";
					res["compile_info"]=res["info"]="In SPJ:\n"+info_string2;return_info("",true);
					mysqli_query(conn,("INSERT INTO status (id,pid,uid,code,lang,result,time) VALUES \
					("+IntToString(id)+","+IntToString(pid)+","+IntToString(uid)+",'"+code+"',"+
					IntToString(lang)+",'"+str_replace("'","\\'",str_replace("\\","\\\\",writer.write(res).c_str()).c_str())+
					"',"+to_string(clock())+")").c_str());
					mysqli_query(conn,("DELETE FROM waited_judge WHERE id="+IntToString(id)).c_str());
					continue;
				}
				res=chdir("../../");
				spj_path="./problem/"+to_string(pid)+"/"+val["spj"]["exec_path"].asString();
			} 
			
			// Invaild Special Judger Configure
			else if (val["spj"]["type"].asInt()>judge["spj"].size()) {
				return_error(("Failed to analyse special judge type in problem config file #"+IntToString(pid)).c_str(),false);
				Json::Value res;
				res["result"]="Compile Error";
				res["info"]=res["compile_info"]="Invaild special judge type!";
				mysqli_query(conn,("INSERT INTO status (id,pid,uid,code,lang,result,time) VALUES \
				("+IntToString(id)+","+IntToString(pid)+","+IntToString(uid)+",'"+code+"',"+
				IntToString(lang)+",'"+str_replace("'","\\'",str_replace("\\","\\\\",writer.write(res).c_str()).c_str())+
				"',"+to_string(clock())+")").c_str());
				mysqli_query(conn,("DELETE FROM waited_judge WHERE id="+IntToString(id)).c_str());
				continue;
			} 
			
			// Exist Special Judger Template
			else spj_path=judge["spj"][val["spj"]["type"].asInt()-1]["path"].asString();
			return_info(("Compile finished, use "+to_string((clock()-st))+"ms").c_str());

			// Copy Special Judger to the Temporary Directory
			int ret=system(("cp "+spj_path+" ./tmp/spj").c_str());




			// ****************************************************
			// Class Name: Program Judger
			// Class Module: Main
			// Class Features: Judge Test Data
			// ****************************************************	
			
			// Solve the Remote IDE Function
			if (pid==0) {
			    mysqli_query(conn,("UPDATE waited_judge SET status='Running...' WHERE id="+to_string(id)).c_str());
				ofstream fout("./tmp/test.in");
				Json::Value ide;int t,m;
				reader.parse(ideinfo,ide);
				fout<<ide["input"].asString();fout.close();
				int ret=chdir("./tmp");
				ret=run_code(judge["lang"][lang]["exec_command"].asString().c_str(),
				t,m,ide["t"].asInt(),ide["m"].asInt());
				int retc=chdir("../");
				if (ret) switch (ret) {
					case 2: res["result"]="Time Limited Exceeded",res["info"]="Time Limited Exceeded";break;
					case 3: res["result"]="Memory Limited Exceeded",res["info"]="Memory Limited Exceeded";break;
					case 4: res["result"]="Runtime Error",
					res["info"]="Runtime Error | "+analysis_reason(runtime_error_reason);break;
					default: res["result"]="Unknown Error",res["info"]="Unknown Error";break;
				} else {
					ifstream fin("./tmp/test.out");
					if (!fin) res["result"]="Wrong Answer";
					else {
						res["result"]="Accepted";
						string resstring="";
						while (!fin.eof()) {
							string a;getline(fin,a);
							resstring+=a+"\n";
						}
						res["output"]=resstring;
					}	
				} res["time"]=t,res["memory"]=m;
				res["compile_info"]=info_string;
				mysqli_query(conn,("INSERT INTO status (id,pid,uid,code,lang,result,time) VALUES \
				("+IntToString(id)+","+IntToString(pid)+","+IntToString(uid)+",'"+code+"',"+
				IntToString(lang)+",'"+str_replace("'","\\'",str_replace("\\","\\\\",writer.write(res).c_str()).c_str())+
				"',"+to_string(clock())+")").c_str());cout<<"INSERT INTO status (id,pid,uid,code,lang,result,time) VALUES \
				("+IntToString(id)+","+IntToString(pid)+","+IntToString(uid)+",'"+code+"',"+
				IntToString(lang)+",'"+str_replace("'","\\'",str_replace("\\","\\\\",writer.write(res).c_str()).c_str())+
				"',"+to_string(clock())+")"<<endl;
				mysqli_query(conn,("DELETE FROM waited_judge WHERE id="+IntToString(id)).c_str());
				return_info("",true);
				continue;
			}

			// Judging Program
			res["compile_info"]=info_string;int sum_t=0,max_m=0;
			for (int i=0;i<val["data"].size();i++) {
				Json::Value single;
				
				// Update Judging State
				mysqli_query(conn,("UPDATE waited_judge SET status='Running on Test "+to_string(i+1)+"...' WHERE id="+to_string(id)).c_str());
				
				// Copy Test Data
				if (system(("cp './problem/"+IntToString(pid)+"/"+val["data"][i]["input"].asString()+"' '"+
				"./tmp/"+val["input"].asString()+"'").c_str())) {
					return_error(("Failed to copy input file in problem #"+IntToString(pid)).c_str(),false);
					return_error(("Error file name: "+val["data"][i]["input"].asString()+"/"+
					val["data"][i]["output"].asString()).c_str(),false);
					continue;
				}
				
				// Remove Exist Output File
				int res=system(("rm ./tmp/"+val["output"].asString()).c_str());
				res=system(("touch ./tmp/"+val["output"].asString()).c_str());
				
				// Update Working Directory
				res=chdir("./tmp/");
				int t=0,m=0,ret;
				ret=run_code(judge["lang"][lang]["exec_command"].asString().c_str()
				,t,m,val["data"][i]["time"].asInt(),val["data"][i]["memory"].asInt());
				
				// Update Working Directory
				res=chdir("../");
				
				// When Exited Abnormally
				if (ret) {
					single["time"]=t;single["memory"]=m;
					sum_t+=t,max_m=max(max_m,m);
					
					// Update the Whole Judging State
					if (!state) state=ret;
					
					// Analyse Exited Reason and Full JSON Object
					switch (ret) {
						case 2: single["state"]="Time Limited Exceeded",single["info"]="Time Limited Exceeded";break;
						case 3: single["state"]="Memory Limited Exceeded",single["info"]="Memory Limited Exceeded";break;
						case 4: single["state"]="Runtime Error",
						single["info"]="Runtime Error | "+analysis_reason(runtime_error_reason);break;
						default: single["state"]="Unknown Error",single["info"]="Unknown Error";break;
					} single["score"]=0;
					return_info(single["info"].asString().c_str());
					
					// Append Result to the whole JSON Object
					info.append(single);
					continue;
				}
				
				// When Exited Normally
				single["time"]=t,single["memory"]=m;
				sum_t+=t,max_m=max(max_m,m);
				
				// Remove Exist Garbase
				ofstream tmpout("./tmp/score.txt");tmpout.close();
				tmpout.open("./tmp/info.txt");
				
				// Gain the Absolute Path for some File
				string inputpath=getpath(("./problem/"+IntToString(pid)+"/"+val["data"][i]["input"].asString()).c_str());
				string outputpath=getpath(("./tmp/"+val["output"].asString()).c_str());
				string answerpath=getpath(("./problem/"+IntToString(pid)+"/"+val["data"][i]["output"].asString()).c_str());
				string resultpath=getpath("./tmp")+"/score.txt",infopath=getpath("./tmp")+"/info.txt";int spjt,spjm;
				
				// Update Working Directory
				res=chdir("./tmp");
				
				// Running Special Judger
				ret=run_code(("./spj "+inputpath+" "+outputpath+" "+answerpath+" "+
				val["data"][i]["score"].asString()+" "+resultpath+" "+infopath+" "+
				val["spj"]["exec_param"].asString()).c_str(),
				spjt,spjm,val["data"][i]["time"].asInt(),val["data"][i]["memory"].asInt(),true);
				
				// Update Working Directory
				res=chdir("../");
				
				// When SPJ Exited Abnormally
				if (ret) {
					single["time"]=spjt+t;single["memory"]=spjm+m;
					sum_t+=spjt+t,max_m=max(max_m,spjm+m);
					
					// Update the Whole State
					if (!state) state=ret;
					
					// Analyse Exited Reason and Full JSON Object
					switch (ret) {
						case 2: single["state"]="Time Limited Exceeded",single["info"]="Time Limited Exceeded";break;
						case 3: single["state"]="Memory Limited Exceeded",single["info"]="Memory Limited Exceeded";break;
						case 4: single["state"]="Runtime Error",
						single["info"]="Runtime Error | "+analysis_reason(runtime_error_reason);break;
						default: single["state"]="Unknown Error",single["info"]="Unknown Error";break;
					} single["score"]=0;
					return_info(single["info"].asString().c_str());
					
					// Append Result to the whole JSON Object
					info.append(single);
					continue;
				}
				
				// Read Score and Judger Info
				int gain_score=0;ifstream scorein("./tmp/score.txt");scorein>>gain_score;scorein.close();
				string spj_info="";ifstream infoin("./tmp/info.txt");
				while (!infoin.eof()) {
					string input;getline(infoin,input);
					spj_info+=input+"\n";
				} infoin.close();
				
				// Analyse Result and Full JSON Object
				int now_state=0; 
				if (gain_score>=val["data"][i]["score"].asInt()) return_info("Accepted | OK!"),single["state"]="Accepted",now_state=0;
				else if (gain_score==0) return_info("Wrong Answer!"),single["state"]="Wrong Answer",now_state=1;
				else now_state=7,return_info(("Partially Correct, Gain "+to_string(gain_score)+"/"+
				val["data"][i]["score"].asString()+"!").c_str()),single["state"]="Partially Correct";
				
				// Update the Whole State
				if (!state) state=now_state;
				
				// Full the Information of this Test Data
				single["info"]=spj_info;single["score"]=gain_score;info.append(single);
			}
			
			// When There are no Test Data
			if (val["data"].size()==0) state=5;
			
			// Analyse the Whole Problem State
			switch(state) {
				case 0: res["result"]="Accepted";break;
				case 1: res["result"]="Wrong Answer";break;
				case 2: res["result"]="Time Limited Exceeded";break;
				case 3: res["result"]="Memory Limited Exceeded";break;
				case 4: res["result"]="Runtime Error";break;
				case 5: res["result"]="No Test Data";break;
				case 6: res["result"]="Unknown Error";break;
				case 7: res["result"]="Partially Correct";break;
			}
			
			// Full the JSON Object
			res["info"]=info;res["time"]=sum_t;res["memory"]=max_m;
			return_info("",true);
			
			// Upload data to the database.
			mysqli_query(conn,("INSERT INTO status (id,pid,uid,code,lang,result,time) VALUES \
			("+IntToString(id)+","+IntToString(pid)+","+IntToString(uid)+",'"+code+"',"+
			IntToString(lang)+",'"+str_replace("'","\\'",str_replace("\\","\\\\",writer.write(res).c_str()).c_str())+
			"',"+to_string(clock())+")").c_str());cout<<"INSERT INTO status (id,pid,uid,code,lang,result,time) VALUES \
			("+IntToString(id)+","+IntToString(pid)+","+IntToString(uid)+",'"+code+"',"+
			IntToString(lang)+",'"+str_replace("'","\\'",str_replace("\\","\\\\",writer.write(res).c_str()).c_str())+
			"',"+to_string(clock())+")"<<endl;
			mysqli_query(conn,("DELETE FROM waited_judge WHERE id="+IntToString(id)).c_str());
		}
    }
}
