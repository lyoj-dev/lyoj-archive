#ifndef _MYSQLD_H_
#define _MYSQLD_H_

#define MYSQLD_CONNECT (1<<0)
#define MYSQLD_QUERYED (1<<1)
#define MYSQLD_USED (1<<2)
using namespace std;

struct mysqld{
    long long f=0;
    MYSQL conn;
    MYSQL_RES* res;
    MYSQL_FIELD *fd;
    MYSQL_ROW row;
    string err="";bool errstate=0;
    vector<string> field;
    map<string,string> dat;
    string operator [] (const char* key) {
        return this->dat[key];
    }
    operator bool() {
        return !errstate;
    }
};
mysqld mysqli_connect(const char* address,const char* user,const char* passwd,const char* db,int port=3306) {
    mysqld res;mysql_init(&res.conn);
    bool re=mysql_real_connect(&res.conn,address,user,passwd,db,port,nullptr,0);
    if (!re) res.err=mysql_error(&res.conn),res.errstate=1;
    res.f|=MYSQLD_CONNECT;return res;
}
mysqld mysqli_query(mysqld conn,const char* sql) {
    mysqld res=conn;
    bool re=mysql_query(&res.conn,sql);
    if (re) res.err=mysql_error(&res.conn),res.errstate=1;
    res.res=mysql_store_result(&res.conn);
    return res;
}
mysqld mysqli_fetch_assoc(mysqld& res) {
    mysqld result=res;
    if (!(result.f&(MYSQLD_QUERYED))) {
        for(int i=0;result.fd=mysql_fetch_field(result.res);i++) 
            result.field.push_back(result.fd->name);
        result.f|=MYSQLD_QUERYED;
    }
    result.row=mysql_fetch_row(result.res);
    if (result.row==NULL) {
        result.errstate=1;
        return result;
    }result.dat.clear();
    for (int i=0;i<result.field.size();i++) 
        result.dat[result.field[i]]=result.row[i];
    res=result;return result;
}
string mysqli_error(mysqld conn) {
    return conn.err;
}
unsigned int mysqli_num_rows(mysqld result) {
    return mysql_num_rows(result.res);
}

#endif

