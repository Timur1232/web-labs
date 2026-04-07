create table if not exists statistics (
    id integer primary key,
    datestr varchar(15) not null,
    web_page varchar(50) not null,
    ip_address varchar(50) default null,
    host_name varchar(50) default null,
    browser_name varchar(50) default null
);
