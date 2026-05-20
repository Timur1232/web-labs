create table if not exists blogs (
    id integer primary key,
    datestr varchar(15) not null,
    author varchar(50) not null,
    title varchar(50) not null,
    image_path varchar(50) default null,
    text text not null
);
