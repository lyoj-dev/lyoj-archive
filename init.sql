create table status (id int(255),uid int(255),pid int(255),lang int(11),code text,result text,time int(11));
create table waited_judge (id int(255),uid int(255),pid int(255),lang int(11),code text,time int(11),status text);
create table problem (id int(255),name text,bg text,descrip text,input text,output text,cases text,hint text);