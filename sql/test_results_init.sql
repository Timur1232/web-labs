create table if not exists test_result (
    id integer primary key,
    datestr varchar(15) not null,
    fio varchar(50) not null,
    lim_answ varchar(50) default null,
    series_answ varchar(50) default null,
    hard_answ varchar(50) default null
);

insert into test_result (id, datestr, fio, lim_answ, series_answ, hard_answ) values
(1,'20260324-153646','Timur Bai Rash',null,'Ожидалось: 2). Получено: 1.',null),
(2,'20260324-153707','Abobus','Ожидалось: 5. Получено: urmom.','Ожидалось: 2). Получено: 1.','Ожидалось: ???. Получено: 4.'),
(3,'20260324-153927','Ахахахахахах','Ожидалось: 5. Получено: 69.',null,null),
(4,'20260324-211709','Urmom',null,null,null);
