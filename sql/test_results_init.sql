create table if not exists test_result(
    id integer primary key,
    datestr varchar(15) not null,
    fio varchar(50) not null,
    lim_answ varchar(50) default null,
    series_answ integer default null,
    hard_answ integer default null
);
