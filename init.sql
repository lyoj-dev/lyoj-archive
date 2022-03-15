create table contest (
    id int(255),
    title longtext character set utf8,
    descrip longtext character set utf8,
    starttime int(255),
    duration int(255)
);
create table crontab (
    id int(255),
    duration int(20),
    lasttime int(20),
    command longtext character set utf8
);
create table judger (
    id int(255),
    config longtext character set utf8,
    name longtext character set utf8,
    heartbeat int(20)
);
create table logindata (
    uid int(255),
    csrf longtext character set utf8,
    sessdata longtext character set utf8,
    time int(20)
);
create table problem (
    id int(255),
    name longtext character set utf8,
    bg longtext character set utf8,
    descrip longtext character set utf8,
    input longtext character set utf8,
    output longtext character set utf8,
    cases longtext character set utf8,
    hint longtext character set utf8,
    hidden bool,
    banned bool,
    difficult int(11),
    contest int(255)
);
create table status (
    id int(255),
    uid int(255),
    pid int(255),
    lang int(11),
    code longtext character set utf8,
    result longtext character set utf8,
    time int(11),
    status longtext character set utf8,
    ideinfo longtext character set utf8,
    judged bool
);
create table tags (
    tagname longtext character set utf8,
    pid int(255)
);
create table user (
    id int(255),
    name varchar(255) character set utf8,
    passwd longtext character set utf8,
    permission int(10),
    email longtext character set utf8,
    salt longtext character set utf8,
    salttime int(20)
);