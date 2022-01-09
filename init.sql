create table status (
    id int(255),
    uid int(255),
    pid int(255),
    lang int(11),
    code text character set utf8,
    result text character set utf8,
    time int(11)
);
create table waited_judge (
    id int(255),
    uid int(255),
    pid int(255),
    lang int(11),
    code text character set utf8,
    time int(11),
    status text character set utf8
);
create table problem (
    id int(255),
    name text character set utf8,
    bg text character set utf8,
    descrip text character set utf8,
    input text character set utf8,
    output text character set utf8,
    cases text character set utf8,
    hint text character set utf8
);