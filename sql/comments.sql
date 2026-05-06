create table if not exists comments (
    id integer primary key,
    blog_id integer not null,
    datestr varchar(15) not null,
    user_name varchar(50) not null,
    text text not null,

    foreign key (blog_id) references blog(id)
);
