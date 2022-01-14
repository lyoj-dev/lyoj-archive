create table status (
    id int(255),
    uid int(255),
    pid int(255),
    lang int(11),
    code longtext character set utf8,
    result longtext character set utf8,
    time int(11)
);
create table waited_judge (
    id int(255),
    uid int(255),
    pid int(255),
    lang int(11),
    code longtext character set utf8,
    time int(11),
    status longtext character set utf8,
    ideinfo longtext character set utf8
);
create table problem (
    id int(255),
    name longtext character set utf8,
    bg longtext character set utf8,
    descrip longtext character set utf8,
    input longtext character set utf8,
    output longtext character set utf8,
    cases longtext character set utf8,
    hint longtext character set utf8
);